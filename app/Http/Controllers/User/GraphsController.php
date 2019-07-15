<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Admin;
use App\Models\Worker;
use App\Models\Machine;
use App\Models\MachineData;
use App\Models\Machine12HourData;
use App\Models\MachineGroup;
use App\Models\WorkerBonus;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use App\Helpers\AbettorHelper;

use Validator;
use Auth;
use PDF;
use DB;
use File;
use Carbon\Carbon;

class GraphsController extends Controller
{
    var $report_path;
    var $Abettor;
    var $setting;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(AbettorHelper $AbettorHelper)
    {
        $this->middleware('auth');
        $this->setting = Admin::find(1)->setting;
        $this->Abettor = $AbettorHelper;
        $this->report_path = public_path() . DS . 'graphs' . DS;
    }

    public function get_machines($display_all=false)
    {
        $isAdmin = ($this->Abettor->isAdmin()) ? true : false;
        if($isAdmin){
            $machine_list = Machine::where([
                'cust_id' => Auth::id(),
                'status_id' => 1
            ])
            ->orderByRaw('LENGTH(machine_id) ASC,machine_id ASC')
            ->pluck('machine_number')
            ->toArray();    
        }else{
            $machine_list = Machine::where([
                'machine_groups.supervisor_id' => auth()->id(),
                'status_id' => 1
            ])
            ->join('machine _groups','machine_groups.id','machines.group_id')
            ->orderByRaw('LENGTH(machine_id) ASC,machine_id ASC')
            ->pluck('machine_number')
            ->toArray();   
        }
        
        if ($display_all) {
            $machine_list = ['all'=>'All'] + $machine_list;
        }

        return $machine_list;

    }

    public function get_workers($display_all=false)
    {
        $cust_id = (!$this->Abettor->isAdmin()) ? auth()->user()->parent_id : Auth::id();
        $tmp_worker_list = Worker::select(['id','first_name','last_name','contact_number_1'])
        ->where([
            'cust_id' => $cust_id
        ])
        ->orderBy('first_name','ASC')
        ->get()->toArray();

        $worker_list = [];
        foreach ($tmp_worker_list as $key => $value) {
            $worker_list[$value['id']] = $value['first_name'] . ' ' .$value['last_name'] . ' - ' . $value['contact_number_1'];    
        }

        if ($display_all) {
            $worker_list = ['all'=>'All'] + $worker_list;
        }

        return $worker_list;

    }

    public function get_groups()
    {
        $isAdmin = ($this->Abettor->isAdmin()) ? true : false;
        if($isAdmin){
            $where['cust_id'] = Auth::id();
            $group_list = MachineGroup::where($where)->pluck('group_name','id')->toArray(); 
        }else{
            $where['supervisor_id'] = Auth::id();     
            $group_list = MachineGroup::where('supervisor_id',"%".Auth::id()."%")->pluck('group_name','id')->toArray();
        }
        $group_list = ['all'=>'All'] + $group_list;
        return $group_list;

    }

    public function production(Request $request)
    {
        $report_data = [];
        $report_html = 'Please select criteria';
        $machine_list = $this->Abettor->get_machines('all',Auth::id());
        $group_list = $this->get_groups();
        $worker_list = $this->get_workers(true);
        $reset = $request->input('reset');
        
        if (!empty($reset)) {
            $request->session()->put('graphs.production.search','');
            return redirect(route('graphs.production'));
        }elseif ($request->_token) {
            $request->session()->put('graphs.production.search',$request->all());
            $search = $request->session()->get('graphs.production.search');
        }else{
            $search = $request->session()->get('graphs.production.search');
        }

        if($request->report_types != "5_min_diff"){

            $validator = Validator::make($request->all(), 
            [
                'from_date' => 'date',
                'to_date' => 'date|after_or_equal:from_date',
            ],
            [
                'from_date.date' => 'Invalid From Date',
                'to_date.date' => 'Invalid To Date',
                'to_date.after_or_equal' => 'To date must be After From Date'
            ]);

            if ($validator->fails()) {
                $report_data = [];
                return view('user.graphs.production',[
                    'machine_list' => $machine_list,
                    'worker_list' => $worker_list,
                    'group_list' => $group_list,
                    'machines' => [],
                    'errors' => $validator->errors(),
                    'machine_search' => $search,
                    'report_html' => $report_html,
                    'setting' => $this->setting
                ]);
            }
        }

        $cust_id =  Auth::id();
        if (!empty($search)) {
            $report_type = $search['report_types'];
            $options = $this->generate_options($search,$cust_id);
            if ($report_type=='5_min_diff') {
                
                $report_data = $this->generate_5min_graph_report($options);
                if ($report_data['status']==1) {
                    $report_html = $report_data['html'];
                }
            }
        }
        return view('user.graphs.production',[
            'machine_list' => $machine_list,
            'worker_list' => $worker_list,
            'group_list' => $group_list,
            'machines' => [],
            'machine_search' => $search,
            'report_html' => $report_html,
            'setting' => $this->setting
        ]);
    }

    public function generate_options($search='',$cust_id = '')
    {
        $options = [];
        $cust_id = (Auth::user()->parent_id == NULL) ? Auth::id() : Auth::user()->parent_id;
        $customer = User::find($cust_id);

        $day_shift = $customer->day_shift;
        $night_shift = $customer->night_shift;
        $company_name = $customer->company_name;

            $options = [
                'shift' => !empty($search['shift']) ? $search['shift'] : 0,
                'group_id' => !empty($search['group_id']) ? $search['group_id'] : 0,
                'machine_no' => !empty($search['machine_no']) ? $search['machine_no'] : 0,
                'worker_list' => !empty($search['worker_list']) ? $search['worker_list'] : 0,
                'from_date' => !empty($search['from_date']) ? $search['from_date'] : '',
                'to_date' => !empty($search['to_date']) ? $search['to_date'] : '',
                'interval' =>  !empty($search['interval']) ? $search['interval'] : 5,
                'day_shift' => $day_shift,
                'night_shift' => $night_shift,
                'company_name' => $company_name,
                'cust_id' => $customer->id,
                'column_settings' => $customer->reports_settings,
                'view_mode' => 'view',
                'setting' => $this->setting
            ];
            
        return $options;
    }

