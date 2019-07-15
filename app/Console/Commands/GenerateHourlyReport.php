<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Machine;
use App\Models\MachineData;
use App\Models\MachineHourlyData;
use Carbon\Carbon;
use File;
class GenerateHourlyReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate_hourly_report';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate Hourly reports for every machines - RUN @ every 1Hour and 5mins';

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
