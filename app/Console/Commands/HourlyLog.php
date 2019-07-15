<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use File;
class HourlyLog extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hourly_log';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        File::append(public_path('/_log/cron.log'),date('Y-m-d H:i:s')."\n");
    }
}