    public function production_export(Request $request)
    {
        $reset = $request->input('reset');
        $request->session()->put('graph.production.search',$request->all());
        $search = $request->session()->get('graph.production.search');
        
        if (!empty($search)) {

            $options = $this->generate_options($search,Auth::id());
            
            $report_type = $search['report_types'];
            $options['view_mode'] = 'download';
            if ($report_type=='5_min_diff') {
                $report_data = $this->generate_5min_graph_report($options);
            }

            if ($report_data['status']==1) {
                $report_url = generate_graph_url($report_data['file_name']);
                return Redirect::to($report_url);
            }else{
                return redirect(route('graphs.production'))->withError('Unable to generate PDF, please try again later.');
            }

        }else{
            return redirect(route('graphs.production'))->withError('Please select criteria.');
        }
            
    }

    public function generate_5min_graph_report($options = [])
    {      
        ini_set('memory_limit', '-1');
        $options['cust_id'] = (!$this->Abettor->isAdmin()) ? auth()->user()->parent_id : Auth::id();
        $result = [];
        $view_mode = $options['view_mode'];
        $machine_list = $this->Abettor->get_machines('all',$options['cust_id']);
        $machine_list_string = '"'.implode('","',$machine_list) .'"';
        $condition = [
            'shift' => $options['shift'],
            'machine_number' => $options['machine_no'],
            'cust_id' => $options['cust_id']
        ];

        $options['header_data'] = false;
     
        $where_condition = [];
       
        $where_condition[] = "md.cust_id='" . $options['cust_id'] . "'";
        $options['worker_name'] = $options['worker_list'];
        if ($options['worker_list'] != 'all') {
            $where_condition[] = "md.worker_id='" . $options['worker_list'] . "'";
            $options['header_data'] = true;
            $options['worker_name'] = $this->Abettor->get_worker_fullname_by_id($options['worker_list']);
        }

        $options['group_name'] = $options['group_id'];
        if ($options['group_id'] != 'all') {
            $where_condition[] = "m.group_id='" . $options['group_id'] . "'";
            $options['header_data'] = true;
            $options['group_name'] = $this->Abettor->get_group_name_by_id($options['group_id']);
        }
        
        if ($options['machine_no'] != 'all') {
            $where_condition[] = "md.machine_number='" . $options['machine_no'] . "'";
            $options['header_data'] = true;
        }else{
            $other_condition[] = "md.machine_number IN (" . $machine_list_string . ")";            
        }

        if ($options['shift'] != 'all') {
            $where_condition[] = "md.shift='" . $options['shift'] . "'";
            $options['header_data'] = true;
        }
        $interval_seconds = $options['interval']*60;
        $from_date = date('Y-m-d',strtotime($options['from_date']));
       
        if ($options['shift']==1) {
            $where_condition[] = "md.report_date > '" . $from_date . ' ' . $options['day_shift'] . "' AND md.report_date <= '" . $from_date . ' ' . $options['night_shift']."'" ;
            $timeArray = $this->generate_time_interval($from_date . ' ' . $options['day_shift'],$from_date . ' ' . $options['night_shift'],$interval_seconds);
        }else if ($options['shift']==2){
            $to_date = date("Y-m-d",strtotime('+1 day',strtotime($from_date)));
            $where_condition[] = "md.report_date > '" . $from_date . ' ' . $options['night_shift'] . "' AND md.report_date <= '" . $to_date . ' ' . $options['day_shift']."'" ;
            $timeArray = $this->generate_time_interval($from_date . ' ' . $options['night_shift'],$to_date . ' ' . $options['day_shift'],$interval_seconds);
        }
        
        $where_condition = implode(" AND ",$where_condition);        

        $sql = "SELECT
        DATE_FORMAT(from_unixtime(unix_timestamp(md.report_date) - unix_timestamp(md.report_date) mod $interval_seconds), '%Y-%m-%d %H:%i:00') as interval_alias,
        md.stitches,md.max_rpm,md.stop_time
        FROM
            machine_data AS md
        LEFT JOIN machines AS m
        ON
            m.machine_number = md.machine_number AND m.cust_id = md.cust_id
        LEFT JOIN workers AS w
        ON
            w.id = md.worker_id
        WHERE
           $where_condition
        GROUP BY
            interval_alias
        ORDER BY
            interval_alias";
        
        $response = DB::select($sql);
        $response = json_decode(json_encode($response),1);
        
        $newResponse = array();
        $stitchgraphdata = array(array("TIME","STITCHES",array('role'=>'annotation')));
        $rpmgraphdata = array(array("TIME","MAX RPM",array('role'=>'annotation')));
        $stopTimegraphdata = array();        

        if(count($response)>0){            
            array_push($newResponse,$response[0]);
            for($index=1; $index < count($response);$index++){
                $stitchesDifference = $response[$index]['stitches'] - $response[$index-1]['stitches'];
                $stopTimeDifference = $response[$index]['stop_time'] - $response[$index-1]['stop_time'];  
                
                array_push($newResponse,array(
                    'interval_alias' => $response[$index]['interval_alias'],
                    'stitches' => $stitchesDifference,
                    'max_rpm' => $response[$index]['max_rpm'],
                    'stop_time' => $response[$index]['stop_time'],
                    'stop_time_minute' => round($stopTimeDifference/60),
                    'stop_time_format' => secondsToMinuteSeconds($stopTimeDifference)
                ));
            }
            $newResponse[0]['stop_time_minute'] = round($response[0]['stop_time']/60);
            $newResponse[0]['stop_time_format'] = secondsToMinuteSeconds($response[0]['stop_time']);
        }    
        $totalStitchesData = $totalRpmData = array();
        $totalStoptime = "";
        foreach($newResponse as $data){

            // REMOVE RECORDS TIME FROM ALL TIMES ARRAY.
            $timeValue = date('H:i',strtotime($data['interval_alias']));
            if (($key = array_search($timeValue, $timeArray)) !== false) {
                unset($timeArray[$key]);
            }
            array_push($stitchgraphdata,array(date('H:i',strtotime($data['interval_alias'])),$data['stitches'],$data['stitches']));
            array_push($rpmgraphdata,array(date('H:i',strtotime($data['interval_alias'])),$data['max_rpm'],$data['max_rpm']));
            array_push($stopTimegraphdata,array(date('H:i',strtotime($data['interval_alias'])),$data['stop_time_minute'],$data['stop_time_format'],$data['stop_time_format']));
            $totalStitchesData[] =$data['stitches'];
            $totalRpmData[] =$data['max_rpm'];
            $totalStoptime = secondsToTime($data['stop_time']);
        } 
        
        // ADD REMAIN TIME RECORDS WITH 0.
        
        foreach($timeArray as $time){
            array_push($stitchgraphdata,array($time,0,0));
            array_push($rpmgraphdata,array($time,0,0));
            array_push($stopTimegraphdata,array($time,0,"",""));
        }
        $stitchesHeader = $stitchgraphdata[0];
        unset($stitchgraphdata[0]);
        $stitchesTimes = array_column($stitchgraphdata,0);
        array_multisort($stitchesTimes, SORT_ASC, $stitchgraphdata);
        array_unshift($stitchgraphdata,$stitchesHeader);

        $rpmHeader = $rpmgraphdata[0];
        unset($rpmgraphdata[0]);
        $rpmTimes = array_column($rpmgraphdata,0);
        array_multisort($rpmTimes, SORT_ASC, $rpmgraphdata);
        array_unshift($rpmgraphdata,$rpmHeader);

        $stoptimeTimes = array_column($stopTimegraphdata,0);
        array_multisort($stoptimeTimes, SORT_ASC, $stopTimegraphdata);

        $totalStitches = array_sum($totalStitchesData);
        if(count(array_filter($totalRpmData))){
            $totalRpm = floor(array_sum(array_filter($totalRpmData))/count(array_filter($totalRpmData)));
        }else{
            $totalRpm = 0;
        }        
        
        $stitchgraphdata[0][1] = "$totalStitches";
        $rpmgraphdata[0][1] = "$totalRpm";
                
        $column_settings = !empty($options['column_settings']) ? json_decode($options['column_settings'],1) : [];

        if ($view_mode=='download') {
            
            $file_name = '5_min_diff_graph_report_' . time() . '.pdf';
            $file_path = $this->report_path . $file_name;

            $pdf = PDF::loadView('user.graphs.5min_graph_report', [
                'stitch_graph_data' => json_encode($stitchgraphdata),
                'rpm_graph_data' => json_encode($rpmgraphdata),
                'stoptime_graph_data' => json_encode($stopTimegraphdata),
                'from_date' => date("d-m-Y",strtotime($from_date)),
                'total_stop_time' => $totalStoptime,
                'view_mode' => 'download',
                'options' => $options,
                'column_settings' => $column_settings,
                'setting' => $this->setting,
                'company_name' => !empty($options['company_name']) ? $options['company_name'] : 'Company Name',
            ])->setPaper('a4', 'landscape')->setWarnings(false);

            $pdf->save($file_path);

            if (File::exists($file_path)) {
                $result['status'] = 1;
                $result['file_name'] = $file_name;
            }else{
                $result['status'] = 0;
            }

        }else{
            $view = view('user.graphs.5min_graph_report',[
                'stitch_graph_data' => json_encode($stitchgraphdata),
                'rpm_graph_data' => json_encode($rpmgraphdata),
                'stoptime_graph_data' => json_encode($stopTimegraphdata),
                'total_stop_time' => $totalStoptime,
                'column_settings' => $column_settings,
                'options' => $options,
                'setting' => $this->setting
            ]);
            $result['status'] = 1;
            $result['html'] = $view->render();
        }

        return $result;
    }

