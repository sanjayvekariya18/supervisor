<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Helpers\ConsentHelper;
use App\Models\User;
use App\Models\Machine;
use App\Models\MachineGroup;
use File;
use DB;
class SendHourlyStopMachineNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send_hourly_stop_machine_notification';
    //Google cloud messaging GCM-API url
	protected $url = 'https://fcm.googleapis.com/fcm/send';
	protected $auth_key='AIzaSyAori6JXh6YkqVtStHsROscflaLAlK0DTk'; // Fire base api key
	protected $logPath = "/_log/stop_machine_notification.log";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';
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
                $this->SendIOSNotification($user,$message);	
            }else{
                $this->SendNotification($user,$message);
            }
            
            $msg = "$user->id : $message.\n";
            File::append(public_path($this->logPath),$msg."\n");
            \Auth::logout();
        }
    }

    function SendNotification($user,$message)
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
			File::append(public_path($this->logPath),$error."\n");
		}else{
			File::append(public_path($this->logPath),$result."\n");
		}
    	curl_close($ch);
        return true;
    }

    public function SendIOSNotification($user,$message)
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
			File::append(public_path($this->logPath),$error."\n");
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
		File::append(public_path($this->logPath),$return."\n");
        return true;
    }
}
