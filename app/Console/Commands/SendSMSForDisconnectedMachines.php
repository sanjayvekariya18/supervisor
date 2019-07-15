<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Machine;
use App\Models\Setting;
use DB;
use Carbon\Carbon;
use File;
class SendSMSForDisconnectedMachines extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send_sms_for_disconnected_machines';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send SMS for Disconnected Machines @ 30 Mins Before Shift changes';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $path = public_path('/_log/sms.log');
        $currertDateTime = date('Y-m-d H:i:s');
		File::append($path,"-------------------------$currertDateTime-------------------------"."\n");
        $Nowdate = Carbon::now();
        $shift = (Carbon::now()->hour == 20) ? 1 : 2;
        File::append($path,"Shift : $shift"."\n");
            
        $template = Setting::where('name','MACHINE_DISCONNECT_SMS_TEMPLATE')->first();    
        $current_time = date("H:i:00");
        $customers = User::where('sms_balance','>=',0)
                    ->whereNull('parent_id')
                    ->where('sms_notification_status',1)
                    ->get();

        
        /* echo "<pre>";
        print_r($customers);
        die; */
        $result=[];
        foreach ($customers as $customer) {
            $contact = "9537830908,8866260266,7359995666";
            File::append($path,"========================================\n");
            File::append($path,json_encode($customer->id)."\n");
            $query = "SELECT 
                    IFNULL(l.machine_number,0) machine_number
                    ,TIMESTAMPDIFF(MINUTE,l.`updated_at`,NOW()) AS stop_since_min
                    FROM machines l WHERE cust_id='".$customer->id."'
                    GROUP BY l.machine_number 
                    HAVING stop_since_min > 30";
            $machines = [];
            $disconnectedMachines = DB::select($query);
            foreach ($disconnectedMachines as $disconnectedMachine) {
                $machines[] = $disconnectedMachine->machine_number;
            }
            File::append($path,"M List : ".implode(',',$machines)."\n");
            //$machines = $customer->machines()->where('status_id',0)->get()->pluck('machine_number')->toarray();
            if(count($machines) == 0)
                continue;
            sort($machines);
            $machine_list = implode(',',$machines);
            $message = __(":company \nYour machine no:- :list have been disconnected from the internet, Kindly reconnect it.\n(Supervisor07)", [
                'company' => $customer->company_name, 
                'list' => $machine_list
            ]);
            if(!empty($customer->contact_number_1) && strlen($customer->contact_number_1) == 10){
                $contact .= ",".$customer->contact_number_1;
                File::append($path,"Contact : ".$contact."\n");
            }else if(!empty($customer->contact_number_2) && strlen($customer->contact_number_2) == 10){
                $contact = ",".$customer->contact_number_2;
                File::append($path,"Contact : ".$contact."\n");
            }else{
                File::append($path,"---------Invalid Contact----------\n");
            }

            $ch = curl_init();
            $url = "http://login.businesslead.co.in/api/mt/SendSMS?";
            $postData = array(
                "user" => 'DWARKESH',
                "password" => 'DWARKESH@952',
                "senderid" => 'FINNEX',
                "channel"=>"Trans",
                "DCS"=>0,
                "route"=>33,
                "flashsms"=>0,
                "text"=>$message,
                "number"=>$contact
            );
            $url = $url . http_build_query($postData);
            curl_setopt($ch, CURLOPT_URL,$url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $server_output = curl_exec($ch);
            //File::append($path,$server_output."\n");
            $response = json_decode($server_output,true);
            curl_close ($ch);
            if (!empty($response["ErrorCode"])  && $response["ErrorCode"]=="000" && !empty($response["ErrorMessage"]) && $response["ErrorMessage"]=="Done") {
                $response = $message;
            }else{
                $response = $response["ErrorMessage"];
            }
            //$location = public_path("_log/sms_send_logs.log");
            $txt = date('Y-m-d H:i:s') . " >>> " . $response;
            File::append($path,$txt."\n");
            //file_put_contents($location, $txt.PHP_EOL , FILE_APPEND | LOCK_EX);
        }
    }
    /* function get_stopped_machines($param=array())
	{
		global $outputjson, $gh, $db, $gm;
		
		$lg       		= $gh->read("lg","en");
	
		
		
        $Nowdate = date("Y-m-d H:i:s");
		$getData = $db->execute("SELECT 
			IFNULL(l.id,0) id
			,IFNULL(l.m_no,0) m_no
			,IFNULL(l.shift,0) shift
			,IFNULL(l.w_no,0) w_no
			,IFNULL(wd.w_name,'N/A') w_name
			,TIMESTAMPDIFF(MINUTE,l.`date`,'".$Nowdate."') AS stop_since_min
			,CONCAT(FLOOR(TIMESTAMPDIFF(MINUTE,l.`date`,'".$Nowdate."')/60),'h ',MOD(TIMESTAMPDIFF(MINUTE,l.`date`,'".$Nowdate."'),60),'m')  as stop_since_hrs
			FROM live l
			LEFT JOIN worker_detail wd ON l.w_no = wd.w_no
			GROUP BY l.m_no 
			HAVING stop_since_min > 30");
	    if($getData > 0)
	    {	
			
	    	$outputjson['data'] = $getData;
	    	$outputjson['success'] = 1;
	    	$outputjson['message'] = $gm->lg_ms("APP-00002",$lg);
	    	
	    }
	    else
	    {
	    	$outputjson['success'] = 0;
	    	$outputjson['message'] = $gm->lg_ms("APP-00003",$lg);
	    }
	} */
}
