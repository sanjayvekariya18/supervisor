<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Controllers\User\ReportsController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Helpers\AbettorHelper;
use App\Models\User;
use App\Models\Worker;
use App\Models\Machine;

use Validator;
use Auth;

class ReportAPIController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    var $Abettor;

    public function __construct(AbettorHelper $AbettorHelper)
    {
        $this->Abettor = $AbettorHelper;
    }

    /**
     * Production Reports
     *
     * @param 
     * @return 
     */
    /* public function production(Request $request, ReportsController $ReportsController)
    {
        
        $result = [
            'messages'=>'Method Not Allowed',
            'status'=>false
        ];

        if ($request->isMethod('post')) {
            
            $report_type = !empty($request->input('report_type')) ? $request->input('report_type') : null;
            $shift = !empty($request->input('shift')) ? $request->input('shift') : null;
            $machine_no = !empty($request->input('machine_no')) ? $request->input('machine_no') : 0;
            $group_id = !empty($request->input('group_id')) ? $request->input('group_id') : null;
            $worker_list = !empty($request->input('worker_list')) ? $request->input('worker_list') : 'all';
            $from_date = !empty($request->input('from_date')) ? $request->input('from_date') : null;
            $to_date = !empty($request->input('to_date')) ? $request->input('to_date') : null;
            $cust_id = !empty($request->input('cust_id')) ? $request->input('cust_id') : null;

            if (!empty($report_type)) {
                
                $search = [
                    'shift' => $shift,
                    'machine_no' => $machine_no,
                    'worker_list' => $worker_list,
                    'group_id' => $group_id,
                    'from_date' => $from_date,
                    'to_date' => $to_date,
                ];


                $options = $ReportsController->generate_options($search,$cust_id);

                $options['view_mode'] = 'download';
                if ($report_type=='5_min_diff') {
                    $report_data = $ReportsController->generate_5min_report($options);
                }elseif ($report_type=='6_hours') {
                    $report_data = $ReportsController->generate_6hr_report($options);
                }elseif ($report_type=='12_hours') {
                    $report_data = $ReportsController->generate_12hr_report($options);
                }

                if ($report_data['status']==1) {
                    $report_url = generate_report_url($report_data['file_name']);
                    $result = [
                        'messages'=>'Data retrieved successfully',
                        'report_link' => $report_url,
                        'status'=>true
                    ];
                }else{
                    $result = [
                        'messages'=>'Unable to generate PDF, please try again later.',
                        'status'=>false
                    ];
                }


            }else{
                $result = [
                    'messages'=>'Report type not found',
                    'status'=>false
                ];
            }

        }

        return response()->json($result);
    } */
    
    /**
     * Average Reports
     *
     * @param 
     * @return 
     */
   /*  public function average(Request $request, ReportsController $ReportsController)
    {
        
        $result = [
            'messages'=>'Method Not Allowed',
            'status'=>false
        ];

        if ($request->isMethod('post')) {
            $user = auth()->user();
            $report_type = !empty($request->input('report_type')) ? $request->input('report_type') : null;
            $worker_list = !empty($request->input('worker_list')) ? $request->input('worker_list') : null;
            $group_id = !empty($request->input('group_id')) ? $request->input('group_id') : null;
            $machine_no = !empty($request->input('machine_no')) ? $request->input('machine_no') : null;
            $from_date = !empty($request->input('from_date')) ? $request->input('from_date') : null;
            $to_date = !empty($request->input('to_date')) ? $request->input('to_date') : null;
            $cust_id =  ($user->parent_id != NULL) ? $user->parent_id : $user->id;

            if (!empty($report_type)) {
                
                $search = [
                    'report_type' => $report_type,
                    'worker_list' => $worker_list,
                    'group_id' => $group_id,
                    'machine_no' => $machine_no,
                    'from_date' => $from_date,
                    'to_date' => $to_date,
                    'cust_id' => $cust_id,
                ];

                $options = $ReportsController->generate_options($search,$cust_id);

                $options['view_mode'] = 'download';
                if ($report_type=='machines') {
                    $report_data = $ReportsController->generate_machines_avg_report($options);
                }elseif ($report_type=='workers') {
                    $report_data = $ReportsController->generate_worker_avg_report($options);
                }
                if ($report_data['status']==1) {
                    $report_url = generate_report_url($report_data['file_name']);
                    $result = [
                        'messages'=>'Data retrieved successfully',
                        'report_link' => $report_url,
                        'status'=>true
                    ];
                }else{
                    $result = [
                        'messages'=>'Unable to generate PDF, please try again later.',
                        'status'=>false
                    ];
                }

            }else{
                $result = [
                    'messages'=>'Report type not found',
                    'status'=>false
                ];
            }

        }

        return response()->json($result,200);
    } */

    /**
     * Worker Total Reports
     *
     * @param 
     * @return 
     */
    /* public function worker_total(Request $request, ReportsController $ReportsController)
    {
        
        $result = [
            'messages'=>'Method Not Allowed',
            'status'=>false
        ];

        if ($request->isMethod('post')) {
            
            $machine_no = !empty($request->input('machine_no')) ? $request->input('machine_no') : null;
            $worker_list = !empty($request->input('worker_list')) ? $request->input('worker_list') : null;
            $from_date = !empty($request->input('from_date')) ? $request->input('from_date') : null;
            $to_date = !empty($request->input('to_date')) ? $request->input('to_date') : null;
            $cust_id = !empty($request->input('cust_id')) ? $request->input('cust_id') : null;

            $search = [
                'machine_no' => $machine_no,
                'worker_list' => $worker_list,
                'from_date' => $from_date,
                'to_date' => $to_date,
            ];

            $options = $ReportsController->generate_options($search,$cust_id);
            $options['view_mode'] = 'download';
            
            $report_data = $ReportsController->generate_worker_total_report($options);

            if ($report_data['status']==1) {
                $report_url = generate_report_url($report_data['file_name']);
                $result = [
                    'messages'=>'Data retrieved successfully',
                    'report_link' => $report_url,
                    'status'=>true
                ];
            }else{
                $result = [
                    'messages'=>'Unable to generate PDF, please try again later.',
                    'status'=>false
                ];
            }


        }

        return response()->json($result);
    } */

    /**
     * Worker Total Reports
     *
     * @param 
     * @return 
     */
    /* public function worker_salary(Request $request, ReportsController $ReportsController)
    {
        
        $result = [
            'messages'=>'Method Not Allowed',
            'status'=>false
        ];

        if ($request->isMethod('post')) {
            
            $from_date = !empty($request->input('from_date')) ? $request->input('from_date') : null;
            $to_date = !empty($request->input('to_date')) ? $request->input('to_date') : null;
            $cust_id = !empty($request->input('cust_id')) ? $request->input('cust_id') : null;

            $search = [
                'from_date' => $from_date,
                'to_date' => $to_date,
            ];

            $options = $ReportsController->generate_options($search,$cust_id);
            $options['view_mode'] = 'download';
            
            $report_data = $ReportsController->generate_worker_salary_report($options);

            if ($report_data['status']==1) {
                $report_url = generate_report_url($report_data['file_name']);
                $result = [
                    'messages'=>'Data retrieved successfully',
                    'report_link' => $report_url,
                    'status'=>true
                ];
            }else{
                $result = [
                    'messages'=>'Unable to generate PDF, please try again later.',
                    'status'=>false
                ];
            }
        

        }

        return response()->json($result);
    } */


    public function productionReport(Request $request, ReportsController $ReportsController)
    {
        $cust_id = (!$this->Abettor->isAdmin()) ? auth()->user()->parent_id : Auth::id();
        $report_type = !empty($request->input('report_type')) ? $request->input('report_type') : null;
        $shift = !empty($request->input('shift')) ? $request->input('shift') : null;
        $machine_no = !empty($request->input('machine_no')) ? $request->input('machine_no') : 0;
        $group_id = !empty($request->input('group_id')) ? $request->input('group_id') : null;
        $worker_list = !empty($request->input('worker_list')) ? $request->input('worker_list') : 'all';
        $from_date = !empty($request->input('from_date')) ? $request->input('from_date') : null;
        $to_date = !empty($request->input('to_date')) ? $request->input('to_date') : null;

        if (!empty($report_type)) {
            
            $search = [
                'shift' => $shift,
                'machine_no' => strtolower($machine_no),
                'worker_list' => strtolower($worker_list),
                'group_id' => strtolower($group_id),
                'from_date' => $from_date,
                'to_date' => $to_date
            ];


            $options = $ReportsController->generate_options($search,$cust_id);

            $options['view_mode'] = 'download';
            if ($report_type=='5_min_diff') {
                $report_data = $ReportsController->generate_5min_report($options);
            }elseif ($report_type=='6_hours') {
                $report_data = $ReportsController->generate_6hr_report($options);
            }elseif ($report_type=='12_hours') {
                $report_data = $ReportsController->generate_12hr_report($options);
            }elseif ($report_type=='3_hours') {
                $report_data = $ReportsController->generate_3hr_report($options);
            }

            if ($report_data['status']==1) {
                $report_url = generate_report_url($report_data['file_name']);
                $result = [
                    'messages'=>'Data retrieved successfully',
                    'data' => $report_url,
                    'status'=>true
                ];
            }else{
                $result = [
                    'messages'=>'Unable to generate PDF, please try again later.',
                    'status'=>false,
                    'data'=>[]
                ];
            }
        }else{
            $result = [
                'messages'=>'Report type not found',
                'status'=>false,
                'data'=>[]
            ];
        }
        return response()->json($result,200);
    }

    public function averageReport(Request $request, ReportsController $ReportsController)
    {
        $cust_id = (!$this->Abettor->isAdmin()) ? auth()->user()->parent_id : Auth::id();
        $report_type = !empty($request->input('report_type')) ? $request->input('report_type') : null;
        $worker_list = !empty($request->input('worker_list')) ? $request->input('worker_list') : null;
        $group_id = !empty($request->input('group_id')) ? $request->input('group_id') : null;
        $machine_no = !empty($request->input('machine_no')) ? $request->input('machine_no') : null;
        $from_date = !empty($request->input('from_date')) ? $request->input('from_date') : null;
        $to_date = !empty($request->input('to_date')) ? $request->input('to_date') : null;

        if (!empty($report_type)) {
            
            $search = [
                'report_type' => $report_type,
                'machine_no' => strtolower($machine_no),
                'worker_list' => strtolower($worker_list),
                'group_id' => strtolower($group_id),
                'from_date' => $from_date,
                'to_date' => $to_date
            ];

            $options = $ReportsController->generate_options($search,$cust_id);

            $options['view_mode'] = 'download';
            if ($report_type=='machines') {
                $report_data = $ReportsController->generate_machines_avg_report($options);
            }elseif ($report_type=='workers') {
                $report_data = $ReportsController->generate_worker_avg_report($options);
            }
            if ($report_data['status']==1) {
                $report_url = generate_report_url($report_data['file_name']);
                $result = [
                    'messages'=>'Data retrieved successfully',
                    'data' => $report_url,
                    'status'=>true
                ];
            }else{
                $result = [
                    'messages'=>'Unable to generate PDF, please try again later.',
                    'status'=>false,
                    'data'=>[]
                ];
            }
        }else{
            $result = [
                'messages'=>'Report type not found',
                'status'=>false,
                'data'=>[]
            ];
        }
        return response()->json($result,200);
    }

    public function averageWeeklyReport(Request $request, ReportsController $ReportsController)
    {
        $cust_id = (!$this->Abettor->isAdmin()) ? auth()->user()->parent_id : Auth::id();
        $worker_list = !empty($request->input('worker_list')) ? $request->input('worker_list') : null;
        $group_id = !empty($request->input('group_id')) ? $request->input('group_id') : null;
        $machine_no = !empty($request->input('machine_no')) ? $request->input('machine_no') : null;
        $from_date = !empty($request->input('from_date')) ? $request->input('from_date') : null;

        $search = [
            'machine_no' => strtolower($machine_no),
            'worker_list' => strtolower($worker_list),
            'group_id' => strtolower($group_id),
            'from_date' => $from_date
        ];

        $options = $ReportsController->generate_options($search,$cust_id);

        $options['view_mode'] = 'download';
        $report_data = $ReportsController->generate_avg_week_report($options);
        if ($report_data['status']==1) {
            $report_url = generate_report_url($report_data['file_name']);
            $result = [
                'messages'=>'Data retrieved successfully',
                'data' => $report_url,
                'status'=>true
            ];
        }else{
            $result = [
                'messages'=>'Unable to generate PDF, please try again later.',
                'status'=>false,
                'data'=>[]
            ];
        }
        return response()->json($result,200);
    }

    public function workerTotalReport(Request $request, ReportsController $ReportsController)
    {
        
        $cust_id = (!$this->Abettor->isAdmin()) ? auth()->user()->parent_id : Auth::id();  
        $machine_no = !empty($request->input('machine_no')) ? $request->input('machine_no') : null;
        $worker_list = !empty($request->input('worker_list')) ? $request->input('worker_list') : null;
        $group_id = !empty($request->input('group_id')) ? $request->input('group_id') : null;
        $from_date = !empty($request->input('from_date')) ? $request->input('from_date') : null;
        $to_date = !empty($request->input('to_date')) ? $request->input('to_date') : null;

        $search = [
            'machine_no' => strtolower($machine_no),
            'worker_list' => strtolower($worker_list),
            'group_id' => strtolower($group_id),
            'from_date' => $from_date,
            'to_date' => $to_date,
        ];
        /* print_r($search);
        die; */
        $options = $ReportsController->generate_options($search,$cust_id);
        $options['view_mode'] = 'download';
        
        $report_data = $ReportsController->generate_worker_total_report($options);

        if ($report_data['status']==1) {
            $report_url = generate_report_url($report_data['file_name']);
            $result = [
                'messages'=>'Data retrieved successfully',
                'data' => $report_url,
                'status'=>true
            ];
        }else{
            $result = [
                'messages'=>'Unable to generate PDF, please try again later.',
                'status'=>false,
                'data'=>[]
            ];
        }
        return response()->json($result,200);
    }

    public function workerSalaryReport(Request $request, ReportsController $ReportsController)
    {

        $cust_id = (!$this->Abettor->isAdmin()) ? auth()->user()->parent_id : Auth::id();  
        $machine_no = !empty($request->input('machine_no')) ? $request->input('machine_no') : null;
        $worker_list = !empty($request->input('worker_list')) ? $request->input('worker_list') : null;
        $group_id = !empty($request->input('group_id')) ? $request->input('group_id') : null;
        $from_date = !empty($request->input('from_date')) ? $request->input('from_date') : null;
        $to_date = !empty($request->input('to_date')) ? $request->input('to_date') : null;

         $search = [
            'machine_no' => strtolower($machine_no),
            'worker_list' => strtolower($worker_list),
            'group_id' => strtolower($group_id),
            'from_date' => $from_date,
            'to_date' => $to_date,
        ];

        $options = $ReportsController->generate_options($search,$cust_id);
        $options['view_mode'] = 'download';
        
        $report_data = $ReportsController->generate_worker_salary_report($options);

        if ($report_data['status']==1) {
            $report_url = generate_report_url($report_data['file_name']);
            $result = [
                'messages'=>'Data retrieved successfully',
                'data' => $report_url,
                'status'=>true
            ];
        }else{
            $result = [
                'messages'=>'Unable to generate PDF, please try again later.',
                'status'=>false,
                'data' => [],
            ];
        }
        return response()->json($result,200);
    }
}