    public function average(Request $request){

        $report_data = [];
        $report_html = 'Please select criteria';

        $reset = $request->input('reset');
        if (!empty($reset)) {
            $request->session()->put('graph.average.search','');
            return redirect(route('graphs.average'));
        }elseif ($request->_token) {
            $request->session()->put('graph.average.search',$request->all());
            $search = $request->session()->get('graph.average.search');
        }else{
            $search = $request->session()->get('graph.average.search');
        }
        $cust_id = Auth::id();
        // $search['from_date'] = !empty($search['from_date']) ? $search['from_date'] : date('d-m-Y');
        if (!empty($search)) {
            $report_type = $search['report_types'];

            $options = $this->generate_options($search,$cust_id);

            if ($report_type=='machines') {                
                $report_data = $this->generate_machines_avg_graph_report($options);
                if ($report_data['status']==1) {
                    $report_html = $report_data['html'];
                }
            }elseif ($report_type=='workers') {
                $report_data = $this->generate_worker_avg_graph_report($options);
                if ($report_data['status']==1) {
                    $report_html = $report_data['html'];
                }
            }
        }

        $machine_list = $this->get_machines(true);
        $group_list = $this->get_groups();
        $worker_list = $this->get_workers(true);

        return view('user.graphs.average',[
            'machine_list' => $machine_list,
            'worker_list' => $worker_list,
            'group_list' => $group_list,
            'machines' => [],
            'search' => $search,
            'report_html' => $report_html,
        ]);
    }

