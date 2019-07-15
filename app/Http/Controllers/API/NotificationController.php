<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class NotificationController extends Controller
{
    public function test_ios_notify(Request $request)
    {
        //$device_token=Token generate by ios device
        $device_token = $request->token;
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
            'type' => 1,
            'alert' => 'Test notification',
            'badge' => 0,
            'sound' => 'newMessage.wav'
        );

        $ctx = stream_context_create();
        stream_context_set_option($ctx, 'ssl', 'local_cert', $ck_pem_path);
        stream_context_set_option($ctx, 'ssl', 'passphrase', $passphrase);
        // Open a connection to the APNS server
        $fp = stream_socket_client(
                $socket_url, $err, $errstr, 60, STREAM_CLIENT_CONNECT | STREAM_CLIENT_PERSISTENT, $ctx
        );
        if (!$fp)
            $error = "Failed to connect: $err $errstr" . PHP_EOL;
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
        echo $return;
    }

    public function test_android_notify(Request $request)
    {
        $url = 'https://fcm.googleapis.com/fcm/send';
        $auth_key='AIzaSyAori6JXh6YkqVtStHsROscflaLAlK0DTk'; // Fire base api key
    
        $response = array(
			'module'=>"12_hours",
			'message'=>"Test Push Notification",
			'data'=>"Test Push Notification",
		);
		$id = array($request->token);
    	$fields = array( 'registration_ids' => $id, 'data' => $response );
    	$headers = array( 'Authorization: key='.$auth_key, 'Content-Type: application/json' );

    	$ch = curl_init();
    	curl_setopt($ch, CURLOPT_URL, $url);
    	curl_setopt($ch, CURLOPT_POST, true);
    	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    	curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
    	$result = curl_exec($ch);
        curl_close($ch);
    	if ($result === FALSE)
    	{
            $error = 'Curl failed: ' . curl_error($ch);
            return $error;
		}else{
            return $result;
		}
    }
}
