<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\User\ReportsController;
use App\Models\MachineHourlyData;
use App\Models\MachineData;
use App\Models\Machine;
use App\Models\User;
use App\Models\Setting;
use App\Models\MachineGroup;
use App\Helpers\ConsentHelper;
use Carbon\Carbon;
use File;
use DB;

class CronController extends Controller
{

    
    //Google cloud messaging GCM-API url
	protected $url = 'https://fcm.googleapis.com/fcm/send';
	protected $auth_key='AIzaSyAori6JXh6YkqVtStHsROscflaLAlK0DTk'; // Fire base api key
    protected $logPath = "/_log/stop_machine_notification.log";
	protected $logPath1 = "/_log/12_hours_notification.log";

    /* Reset Max RPM - RUN @ every 30mins */
    public function reset_max_rpm()
    {
        $flag = Machine::where([
            'status_id' => 1
        ])->update([
            'max_rpm' => 0
        ]);

        if ($flag) {
            $this->info("Successfully reset Max RPM");
        }else{
            $this->error("Unable to reset Max RPM");
        }
    }

    /* Delete 5 Min Record */
    public function delete_5min_record()
    {
        MachineData::whereDate('created_at','<',Carbon::now()->subDays(7))->delete();
    }

    /* Delete 1 Hour Record */
    public function delete_1hour_record()
    {
        MachineHourlyData::whereDate('report_date','<',Carbon::now()->subDays(45))->delete();
    }

    public function send_sms_for_disconnected_machines()
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

    public function send_hourly_stop_machine_notification()
    {
        $currertDateTime = date('Y-m-d H:i:s');
		File::append(public_path($this->logPath),"-------------------------$currertDateTime-------------------------"."\n");
        $users =  User::all();
        // $users =  User::where('id','HEER@101')->get();
        foreach ($users as $user) {
			if($user->token == NULL)
                continue;
            \Auth::loginUsingId($user->id);
            $owner =  User::find($user->parent_id);
            $userId = ($user->parent_id == NULL) ? $user->id : $user->parent_id ;
            $comapnyName = ($user->parent_id == NULL) ? $user->company_name : $owner->company_name;
            $m_conditions['cust_id'] = $userId;

            $machines = Machine::selectRaw(
                    'IFNULL(machine_number,0) machine_number,
                    TIMESTAMPDIFF(MINUTE,last_sync,NOW()) AS stop_since_min'
                )->where($m_conditions);

            if ($user->parent_id != NULL) {
                $groups = MachineGroup::where('supervisor_id','LIKE',"%".$user->id."%")->pluck('id')->toArray();
                $machines = $machines->whereIn('group_id',$groups);
            }
            $machines = $machines->orderByRaw('LENGTH(machine_id) ASC')
                ->havingRaw('stop_since_min > 15')
                ->orderBy('machine_id','ASC')
                ->get()->pluck('machine_number')->toarray();

            $machine_list = implode(',',$machines);
            if(count($machines) == 0){
                $msg = "$user->id : All machine are Running Now.\n";
                File::append(public_path($this->logPath),$msg."\n");
                continue;
            }
            $message = __(":company \nYour machine no:- :list has been stopped since 15 minutes, Kindly reconnect it.\n(Supervisor07)",
                 [
                     'list' => $machine_list,
                     'company' => $comapnyName,
                 ]
            );
            if($user->device_type == "ios"){
                $this->SendIOSNotification($user,$message,$this->logPath);	
            }else{
                $this->SendNotification($user,$message,$this->logPath);
            }
            
            $msg = "$user->id : $message.\n";
            File::append(public_path($this->logPath),$msg."\n");
            \Auth::logout();
        }
    }

