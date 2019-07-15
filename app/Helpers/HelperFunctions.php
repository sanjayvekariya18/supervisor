<?php 
    
    use App\Models\Machine;
    use App\Models\MachineGroup;
    use App\Models\User;
    // use Auth;

	define('ENC_METHOD', 'AES-256-CBC');
    define('ENC_SECRET_KEY', '5f8h7e4x5j7h4r8e8c5v5n8j7e9s3c3b41n747h8d9s9xv4');
    define('ENC_SECRET_IV', 'q1w2e2r3ty6y5u5o78a8df88j7k7l88z8c8b8mm2');
    define('ENC_KEY', hash('sha256', ENC_SECRET_KEY));
    define('ENC_IV', substr(hash('sha256', ENC_SECRET_IV), 0, 16));
    
    // Custom Constants
    define('PAGE_LIMIT', 25);
    define('DS', DIRECTORY_SEPARATOR);

	function shifts()
	{
		return [
            0 => 'All',
			1 => 'Day',
			2 => 'Night'
		];
	}

	function range_colors()
	{
		return [
			'#EC3305' => 'Red',
			'#FFB64D' => 'Orange',
            '#000000' => 'Black',
            '#B014FF' => 'Purple',
            '#E3BF40' => 'Golden',
            '#05C6E3' => 'Sky-Blue',
			'#008000' => 'Green',
			'#0000FF' => 'Blue'
        ];
        // return [
        //     '#00ff80' => "Spring Green",
        //     '#f0ff27' => "Yellow",
        //     '#01fff5'=> "Cyan",
        //     '#fbc318'=> "Lightning Yellow",
        //     '#00EAFA'=> "Aqua",
        //     '#ffa07a'=> "Vivid Tangerine"
        // ];
	}

	function encrypt_str($sData)
    {
        $output = false;
        $output = openssl_encrypt($sData, ENC_METHOD, ENC_KEY, 0, ENC_IV);
        $output = base64_encode($output);

        return $output;
    }

    function decrypt_str($sData)
    {
        $output = openssl_decrypt(base64_decode($sData), ENC_METHOD, ENC_KEY, 0, ENC_IV);
        return $output;
    }

    function day_shifts()
    {
    	return [
    		'08:00:00' => '08:00 AM',
    		'08:30:00' => '08:30 AM',
    		'09:00:00' => '09:00 AM',
    		'09:30:00' => '09:30 AM',
    		'10:00:00' => '10:00 AM',
    	];
    }

    function night_shifts()
    {
    	return [
    		'20:00:00' => '08:00 PM',
    		'20:30:00' => '08:30 PM',
    		'21:00:00' => '09:00 PM',
    		'21:30:00' => '09:30 PM',
    		'22:00:00' => '10:00 PM',
    	];
    }

    function buzz_time()
    {
        return [
            '0' => 'Select',
            '00:00:10' => '10 Seconds',
            '00:00:30' => '30 Seconds',
            '00:01:00' => '1 Minute',
            '00:02:00' => '2 Minutes',
        ];
    }

    function default_buzz_time_minutes()
    {
        $minutes = [];
        for ($i=0; $i <= 15; $i++) { 

            if ($i<=1) {
                $minutes[$i] = $i . ' Minute';
            }else{
                $minutes[$i] = $i . ' Minutes';
            }
        }
        return $minutes;
        
    }

    function default_buzz_time_seconds()
    {
        $seconds = [];
        for ($i=0; $i <= 60; $i+=5) { 

            if ($i<=1) {
                $seconds[$i] = $i . ' Second';
            }else{
                $seconds[$i] = $i . ' Seconds';
            }
        }
        return $seconds;
    }

    function buzz_blank_time()
    {
        return [
            '10_min'=>0,
            '20_min'=>0,
            '30_min'=>0
        ];
    }

    function transaction_type()
    {
        return [
            'credit' => 'Credit',
            'debit' => 'Debit'
        ];
    }

    function format_date($date_time='',$format = 'd-m-Y h:i:s A')
    {
        return date($format,strtotime($date_time));
    }

    function report_types()
    {
        $report = array();
        (hasAccess('report_hour_12')) ? $report['12_hours'] = '12 Hours' : '';
        (hasAccess('report_hour_6')) ? $report['6_hours'] = '6 Hours' : '';
        (hasAccess('report_hour_3')) ? $report['3_hours'] = '3 Hours' : '';
        (hasAccess('report_min_5')) ? $report['5_min_diff'] = '5 Minutes Diff' : '';
        return $report;
        /* return [
            // '1_hour' => '1 Hour',            
            '12_hours' => '12 Hours',
            '6_hours' => '6 Hours',
            '3_hours' => '3 Hours',
            '5_min_diff' => '5 Minutes Diff',
        ]; */
    }

    function columns_skeleton()
    {
        return [
            'columns' => [
                'thred_break' => 0,
                'working_head' => 0,
                'manual_reading' => 0,
            ]
        ];
    }

    function generate_report_url($file_name='')
    {
        $url = '';
        if (!empty($file_name)) {
            $app_url = env('APP_URL','//emb.supervisor07.com/');
            $url = $app_url . 'reports' . DS . $file_name;
        }   

        return $url;
    }

    function generate_graph_url($file_name='')
    {
        $url = '';
        if (!empty($file_name)) {
            $app_url = env('APP_URL','//emb.supervisor07.com/');
            $url = $app_url . 'graphs' . DS . $file_name;
        }   

        return $url;
    }

    function secondsToTime($secs=0) {
        $secs = round($secs);
        $secs = abs($secs);
        $hours = floor($secs / 3600) . ':';
        $minutes = substr('00' . floor(($secs / 60) % 60), -2) . ':';
        $seconds = substr('00' . $secs % 60, -2);
        return $hours . $minutes . $seconds;
    }

    function secondsToMinuteSeconds($secs=0) {
        $secs = round($secs);
        $secs = abs($secs);
        $hours = floor($secs / 3600) . ':';
        $minutes = substr(floor(($secs / 60) % 60), -2) . ':';
        $seconds = substr($secs % 60, -2);
        return $minutes . $seconds;
    }

    function disconnect_alert()
    {
        return [
            '0' => 'Select',
            '15' => '15 Minutes',
            '30' => '30 Minutes',
            '40' => '40 Minutes',
            '60' => '1 Hour',
        ];
    }

    function dateDiffInDays($date1, $date2)  
    { 
        // Calculating the difference in timestamps 
        $diff = strtotime($date2) - strtotime($date1); 
          
        // 1 day = 24 hours 
        // 24 * 60 * 60 = 86400 seconds 
        return abs(round($diff / 86400)); 
    }


    function make_m_no_as_key($array=[],$merge_array = [])
    {
        $tmp_array = [];

        foreach ($array as $key => $value) {
            if (!empty($value['machine_number'])) {
                $tmp_array[$value['machine_number']] = $value;
            }else{
                $tmp_array[$key] = $value;
            }
            /* if (!empty($value['machine_id'])) {
                $tmp_array[$value['machine_id']] = $value;
            }else{
                $tmp_array[$key] = $value;
            } */
        }

        ksort($tmp_array);
        
        return $tmp_array;

    }

    function array_merge_recursive_distinct (array &$array1, array &$array2)
    {
        $merged = $array1;

        if (empty($array2)) {
            return $merged;
        }

        foreach ($array2 as $key => &$value)
        {
           if (is_array($value) && isset ($merged[$key]) && is_array($merged[$key]))
            {
                $merged[$key] = array_merge_recursive_distinct($merged[$key],$value);
            }
            else
            {
                $merged[$key] = $value;
            }
        }

        return $merged;
    }

    // Sort By date 
    function sort_by_date($element1, $element2) { 
        $datetime1 = strtotime($element1['created_at']); 
        $datetime2 = strtotime($element2['created_at']); 
        return $datetime1 - $datetime2; 
    }

    function get_machine_number_by_ids($machine_ids=[])
    {
        $machine_numbers = '';

        if (!empty($machine_ids) && is_array($machine_ids))
        {
            $machine_numbers = Machine::whereIn('machine_id',$machine_ids)->pluck('machine_number')->toArray();
        }
        return $machine_numbers;
    }

    function display_range_braker($ranges='')
    {
        $result = [];
        $ranges = json_decode($ranges,1);
        foreach ($ranges as $key => $range) {
            if (!empty($range['from_stitches']) || !empty($range['to_stitches']) || !empty($range['bonus_amount'])) {
                $result[] = '<strong>From Stitch</strong> : ' . $range['from_stitches'] . ' | <strong>To Stitch</strong> : ' . $range['to_stitches'] . ' | <strong>Bonus</strong> : ' . $range['bonus_amount'];
            }
        }
        return implode("<br> \n",$result);
        
    }

    function supervisor_permissions()
    {
        return [
            'workers' => 0,
            'dashboard' => 0,
            'machines' => 0,
            'reports' => 0,
            'bonus' => 0,
            //'app_settings' => 0,
            'disconnected_machines' => 0,
        ];
    }

    function get_all_machines(){
        $cust_id = (auth()->user()->parent_id == NULL)?auth()->user()->id:auth()->user()->parent_id;
        $machines = Machine::select('machine_id','machine_number','machine_name')->where('cust_id',$cust_id)->get();
        $machineData = array();
        
        foreach($machines as $machine){
            $machineData[$machine->machine_number] = $machine->machine_name;
            // $machineData[$machine->machine_id] = $machine->machine_name;
        }
        /* echo "<pre>";
        print_r($machineData);
        die; */
        return $machineData;
    }

    function getTotalUser(){
        $cust_id = auth()->user()->parent_id == NULL ? auth()->id() : auth()->user()->parent_id;
        return User::where('parent_id',$cust_id)->count();
    }
    
    function permissions(){
        $cust_id = auth()->user()->parent_id == NULL ? auth()->id() : auth()->user()->parent_id;
        return auth()->user()->permission;
    }

    function getSupervisorGroups($userId = NULL)
    {
        if($userId == NULL){
            $userId = auth()->user()->parent_id == NULL ? auth()->id() : auth()->user()->parent_id;
        }
        $machineGroups = MachineGroup::where('supervisor_id','like',"%$userId%")->get();
        return $machineGroups;
    }

    function hasAccess($section)
    {
        $permissions = auth()->user()->permission;
        if(isset($permissions->{$section}) && $permissions->{$section}){
            return true;
        }
        return false;
    }

    function isAdmin()
    {
        return (\Auth::check() && \Auth::user()->parent_id == NULL) ? true : false;
    }

    function sendMessage($contactNo,$message)
    {
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
            "number"=>$contactNo
        );
        $url = $url . http_build_query($postData);
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $server_output = curl_exec($ch);
        $response = json_decode($server_output,true);
        curl_close ($ch);
        if (!empty($response["ErrorCode"])  && $response["ErrorCode"]=="000" && !empty($response["ErrorMessage"]) && $response["ErrorMessage"]=="Done") {
            $response = "success";
        }else{
            $response = $response["ErrorMessage"];
        }
        return $response;
    }

?>