    public function average_export(Request $request)
    {
        $reset = $request->input('reset');
        if (!empty($reset)) {
            $request->session()->put('graph.average.search','');
            return redirect(route('graphs.average'));
        }elseif ($request->_token) {
            $request->session()->put('graph.average.search',$request->all());
            $search = $request->session()->get('graph.average.search');
        }else{
            $search = $request->session()->get('graph.average.search');
        }

        
        if (!empty($search)) {

            $options = $this->generate_options($search,Auth::id());
            $report_type = $search['report_types'];
            $options['view_mode'] = 'download';

           if ($report_type=='machines') {
                $report_data = $this->generate_machines_avg_graph_report($options);
            }elseif ($report_type=='workers') {
                $report_data = $this->generate_worker_avg_graph_report($options);
            }

            if ($report_data['status']==1) {
                $report_url = generate_graph_url($report_data['file_name']);
                return Redirect::to($report_url);
            }else{
                return redirect(route('graphs.average'))->withError('Unable to generate PDF, please try again later.');
            }
        }else{
            return redirect(route('graphs.average'))->withError('Please select criteria.');
        }
    }

    public function generate_machines_avg_graph_report($options = [])
    {
        ini_set('memory_limit', '-1');

        $result = [];
        $options['cust_id'] = (!$this->Abettor->isAdmin()) ? auth()->user()->parent_id : Auth::id();
        $column_settings = !empty($options['column_settings']) ? json_decode($options['column_settings'],1) : [];
        $from_date = date('Y-m-d',strtotime($options['from_date']));
        $to_date = date('Y-m-d',strtotime($options['to_date']));        
        
        $day_shift = date('H:i A',strtotime($options['day_shift']));
        $night_shift = date('h:i A',strtotime($options['night_shift']));
        $machine_list = $this->Abettor->get_machines('all',$options['cust_id']);
        $machine_list_string = '"'.implode('","',$machine_list) .'"';
        $options['header_data'] = false;
     
        $other_condition = [];
        if ($options['machine_no'] != 'all') {
            $other_condition[] = "md.machine_number='" . $options['machine_no'] . "'";
            $options['header_data'] = true;
        }else{
            $other_condition[] = "md.machine_number IN (" . $machine_list_string . ")";            
        }       

        $options['group_name'] = $options['group_id'];
        if ($options['group_id'] != 'all') {
            $other_condition[] = "m.group_id='" . $options['group_id'] . "'";
            $options['header_data'] = true;
            $options['group_name'] = $this->Abettor->get_group_name_by_id($options['group_id']);
        }

        $options['worker_name'] = $options['worker_list'];
        if ($options['worker_list'] != 'all') {
            $other_condition[] = "md.worker_id='" . $options['worker_list'] . "'";
            $options['header_data'] = true;
            $options['worker_name'] = $this->Abettor->get_worker_fullname_by_id($options['worker_list']);
        }

        $other_condition[] = "md.cust_id='". $options['cust_id'] ."'";
        $where_condition = implode(" AND ",$other_condition);

        // Get 12 Hours Report with Bonus
        $sql = "SELECT
        worker.machine_number,
        worker.shift,
        ROUND(AVG(worker.rpm)) AS rpm,
        ROUND(AVG(worker.max_rpm)) AS max_rpm,
        ROUND(AVG(worker.stitches)) AS stitches,
        ROUND(AVG(worker.thred_break)) AS thred_break,
        ROUND(AVG(worker.stop_time)) AS stop_time,
        IFNULL(
            ROUND(AVG(worker.bonus_amount)),
            0
        ) AS worker_bonus
        FROM
            (
            SELECT
                m.machine_name,
                md.cust_id,
                md.machine_number,
                md.shift,
                md.stitches,
                md.rpm,
                md.max_rpm,
                md.thred_break,
                md.stop_time,
                wb.bonus_amount
            FROM
                `machine_12_hour_data` AS md
            JOIN machines AS m 
            ON
                m.machine_number = md.machine_number AND m.cust_id=md.cust_id
            LEFT JOIN worker_bonuses AS wb
            ON
                wb.cust_id = md.cust_id AND wb.shift = md.shift AND wb.worker_id = md.worker_id AND wb.machine_number = md.machine_number AND wb.bonus_date = md.report_date
            WHERE
                md.report_date >= '$from_date' AND md.report_date <= '$to_date' AND $where_condition
            GROUP BY
                md.report_date,
                md.shift,
                md.machine_number
            ORDER BY
                LENGTH(md.machine_id) ASC,
                md.machine_id ASC
        ) AS worker
        GROUP BY
            worker.machine_number,worker.shift";
             

        $response = DB::select($sql);
        $response = json_decode(json_encode($response),1);

        $totalDayStitchesData = $totalNightStitchesData = $totalDayRpmData = $totalNightRpmData = $totalDayStoptimeData = $totalNightStoptimeData = array();
        $dayStitchgraphdata = array(array('MACHINE','STITCHES',array('role'=>'annotation')));
        $nightStitchgraphdata = array(array('MACHINE','STITCHES',array('role'=>'annotation')));
        $dayRpmgraphdata = array(array('MACHINE','RPM',array('role'=>'annotation')));
        $nightRpmgraphdata = array(array('MACHINE','RPM',array('role'=>'annotation')));
        $dayStopTimegraphdata = array();
        $nightStopTimegraphdata = array();
                
        foreach($response as $key => $avgData){
            $response[$key]['stop_time_minute'] = floor($avgData['stop_time'] / 60); 
            $response[$key]['stop_time_format'] = secondsToTime($avgData['stop_time']);
        }
        
        foreach($response as $data){
            if($data['shift'] == 1){
                array_push($dayStitchgraphdata,array($data['machine_number'],(int)$data['stitches'],(int)$data['stitches']));
                array_push($dayRpmgraphdata,array($data['machine_number'],(int)$data['rpm'],(int)$data['rpm']));
                array_push($dayStopTimegraphdata,array($data['machine_number'],$data['stop_time_minute'],$data['stop_time_format'],$data['stop_time_format']));
                $totalDayStitchesData[] = (int)$data['stitches'];
                $totalDayRpmData[] = (int)$data['rpm'];
                $totalDayStoptimeData[] = $data['stop_time'];
            }else{
                array_push($nightStitchgraphdata,array($data['machine_number'],(int)$data['stitches'],(int)$data['stitches']));
                array_push($nightRpmgraphdata,array($data['machine_number'],(int)$data['rpm'],(int)$data['rpm']));
                array_push($nightStopTimegraphdata,array($data['machine_number'],$data['stop_time_minute'],$data['stop_time_format'],$data['stop_time_format']));
                $totalNightStitchesData[] = (int)$data['stitches'];
                $totalNightRpmData[] = (int)$data['rpm'];
                $totalNightStoptimeData[] = $data['stop_time'];
            }            
        }      
        
        $dayStitchMachineHeader = $dayStitchgraphdata[0];
        unset($dayStitchgraphdata[0]);
        $dayStitchMachine = array_column($dayStitchgraphdata,0);
        array_multisort($dayStitchMachine, SORT_ASC, $dayStitchgraphdata);
        array_unshift($dayStitchgraphdata,$dayStitchMachineHeader);

        $nightStitchMachineHeader = $nightStitchgraphdata[0];
        unset($nightStitchgraphdata[0]);
        $nightStitchMachine = array_column($nightStitchgraphdata,0);
        array_multisort($nightStitchMachine, SORT_ASC, $nightStitchgraphdata);
        array_unshift($nightStitchgraphdata,$nightStitchMachineHeader);

        $dayRpmgraphHeader = $dayRpmgraphdata[0];
        unset($dayRpmgraphdata[0]);
        $dayRpmMachine = array_column($dayRpmgraphdata,0);
        array_multisort($dayRpmMachine, SORT_ASC, $dayRpmgraphdata);
        array_unshift($dayRpmgraphdata,$dayRpmgraphHeader);

        $nightRpmgraphHeader = $nightRpmgraphdata[0];
        unset($nightRpmgraphdata[0]);
        $nightRpmMachine = array_column($nightRpmgraphdata,0);
        array_multisort($nightRpmMachine, SORT_ASC, $nightRpmgraphdata);
        array_unshift($nightRpmgraphdata,$nightRpmgraphHeader);

        $dayStopTimeMachine = array_column($dayStopTimegraphdata,0);
        array_multisort($dayStopTimeMachine, SORT_ASC, $dayStopTimegraphdata);

        $nightStopTimeMachine = array_column($nightStopTimegraphdata,0);
        array_multisort($nightStopTimeMachine, SORT_ASC, $nightStopTimegraphdata);

        $totalDayStitches = round(array_sum(array_filter($totalDayStitchesData))/count(array_filter($totalDayStitchesData)));
        $totalNightStitches = round(array_sum(array_filter($totalNightStitchesData))/count(array_filter($totalNightStitchesData)));

        $totalDayRpm = round(array_sum(array_filter($totalDayRpmData))/count(array_filter($totalDayRpmData)));
        $totalNightRpm = round(array_sum(array_filter($totalNightRpmData))/count(array_filter($totalNightRpmData)));

        $totalDayStoptime = secondsToTime(round(array_sum(array_filter($totalDayStoptimeData))/count(array_filter($totalDayStoptimeData))));
        $totalNightStoptime = secondsToTime(round(array_sum(array_filter($totalNightStoptimeData))/count(array_filter($totalNightStoptimeData))));

        $dayStitchgraphdata[0][1] = "$totalDayStitches";
        $nightStitchgraphdata[0][1] = "$totalNightStitches";
        $dayRpmgraphdata[0][1] = "$totalDayRpm";
        $nightRpmgraphdata[0][1] = "$totalNightRpm";

        $view_mode = $options['view_mode'];
        if ($view_mode=='download') {
            
            $file_name = 'machine_average_graph_report_' . time() . '.pdf';
            $file_path = $this->report_path . $file_name;

            $pdf = PDF::loadView('user.graphs.machine_average_graph_report', [
                'day_stitches_graph_data' => json_encode($dayStitchgraphdata),
                'night_stitches_graph_data' => json_encode($nightStitchgraphdata),
                'day_rpm_graph_data' => json_encode($dayRpmgraphdata),
                'night_rpm_graph_data' => json_encode($nightRpmgraphdata),
                'day_stoptime_graph_data' => json_encode($dayStopTimegraphdata),
                'night_stoptime_graph_data' => json_encode($nightStopTimegraphdata),
                'total_day_stoptime' => $totalDayStoptime,
                'total_night_stoptime' => $totalNightStoptime,
                'column_settings' => $column_settings,
                'day_shift' => $day_shift,
                'night_shift' => $night_shift,
                'from_date' => date("d-m-Y",strtotime($from_date)),
                'to_date' => date("d-m-Y",strtotime($to_date)),
                'view_mode' => 'download',
                'setting' => $this->setting,
                'company_name' => !empty($options['company_name']) ? $options['company_name'] : 'Company Name',
            ])->setPaper('a4', 'landscape')->setWarnings(false);

            $pdf->save($file_path);

            if (File::exists($file_path)) {
                $result['status'] = 1;
                $result['file_name'] = $file_name;
            }else{
                $result['status'] = 0;
            }

        }else{
            $view = view('user.graphs.machine_average_graph_report',[
                'day_stitches_graph_data' => json_encode($dayStitchgraphdata),
                'night_stitches_graph_data' => json_encode($nightStitchgraphdata),
                'day_rpm_graph_data' => json_encode($dayRpmgraphdata),
                'night_rpm_graph_data' => json_encode($nightRpmgraphdata),
                'day_stoptime_graph_data' => json_encode($dayStopTimegraphdata),
                'night_stoptime_graph_data' => json_encode($nightStopTimegraphdata),
                'total_day_stoptime' => $totalDayStoptime,
                'total_night_stoptime' => $totalNightStoptime,
                'column_settings' => $column_settings,
                'day_shift' => $day_shift,
                'night_shift' => $night_shift,
                'setting' => $this->setting
            ]);
            $result['status'] = 1;
            $result['html'] = $view->render();
        }
        return $result;
    }

