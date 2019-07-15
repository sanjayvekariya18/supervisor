<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Machine;
use App\Models\MachineData;
use DB;
class GenerateWorkerBonus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate_worker_bonus';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate Worker Bonus after shift is changed - RUN @ every 10PM for Current day\'s Day shift and 10AM next day for previous day\'s Night shift';

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
        
        $shift = 1;
        $cust_id = 'CUST001';
        $machine_id = 1;
        $date = '2019-03-27';

        // Get All Machines Data
        $data = MachineData::
        whereRaw("DATE_FORMAT(created_at,'%Y-%m-%d') = '" . $date."'")
        ->where([
            'shift'=>$shift,
            'cust_id'=>$cust_id,
            'machine_id'=>$machine_id,
        ])
        ->orderBy('created_at','DESC')
        ->limit(1)
        // ->toSql();
        ->get()->toArray();

        echo "<pre>";
        print_r($data);echo "<br></pre>";
        exit;


    }
}
