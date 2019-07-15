<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Machine;

class ResetMaxRPM extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reset_max_rpm';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset Max RPM - RUN @ every 30mins';

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
        $flag = Machine::where([
            'status_id' => 1
        ])->update([
            'max_rpm' => 0
        ]);

        if ($flag) {
            $this->info("Successfully reset Max RPM");
        }else{
            $this->error("Unable to reset Max RPM");
        }
    }
}
