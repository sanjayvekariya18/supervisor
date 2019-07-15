<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Machine;
use App\Models\MachineData;

class Generate5MinReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate_report:5min';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate 5min reports for every machines - RUN @ every 5mins';

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
        $machines = Machine::where([
            'status_id' => 1
        ])->get();

        // Insert 5 Min report
        foreach ($machines as $key => $machine) {
                
            $machine_data = new MachineData;
            $machine_data->cust_id = $machine->cust_id;
            $machine_data->machine_id = $machine->id;
            $machine_data->machine_number = $machine->machine_number;
            $machine_data->shift = $machine->shift;
            $machine_data->stitches = $machine->stitches;
            $machine_data->thred_break = $machine->thred_break;
            $machine_data->rpm = $machine->rpm;
            $machine_data->max_rpm   = $machine->max_rpm;
            $machine_data->stop_time   = $machine->stop_time;
            $machine_data->worker_id   = $machine->worker_id;
            $machine_data->working_head   = $machine->working_head;
            $machine_data->created_at      = date("Y-m-d H:i:s");

            if($machine_data->save()){
                $this->info("Machine " . $machine->id . " report generated successfully");
            }else{
                $this->error("Unable to generate report for Machine " . $machine->id);
            }

        }

    }
}