    public function generate_worker_avg_graph_report($options = [])
    {
        ini_set('memory_limit', '-1');

        $result = [];
        $options['cust_id'] = (!$this->Abettor->isAdmin()) ? auth()->user()->parent_id : Auth::id();
        $column_settings = !empty($options['column_settings']) ? json_decode($options['column_settings'],1) : [];
        $from_date = date('Y-m-d',strtotime($options['from_date']));
        $to_date = date('Y-m-d',strtotime($options['to_date']));        
        
        $day_shift = date('H:i A',strtotime($options['day_shift']));
        $night_shift = date('h:i A',strtotime($options['night_shift']));
        $machine_list = $this->Abettor->get_machines('all',$options['cust_id']);
        $machine_list_string = '"'.implode('","',$machine_list) .'"';
        $options['header_data'] = false;
     
        $other_condition = [];
        if ($options['machine_no'] != 'all') {
            $other_condition[] = "md.machine_number='" . $options['machine_no'] . "'";
            $options['header_data'] = true;
        }else{
            $other_condition[] = "md.machine_number IN (" . $machine_list_string . ")";            
        }       

        $options['group_name'] = $options['group_id'];
        if ($options['group_id'] != 'all') {
            $other_condition[] = "m.group_id='" . $options['group_id'] . "'";
            $options['header_data'] = true;
            $options['group_name'] = $this->Abettor->get_group_name_by_id($options['group_id']);
        }

        $options['worker_name'] = $options['worker_list'];
        if ($options['worker_list'] != 'all') {
            $other_condition[] = "md.worker_id='" . $options['worker_list'] . "'";
            $options['header_data'] = true;
            $options['worker_name'] = $this->Abettor->get_worker_fullname_by_id($options['worker_list']);
        }

        $other_condition[] = "md.cust_id='". $options['cust_id'] ."'";
        $where_condition = implode(" AND ",$other_condition);

        // Get 12 Hours Report with Bonus
        $sql = "SELECT
        worker.worker_id,
        worker.first_name,
        worker.last_name,
        worker.contact_number_1,
        worker.cust_id,
        ROUND(AVG(worker.rpm)) AS rpm,
        ROUND(AVG(worker.max_rpm)) AS max_rpm,
        ROUND(AVG(worker.stitches)) AS stitches,
        ROUND(AVG(worker.thred_break)) AS thred_break,
        ROUND(AVG(worker.stop_time)) AS stop_time,
        IFNULL(
            ROUND(AVG(worker.bonus_amount)),
            0
        ) AS worker_bonus
        FROM
            (
            SELECT
                w.worker_id,
                w.first_name,
                w.last_name,
                w.contact_number_1,
                md.cust_id,
                md.machine_number,
                md.stitches,
                md.rpm,
                md.max_rpm,
                md.thred_break,
                md.stop_time,
                wb.bonus_amount
            FROM
                `machine_12_hour_data` AS md
            JOIN workers AS w
            ON
                w.id = md.worker_id AND w.cust_id = md.cust_id
            JOIN machines AS m 
            ON
                m.machine_number = md.machine_number AND m.cust_id=md.cust_id    
            LEFT JOIN worker_bonuses AS wb
            ON
                wb.cust_id = md.cust_id AND wb.shift = md.shift AND wb.worker_id = md.worker_id AND wb.machine_number = md.machine_number AND wb.bonus_date = md.report_date
            WHERE
                md.report_date >= '$from_date' AND md.report_date <= '$to_date' AND $where_condition
            GROUP BY
                md.report_date,
                md.worker_id
            ORDER BY
                LENGTH(md.machine_id) ASC,
                md.machine_id ASC
        ) AS worker
        GROUP BY
            worker.worker_id";
             

        $response = DB::select($sql);
        $response = json_decode(json_encode($response),1);

        $totalStitchesData =$totalRpmData = $totalStoptimeData = array();
        $stitchgraphdata = array(array('WORKER','STITCHES',array('role'=>'annotation')));
        $rpmgraphdata = array(array('WORKER','RPM',array('role'=>'annotation')));
        $tbgraphdata = array(array('WORKER','THRED BREAK',array('role'=>'annotation')));
        $stopTimegraphdata = array();
                
        foreach($response as $key => $avgData){
            $response[$key]['stop_time_minute'] = floor($avgData['stop_time'] / 60); 
            $response[$key]['stop_time_format'] = secondsToTime($avgData['stop_time']);
        }
        
        foreach($response as $data){
            array_push($stitchgraphdata,array($data['first_name'],(int)$data['stitches'],(int)$data['stitches']));
            array_push($rpmgraphdata,array($data['first_name'],(int)$data['rpm'],(int)$data['rpm']));
            array_push($tbgraphdata,array($data['first_name'],(int)$data['thred_break'],(int)$data['thred_break']));
            array_push($stopTimegraphdata,array($data['first_name'],$data['stop_time_minute'],$data['stop_time_format'],$data['stop_time_format']));
            $totalStitchesData[] = (int)$data['stitches'];
            $totalRpmData[] = (int)$data['rpm'];
            $totalTbData[] = (int)$data['thred_break'];
            $totalStoptimeData[] = $data['stop_time'];
        } 
        
        $totalStitches = (count(array_filter($totalStitchesData))!=0)?round(array_sum(array_filter($totalStitchesData))/count(array_filter($totalStitchesData))):0;
        $totalRpm = (count(array_filter($totalRpmData))!=0)?round(array_sum(array_filter($totalRpmData))/count(array_filter($totalRpmData))):0;
        $totalTb = (count(array_filter($totalTbData))!=0)?round(array_sum(array_filter($totalTbData))/count(array_filter($totalTbData))):0;
        $totalStoptime = (count(array_filter($totalStoptimeData))!=0)?secondsToTime(round(array_sum(array_filter($totalStoptimeData))/count(array_filter($totalStoptimeData)))):0;

        $stitchgraphdata[0][1] = "$totalStitches";
        $rpmgraphdata[0][1] = "$totalRpm";
        $tbgraphdata[0][1] = "$totalTb";

        $view_mode = $options['view_mode'];
        if ($view_mode=='download') {
            
            $file_name = 'worker_average_graph_report_' . time() . '.pdf';
            $file_path = $this->report_path . $file_name;

            $pdf = PDF::loadView('user.graphs.worker_average_graph_report', [
                'stitches_graph_data' => json_encode($stitchgraphdata),
                'rpm_graph_data' => json_encode($rpmgraphdata),
                'tb_graph_data' => json_encode($tbgraphdata),
                'stoptime_graph_data' => json_encode($stopTimegraphdata),
                'total_stoptime' => $totalStoptime,
                'column_settings' => $column_settings,
                'day_shift' => $day_shift,
                'night_shift' => $night_shift,
                'from_date' => date("d-m-Y",strtotime($from_date)),
                'to_date' => date("d-m-Y",strtotime($to_date)),
                'view_mode' => 'download',
                'setting' => $this->setting,
                'company_name' => !empty($options['company_name']) ? $options['company_name'] : 'Company Name',
            ])->setPaper('a4', 'landscape')->setWarnings(false);

            $pdf->save($file_path);

            if (File::exists($file_path)) {
                $result['status'] = 1;
                $result['file_name'] = $file_name;
            }else{
                $result['status'] = 0;
            }

        }else{
            $view = view('user.graphs.worker_average_graph_report',[
                'stitches_graph_data' => json_encode($stitchgraphdata),
                'rpm_graph_data' => json_encode($rpmgraphdata),
                'tb_graph_data' => json_encode($tbgraphdata),
                'stoptime_graph_data' => json_encode($stopTimegraphdata),
                'total_stoptime' => $totalStoptime,
                'column_settings' => $column_settings,
                'day_shift' => $day_shift,
                'night_shift' => $night_shift,
                'setting' => $this->setting
            ]);
            $result['status'] = 1;
            $result['html'] = $view->render();
        }
        return $result;
    }

