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
use App\Models\FixedBonus;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use App\Helpers\AbettorHelper;

use Validator;
use Auth;
use PDF;
use DB;
use File;
use Carbon\Carbon;

class ReadingController extends Controller
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
        $report_data = [];
        $report_html = 'Please select criteria';
        $machine_list = $this->Abettor->get_machines('all',Auth::id());

        $reset = $request->input('reset');
        if (!empty($reset)) {
            $request->session()->put('reading.production.search','');
            return redirect(route('reading.production'));
        }elseif ($request->_token) {
            $request->session()->put('reading.production.search',$request->all());
            $search = $request->session()->get('reading.production.search');
        }else{
            $search = $request->session()->get('reading.production.search');
        }

        if (!empty($search)) {
            $options = $this->generate_options($search,Auth::id());
            $report_data = $this->set_reading($options);
            if ($report_data['status']==1) {
                $report_html = $report_data['html'];
            }
        }
        return view('user.reading.production',[
            'machine_list' => $machine_list,
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

        $options = [
            'reading_date' => !empty($search['reading_date']) ? $search['reading_date'] : '',
            'day_shift' => $customer->day_shift,
            'night_shift' => $customer->night_shift,
            'company_name' => $customer->company_name,
            'cust_id' => $customer->id,
            'column_settings' => $customer->reports_settings,
            'view_mode' => 'view',
            'setting' => $this->setting
        ];
            
        return $options;
    }  

    public function set_reading($options = [])
    {
        
        ini_set('memory_limit', '-1');
        if(!isset($options['cust_id'])){
            $options['cust_id'] = (!$this->Abettor->isAdmin()) ? auth()->user()->parent_id : Auth::id();
        }
        
        $result = [];
        $column_settings = !empty($options['column_settings']) ? json_decode($options['column_settings'],1) : [];
        $reading_date = date('Y-m-d',strtotime($options['reading_date']));
        
        $machine_list = $this->Abettor->get_machines('all',$options['cust_id']);
        $machine_list_string = '"'.implode('","',$machine_list) .'"';

        $color_range = $this->Abettor->get_customer_color_range($options['cust_id']);
        $day_shift = date('H:i A',strtotime($options['day_shift']));
        $night_shift = date('h:i A',strtotime($options['night_shift']));
        
        $options['header_data'] = false;
        $other_condition = [];
        (count($machine_list) > 0) ? $other_condition[] = "md.machine_number IN (" . $machine_list_string . ")" : '';

        $report_data= [];
        $where_condition = $other_condition;           
        $where_condition[] = "md.cust_id='". $options['cust_id'] ."'";
        $where_condition[] = "md.report_date = '". $reading_date . "'";
        $where_condition = implode(" AND ",$where_condition);

        $sql = "SELECT
                md.*,
                IFNULL(wb.bonus_amount, 0) AS bonus_amount,
                m.machine_name,
                w.first_name,
                w.last_name
            FROM
                `machine_12_hour_data` AS md
            LEFT JOIN machines AS m
            ON
                m.machine_id = md.machine_id AND m.cust_id = md.cust_id
            LEFT JOIN workers AS w
            ON
                w.id = md.worker_id
            LEFT JOIN worker_bonuses AS wb
            ON
                wb.cust_id = md.cust_id AND wb.shift = md.shift AND wb.worker_id = md.worker_id AND wb.machine_id = md.machine_id AND wb.bonus_date = md.report_date
            WHERE
                ". $where_condition ." AND md.shift = 1
            ORDER BY
                LENGTH(md.machine_id) ASC,
                md.machine_id ASC";

        $report_data['day'] = DB::select($sql);

        $sql = "SELECT
                md.*,
                IFNULL(wb.bonus_amount, 0) AS bonus_amount,
                m.machine_name,
                w.first_name,
                w.last_name
            FROM
                `machine_12_hour_data` AS md
            LEFT JOIN machines AS m
            ON
                m.machine_id = md.machine_id AND m.cust_id = md.cust_id
            LEFT JOIN workers AS w
            ON
                w.id = md.worker_id
            LEFT JOIN worker_bonuses AS wb
            ON
                wb.cust_id = md.cust_id AND wb.shift = md.shift AND wb.worker_id = md.worker_id AND wb.machine_id = md.machine_id AND wb.bonus_date = md.report_date
            WHERE
                ". $where_condition ." AND md.shift = 2
            ORDER BY
                LENGTH(md.machine_id) ASC,
                md.machine_id ASC";

        $report_data['night'] = DB::select($sql);
        $report_data = json_decode(json_encode($report_data),1);
        
        $view = view('user.reading.manual_reading',[
            'machine_list' => $machine_list,
            'report_data' => $report_data,
            'column_settings' => $column_settings,
            'day_shift' => $day_shift,
            'night_shift' => $night_shift,
            'options' => $options,
            'color_range' => $color_range,
            'reading_date' => date('d-m-Y',strtotime($reading_date)),
            'setting' => $this->setting
        ]);
        $result['status'] = 1;
        $result['html'] = $view->render();
        return $result;        
    }

    public function updateReading(Request $request){
        $readingDate = $request->reading_date;
        $readingData = $request->readingData;
        $cust_id = Auth::id();
        $customer = User::find($cust_id);

        if(count($readingData) >0){
            foreach($readingData as $shift => $readingData){
                foreach($readingData as $machine_number => $stitches){
                    $machine = Machine::where('cust_id',$cust_id)->where('machine_number',$machine_number)->first();
                    $machineWhere = array(
                        'cust_id' => $cust_id,
                        'machine_id' => $machine->machine_id,
                        'shift' => $shift,
                        'report_date' => date('Y-m-d',strtotime($readingDate))
                    );
                    
                    $machineDataExist = Machine12HourData::where($machineWhere)->first();
                    $reportDate = date('Y-m-d',strtotime($readingDate));
                    $shiftTime = ($shift == 1)?$customer->day_shift:$customer->night_shift;
                    $createdAt = "$reportDate $shiftTime";
                    $workerId = ($shift == 1)?$machine->day_worker_id:$machine->night_worker_id;

                    if(!$machineDataExist){
                        $Machine12HourData = new Machine12HourData();
                        $Machine12HourData->cust_id = $cust_id;
                        $Machine12HourData->machine_id = $machine->machine_id;
                        $Machine12HourData->machine_number = $machine->machine_number;
                        $Machine12HourData->shift = $shift;
                        $Machine12HourData->stitches = $stitches;
                        $Machine12HourData->thred_break = 0;
                        $Machine12HourData->rpm = 0;
                        $Machine12HourData->max_rpm = 0;
                        $Machine12HourData->stop_time = 0;
                        $Machine12HourData->worker_id = $workerId;
                        $Machine12HourData->working_head = $machine->working_head;
                        $Machine12HourData->report_date = $reportDate;
                        $Machine12HourData->created_at = $createdAt;
                        $Machine12HourData->edited = 1;
                        $Machine12HourData->save();
                    }else{
                        $Machine12HourData = Machine12HourData::findOrFail($machineDataExist->id);
                        if($Machine12HourData->edited == 0 && $Machine12HourData->stitches != $stitches){
                            $Machine12HourData->edited = 1;
                        }
                        $Machine12HourData->stitches = $stitches;
                        $Machine12HourData->save();
                    }

                    $FixedBonus = FixedBonus::where('cust_id',$cust_id)->where('machine_id','LIKE','%"'.$machine->machine_id.'"%')->first();
                    if(isset($FixedBonus->id)){
                        $bonus_type = $FixedBonus->bonus_type;
                        $min_stitches = $FixedBonus->min_stitches;
                        $min_stitches_bonus = $FixedBonus->min_stitches_bonus;
                        $after_min_per_stitches = $FixedBonus->after_min_per_stitches;
                        $after_min_per_stitches_bonus = $FixedBonus->after_min_per_stitches_bonus;
                        $stitches_range = $FixedBonus->stitches_range;
                        $bonus_amount = 0;
                        // Bonus calculation based on Type
                        if ($bonus_type=='fixed') {

                            // Check if Stitches if more then minimum stitches
                            if ($stitches >= $min_stitches) {
                                $bonus_amount += $min_stitches_bonus;
                                
                                // Check next stage bonus
                                if (($stitches-$min_stitches)>0) {
                                    $extra_bonus = floor(($stitches-$min_stitches) / $after_min_per_stitches) * $after_min_per_stitches_bonus;
                                    $bonus_amount += $extra_bonus;
                                }
                            }

                        }else if ($bonus_type=='range_wise') {
                            if(!empty($stitches_range)){
                                $stitches_range = json_decode($stitches_range);                            
                                foreach($stitches_range as $range){
                                    if (($stitches >= $range->from_stitches) && ($stitches <= $range->to_stitches)) {
                                        $bonus_amount = $range->bonus_amount;
                                        break;
                                    }
                                }
                            }
                        }

                        if(!empty($workerId)){
                            $bonusWhere = array(
                                'cust_id' => $cust_id,
                                'worker_id' => $workerId,
                                'machine_id' => $machine->machine_id,
                                'shift' => $shift,
                                'bonus_date' => $reportDate
                            );
                            $checkBonus = WorkerBonus::where($bonusWhere)->first();
    
                            if(!$checkBonus){
                                $WorkerBonus = new WorkerBonus();
                            }else{
                                $WorkerBonus = WorkerBonus::findOrFail($checkBonus->id);
                            }
                            
                            $WorkerBonus->cust_id = $cust_id;
                            $WorkerBonus->worker_id = $workerId;
                            $WorkerBonus->machine_id = $machine->machine_id;
                            $WorkerBonus->machine_number = $machine->machine_number;
                            $WorkerBonus->shift = $shift;
                            $WorkerBonus->stitches = $stitches;
                            $WorkerBonus->bonus_amount = $bonus_amount;
                            $WorkerBonus->bonus_date = $reportDate;
                            $WorkerBonus->created_at = $createdAt;
                            $WorkerBonus->save();
                        }
                    }
                }                
            }
        }
        return redirect('/reading/production')->withSuccess('Stitches updated successfully.');
    }
}
