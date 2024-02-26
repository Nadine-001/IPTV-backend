<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DeleteRecords extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'delete-records';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete records older than one hour from the table';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        DB::table('temp_cart_room_services')->where('created_at', '<', now()->subHour())->delete();
        DB::table('temp_cart_food_services')->where('created_at', '<', now()->subHour())->delete();
    }
}
