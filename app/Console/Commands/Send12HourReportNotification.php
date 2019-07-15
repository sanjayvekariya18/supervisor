<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Http\Controllers\User\ReportsController;
use File;
class Send12HourReportNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
	protected $signature = 'send_12hour_report_notification';
	//Google cloud messaging GCM-API url
	protected $url = 'https://fcm.googleapis.com/fcm/send';
	protected $auth_key='AIzaSyAori6JXh6YkqVtStHsROscflaLAlK0DTk'; // Fire base api key
	protected $logPath = "/_log/12_hours_notification.log";

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
    public function handle(ReportsController $ReportsController)
    {
		$currertDateTime = date('Y-m-d H:i:s');
		File::append(public_path($this->logPath),"-------------------------$currertDateTime-------------------------"."\n");
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
						$this->SendIOSNotification($user,$report_url);	
					}else{
						$this->SendNotification($user,$report_url);
					}
					$msg = "$currertDateTime : $user->id : $report_url.\n";
				}else{
					$msg = "$currertDateTime : $user->id : Unable to generate PDF, please try again later.\n";
				}
				File::append(public_path($this->logPath),$msg."\n");
        	/* }else{
				echo "Supervisor Updated.\n";
			} */
			\Auth::logout();
       	}
	}
	
	function SendNotification($user,$report_url)
    {   
    	$response = array(
			'module'=>"12_hours",
			'message'=>"You 12 hour Report is ready. Please click on below link to view.",
			'data'=>$report_url,
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
	
	public function SendIOSNotification($user,$report_url)
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
            'type' => '12_hours',
            'alert' => 'You 12 hour Report is ready. Please click on below link to view.',
            'badge' => 0,
			'sound' => 'newMessage.wav',
			'data' => $report_url,
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
