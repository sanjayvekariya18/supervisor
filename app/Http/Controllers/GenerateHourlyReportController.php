<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Machine;
use App\Models\MachineData;
use App\Models\MachineHourlyData;
use Carbon\Carbon;
use File;

class GenerateHourlyReportController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        
            
            // $hours = array(00,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23);
            
            // foreach ($hours as $hour) {
            //     $previousHourDate = Carbon::now()->format("Y-m-d $hour:00:00");
            //     $machineData = MachineData::
            //         where('report_date',$previousHourDate)
            //         ->orderBy('cust_id')
            //         ->orderByRaw('LENGTH(machine_id) ASC,machine_id ASC')
            //         ->get()->toarray();
            // /*  echo "<pre>";
            //     print_r($machineData);
            //     die; */
            //     foreach ($machineData as $key => $machine) {
            //         unset($machine['id']);
            //         try {
            //             MachineHourlyData::insert($machine);
            //         } catch (\Exception $ex) {
            //             echo $ex->getMessage()."<br>";
            //             continue;
            //         }
            //         $json = [
            //             'cust_id' => $machine['cust_id'],
            //             'machine_id' => $machine['machine_id'],
            //             'machine_number' => $machine['machine_number'],
            //             'shift' => $machine['shift'],
            //             'stitches' => $machine['stitches'],
            //             'report_date' => $machine['report_date'],
            //         ];
            //         $log = Carbon::now(). " => ".json_encode($json) ."\n";
            //         File::append(public_path('/_log/hourly.log'),$log);
            //     }
            // }

        //code...
        
        
        $previousHourDate = Carbon::now()->format('Y-m-d H:00:00');
        $machineData = MachineData::
            where('report_date',$previousHourDate)
            ->orderBy('cust_id')
            ->orderByRaw('LENGTH(machine_id) ASC,machine_id ASC')
            ->get()->toarray();
        
        foreach ($machineData as $key => $machine) {
            unset($machine['id']);
            try {
                MachineHourlyData::insert($machine);
            } catch (\Exception $ex) {
                continue;
            }
            $json = [
                'cust_id' => $machine['cust_id'],
                'machine_id' => $machine['machine_id'],
                'machine_number' => $machine['machine_number'],
                'shift' => $machine['shift'],
                'stitches' => $machine['stitches'],
                'report_date' => $machine['report_date'],
            ];
            $log = Carbon::now(). " => ".json_encode($json) ."\n";
            File::append(public_path('/_log/hourly.log'),$log);
        }
    }
}