    public function generate_time_interval($fromDatetime,$toDatetime,$interval){
        $timeArray = array();
        while (strtotime($fromDatetime) <= strtotime($toDatetime)) {
            $timestamp = strtotime($fromDatetime);
            $time = date('H:i',$timestamp);
            $timeArray[$timestamp] = $time;
            $fromDatetime = date ("Y-m-d H:i:s", strtotime("+{$interval} minutes", strtotime($fromDatetime)));
        }
        return $timeArray;
    }

    public function average_weekly(Request $request){

        $report_data = [];
        $report_html = 'Please select criteria';

        $reset = $request->input('reset');
        if (!empty($reset)) {
            $request->session()->put('graphs.average_weekly.search','');
            return redirect(route('graphs.average_weekly'));
        }elseif ($request->_token) {
            $request->session()->put('report.average_weekly.search',$request->all());
            $search = $request->session()->get('report.average_weekly.search');
        }else{
            $search = $request->session()->get('report.average_weekly.search');
        }
        $cust_id = Auth::id();
                
        if (!empty($search)) {
            $options = $this->generate_options($search,$cust_id);
            $report_data = $this->generate_avg_week_graph_report($options);
            if ($report_data['status']==1) {
                $report_html = $report_data['html'];
            }
        }

        $machine_list = $this->Abettor->get_machines('all',Auth::id());
        $group_list = $this->get_groups();
        $worker_list = $this->get_workers(true);

        return view('user.graphs.average_weekly',[
            'machine_list' => $machine_list,
            'worker_list' => $worker_list,
            'group_list' => $group_list,
            'search' => $search,
            'report_html' => $report_html
        ]);
    }

