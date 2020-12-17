<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DeliveryCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:delivery';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'set free after food delivery';

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
     *
     */
    public function handle()
    {
        $current = Carbon::now();
        DB::table('deliveries')->where('delivery_time', '<=', $current)
            ->where('is_busy', '=', true)
            ->update(['is_busy' => false]);
        $this->info('Successfully run.');
    }
}
