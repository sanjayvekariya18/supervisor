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

class ReportsController extends Controller
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
        $this->report_path = public_path() . DS . 'reports' . DS;
    }

    public function production(Request $request)
    {
        /* echo "<pre>";
        print_r($request->all());
        die; */
        
        $report_data = [];
        $report_html = 'Please select criteria';
        $machine_list = $this->Abettor->get_machines('all',Auth::id());
        $group_list = $this->get_groups();
        $worker_list = $this->get_workers(true);
        $cust_id =  Auth::id();        
        $column_settings = User::find($cust_id)->reports_settings;
        $column_settings = !empty($column_settings) ? json_decode($column_settings,1) : [];        
        $reset = $request->input('reset');
        
        if (!empty($reset)) {
            $request->session()->put('report.production.search','');
            return redirect(route('reports.production'));
        }elseif ($request->_token) {
            $request->session()->put('report.production.search',$request->all());
            $search = $request->session()->get('report.production.search');
        }else{
            $search = $request->session()->get('report.production.search');
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
                return view('user.reports.production',[
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
        
        if (!empty($search)) {
            $report_type = $search['report_types'];
            $options = $this->generate_options($search,$cust_id);
            
            if ($report_type=='5_min_diff') {
                
                $report_data = $this->generate_5min_report($options);
                if ($report_data['status']==1) {
                    $report_html = $report_data['html'];
                }
            }elseif ($report_type=='3_hours') {
                $report_data = $this->generate_3hr_report($options);
                if ($report_data['status']==1) {
                    $report_html = $report_data['html'];
                }
            }elseif ($report_type=='6_hours') {
                $report_data = $this->generate_6hr_report($options);
                if ($report_data['status']==1) {
                    $report_html = $report_data['html'];
                }
            }elseif ($report_type=='12_hours') {
                $report_data = $this->generate_12hr_report($options);
                if ($report_data['status']==1) {
                    $report_html = $report_data['html'];
                }
            }
        }

        return view('user.reports.production',[
            'machine_list' => $machine_list,
            'worker_list' => $worker_list,
            'group_list' => $group_list,
            'machines' => [],
            'machine_search' => $search,
            'report_html' => $report_html,
            'setting' => $this->setting,
            'column_settings' => $column_settings
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
    /**
     * Export Reports - Get the data from Session
     *
     * @return URL
     */
    public function production_export(Request $request)
    {
       
        $reset = $request->input('reset');

        $request->session()->put('report.production.search',$request->all());
        $search = $request->session()->get('report.production.search');
        
        if (!empty($search)) {

            $options = $this->generate_options($search,Auth::id());
            /* echo "<pre>";
            print_r($options);
            die; */
            $report_type = $search['report_types'];
            $options['view_mode'] = 'download';
            if ($report_type=='5_min_diff') {
                $report_data = $this->generate_5min_report($options);
            }elseif ($report_type=='3_hours') {
                $report_data = $this->generate_3hr_report($options);
            }elseif ($report_type=='6_hours') {
                $report_data = $this->generate_6hr_report($options);
            }elseif ($report_type=='12_hours') {  
                $report_data = $this->generate_12hr_report($options);
            }

            if ($report_data['status']==1) {
                $report_url = generate_report_url($report_data['file_name']);
                return Redirect::to($report_url);
            }else{
                return redirect(route('reports.production'))->withError('Unable to generate PDF, please try again later.');
            }
                


        }else{
            return redirect(route('reports.production'))->withError('Please select criteria.');
        }
            
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
        }else{
            $where['supervisor_id'] = Auth::id();
        }
        $group_list = MachineGroup::where($where)->pluck('group_name','id')->toArray();
        $group_list = ['all'=>'All'] + $group_list;
        return $group_list;

    }

    /**
     * Generate 5 min Different Report
     *
     * @param Array mix
     * @return Data or Link
     */
    public function generate_5min_report($options = [])
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
            (count($machine_list) > 0) ? $other_condition[] = "md.machine_number IN (" . $machine_list_string . ")" : '';
        }

        if ($options['shift'] != 'all') {
            $where_condition[] = "md.shift='" . $options['shift'] . "'";
            $options['header_data'] = true;
        }

        $from_date = date('Y-m-d',strtotime($options['from_date']));
       
        if ($options['shift']==1) {
            $where_condition[] = "md.report_date > '" . $from_date . ' ' . $options['day_shift'] . "' AND md.report_date <= '" . $from_date . ' ' . $options['night_shift']."'" ;
        }else if ($options['shift']==2){
            $to_date = date("Y-m-d",strtotime('+1 day',strtotime($from_date)));
            $where_condition[] = "md.report_date > '" . $from_date . ' ' . $options['night_shift'] . "' AND md.report_date <= '" . $to_date . ' ' . $options['day_shift']."'" ;
        }else if ($options['shift']==0){
            $to_date = date("Y-m-d",strtotime('+1 day',strtotime($from_date)));
            $where_condition[] = "md.report_date > '" . $from_date . ' ' . $options['day_shift'] . "' AND md.report_date <= '" . $to_date . ' ' . $options['day_shift']."'" ;
        }

        $where_condition = implode(" AND ",$where_condition);

        $data = [];
        $sql = 'SELECT md.*,m.machine_name,w.first_name,w.last_name,(case when md.shift = 1 then "Day" when md.shift = 2 then "Night" end) AS shift FROM `machine_data` AS md LEFT JOIN machines AS m ON m.machine_number=md.machine_number AND m.cust_id=md.cust_id LEFT JOIN workers AS w ON w.id = md.worker_id WHERE ' . $where_condition . ' ORDER BY LENGTH(md.machine_id) ASC, md.machine_id';
        
        $tmp_data = DB::select($sql);
        $tmp_data = json_decode(json_encode($tmp_data),1);
        foreach ($tmp_data as $key => $value) {
            $data[$value['machine_number']][] = $value;
        }

        foreach ($data as $key => $value) {
            usort($value, 'sort_by_date'); 

            // Replace data on same key
            $data[$key] = $value;
        }

        $column_settings = !empty($options['column_settings']) ? json_decode($options['column_settings'],1) : [];

        if ($view_mode=='download') {
            
            $file_name = '5_min_diff_report_' . time() . '.pdf';
            $file_path = $this->report_path . $file_name;

            $pdf = PDF::loadView('user.reports.5min_report', [
                'report_data' => $data,
                'from_date' => date("d-m-Y",strtotime($from_date)),
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
            $view = view('user.reports.5min_report',[
                'report_data' => $data,
                'column_settings' => $column_settings,
                'options' => $options,
                'setting' => $this->setting
            ]);
            $result['status'] = 1;
            $result['html'] = $view->render();
        }

        return $result;
    }

    /**
     * Generate 3 Hours Reports
     *
     * @param Array $mix
     * @return Data
     */
    public function generate_3hr_report($options = [])
    {
        ini_set('memory_limit', '-1');
        $options['cust_id'] = (!$this->Abettor->isAdmin()) ? auth()->user()->parent_id : Auth::id();
        $result = [];
        $column_settings = !empty($options['column_settings']) ? json_decode($options['column_settings'],1) : [];
        $from_date = strtotime(date('Y-m-d',strtotime($options['from_date'])));
        $to_date = strtotime(date('Y-m-d',strtotime($options['to_date'])));
        $day_shift = date('H:i:s',strtotime($options['day_shift']));
        $night_shift = date('H:i:s',strtotime($options['night_shift']));
        $machine_list = $this->Abettor->get_machines('all',$options['cust_id']);
        $machine_list_string = '"'.implode('","',$machine_list) .'"';
        $color_range = $this->Abettor->get_customer_color_range($options['cust_id']);

        $options['header_data'] = false;
     
        $other_condition = [];
        if ($options['machine_no'] != 'all') {
            $other_condition[] = "md.machine_number='" . $options['machine_no'] . "'";
            $options['header_data'] = true;
        }else{
            (count($machine_list) > 0) ? $other_condition[] = "md.machine_number IN (" . $machine_list_string . ")" : '';
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

        $where_condition = $other_condition;
        $where_condition[] = "md.cust_id='". $options['cust_id'] ."'";
        $where_condition = implode(" AND ",$where_condition);
        
        $days_count = $to_date - $from_date;
        $days_count = round($days_count / (60 * 60 * 24));
        
        // Loop through Each days
        for ($i=0; $i <= $days_count; $i++) { 
            $new_date = date("Y-m-d",strtotime("+". $i ." days",$from_date));
            $current_date = $new_date;

            $data[$current_date]['day_shift_part1'] = [];
            $data[$current_date]['day_shift_part2'] = [];
            $data[$current_date]['day_shift_part3'] = [];
            $data[$current_date]['day_shift_part4'] = [];
            $data[$current_date]['night_shift_part1'] = [];
            $data[$current_date]['night_shift_part2'] = [];
            $data[$current_date]['night_shift_part3'] = [];
            $data[$current_date]['night_shift_part4'] = [];

            $dayShift_DateTime = $current_date." ".$day_shift;
            $nightShift_DateTime = $current_date." ".$night_shift; 

            $dayShift_DateTime_part1 = Carbon::parse($dayShift_DateTime)->addHours(3);
            $dayShift_DateTime_part2 = Carbon::parse($dayShift_DateTime)->addHours(6);
            $dayShift_DateTime_part3 = Carbon::parse($dayShift_DateTime)->addHours(9);
            $dayShift_DateTime_part4 = Carbon::parse($dayShift_DateTime)->addHours(12);

            $nightShift_DateTime_part1 = Carbon::parse($nightShift_DateTime)->addHours(3);
            $nightShift_DateTime_part2 = Carbon::parse($nightShift_DateTime)->addHours(6);
            $nightShift_DateTime_part3 = Carbon::parse($nightShift_DateTime)->addHours(9);
            $nightShift_DateTime_part4 = Carbon::parse($nightShift_DateTime)->addHours(12);

            // Day shift - Part 1

            $dayShift_1 = "SELECT
                md.*,
                DATE_FORMAT(md.report_date, '%Y-%m-%d') AS reports_date,
                m.machine_name,
                w.first_name AS wrk_first_name,
                w.last_name AS wrk_last_name
            FROM
                `machine_hourly_data` AS md
            JOIN machines AS m
            ON
                m.machine_number = md.machine_number and m.cust_id = md.cust_id
            LEFT JOIN workers AS w
            ON
                w.id = md.worker_id
            WHERE
                md.report_date > '$dayShift_DateTime' AND md.report_date <= '$dayShift_DateTime_part1' AND md.shift = 1 AND $where_condition 
            ORDER BY
                md.id DESC";

            $dayShift1_data = (array) DB::select($dayShift_1);      
            $dayShift1_data_new = array();
            $dayShift1_data_rmp = array();

            foreach ($dayShift1_data as $dayShiftKey1 => $dayShift1) {
                $dayShift1_data[$dayShiftKey1]->stitches_total = $dayShift1->stitches;
                $dayShift1_data[$dayShiftKey1]->thred_break_total = $dayShift1->thred_break;
            }

            foreach($dayShift1_data as $shift1_data){
                if(!array_key_exists($shift1_data->machine_number,$dayShift1_data_new)){
                    $dayShift1_data_new[$shift1_data->machine_number] = $shift1_data;
                }     
                
                if(!array_key_exists($shift1_data->machine_number,$dayShift1_data_rmp)){
                    $dayShift1_data_rmp[$shift1_data->machine_number] = [];                    
                }
                $dayShift1_data_rmp[$shift1_data->machine_number][] = $shift1_data->rpm;
            }   
            
            ksort($dayShift1_data_new);

            foreach($dayShift1_data_new as $machineNo => $dayShift_obj){
                $dayShift1_data_rmp_filter = array_filter($dayShift1_data_rmp[$machineNo]);
                $average_rpm = 0;
                if(count($dayShift1_data_rmp_filter) > 0){
                    $average_rpm = array_sum($dayShift1_data_rmp_filter)/count($dayShift1_data_rmp_filter);
                }
                $dayShift1_data_new[$machineNo]->avg_rpm = (int)$average_rpm;
            }

            $data[$current_date]['day_shift_part1'] = array_merge($data[$current_date]['day_shift_part1'],$dayShift1_data_new);
            $data[$current_date]['day_shift_part1']['startDateTime'] = $dayShift_DateTime;
            $data[$current_date]['day_shift_part1']['endDateTime'] = $dayShift_DateTime_part1->toDateTimeString();

            // Day shift - Part 2
            $dayShift_2 = "SELECT
                md.*,
                DATE_FORMAT(md.report_date, '%Y-%m-%d') AS reports_date,
                m.machine_name,
                w.first_name AS wrk_first_name,
                w.last_name AS wrk_last_name
            FROM
                `machine_hourly_data` AS md
            JOIN machines AS m
            ON
                m.machine_number = md.machine_number and m.cust_id = md.cust_id
            LEFT JOIN workers AS w
            ON
                w.id = md.worker_id
            WHERE
                md.report_date > '$dayShift_DateTime_part1' AND md.report_date <= '$dayShift_DateTime_part2' AND md.shift = 1 AND $where_condition 
            ORDER BY
                md.id DESC";

            $dayShift2_data = (array) DB::select($dayShift_2);
            $dayShift2_data_new = array();
            $dayShift2_data_rmp = array();

            foreach($dayShift2_data as $shift2_data){
                if(!array_key_exists($shift2_data->machine_number,$dayShift2_data_new)){
                    $dayShift2_data_new[$shift2_data->machine_number] = $shift2_data;
                }                

                if(!array_key_exists($shift2_data->machine_number,$dayShift2_data_rmp)){
                    $dayShift2_data_rmp[$shift2_data->machine_number] = [];                    
                }
                $dayShift2_data_rmp[$shift2_data->machine_number][] = $shift2_data->rpm;
            }
            ksort($dayShift2_data_new);
            
            foreach($dayShift2_data_new as $machineNo => $dayShift_obj){
                $dayShift2_data_rmp_filter = array_filter($dayShift2_data_rmp[$machineNo]);
                $average_rpm = 0;
                if(count($dayShift2_data_rmp_filter) > 0){
                    $average_rpm = array_sum($dayShift2_data_rmp_filter)/count($dayShift2_data_rmp_filter);
                }
                $dayShift2_data_new[$machineNo]->avg_rpm = (int)$average_rpm;
            }

            foreach ($dayShift2_data_new as $dayShiftKey2 => $dayShift2) {
                foreach ($dayShift1_data_new as $dayShiftKey1 => $dayShift1) {
                   if($dayShift2->machine_id == $dayShift1->machine_id){
                       $dayShift2_data_new[$dayShiftKey2]->stitches_total = $dayShift2->stitches;
                       $dayShift2_data_new[$dayShiftKey2]->thred_break_total = $dayShift2->thred_break;
                       $dayShift2_data_new[$dayShiftKey2]->stitches = isset($dayShift1->stitches_total)?$dayShift2->stitches - $dayShift1->stitches_total:$dayShift2->stitches;
                       $dayShift2_data_new[$dayShiftKey2]->thred_break = isset($dayShift1->thred_break_total)?$dayShift2->thred_break - $dayShift1->thred_break_total:$dayShift2->thred_break;
                       $dayShift2_data_new[$dayShiftKey2]->stop_time = $dayShift2->stop_time - $dayShift1->stop_time;
                   }
                }
            }  
            
            if(count($dayShift2_data_new)==0){
                foreach($dayShift1_data_new as $machineNo => $dayShift_obj){
                    $dayShift2_data_new[$machineNo] = clone(object)$dayShift_obj;
                    $dayShift2_data_new[$machineNo]->stitches = 0;
                    $dayShift2_data_new[$machineNo]->thred_break = 0;
                    $dayShift2_data_new[$machineNo]->rpm = 0;
                    $dayShift2_data_new[$machineNo]->max_rpm = 0;
                    $dayShift2_data_new[$machineNo]->stop_time = 0;
                }
            }
            
            $data[$current_date]['day_shift_part2'] = array_merge($data[$current_date]['day_shift_part2'],$dayShift2_data_new);
            $data[$current_date]['day_shift_part2']['startDateTime'] = $dayShift_DateTime_part1->toDateTimeString();
            $data[$current_date]['day_shift_part2']['endDateTime'] = $dayShift_DateTime_part2->toDateTimeString();

            // Day shift - Part 3
            $dayShift_3 = "SELECT
                md.*,
                DATE_FORMAT(md.report_date, '%Y-%m-%d') AS reports_date,
                m.machine_name,
                w.first_name AS wrk_first_name,
                w.last_name AS wrk_last_name
            FROM
                `machine_hourly_data` AS md
            JOIN machines AS m
            ON
                m.machine_number = md.machine_number and m.cust_id = md.cust_id
            LEFT JOIN workers AS w
            ON
                w.id = md.worker_id
            WHERE
                md.report_date > '$dayShift_DateTime_part2' AND md.report_date <= '$dayShift_DateTime_part3' AND md.shift = 1 AND $where_condition 
            ORDER BY
                md.id DESC";
            
            $dayShift3_data = (array) DB::select($dayShift_3);

            $dayShift3_data_new = array();
            $dayShift3_data_rmp = array();

            foreach($dayShift3_data as $shift3_data){
                if(!array_key_exists($shift3_data->machine_number,$dayShift3_data_new)){
                    $dayShift3_data_new[$shift3_data->machine_number] = $shift3_data;
                }                

                if(!array_key_exists($shift3_data->machine_number,$dayShift3_data_rmp)){
                    $dayShift3_data_rmp[$shift3_data->machine_number] = [];                    
                }
                $dayShift3_data_rmp[$shift3_data->machine_number][] = $shift3_data->rpm;
            }
            ksort($dayShift3_data_new);

            foreach($dayShift3_data_new as $machineNo => $dayShift_obj){
                $dayShift3_data_rmp_filter = array_filter($dayShift3_data_rmp[$machineNo]);
                $average_rpm = 0;
                if(count($dayShift3_data_rmp_filter) > 0){
                    $average_rpm = array_sum($dayShift3_data_rmp_filter)/count($dayShift3_data_rmp_filter);
                }
                $dayShift3_data_new[$machineNo]->avg_rpm = (int)$average_rpm;
            }

            foreach ($dayShift3_data_new as $dayShiftKey3 => $dayShift3) {
                foreach ($dayShift2_data_new as $dayShiftKey2 => $dayShift2) {
                   if($dayShift3->machine_id == $dayShift2->machine_id){
                       $dayShift3_data_new[$dayShiftKey3]->stitches_total = $dayShift3->stitches;
                       $dayShift3_data_new[$dayShiftKey3]->thred_break_total = $dayShift3->thred_break;
                       $dayShift3_data_new[$dayShiftKey3]->stitches = isset($dayShift2->stitches_total)?$dayShift3->stitches - $dayShift2->stitches_total:$dayShift3->stitches;
                       $dayShift3_data_new[$dayShiftKey3]->thred_break = isset($dayShift2->thred_break_total)?$dayShift3->thred_break - $dayShift2->thred_break_total:$dayShift3->thred_break;
                       $dayShift3_data_new[$dayShiftKey3]->stop_time = $dayShift3->stop_time - $dayShift2->stop_time;
                   }
                }
            }   
            
            if(count($dayShift3_data_new)==0){
                foreach($dayShift2_data_new as $machineNo => $dayShift_obj){
                    $dayShift3_data_new[$machineNo] = clone(object)$dayShift_obj;
                    $dayShift3_data_new[$machineNo]->stitches = 0;
                    $dayShift3_data_new[$machineNo]->thred_break = 0;
                    $dayShift3_data_new[$machineNo]->rpm = 0;
                    $dayShift3_data_new[$machineNo]->max_rpm = 0;
                    $dayShift3_data_new[$machineNo]->stop_time = 0;
                }
            }

            $data[$current_date]['day_shift_part3'] = array_merge($data[$current_date]['day_shift_part3'],$dayShift3_data_new);
            $data[$current_date]['day_shift_part3']['startDateTime'] = $dayShift_DateTime_part2->toDateTimeString();
            $data[$current_date]['day_shift_part3']['endDateTime'] = $dayShift_DateTime_part3->toDateTimeString();

            // Day shift - Part 4
            $dayShift_4 = "SELECT
                md.*,
                DATE_FORMAT(md.report_date, '%Y-%m-%d') AS reports_date,
                m.machine_name,
                w.first_name AS wrk_first_name,
                w.last_name AS wrk_last_name
            FROM
                `machine_hourly_data` AS md
            JOIN machines AS m
            ON
                m.machine_number = md.machine_number and m.cust_id = md.cust_id
            LEFT JOIN workers AS w
            ON
                w.id = md.worker_id
            WHERE
                md.report_date > '$dayShift_DateTime_part3' AND md.report_date <= '$dayShift_DateTime_part4' AND md.shift = 1 AND $where_condition 
            ORDER BY
                md.id DESC";

            $dayShift4_data = (array) DB::select($dayShift_4);

            $dayShift4_data_new = array();
            $dayShift4_data_rmp = array();

            foreach($dayShift4_data as $shift4_data){
                if(!array_key_exists($shift4_data->machine_number,$dayShift4_data_new)){
                    $dayShift4_data_new[$shift4_data->machine_number] = $shift4_data;
                }                

                if(!array_key_exists($shift4_data->machine_number,$dayShift4_data_rmp)){
                    $dayShift4_data_rmp[$shift4_data->machine_number] = [];                    
                }
                $dayShift4_data_rmp[$shift4_data->machine_number][] = $shift4_data->rpm;
            }
            ksort($dayShift4_data_new);

            foreach($dayShift4_data_new as $machineNo => $dayShift_obj){
                $dayShift4_data_rmp_filter = array_filter($dayShift4_data_rmp[$machineNo]);
                $average_rpm = 0;
                if(count($dayShift4_data_rmp_filter) > 0){
                    $average_rpm = array_sum($dayShift4_data_rmp_filter)/count($dayShift4_data_rmp_filter);
                }
                $dayShift4_data_new[$machineNo]->avg_rpm = (int)$average_rpm;
            }

            foreach ($dayShift4_data_new as $dayShiftKey4 => $dayShift4) {
                foreach ($dayShift3_data_new as $dayShiftKey3 => $dayShift3) {
                   if($dayShift4->machine_id == $dayShift3->machine_id){
                       $dayShift4_data_new[$dayShiftKey4]->stitches_total = $dayShift4->stitches;
                       $dayShift4_data_new[$dayShiftKey4]->thred_break_total = $dayShift4->thred_break;
                       $dayShift4_data_new[$dayShiftKey4]->stitches = isset($dayShift3->stitches_total)?$dayShift4->stitches - $dayShift3->stitches_total:$dayShift4->stitches;
                       $dayShift4_data_new[$dayShiftKey4]->thred_break = isset($dayShift3->thred_break_total)?$dayShift4->thred_break - $dayShift3->thred_break_total:$dayShift4->thred_break;
                       $dayShift4_data_new[$dayShiftKey4]->stop_time = $dayShift4->stop_time - $dayShift3->stop_time;
                   }
                }
            }

            if(count($dayShift4_data_new)==0){
                foreach($dayShift3_data_new as $machineNo => $dayShift_obj){
                    $dayShift4_data_new[$machineNo] = clone(object)$dayShift_obj;
                    $dayShift4_data_new[$machineNo]->stitches = 0;
                    $dayShift4_data_new[$machineNo]->thred_break = 0;
                    $dayShift4_data_new[$machineNo]->rpm = 0;
                    $dayShift4_data_new[$machineNo]->max_rpm = 0;
                    $dayShift4_data_new[$machineNo]->stop_time = 0;
                }
            }

            $data[$current_date]['day_shift_part4'] = array_merge($data[$current_date]['day_shift_part4'],$dayShift4_data_new);
            $data[$current_date]['day_shift_part4']['startDateTime'] = $dayShift_DateTime_part3->toDateTimeString();
            $data[$current_date]['day_shift_part4']['endDateTime'] = $dayShift_DateTime_part4->toDateTimeString();


            // -------------------------------------------------------------------------------
                        
            // Night shift - Part 1
            $nightShift_1 = "SELECT
                md.*,
                DATE_FORMAT(md.report_date, '%Y-%m-%d') AS reports_date,
                m.machine_name,
                w.first_name AS wrk_first_name,
                w.last_name AS wrk_last_name
            FROM
                `machine_hourly_data` AS md
            JOIN machines AS m
            ON
                m.machine_number = md.machine_number and m.cust_id = md.cust_id
            LEFT JOIN workers AS w
            ON
                w.id = md.worker_id
            WHERE
                md.report_date > '$nightShift_DateTime' AND md.report_date <= '$nightShift_DateTime_part1' AND md.shift = 2 AND $where_condition 
            ORDER BY
                md.id DESC";            
            
            $nightShift1_data = (array) DB::select($nightShift_1);

            foreach ($nightShift1_data as $nightShiftKey1 => $nightShift1) {
                $nightShift1_data[$nightShiftKey1]->stitches_total = $nightShift1->stitches;
                $nightShift1_data[$nightShiftKey1]->thred_break_total = $nightShift1->thred_break;
            }  

            $nightShift1_data_new = array();
            $nightShift1_data_rmp = array();

            foreach($nightShift1_data as $shift1_data){
                if(!array_key_exists($shift1_data->machine_number,$nightShift1_data_new)){
                    $nightShift1_data_new[$shift1_data->machine_number] = $shift1_data;
                }      
                
                if(!array_key_exists($shift1_data->machine_number,$nightShift1_data_rmp)){
                    $nightShift1_data_rmp[$shift1_data->machine_number] = [];                    
                }
                $nightShift1_data_rmp[$shift1_data->machine_number][] = $shift1_data->rpm;
            }

            ksort($nightShift1_data_new);

            foreach($nightShift1_data_new as $machineNo => $nightShift_obj){
                $nightShift1_data_rmp_filter = array_filter($nightShift1_data_rmp[$machineNo]);
                $average_rpm = 0;
                if(count($nightShift1_data_rmp_filter) > 0){
                    $average_rpm = array_sum($nightShift1_data_rmp_filter)/count($nightShift1_data_rmp_filter);
                }
                $nightShift1_data_new[$machineNo]->avg_rpm = (int)$average_rpm;
            }

            $data[$current_date]['night_shift_part1'] = array_merge($data[$current_date]['night_shift_part1'],$nightShift1_data_new);
            $data[$current_date]['night_shift_part1']['startDateTime'] = $nightShift_DateTime;
            $data[$current_date]['night_shift_part1']['endDateTime'] = $nightShift_DateTime_part1->toDateTimeString();

            // Night shift - Part 2
            $nightShift_2 = "SELECT
                md.*,
                DATE_FORMAT(md.report_date, '%Y-%m-%d') AS reports_date,
                m.machine_name,
                w.first_name AS wrk_first_name,
                w.last_name AS wrk_last_name
            FROM
                `machine_hourly_data` AS md
            JOIN machines AS m
            ON
                m.machine_number = md.machine_number and m.cust_id = md.cust_id
            LEFT JOIN workers AS w
            ON
                w.id = md.worker_id
            WHERE
                md.report_date > '$nightShift_DateTime_part1' AND md.report_date <= '$nightShift_DateTime_part2' AND md.shift = 2 AND $where_condition 
            ORDER BY
                md.id DESC";            
            
            $nightShift2_data = (array) DB::select($nightShift_2);
            
            $nightShift2_data_new = array();
            $nightShift2_data_rmp = array();

            foreach($nightShift2_data as $shift2_data){
                if(!array_key_exists($shift2_data->machine_number,$nightShift2_data_new)){
                    $nightShift2_data_new[$shift2_data->machine_number] = $shift2_data;
                }
                if(!array_key_exists($shift2_data->machine_number,$nightShift2_data_rmp)){
                    $nightShift2_data_rmp[$shift2_data->machine_number] = [];                    
                }
                $nightShift2_data_rmp[$shift2_data->machine_number][] = $shift2_data->rpm;
                
            }
            ksort($nightShift2_data_new);

            foreach($nightShift2_data_new as $machineNo => $nightShift_obj){
                $nightShift2_data_rmp_filter = array_filter($nightShift2_data_rmp[$machineNo]);
                $average_rpm = 0;
                if(count($nightShift2_data_rmp_filter) > 0){
                    $average_rpm = array_sum($nightShift2_data_rmp_filter)/count($nightShift2_data_rmp_filter);
                }
                $nightShift2_data_new[$machineNo]->avg_rpm = (int)$average_rpm;
            }           

            foreach ($nightShift2_data_new as $nightShiftKey2 => $nightShift2) {
                foreach ($nightShift1_data_new as $nightShiftKey1 => $nightShift1) {
                   if($nightShift2->machine_id == $nightShift1->machine_id){
                       $nightShift2_data_new[$nightShiftKey2]->stitches_total = $nightShift2->stitches;
                       $nightShift2_data_new[$nightShiftKey2]->thred_break_total = $nightShift2->thred_break;
                       $nightShift2_data_new[$nightShiftKey2]->stitches = isset($nightShift1->stitches_total)?$nightShift2->stitches - $nightShift1->stitches_total:$nightShift2->stitches;
                       $nightShift2_data_new[$nightShiftKey2]->thred_break = isset($nightShift1->thred_break_total)?$nightShift2->thred_break - $nightShift1->thred_break_total:$nightShift2->thred_break;
                       $nightShift2_data_new[$nightShiftKey2]->stop_time = $nightShift2->stop_time - $nightShift1->stop_time;
                   }
                }
            }

            if(count($nightShift2_data_new)==0){
                foreach($nightShift1_data_new as $machineNo => $dayShift_obj){
                    $nightShift2_data_new[$machineNo] = clone(object)$dayShift_obj;
                    $nightShift2_data_new[$machineNo]->stitches = 0;
                    $nightShift2_data_new[$machineNo]->thred_break = 0;
                    $nightShift2_data_new[$machineNo]->rpm = 0;
                    $nightShift2_data_new[$machineNo]->max_rpm = 0;
                    $nightShift2_data_new[$machineNo]->stop_time = 0;
                }
            }

            $data[$current_date]['night_shift_part2'] = array_merge($data[$current_date]['night_shift_part2'],$nightShift2_data_new);
            $data[$current_date]['night_shift_part2']['startDateTime'] = $nightShift_DateTime_part1->toDateTimeString();
            $data[$current_date]['night_shift_part2']['endDateTime'] = $nightShift_DateTime_part2->toDateTimeString();

            // Night shift - Part 3
            $nightShift_3 = "SELECT
                md.*,
                DATE_FORMAT(md.report_date, '%Y-%m-%d') AS reports_date,
                m.machine_name,
                w.first_name AS wrk_first_name,
                w.last_name AS wrk_last_name
            FROM
                `machine_hourly_data` AS md
            JOIN machines AS m
            ON
                m.machine_number = md.machine_number and m.cust_id = md.cust_id
            LEFT JOIN workers AS w
            ON
                w.id = md.worker_id
            WHERE
                md.report_date > '$nightShift_DateTime_part2' AND md.report_date <= '$nightShift_DateTime_part3' AND md.shift = 2 AND $where_condition 
            ORDER BY
                md.id DESC";            
            
            $nightShift3_data = (array) DB::select($nightShift_3);

            $nightShift3_data_new = array();
            $nightShift3_data_rmp = array();

            foreach($nightShift3_data as $shift3_data){
                if(!array_key_exists($shift3_data->machine_number,$nightShift3_data_new)){
                    $nightShift3_data_new[$shift3_data->machine_number] = $shift3_data;
                }
                if(!array_key_exists($shift3_data->machine_number,$nightShift3_data_rmp)){
                    $nightShift3_data_rmp[$shift3_data->machine_number] = [];                    
                }
                $nightShift3_data_rmp[$shift3_data->machine_number][] = $shift3_data->rpm;
                
            }
            ksort($nightShift3_data_new);

            foreach($nightShift3_data_new as $machineNo => $nightShift_obj){
                $nightShift3_data_rmp_filter = array_filter($nightShift3_data_rmp[$machineNo]);
                $average_rpm = 0;
                if(count($nightShift3_data_rmp_filter) > 0){
                    $average_rpm = array_sum($nightShift3_data_rmp_filter)/count($nightShift3_data_rmp_filter);
                }
                $nightShift3_data_new[$machineNo]->avg_rpm = (int)$average_rpm;
            }

            foreach ($nightShift3_data_new as $nightShiftKey3 => $nightShift3) {
                foreach ($nightShift2_data_new as $nightShiftKey2 => $nightShift2) {
                   if($nightShift3->machine_id == $nightShift2->machine_id){
                       $nightShift3_data_new[$nightShiftKey3]->stitches_total = $nightShift3->stitches;
                       $nightShift3_data_new[$nightShiftKey3]->thred_break_total = $nightShift3->thred_break;
                       $nightShift3_data_new[$nightShiftKey3]->stitches = isset($nightShift2->stitches_total)?$nightShift3->stitches - $nightShift2->stitches_total:$nightShift3->stitches;
                       $nightShift3_data_new[$nightShiftKey3]->thred_break = isset($nightShift2->thred_break_total)?$nightShift3->thred_break - $nightShift2->thred_break_total:$nightShift3->thred_break;
                       $nightShift3_data_new[$nightShiftKey3]->stop_time = $nightShift3->stop_time - $nightShift2->stop_time;
                   }
                }
            }

            if(count($nightShift3_data_new)==0){
                foreach($nightShift2_data_new as $machineNo => $dayShift_obj){
                    $nightShift3_data_new[$machineNo] = clone(object)$dayShift_obj;
                    $nightShift3_data_new[$machineNo]->stitches = 0;
                    $nightShift3_data_new[$machineNo]->thred_break = 0;
                    $nightShift3_data_new[$machineNo]->rpm = 0;
                    $nightShift3_data_new[$machineNo]->max_rpm = 0;
                    $nightShift3_data_new[$machineNo]->stop_time = 0;
                }
            }

            $data[$current_date]['night_shift_part3'] = array_merge($data[$current_date]['night_shift_part3'],$nightShift3_data_new);
            $data[$current_date]['night_shift_part3']['startDateTime'] = $nightShift_DateTime_part2->toDateTimeString();
            $data[$current_date]['night_shift_part3']['endDateTime'] = $nightShift_DateTime_part3->toDateTimeString();

            // Night shift - Part 4
            $nightShift_4 = "SELECT
                md.*,
                DATE_FORMAT(md.report_date, '%Y-%m-%d') AS reports_date,
                m.machine_name,
                w.first_name AS wrk_first_name,
                w.last_name AS wrk_last_name
            FROM
                `machine_hourly_data` AS md
            JOIN machines AS m
            ON
                m.machine_number = md.machine_number and m.cust_id = md.cust_id
            LEFT JOIN workers AS w
            ON
                w.id = md.worker_id
            WHERE
                md.report_date > '$nightShift_DateTime_part3' AND md.report_date <= '$nightShift_DateTime_part4' AND md.shift = 2 AND $where_condition 
            ORDER BY
                md.id DESC";            
            
            $nightShift4_data = (array) DB::select($nightShift_4);

            $nightShift4_data_new = array();
            $nightShift4_data_rmp = array();

            foreach($nightShift4_data as $shift4_data){
                if(!array_key_exists($shift4_data->machine_number,$nightShift4_data_new)){
                    $nightShift4_data_new[$shift4_data->machine_number] = $shift4_data;
                }
                if(!array_key_exists($shift4_data->machine_number,$nightShift4_data_rmp)){
                    $nightShift4_data_rmp[$shift4_data->machine_number] = [];                    
                }
                $nightShift4_data_rmp[$shift4_data->machine_number][] = $shift4_data->rpm;
                
            }
            ksort($nightShift4_data_new);

            foreach($nightShift4_data_new as $machineNo => $nightShift_obj){
                $nightShift4_data_rmp_filter = array_filter($nightShift4_data_rmp[$machineNo]);
                $average_rpm = 0;
                if(count($nightShift4_data_rmp_filter) > 0){
                    $average_rpm = array_sum($nightShift4_data_rmp_filter)/count($nightShift4_data_rmp_filter);
                }
                $nightShift4_data_new[$machineNo]->avg_rpm = (int)$average_rpm;
            }

            foreach ($nightShift4_data_new as $nightShiftKey4 => $nightShift4) {
                foreach ($nightShift3_data_new as $nightShiftKey3 => $nightShift3) {
                   if($nightShift4->machine_id == $nightShift3->machine_id){
                       $nightShift4_data_new[$nightShiftKey4]->stitches_total = $nightShift4->stitches;
                       $nightShift4_data_new[$nightShiftKey4]->thred_break_total = $nightShift4->thred_break;
                       $nightShift4_data_new[$nightShiftKey4]->stitches = isset($nightShift3->stitches_total)?$nightShift4->stitches - $nightShift3->stitches_total:$nightShift4->stitches;
                       $nightShift4_data_new[$nightShiftKey4]->thred_break = isset($nightShift3->thred_break_total)?$nightShift4->thred_break - $nightShift3->thred_break_total:$nightShift4->thred_break;
                       $nightShift4_data_new[$nightShiftKey4]->stop_time = $nightShift4->stop_time - $nightShift3->stop_time;
                   }
                }
            }

            if(count($nightShift4_data_new)==0){
                foreach($nightShift3_data_new as $machineNo => $dayShift_obj){
                    $nightShift4_data_new[$machineNo] = clone(object)$dayShift_obj;
                    $nightShift4_data_new[$machineNo]->stitches = 0;
                    $nightShift4_data_new[$machineNo]->thred_break = 0;
                    $nightShift4_data_new[$machineNo]->rpm = 0;
                    $nightShift4_data_new[$machineNo]->max_rpm = 0;
                    $nightShift4_data_new[$machineNo]->stop_time = 0;
                }
            }

            $data[$current_date]['night_shift_part4'] = array_merge($data[$current_date]['night_shift_part4'],$nightShift4_data_new);
            $data[$current_date]['night_shift_part4']['startDateTime'] = $nightShift_DateTime_part3->toDateTimeString();
            $data[$current_date]['night_shift_part4']['endDateTime'] = $nightShift_DateTime_part4->toDateTimeString();

            $data = json_decode(json_encode($data),1);
            
        }    

        $view_mode = $options['view_mode'];
        if ($view_mode=='download') {
            
            $file_name = '3_hrs_production_report_' . time() . '.pdf';
            
            $file_path = $this->report_path . $file_name;

            $pdf = PDF::loadView('user.reports.3hour_report', [
                'machine_list' => $machine_list,
                'color_range' => $color_range,  
                'report_data' => $data,
                'column_settings' => $column_settings,
                'day_shift' => $day_shift,
                'night_shift' => $night_shift,
                'from_date' => date("d-m-Y",$from_date),
                'to_date' => date("d-m-Y",$to_date),
                'company_name' => !empty($options['company_name']) ? $options['company_name'] : 'Company Name',
                'view_mode' => 'download',
                'options' => $options,
                'setting' => $this->setting
            ])->setPaper('a4', 'landscape')->setWarnings(false);

            $pdf->save($file_path);

            if (File::exists($file_path)) {
                $result['status'] = 1;
                $result['file_name'] = $file_name;
            }else{
                $result['status'] = 0;
            }

        }else{

            $view = view('user.reports.3hour_report',[
                'machine_list' => $machine_list,
                'report_data' => $data,
                'column_settings' => $column_settings,
                'day_shift' => $day_shift,
                'options' => $options,
                'night_shift' => $night_shift,
                'color_range' => $color_range,
                'from_date' => date('d-m-Y',strtotime($from_date)),
                'to_date' => date('d-m-Y',strtotime($to_date)),
                'setting' => $this->setting
            ]);
            $result['status'] = 1;
            $result['html'] = $view->render();
        }

        return $result;
        
    }

    /**
     * Generate 6 Hours Reports
     *
     * @param Array $mix
     * @return Data
     */
    public function generate_6hr_report($options = [])
    {
        ini_set('memory_limit', '-1');
        $options['cust_id'] = (!$this->Abettor->isAdmin()) ? auth()->user()->parent_id : Auth::id();
        $result = [];
        $column_settings = !empty($options['column_settings']) ? json_decode($options['column_settings'],1) : [];
        $from_date = strtotime(date('Y-m-d',strtotime($options['from_date'])));
        $to_date = strtotime(date('Y-m-d',strtotime($options['to_date'])));
        $day_shift = date('H:i:s',strtotime($options['day_shift']));
        $night_shift = date('H:i:s',strtotime($options['night_shift']));
        $machine_list = $this->Abettor->get_machines('all',$options['cust_id']);
        $machine_list_string = '"'.implode('","',$machine_list) .'"';
        $color_range = $this->Abettor->get_customer_color_range($options['cust_id']);

        $options['header_data'] = false;
        
        $other_condition = [];
        if ($options['machine_no'] != 'all') {
            $other_condition[] = "md.machine_number='" . $options['machine_no'] . "'";
            $options['header_data'] = true;
        }else{
            (count($machine_list) > 0) ? $other_condition[] = "md.machine_number IN (" . $machine_list_string . ")" : '';
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
        /* echo "<pre>";
        print_r($other_condition);
        die; */
        $where_condition = $other_condition;
        $where_condition[] = "md.cust_id='". $options['cust_id'] ."'";
        $where_condition = implode(" AND ",$where_condition);

        $days_count = $to_date - $from_date;
        $days_count = round($days_count / (60 * 60 * 24));

        // Loop through Each days
        for ($i=0; $i <= $days_count; $i++) { 
            $new_date = date("Y-m-d",strtotime("+". $i ." days",$from_date));
            $current_date = $new_date;

            $data[$current_date]['day_shift_part1'] = [];
            $data[$current_date]['day_shift_part2'] = [];
            $data[$current_date]['night_shift_part1'] = [];
            $data[$current_date]['night_shift_part2'] = [];

            $dayShift_DateTime = $current_date." ".$day_shift;
            $nightShift_DateTime = $current_date." ".$night_shift; 

            $dayShift_DateTime_part1 = Carbon::parse($dayShift_DateTime)->addHours(6);
            $dayShift_DateTime_part2 = Carbon::parse($dayShift_DateTime)->addHours(12);

            $nightShift_DateTime_part1 = Carbon::parse($nightShift_DateTime)->addHours(6);
            $nightShift_DateTime_part2 = Carbon::parse($nightShift_DateTime)->addHours(12);

            // Day shift - Part 1
            $dayShift_1 = "SELECT
                md.*,
                DATE_FORMAT(md.report_date, '%Y-%m-%d') AS reports_date,
                m.machine_name,
                w.first_name AS wrk_first_name,
                w.last_name AS wrk_last_name
            FROM
                `machine_hourly_data` AS md
            LEFT JOIN machines AS m
            ON
                m.machine_number = md.machine_number and m.cust_id = md.cust_id
            JOIN workers AS w
            ON
                w.id = md.worker_id
            WHERE
                md.report_date > '$dayShift_DateTime' AND md.report_date <= '$dayShift_DateTime_part1' AND md.shift = 1 AND $where_condition 
            ORDER BY
                md.id DESC";  
            
            $dayShift1_data =(array) DB::select($dayShift_1);
            $dayShift1_data_new = array();
            $dayShift1_data_rmp = array();

            foreach($dayShift1_data as $shift1_data){
                if(!array_key_exists($shift1_data->machine_number,$dayShift1_data_new)){
                    $dayShift1_data_new[$shift1_data->machine_number] = $shift1_data;
                }     
                
                if(!array_key_exists($shift1_data->machine_number,$dayShift1_data_rmp)){
                    $dayShift1_data_rmp[$shift1_data->machine_number] = [];                    
                }
                $dayShift1_data_rmp[$shift1_data->machine_number][] = $shift1_data->rpm;
            }

            ksort($dayShift1_data_new);

            foreach($dayShift1_data_new as $machineNo => $dayShift_obj){
                $dayShift1_data_rmp_filter = array_filter($dayShift1_data_rmp[$machineNo]);
                $average_rpm = 0;
                if(count($dayShift1_data_rmp_filter) > 0){
                    $average_rpm = array_sum($dayShift1_data_rmp_filter)/count($dayShift1_data_rmp_filter);
                }
                $dayShift1_data_new[$machineNo]->avg_rpm = (int)$average_rpm;
            }

            $data[$current_date]['day_shift_part1'] = array_merge($data[$current_date]['day_shift_part1'],$dayShift1_data_new);
            $data[$current_date]['day_shift_part1']['startDateTime'] = $dayShift_DateTime;
            $data[$current_date]['day_shift_part1']['endDateTime'] = $dayShift_DateTime_part1->toDateTimeString();
            
            // Day shift - Part 2
            $dayShift_2 = "SELECT
                md.*,
                DATE_FORMAT(md.report_date, '%Y-%m-%d') AS reports_date,
                m.machine_name,
                w.first_name AS wrk_first_name,
                w.last_name AS wrk_last_name
            FROM
                `machine_hourly_data` AS md
            JOIN machines AS m
            ON
                m.machine_number = md.machine_number and m.cust_id = md.cust_id
            LEFT JOIN workers AS w
            ON
                w.id = md.worker_id
            WHERE
                md.report_date > '$dayShift_DateTime_part1' AND md.report_date <= '$dayShift_DateTime_part2' AND md.shift = 1 AND $where_condition 
            ORDER BY
                md.id DESC";

            $dayShift2_data = (array) DB::select($dayShift_2);
            $dayShift2_data_new = array();
            $dayShift2_data_rmp = array();

            foreach($dayShift2_data as $shift2_data){
                if(!array_key_exists($shift2_data->machine_number,$dayShift2_data_new)){
                    $dayShift2_data_new[$shift2_data->machine_number] = $shift2_data;
                }                

                if(!array_key_exists($shift2_data->machine_number,$dayShift2_data_rmp)){
                    $dayShift2_data_rmp[$shift2_data->machine_number] = [];                    
                }
                $dayShift2_data_rmp[$shift2_data->machine_number][] = $shift2_data->rpm;
            }
            ksort($dayShift2_data_new);

            foreach($dayShift2_data_new as $machineNo => $dayShift_obj){
                $dayShift2_data_rmp_filter = array_filter($dayShift2_data_rmp[$machineNo]);
                $average_rpm = 0;
                if(count($dayShift2_data_rmp_filter) > 0){
                    $average_rpm = array_sum($dayShift2_data_rmp_filter)/count($dayShift2_data_rmp_filter);
                }
                $dayShift2_data_new[$machineNo]->avg_rpm = (int)$average_rpm;
            }

            foreach ($dayShift2_data_new as $dayShiftKey2 => $dayShift2) {
                foreach ($dayShift1_data_new as $dayShiftKey1 => $dayShift1) {
                   if($dayShift2->machine_id == $dayShift1->machine_id){
                       $dayShift2_data_new[$dayShiftKey2]->stitches_total = $dayShift2->stitches;
                       $dayShift2_data_new[$dayShiftKey2]->stitches = $dayShift2->stitches - $dayShift1->stitches;
                       $dayShift2_data_new[$dayShiftKey2]->thred_break = $dayShift2->thred_break - $dayShift1->thred_break;
                       $dayShift2_data_new[$dayShiftKey2]->stop_time = $dayShift2->stop_time - $dayShift1->stop_time;
                   }
                }
            }

            if(count($dayShift2_data_new)==0){
                foreach($dayShift1_data_new as $machineNo => $dayShift_obj){
                    $dayShift2_data_new[$machineNo] = clone(object)$dayShift_obj;
                    $dayShift2_data_new[$machineNo]->stitches = 0;
                    $dayShift2_data_new[$machineNo]->thred_break = 0;
                    $dayShift2_data_new[$machineNo]->rpm = 0;
                    $dayShift2_data_new[$machineNo]->max_rpm = 0;
                    $dayShift2_data_new[$machineNo]->stop_time = 0;
                }
            }

            $data[$current_date]['day_shift_part2'] = array_merge($data[$current_date]['day_shift_part2'],$dayShift2_data_new);
            $data[$current_date]['day_shift_part2']['startDateTime'] = $dayShift_DateTime_part1->toDateTimeString();
            $data[$current_date]['day_shift_part2']['endDateTime'] = $dayShift_DateTime_part2->toDateTimeString();
            // -------------------------------------------------------------------------------
                        
            // Night shift - Part 1
            $nightShift_1 = "SELECT
                md.*,
                DATE_FORMAT(md.report_date, '%Y-%m-%d') AS reports_date,
                m.machine_name,
                w.first_name AS wrk_first_name,
                w.last_name AS wrk_last_name
            FROM
                `machine_hourly_data` AS md
            JOIN machines AS m
            ON
                m.machine_number = md.machine_number and m.cust_id = md.cust_id
            LEFT JOIN workers AS w
            ON
                w.id = md.worker_id
            WHERE
                md.report_date > '$nightShift_DateTime' AND md.report_date <= '$nightShift_DateTime_part1' AND md.shift = 2 AND $where_condition 
            ORDER BY
                md.id DESC";
                        
            $nightShift1_data = DB::select($nightShift_1);
            $nightShift1_data_new = array();
            $nightShift1_data_rmp = array();

            foreach($nightShift1_data as $shift1_data){
                if(!array_key_exists($shift1_data->machine_number,$nightShift1_data_new)){
                    $nightShift1_data_new[$shift1_data->machine_number] = $shift1_data;
                }      
                
                if(!array_key_exists($shift1_data->machine_number,$nightShift1_data_rmp)){
                    $nightShift1_data_rmp[$shift1_data->machine_number] = [];                    
                }
                $nightShift1_data_rmp[$shift1_data->machine_number][] = $shift1_data->rpm;
            }

            ksort($nightShift1_data_new);

            foreach($nightShift1_data_new as $machineNo => $nightShift_obj){
                $nightShift1_data_rmp_filter = array_filter($nightShift1_data_rmp[$machineNo]);
                $average_rpm = 0;
                if(count($nightShift1_data_rmp_filter) > 0){
                    $average_rpm = array_sum($nightShift1_data_rmp_filter)/count($nightShift1_data_rmp_filter);
                }
                $nightShift1_data_new[$machineNo]->avg_rpm = (int)$average_rpm;
            }

            $data[$current_date]['night_shift_part1'] = array_merge($data[$current_date]['night_shift_part1'],$nightShift1_data_new);
            $data[$current_date]['night_shift_part1']['startDateTime'] = $nightShift_DateTime;
            $data[$current_date]['night_shift_part1']['endDateTime'] = $nightShift_DateTime_part1->toDateTimeString();

            // Night shift - Part 2
            $nightShift_2 = "SELECT
                md.*,
                DATE_FORMAT(md.report_date, '%Y-%m-%d') AS reports_date,
                m.machine_name,
                w.first_name AS wrk_first_name,
                w.last_name AS wrk_last_name
            FROM
                `machine_hourly_data` AS md
            JOIN machines AS m
            ON
                m.machine_number = md.machine_number and m.cust_id = md.cust_id
            LEFT JOIN workers AS w
            ON
                w.id = md.worker_id
            WHERE
                md.report_date > '$nightShift_DateTime_part1' AND md.report_date <= '$nightShift_DateTime_part2' AND md.shift = 2 AND $where_condition 
            ORDER BY
                md.id DESC";

            $nightShift2_data = DB::select($nightShift_2);
            $nightShift2_data_new = array();
            $nightShift2_data_rmp = array();

            foreach($nightShift2_data as $shift2_data){
                if(!array_key_exists($shift2_data->machine_number,$nightShift2_data_new)){
                    $nightShift2_data_new[$shift2_data->machine_number] = $shift2_data;
                }
                if(!array_key_exists($shift2_data->machine_number,$nightShift2_data_rmp)){
                    $nightShift2_data_rmp[$shift2_data->machine_number] = [];                    
                }
                $nightShift2_data_rmp[$shift2_data->machine_number][] = $shift2_data->rpm;
                
            }
            ksort($nightShift2_data_new);

            foreach($nightShift2_data_new as $machineNo => $nightShift_obj){
                $nightShift2_data_rmp_filter = array_filter($nightShift2_data_rmp[$machineNo]);
                $average_rpm = 0;
                if(count($nightShift2_data_rmp_filter) > 0){
                    $average_rpm = array_sum($nightShift2_data_rmp_filter)/count($nightShift2_data_rmp_filter);
                }
                $nightShift2_data_new[$machineNo]->avg_rpm = (int)$average_rpm;
            }

            foreach ($nightShift2_data_new as $nightShiftKey2 => $nightShift2) {
                foreach ($nightShift1_data_new as $nightShiftKey1 => $nightShift1) {
                   if($nightShift2->machine_id == $nightShift1->machine_id){
                       $nightShift2_data_new[$nightShiftKey2]->stitches_total = $nightShift2->stitches;
                       $nightShift2_data_new[$nightShiftKey2]->stitches = $nightShift2->stitches - $nightShift1->stitches;
                       $nightShift2_data_new[$nightShiftKey2]->thred_break = $nightShift2->thred_break - $nightShift1->thred_break;
                       $nightShift2_data_new[$nightShiftKey2]->stop_time = $nightShift2->stop_time - $nightShift1->stop_time;
                   }
                }
            }

            if(count($nightShift2_data_new)==0){
                foreach($nightShift1_data_new as $machineNo => $dayShift_obj){
                    $nightShift2_data_new[$machineNo] = clone(object)$dayShift_obj;
                    $nightShift2_data_new[$machineNo]->stitches = 0;
                    $nightShift2_data_new[$machineNo]->thred_break = 0;
                    $nightShift2_data_new[$machineNo]->rpm = 0;
                    $nightShift2_data_new[$machineNo]->max_rpm = 0;
                    $nightShift2_data_new[$machineNo]->stop_time = 0;
                }
            }

            $data[$current_date]['night_shift_part2'] = array_merge($data[$current_date]['night_shift_part2'],$nightShift2_data_new);
            $data[$current_date]['night_shift_part2']['startDateTime'] = $nightShift_DateTime_part1->toDateTimeString();
            $data[$current_date]['night_shift_part2']['endDateTime'] = $nightShift_DateTime_part2->toDateTimeString();

            $data = json_decode(json_encode($data),1);
            
        } 

        $view_mode = $options['view_mode'];
        if ($view_mode=='download') {
            
            $file_name = '6_hrs_production_report_' . time() . '.pdf';
            // $file_name = '5_min_diff_report.pdf';
            $file_path = $this->report_path . $file_name;

            $pdf = PDF::loadView('user.reports.6hour_report', [
                'machine_list' => $machine_list,
                'color_range' => $color_range,  
                'report_data' => $data,
                'column_settings' => $column_settings,
                'day_shift' => $day_shift,
                'night_shift' => $night_shift,
                'from_date' => date("d-m-Y",$from_date),
                'to_date' => date("d-m-Y",$to_date),
                'company_name' => !empty($options['company_name']) ? $options['company_name'] : 'Company Name',
                'view_mode' => 'download',
                'options' => $options,
                'setting' => $this->setting
            ])->setPaper('a4', 'landscape')->setWarnings(false);

            $pdf->save($file_path);

            if (File::exists($file_path)) {
                $result['status'] = 1;
                $result['file_name'] = $file_name;
            }else{
                $result['status'] = 0;
            }

        }else{

            $view = view('user.reports.6hour_report',[
                'machine_list' => $machine_list,
                'report_data' => $data,
                'column_settings' => $column_settings,
                'day_shift' => $day_shift,
                'options' => $options,
                'night_shift' => $night_shift,
                'color_range' => $color_range,
                'from_date' => date('d-m-Y',strtotime($from_date)),
                'to_date' => date('d-m-Y',strtotime($to_date)),
                'setting' => $this->setting
            ]);
            $result['status'] = 1;
            $result['html'] = $view->render();
        }

        return $result;
        
    }

    /**
     * Generate 12 Hours Reports
     *
     * @param Array $mix
     * @return Data
     */
    public function generate_12hr_report($options = [])
    {
        
        ini_set('memory_limit', '-1');
        if(!isset($options['cust_id'])){
            $options['cust_id'] = (!$this->Abettor->isAdmin()) ? auth()->user()->parent_id : Auth::id();
        }
        
        $result = [];
        $column_settings = !empty($options['column_settings']) ? json_decode($options['column_settings'],1) : [];
        $from_date = date('Y-m-d',strtotime($options['from_date']));
        $to_date = date('Y-m-d',strtotime($options['to_date']));
        
        $machine_list = $this->Abettor->get_machines('all',$options['cust_id']);
        $machine_list_string = '"'.implode('","',$machine_list) .'"';
        /* echo "<pre>";
        print_r($machine_list);
        die; */
        $day_shift = date('H:i A',strtotime($options['day_shift']));
        $night_shift = date('h:i A',strtotime($options['night_shift']));
        
        $options['header_data'] = false;
     
        $other_condition = [];
        if ($options['machine_no'] != 'all') {
            $other_condition[] = "md.machine_number='" . $options['machine_no'] . "'";
            $options['header_data'] = true;
        }else{
            (count($machine_list) > 0) ? $other_condition[] = "md.machine_number IN (" . $machine_list_string . ")" : '';
        }

        $color_range = $this->Abettor->get_customer_color_range($options['cust_id']);

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
        

        // Loop through each date
        $total_days = dateDiffInDays($from_date,$to_date);
        $report_data= [];

        for ($i=0; $i <= $total_days; $i++) {

            $where_condition = $other_condition;
            
            $current_date = date("Y-m-d",strtotime("+".$i." day",strtotime($from_date)));
            
            $where_condition[] = "md.cust_id='". $options['cust_id'] ."'";
            $where_condition[] = "md.report_date = '". $current_date . "'";

            $where_condition = implode(" AND ",$where_condition);

            $sql = "SELECT md.*,IFNULL(wb.bonus_amount,0) AS bonus_amount,m.machine_name,w.first_name,w.last_name FROM `machine_12_hour_data` AS md LEFT JOIN machines AS m ON m.machine_id=md.machine_id AND m.cust_id=md.cust_id LEFT JOIN workers AS w ON w.id=md.worker_id LEFT JOIN worker_bonuses AS wb ON wb.cust_id=md.cust_id AND wb.shift=md.shift AND wb.worker_id=md.worker_id AND wb.machine_id=md.machine_id AND wb.bonus_date = md.report_date WHERE ". $where_condition ." AND md.shift=1 ORDER BY LENGTH(md.machine_id) ASC,md.machine_id ASC";
            $report_data[$current_date]['day'] = DB::select($sql);

            $sql = "SELECT md.*,IFNULL(wb.bonus_amount,0) AS bonus_amount,m.machine_name,w.first_name,w.last_name FROM `machine_12_hour_data` AS md LEFT JOIN machines AS m ON m.machine_id=md.machine_id AND m.cust_id=md.cust_id LEFT JOIN workers AS w ON w.id=md.worker_id LEFT JOIN worker_bonuses AS wb ON wb.cust_id=md.cust_id AND wb.shift=md.shift AND wb.worker_id=md.worker_id AND wb.machine_id=md.machine_id AND wb.bonus_date = md.report_date WHERE ". $where_condition ." AND md.shift=2 ORDER BY LENGTH(md.machine_id) ASC,md.machine_id ASC";
            $report_data[$current_date]['night'] = DB::select($sql);

        }

        $report_data = json_decode(json_encode($report_data),1);
        /* echo "<pre>";
        print_r($options);
        die; */
        $view_mode = $options['view_mode'];
        if ($view_mode=='download') {
            
            $file_name = '12_hrs_production_report_' . time() . '.pdf';
            // $file_name = '5_min_diff_report.pdf';
            $file_path = $this->report_path . $file_name;
            
            $pdf = PDF::loadView('user.reports.12hour_report', [
                'machine_list' => $machine_list,
                'report_data' => $report_data,
                'column_settings' => $column_settings,
                'day_shift' => $day_shift,
                'night_shift' => $night_shift,
                'options' => $options,
                'company_name' => !empty($options['company_name']) ? $options['company_name'] : 'Company Name',
                'from_date' => date('d-m-Y',strtotime($from_date)),
                'to_date' => date('d-m-Y',strtotime($to_date)),
                'color_range' => $color_range,
                'view_mode' => 'download',
                'setting' => $this->setting
            ])->setPaper('a4', 'landscape')->setWarnings(false);

            $pdf->save($file_path);

            if (File::exists($file_path)) {
                $result['status'] = 1;
                $result['file_name'] = $file_name;
            }else{
                $result['status'] = 0;
            }

        }else{
            $view = view('user.reports.12hour_report',[
                'machine_list' => $machine_list,
                'report_data' => $report_data,
                'column_settings' => $column_settings,
                'day_shift' => $day_shift,
                'options' => $options,
                'night_shift' => $night_shift,
                'color_range' => $color_range,
                'from_date' => date('d-m-Y',strtotime($from_date)),
                'to_date' => date('d-m-Y',strtotime($to_date)),
                'setting' => $this->setting
            ]);
            $result['status'] = 1;
            $result['html'] = $view->render();
        }
        // echo $view->render();exit;
        return $result;
        
    }

    public function average(Request $request){

        $report_data = [];
        $report_html = 'Please select criteria';

        $reset = $request->input('reset');
        if (!empty($reset)) {
            $request->session()->put('report.average.search','');
            return redirect(route('reports.average'));
        }elseif ($request->_token) {
            $request->session()->put('report.average.search',$request->all());
            $search = $request->session()->get('report.average.search');
        }else{
            $search = $request->session()->get('report.average.search');
        }
        $cust_id = Auth::id();
        // $search['from_date'] = !empty($search['from_date']) ? $search['from_date'] : date('d-m-Y');
        if (!empty($search)) {
            $report_type = $search['report_types'];

            $options = $this->generate_options($search,$cust_id);

            if ($report_type=='machines') {                
                $report_data = $this->generate_machines_avg_report($options);
                if ($report_data['status']==1) {
                    $report_html = $report_data['html'];
                }
            }elseif ($report_type=='workers') {
                $report_data = $this->generate_worker_avg_report($options);
                if ($report_data['status']==1) {
                    $report_html = $report_data['html'];
                }
            }
        }

        $machine_list = $this->get_machines(true);
        $group_list = $this->get_groups();
        $worker_list = $this->get_workers(true);

        return view('user.reports.average',[
            'machine_list' => $machine_list,
            'worker_list' => $worker_list,
            'group_list' => $group_list,
            'machines' => [],
            'search' => $search,
            'report_html' => $report_html,
        ]);
    }

    public function average_weekly(Request $request){

        $report_data = [];
        $report_html = 'Please select criteria';

        $reset = $request->input('reset');
        if (!empty($reset)) {
            $request->session()->put('report.average_weekly.search','');
            return redirect(route('reports.average_weekly'));
        }elseif ($request->_token) {
            $request->session()->put('report.average_weekly.search',$request->all());
            $search = $request->session()->get('report.average_weekly.search');
        }else{
            $search = $request->session()->get('report.average_weekly.search');
        }
        $cust_id = Auth::id();
                
        if (!empty($search)) {
            $options = $this->generate_options($search,$cust_id);
            $report_data = $this->generate_avg_week_report($options);
            if ($report_data['status']==1) {
                $report_html = $report_data['html'];
            }
        }

        $machine_list = $this->Abettor->get_machines('all',Auth::id());
        $group_list = $this->get_groups();
        $worker_list = $this->get_workers(true);

        return view('user.reports.average_weekly',[
            'machine_list' => $machine_list,
            'worker_list' => $worker_list,
            'group_list' => $group_list,
            'search' => $search,
            'report_html' => $report_html
        ]);
    }

    public function average_export(Request $request)
    {
        $reset = $request->input('reset');
        if (!empty($reset)) {
            $request->session()->put('report.average.search','');
            return redirect(route('reports.average'));
        }elseif ($request->_token) {
            $request->session()->put('report.average.search',$request->all());
            $search = $request->session()->get('report.average.search');
        }else{
            $search = $request->session()->get('report.average.search');
        }

        
        if (!empty($search)) {

            $options = $this->generate_options($search,Auth::id());
            $report_type = $search['report_types'];
            $options['view_mode'] = 'download';

           if ($report_type=='machines') {
                $report_data = $this->generate_machines_avg_report($options);
            }elseif ($report_type=='workers') {
                $report_data = $this->generate_worker_avg_report($options);
            }

            if ($report_data['status']==1) {
                $report_url = generate_report_url($report_data['file_name']);
                return Redirect::to($report_url);
            }else{
                return redirect(route('reports.average'))->withError('Unable to generate PDF, please try again later.');
            }
                


        }else{
            return redirect(route('reports.average'))->withError('Please select criteria.');
        }
    }

    public function average_weekly_export(Request $request)
    {
        $report_data = [];
        $report_html = 'Please select criteria';

        $reset = $request->input('reset');
        if (!empty($reset)) {
            $request->session()->put('report.average_weekly.search','');
            return redirect(route('reports.average_weekly'));
        }elseif ($request->_token) {
            $request->session()->put('report.average_weekly.search',$request->all());
            $search = $request->session()->get('report.average_weekly.search');
        }else{
            $search = $request->session()->get('report.average_weekly.search');
        }
        $cust_id = Auth::id();
                
        if (!empty($search)) {
            $options = $this->generate_options($search,$cust_id);
            $options['view_mode'] = 'download';
            $report_data = $this->generate_avg_week_report($options);

            if ($report_data['status']==1) {
                $report_url = generate_report_url($report_data['file_name']);
                return Redirect::to($report_url);
            }
        }else{
            return redirect(route('reports.average_weekly'))->withError('Please select criteria.');
        }
    }

    /**
     * Generate Machine Average Report
     *
     * @param Array Mix
     * @return report
     */
    public function generate_machines_avg_report($options = [])
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
            (count($machine_list) > 0) ? $other_condition[] = "md.machine_number IN (" . $machine_list_string . ")" : '';            
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
        worker.machine_name,
        worker.machine_id,
        worker.machine_number,
        worker.cust_id,
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
                m.machine_id,
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
            worker.machine_number,worker.shift
        ORDER BY
            LENGTH(worker.machine_id) ASC,
            worker.machine_id ASC";

        $data = DB::select($sql);
        $data = json_decode(json_encode($data),1);

        $view_mode = $options['view_mode'];
        if ($view_mode=='download') {
            
            $file_name = 'machine_average_report_' . time() . '.pdf';
            $file_path = $this->report_path . $file_name;

            $pdf = PDF::loadView('user.reports.machine_average', [
                'report_data' => $data,
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
            $view = view('user.reports.machine_average',[
                'report_data' => $data,
                'column_settings' => $column_settings,
                'day_shift' => $day_shift,
                'night_shift' => $night_shift,
                'setting' => $this->setting
            ]);
            $result['status'] = 1;
            $result['html'] = $view->render();
        }
        // echo $view->render();exit;
        return $result;
    }

    public function generate_avg_week_report($options = [])
    {

        ini_set('memory_limit', '-1');

        $result = [];
        $options['cust_id'] = (!$this->Abettor->isAdmin()) ? auth()->user()->parent_id : Auth::id();
        $column_settings = !empty($options['column_settings']) ? json_decode($options['column_settings'],1) : [];
        $other_condition = [];
        $machine_list = $this->Abettor->get_machines('all',$options['cust_id']);
        $machine_list_string = '"'.implode('","',$machine_list) .'"';
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
            $data[$machine_id]['avg'] = array('1' => 0,'2' => 0);
            for ($i=0; $i <= 6; $i++) { 
                $current_date = Carbon::parse($from_date)->addDays($i)->toDateString();
                $productionSql = "SELECT machine_12_hour_data.shift,machine_12_hour_data.stitches from machine_12_hour_data JOIN machines ON machines.machine_number = machine_12_hour_data.machine_number AND machines.cust_id=machine_12_hour_data.cust_id where $where_condition AND machine_12_hour_data.machine_number = '$machine_id' AND report_date = '$current_date' order by machine_12_hour_data.shift";
                $productionData = DB::select($productionSql);
                $productionData = json_decode(json_encode($productionData),1);

                $data[$machine_id][$current_date] = array('1' => 0,'2' => 0);

                foreach($productionData as $productionKey => $productionValue){
                    $data[$machine_id][$current_date][$productionValue['shift']] = $productionValue['stitches'];
                    $data[$machine_id]['avg'][$productionValue['shift']] += $productionValue['stitches'];
                }               
            }
        }  

        $view_mode = $options['view_mode'];
        if ($view_mode=='download') {
            
            $file_name = 'machine_average_weekly_report_' . time() . '.pdf';
            $file_path = $this->report_path . $file_name;

            $pdf = PDF::loadView('user.reports.machine_average_weekly', [
                'report_data' => $data,
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
            
            $view = view('user.reports.machine_average_weekly',[
                'report_data' => $data,
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

    /**
     * Generate Machine Average Report
     *
     * @param Array Mix
     * @return report
     */
    public function generate_worker_avg_report($options = [])
    {

        ini_set('memory_limit', '-1');

        $result = [];
        $options['cust_id'] = (!$this->Abettor->isAdmin()) ? auth()->user()->parent_id : Auth::id();
        $column_settings = !empty($options['column_settings']) ? json_decode($options['column_settings'],1) : [];
        $machine_list = $this->Abettor->get_machines('all',$options['cust_id']);
        $machine_list_string = '"'.implode('","',$machine_list) .'"';
        $from_date = date('Y-m-d',strtotime($options['from_date']));
        $to_date = date('Y-m-d',strtotime($options['to_date']));
        
        $day_shift = date('H:i A',strtotime($options['day_shift']));
        $night_shift = date('h:i A',strtotime($options['night_shift']));

        $options['header_data'] = false;
     
        $other_condition = [];
        if ($options['machine_no'] != 'all') {
            $other_condition[] = "md.machine_number='" . $options['machine_no'] . "'";
            $options['header_data'] = true;
        }else{
            (count($machine_list) > 0) ? $other_condition[] = "md.machine_number IN (" . $machine_list_string . ")" : '';            
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

        // Worker Wise report        
        $sql = "SELECT
        worker.worker_id,
        worker.machine_id,
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
                md.machine_id,
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
            worker.worker_id
        ORDER BY
            LENGTH(worker.machine_id) ASC,
            worker.machine_id ASC";

        $data = DB::select($sql);
        $data = json_decode(json_encode($data),1);

        $view_mode = $options['view_mode'];
        if ($view_mode=='download') {
            
            $file_name = 'worker_average_report_' . time() . '.pdf';
            $file_path = $this->report_path . $file_name;

            $pdf = PDF::loadView('user.reports.worker_average', [
                'report_data' => $data,
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
            $view = view('user.reports.worker_average',[
                'report_data' => $data,
                'column_settings' => $column_settings,
                'day_shift' => $day_shift,
                'night_shift' => $night_shift,
                'setting' => $this->setting
            ]);
            $result['status'] = 1;
            $result['html'] = $view->render();
        }
        // echo $view->render();exit;
        return $result;
    }

    public function worker_total(Request $request)
    {
        $report_data = [];
        $report_html = 'Please select criteria';

        $reset = $request->input('reset');
        if (!empty($reset)) {
            $request->session()->put('report.worker_total.search','');
            return redirect(route('reports.worker.total'));
        }elseif ($request->_token) {
            $request->session()->put('report.worker_total.search',$request->all());
            $search = $request->session()->get('report.worker_total.search');
        }else{
            $search = $request->session()->get('report.worker_total.search');
        }
        $cust_id = Auth::id();
        
        if (!empty($search)) {

            $options = $this->generate_options($search,$cust_id);

            $report_data = $this->generate_worker_total_report($options);
            if ($report_data['status']==1) {
                $report_html = $report_data['html'];
            }
            
        }

        $machine_list = $this->get_machines(true);
        $group_list = $this->get_groups();
        $worker_list = $this->get_workers(true);

        return view('user.reports.worker_total',[
            'machine_list' => $machine_list,
            'worker_list' => $worker_list,
            'group_list' => $group_list,
            'search' => $search,
            'report_html' => $report_html,
        ]);
    }

    public function generate_worker_total_report($options = [])
    {
        ini_set('memory_limit', '-1');

        $result = [];      
        $options['cust_id'] = (!$this->Abettor->isAdmin()) ? auth()->user()->parent_id : Auth::id();  
        $column_settings = !empty($options['column_settings']) ? json_decode($options['column_settings'],1) : [];
        $machine_list = $this->Abettor->get_machines('all',$options['cust_id']);
        $machine_list_string = '"'.implode('","',$machine_list) .'"';
        $from_date = date('Y-m-d',strtotime($options['from_date']));
        $to_date = date('Y-m-d',strtotime($options['to_date']));  
        
        $day_shift = date('H:i A',strtotime($options['day_shift']));
        $night_shift = date('h:i A',strtotime($options['night_shift']));

        $options['header_data'] = false;
     
        $other_condition = [];
        if ($options['machine_no'] != 'all') {
            $other_condition[] = "md.machine_number='" . $options['machine_no'] . "'";
            $options['header_data'] = true;
        }else{
            (count($machine_list) > 0) ? $other_condition[] = "md.machine_number IN (" . $machine_list_string . ")" : '';            
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

        // Worker Wise report
        $sql = "SELECT
        worker.worker_id,
        worker.first_name,
        worker.last_name,
        worker.contact_number_1,
        worker.cust_id,
        worker.machine_number,
        worker.shift,
        SUM(worker.stitches) AS stitches,
        SUM(worker.thred_break) AS thred_break,
        SUM(worker.stop_time) AS stop_time,
        IFNULL(
            SUM(worker.bonus_amount),
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
                md.thred_break,
                md.stop_time,
                wb.bonus_amount,
                (case when md.shift = 1 then 'Day' when md.shift = 2 then 'Night' end) AS shift
            FROM
                `machine_12_hour_data` AS md
            LEFT JOIN workers AS w
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
            worker.worker_id
        ORDER BY
            worker.machine_number";

        $data = DB::select($sql);
        $data = json_decode(json_encode($data),1); 

        $view_mode = $options['view_mode'];
        if ($view_mode=='download') {
            
            $file_name = 'worker_total_report_' . time() . '.pdf';
            $file_path = $this->report_path . $file_name;

            $pdf = PDF::loadView('user.reports.worker_total_report', [
                'report_data' => $data,
                'column_settings' => $column_settings,
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
            $view = view('user.reports.worker_total_report',[
                'report_data' => $data,
                'column_settings' => $column_settings,
                'setting' => $this->setting
            ]);
            $result['status'] = 1;
            $result['html'] = $view->render();
        }
        // echo $view->render();exit;
        return $result;
    }

    public function worker_total_export(Request $request)
    {
        $reset = $request->input('reset');
        if (!empty($reset)) {
            $request->session()->put('report.worker_total.search','');
            return redirect(route('reports.worker.total'));
        }elseif ($request->_token) {
            $request->session()->put('report.worker_total.search',$request->all());
            $search = $request->session()->get('report.worker_total.search');
        }else{
            $search = $request->session()->get('report.worker_total.search');
        }

        if (!empty($search)) {

            $options = $this->generate_options($search,Auth::id());
            $options['view_mode'] = 'download';

            $report_data = $this->generate_worker_total_report($options);

            if ($report_data['status']==1) {
                $report_url = generate_report_url($report_data['file_name']);
                return Redirect::to($report_url);
            }else{
                return redirect(route('reports.worker.total.export'))->withError('Unable to generate PDF, please try again later.');
            }

        }else{
            return redirect(route('reports.worker.total.export'))->withError('Please select criteria.');
        }
    }
    
    public function worker_salary(Request $request){

        $report_data = [];
        $report_html = 'Please select criteria';

        $reset = $request->input('reset');
        if (!empty($reset)) {
            $request->session()->put('report.worker_salary.search','');
            return redirect(route('reports.worker.salary'));
        }elseif ($request->_token) {
            $request->session()->put('report.worker_salary.search',$request->all());
            $search = $request->session()->get('report.worker_salary.search');
        }else{
            $search = $request->session()->get('report.worker_salary.search');
        }
        $cust_id = Auth::id();
        
        if (!empty($search)) {
            $options = $this->generate_options($search,$cust_id);
            if ($options['worker_list'] != 'all') {
                $report_data = $this->generate_single_worker_salary_report($options);
            }else{
                $report_data = $this->generate_worker_salary_report($options);
            }            
            if ($report_data['status']==1) {
                $report_html = $report_data['html'];
            }
        }

        $machine_list = $this->get_machines(true);
        $group_list = $this->get_groups();
        $worker_list = $this->get_workers(true);

        return view('user.reports.worker_salary',[
            'machines' => [],
            'search' => $search,
            'report_html' => $report_html,
            'machine_list' => $machine_list,
            'worker_list' => $worker_list,
            'group_list' => $group_list
        ]);
    }

    public function worker_salary_export(Request $request)
    {
        $reset = $request->input('reset');
        if (!empty($reset)) {
            $request->session()->put('report.worker_salary.search','');
            return redirect(route('reports.worker.salary'));
        }elseif ($request->_token) {
            $request->session()->put('report.worker_salary.search',$request->all());
            $search = $request->session()->get('report.worker_salary.search');
        }else{
            $search = $request->session()->get('report.worker_salary.search');
        }

        if (!empty($search)) {

            $options = $this->generate_options($search,Auth::id());
            $options['view_mode'] = 'download';
            if ($options['worker_list'] != 'all') {
                $report_data = $this->generate_single_worker_salary_report($options);
            }else{
                $report_data = $this->generate_worker_salary_report($options);
            }

            if ($report_data['status']==1) {
                $report_url = generate_report_url($report_data['file_name']);
                return Redirect::to($report_url);
            }else{
                return redirect(route('reports.worker.salary'))->withError('Unable to generate PDF, please try again later.');
            }

        }else{
            return redirect(route('reports.worker.salary'))->withError('Please select criteria.');
        }
    }

    /**
     * Generate Worker Salary Report
     *
     * @param Array Mix
     * @return report
     */
    public function generate_worker_salary_report($options = [])
    {

        ini_set('memory_limit', '-1');

        $result = [];
        $options['cust_id'] = (!$this->Abettor->isAdmin()) ? auth()->user()->parent_id : Auth::id();
        $column_settings = !empty($options['column_settings']) ? json_decode($options['column_settings'],1) : [];
        $machine_list = $this->Abettor->get_machines('all',$options['cust_id']);
        $machine_list_string = '"'.implode('","',$machine_list) .'"';
        $from_date = date('Y-m-d',strtotime($options['from_date']));
        $to_date = date('Y-m-d',strtotime($options['to_date']));        
        
        $day_shift = date('H:i A',strtotime($options['day_shift']));
        $night_shift = date('h:i A',strtotime($options['night_shift']));

        $options['header_data'] = false;
     
        $other_condition = [];
        if ($options['machine_no'] != 'all') {
            $other_condition[] = "md.machine_number='" . $options['machine_no'] . "'";
            $options['header_data'] = true;
        }else{
            (count($machine_list) > 0) ? $other_condition[] = "md.machine_number IN (" . $machine_list_string . ")" : '';
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

        // Worker 12 Hours report
        $sql = "SELECT
        COUNT(worker.id) AS available_days,
        worker.worker_id,
        worker.first_name,
        worker.last_name,
        worker.salary,
        worker.contact_number_1,
        worker.cust_id,
        worker.machine_number,
        worker.shift,
        worker.report_date,
        ROUND(AVG(NULLIF(worker.stitches, 0))) avg_stitches,
        SUM(worker.stitches) AS stitches,
        SUM(worker.thred_break) AS thred_break,
        SUM(worker.stop_time) AS stop_time,
        IFNULL(
            SUM(worker.bonus_amount),
            0
        ) AS worker_bonus
        FROM
            (
            SELECT
                w.worker_id,
                w.first_name,
                w.last_name,
                w.salary,
                w.contact_number_1,
                md.id,
                md.report_date,
                md.cust_id,
                md.machine_number,
                md.stitches,
                md.thred_break,
                md.stop_time,
                wb.bonus_amount,
                (case when md.shift = 1 then 'Day' when md.shift = 2 then 'Night' end) AS shift
            FROM
                `machine_12_hour_data` AS md
            LEFT JOIN workers AS w
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
            worker.worker_id
        ORDER BY
            worker.machine_number";        

        // Add Salary to Worker 12 Hour report
        
        $data = DB::select($sql);
        $data = json_decode(json_encode($data),1);

        foreach ($data as $key => $w_data) {
            $total_days = date('t',strtotime($w_data['report_date']));
            $worker_salary = $w_data['salary'];
            $available_days = $w_data['available_days'];
            $working_salary = round(($worker_salary/ $total_days) * $available_days);
            $total_salary = $working_salary + $w_data['worker_bonus'];
            $data[$key]['working_salary'] = $working_salary;
            $data[$key]['total_salary'] = $total_salary;
        }

        $view_mode = $options['view_mode'];
        if ($view_mode=='download') {
            
            $file_name = 'worker_salary_report_' . time() . '.pdf';
            $file_path = $this->report_path . $file_name;

            $pdf = PDF::loadView('user.reports.worker_salary_report', [
                'report_data' => $data,
                'column_settings' => $column_settings,
                'day_shift' => $day_shift,
                'night_shift' => $night_shift,
                'company_name' => !empty($options['company_name']) ? $options['company_name'] : 'Company Name',
                'from_date' => date("d-m-Y",strtotime($from_date)),
                'to_date' => date("d-m-Y",strtotime($to_date)),
                'view_mode' => 'download',
                'setting' => $this->setting
            ])->setPaper('a4', 'landscape')->setWarnings(false);

            $pdf->save($file_path);

            if (File::exists($file_path)) {
                $result['status'] = 1;
                $result['file_name'] = $file_name;
            }else{
                $result['status'] = 0;
            }

        }else{
            $view = view('user.reports.worker_salary_report',[
                'report_data' => $data,
                'column_settings' => $column_settings,
                'day_shift' => $day_shift,
                'night_shift' => $night_shift,
                'from_date' => date("d-m-Y",strtotime($from_date)),
                'to_date' => date("d-m-Y",strtotime($to_date)),
                'setting' => $this->setting,
                'company_name' => !empty($options['company_name']) ? $options['company_name'] : 'Company Name',
            ]);
            $result['status'] = 1;
            $result['html'] = $view->render();
        }
        // echo $view->render();exit;
        return $result;
    }    

    public function generate_single_worker_salary_report($options = [])
    {

        ini_set('memory_limit', '-1');

        $result = [];
        $options['cust_id'] = (!$this->Abettor->isAdmin()) ? auth()->user()->parent_id : Auth::id();
        $column_settings = !empty($options['column_settings']) ? json_decode($options['column_settings'],1) : [];
        $machine_list = $this->Abettor->get_machines('all',$options['cust_id']);

        $from_date = date('Y-m-d',strtotime($options['from_date']));
        $to_date = date('Y-m-d',strtotime($options['to_date']));        
        
        $day_shift = date('H:i A',strtotime($options['day_shift']));
        $night_shift = date('h:i A',strtotime($options['night_shift']));

        $options['header_data'] = false;
     
        $other_condition = [];   

        if ($options['worker_list'] != 'all') {
            $other_condition[] = "md.worker_id='" . $options['worker_list'] . "'";
            $options['header_data'] = true;
            $options['worker_name'] = $this->Abettor->get_worker_fullname_by_id($options['worker_list']);
        }

        $other_condition[] = "md.cust_id='". $options['cust_id'] ."'";
        $where_condition = implode(" AND ",$other_condition);

        // Worker 12 Hours report
        $sql = "SELECT
            md.report_date,
            w.first_name,
            md.shift,
            md.stitches,
            wb.bonus_amount,
            w.salary,
            (case when md.shift = 1 then 'Day' when md.shift = 2 then 'Night' end) AS shiftname
            FROM
                `machine_12_hour_data` AS md
            LEFT JOIN workers AS w
            ON
                w.id = md.worker_id AND w.cust_id = md.cust_id
            LEFT JOIN worker_bonuses AS wb
            ON
                wb.cust_id = md.cust_id AND wb.shift = md.shift AND wb.worker_id = md.worker_id AND wb.machine_number = md.machine_number AND wb.bonus_date = md.report_date
            WHERE
                md.report_date >= '$from_date' AND md.report_date <= '$to_date' AND $where_condition 
            GROUP BY report_date,shift";
        
        // Add Salary to Worker 12 Hour report
        
        $data = DB::select($sql);
        $data = json_decode(json_encode($data),1);

        foreach ($data as $key => $w_data) {
            $total_days = date('t',strtotime($w_data['report_date']));
            $worker_salary = $w_data['salary'];
            $working_salary = round($worker_salary/ $total_days);
            $total_salary = $working_salary + $w_data['bonus_amount'];
            $data[$key]['working_salary'] = $working_salary;
            $data[$key]['total_salary'] = $total_salary;
        }
        
        $view_mode = $options['view_mode'];
        if ($view_mode=='download') {
            
            $file_name = 'single_worker_salary_report_' . time() . '.pdf';
            $file_path = $this->report_path . $file_name;

            $pdf = PDF::loadView('user.reports.single_worker_salary_report', [
                'report_data' => $data,
                'column_settings' => $column_settings,
                'day_shift' => $day_shift,
                'night_shift' => $night_shift,
                'company_name' => !empty($options['company_name']) ? $options['company_name'] : 'Company Name',
                'from_date' => date("d-m-Y",strtotime($from_date)),
                'to_date' => date("d-m-Y",strtotime($to_date)),
                'view_mode' => 'download',
                'setting' => $this->setting
            ])->setPaper('a4', 'landscape')->setWarnings(false);

            $pdf->save($file_path);

            if (File::exists($file_path)) {
                $result['status'] = 1;
                $result['file_name'] = $file_name;
            }else{
                $result['status'] = 0;
            }

        }else{
            $view = view('user.reports.single_worker_salary_report',[
                'report_data' => $data,
                'column_settings' => $column_settings,
                'day_shift' => $day_shift,
                'night_shift' => $night_shift,
                'from_date' => date("d-m-Y",strtotime($from_date)),
                'to_date' => date("d-m-Y",strtotime($to_date)),
                'setting' => $this->setting,
                'company_name' => !empty($options['company_name']) ? $options['company_name'] : 'Company Name',
            ]);
            $result['status'] = 1;
            $result['html'] = $view->render();
        }
        // echo $view->render();exit;
        return $result;
    }      

}
