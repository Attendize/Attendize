<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;

class CreateDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:database {db_name}';


    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new database';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        return DB::statement('CREATE DATABASE IF NOT EXISTS '.$this->argument('db_name').";") ? 0 : 1;
    }
}