    public function send_12hour_report_notification(ReportsController $ReportsController)
    {
		$currertDateTime = date('Y-m-d H:i:s');
		File::append(public_path($this->logPath1),"-------------------------$currertDateTime-------------------------"."\n");
       	$users =  User::all();
		//$users =  User::where('id','SHREERAM@SUP3')->get();
       	foreach ($users as $user) {
			if($user->token == NULL)
				continue;
			\Auth::loginUsingId($user->id);
			//if($user->parent_id == NULL){
				$report_type = "12_hours";
				$machine_no = "all";
				$group_id = "all";;
				$worker_list = "all";;
				$from_date = date('Y-m-d');
				$to_date = 	date('Y-m-d');

				$search = [
					'machine_no' => "all",
					'worker_list' => "all",
					'group_id' => "all",
					'from_date' => date('Y-m-d'),
					'to_date' => date('Y-m-d')
				];

				$options = $ReportsController->generate_options($search,$user->id);
				
				$options['view_mode'] = 'download';
				$report_data = $ReportsController->generate_12hr_report($options);
				
				if ($report_data['status']==1) {
					$report_url = generate_report_url($report_data['file_name']);
					if($user->device_type == "ios"){
						$this->SendIOSNotification($user,$report_url,$this->logPath1);	
					}else{
						$this->SendNotification($user,$report_url,$this->logPath1);
					}
					$msg = "$currertDateTime : $user->id : $report_url.\n";
				}else{
					$msg = "$currertDateTime : $user->id : Unable to generate PDF, please try again later.\n";
				}
				File::append(public_path($this->logPath1),$msg."\n");
        	/* }else{
				echo "Supervisor Updated.\n";
			} */
			\Auth::logout();
       	}
	}
    
    function SendNotification($user,$message,$logPath)
    {   
    	$response = array(
			'module'=>"stop_machine",
			'message'=>$message,
			'data'=>'',
		);
		$id = array($user->token);
    	$fields = array( 'registration_ids' => $id, 'data' => $response );
    	$headers = array( 'Authorization: key='.$this->auth_key, 'Content-Type: application/json' );

    	$ch = curl_init();
    	curl_setopt($ch, CURLOPT_URL, $this->url);
    	curl_setopt($ch, CURLOPT_POST, true);
    	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    	curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
    	$result = curl_exec($ch);

    	if ($result === FALSE)
    	{
			$error = 'Curl failed: ' . curl_error($ch);
			File::append(public_path($logPath),$error."\n");
		}else{
			File::append(public_path($logPath),$result."\n");
		}
    	curl_close($ch);
        return true;
    }

    public function SendIOSNotification($user,$message,$logPath)
    {
        //$device_token=Token generate by ios device
        $device_token = $user->token;
        $deviceToken = "" . $device_token . "";

        //$passphrase=When you generate ck.pem used inside 
        $passphrase = '';

        //Ck.pem half path of server
        $ck_pem_path = public_path("ck.pem");

        //When your application live then change development to production
        $development = "development";


        if ($development == "development") {
            $socket_url = "ssl://gateway.sandbox.push.apple.com:2195";
        } else {
            $socket_url = "ssl://gateway.push.apple.com:2195";
        }

        $message_body = array(
            'type' => 'stop_machine',
            'alert' => $message,
            'badge' => 0,
			'sound' => 'newMessage.wav',
			'data' => null,
        );

        $ctx = stream_context_create();
        stream_context_set_option($ctx, 'ssl', 'local_cert', $ck_pem_path);
        stream_context_set_option($ctx, 'ssl', 'passphrase', $passphrase);
        // Open a connection to the APNS server
        $fp = stream_socket_client(
                $socket_url, $err, $errstr, 60, STREAM_CLIENT_CONNECT | STREAM_CLIENT_PERSISTENT, $ctx
        );
        if (!$fp){
			$error = "Failed to connect: $err $errstr" . PHP_EOL;
			File::append(public_path($logPath),$error."\n");
		}
        $body['aps'] = $message_body;
        $payload = json_encode($body);
        // Build the binary notification
        $msg = chr(0) . pack('n', 32) . pack('H*', $deviceToken) . pack('n', strlen($payload)) . $payload;
        // Send it to the server
        $result = fwrite($fp, $msg, strlen($msg));
        fclose($fp);
        if (!$result) {
            $return = "Error, notification not sent" . PHP_EOL;
        } else {
            $return = "Success, notification sent";
		}
		File::append(public_path($logPath),$return."\n");
        return true;
    }

}