    public function average_weekly_export(Request $request)
    {
        $report_data = [];
        $report_html = 'Please select criteria';

        $reset = $request->input('reset');
        if (!empty($reset)) {
            $request->session()->put('graph.average_weekly.search','');
            return redirect(route('graphs.average_weekly'));
        }elseif ($request->_token) {
            $request->session()->put('graph.average_weekly.search',$request->all());
            $search = $request->session()->get('graph.average_weekly.search');
        }else{
            $search = $request->session()->get('graph.average_weekly.search');
        }
        $cust_id = Auth::id();
                
        if (!empty($search)) {
            $options = $this->generate_options($search,$cust_id);
            $options['view_mode'] = 'download';
            $report_data = $this->generate_avg_week_graph_report($options);

            if ($report_data['status']==1) {
                $report_url = generate_graph_url($report_data['file_name']);
                return Redirect::to($report_url);
            }
        }else{
            return redirect(route('graphs.average_weekly'))->withError('Please select criteria.');
        }
    }

    public function generate_avg_week_graph_report($options = [])
    {

        ini_set('memory_limit', '-1');

        $result = [];
        $options['cust_id'] = (!$this->Abettor->isAdmin()) ? auth()->user()->parent_id : Auth::id();
        $column_settings = !empty($options['column_settings']) ? json_decode($options['column_settings'],1) : [];
        $other_condition = [];
        $machine_list = $this->Abettor->get_machines('all',$options['cust_id']);
        $machine_list_string = '"'.implode('","',$machine_list) .'"';
        $response['total'] = array('0' => 'Average','1' => 0,'2' => 0);

        $options['group_name'] = $options['group_id'];
        if ($options['group_id'] != 'all') {
            $other_condition[] = "machines.group_id='" . $options['group_id'] . "'";            
        }

        $options['worker_name'] = $options['worker_list'];
        if ($options['worker_list'] != 'all') {
            $other_condition[] = "machines.worker_id='" . $options['worker_list'] . "'";
        }

        if ($options['machine_no'] != 'all') {
            $other_condition[] = "machines.machine_number='" . $options['machine_no'] . "'";            
        }else{
            (count($machine_list) > 0) ? $other_condition[] = "machines.machine_number IN (" . $machine_list_string . ")" : '';            
        } 

        $other_condition[] = "machines.cust_id='". $options['cust_id'] ."'";
        $where_condition = implode(" AND ",$other_condition);
        
        // Get machine id's        
        $sql = "SELECT machine_number from machines where $where_condition ORDER BY machine_id ASC";
        $machineData = DB::select($sql);
        $machineData = json_decode(json_encode($machineData),1);

        $from_date = date('Y-m-d',strtotime($options['from_date']));
        
        foreach($machineData as $machineKey => $machineData){
            $machine_id = $machineData['machine_number'];
            for ($i=0; $i <= 6; $i++) { 
                $current_date = Carbon::parse($from_date)->addDays($i)->toDateString();
                $productionSql = "SELECT machine_12_hour_data.shift,machine_12_hour_data.stitches from machine_12_hour_data JOIN machines ON machines.machine_number = machine_12_hour_data.machine_number AND machines.cust_id=machine_12_hour_data.cust_id where $where_condition AND machine_12_hour_data.machine_number = '$machine_id' AND report_date = '$current_date' order by machine_12_hour_data.shift";
                $productionData = DB::select($productionSql);
                $productionData = json_decode(json_encode($productionData),1);

                $response[$i] = array('1' => 0,'2' => 0);
                $response[$i][0] = $current_date;
                foreach($productionData as $productionKey => $productionValue){
                    $response[$i][$productionValue['shift']] = $productionValue['stitches'];
                    $response['total'][$productionValue['shift']] += $productionValue['stitches'];
                }               
            }
        }  

        $total = $response['total'];
        $total[1] = floor(($total[1] != 0)?$total[1]/7:0);
        $total[2] = floor(($total[2] != 0)?$total[2]/7:0);
        unset($response['total']);
        $stitchgraphdata = array(array('DATE','DAY','NIGHT'));
        foreach($response as $data){
            array_push($stitchgraphdata,array($data[0],(int)$data[1],(int)$data[2]));
        }      
        array_push($stitchgraphdata,$total);

        $view_mode = $options['view_mode'];
        if ($view_mode=='download') {
            
            $file_name = 'machine_average_weekly_report_' . time() . '.pdf';
            $file_path = $this->report_path . $file_name;

            $pdf = PDF::loadView('user.graphs.machine_average_weekly_graph_report', [
                'stitches_graph_data' => json_encode($stitchgraphdata),
                'column_settings' => $column_settings,
                'from_date' => date("d-m-Y",strtotime($from_date)),
                'to_date' => date("d-m-Y",strtotime($current_date)),
                'view_mode' => 'download',
                'setting' => $this->setting,
                'company_name' => !empty($options['company_name']) ? $options['company_name'] : 'Company Name',
            ])->setPaper('a4', 'landscape')->setWarnings(false);

            $pdf->save($file_path);

            if (File::exists($file_path)) {
                $result['status'] = 1;
                $result['file_name'] = $file_name;
            }else{
                $result['status'] = 0;
            }

        }else{
            
            $view = view('user.graphs.machine_average_weekly_graph_report',[
                'stitches_graph_data' => json_encode($stitchgraphdata),
                'column_settings' => $column_settings,
                'from_date' => date("d-m-Y",strtotime($from_date)),
                'to_date' => date("d-m-Y",strtotime($current_date)),
                'setting' => $this->setting,
                'company_name' => !empty($options['company_name']) ? $options['company_name'] : 'Company Name'
            ]);
            $result['status'] = 1;
            $result['html'] = $view->render();
        }
        
        return $result;
    }
}
