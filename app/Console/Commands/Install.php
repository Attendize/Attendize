<?php

namespace App\Console\Commands;

use App\Models\Account;
use App\Models\Timezone;
use App\Models\User;
use DB;
use Hash;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use PhpSpec\Exception\Exception;

class Install extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'attendize:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install Attendize';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $version = file_get_contents(base_path('VERSION'));
        try {
            DB::connection();
        } catch (\Exception $e) {
            $this->error('Unable to connect to database.');
            $this->error('Please fill valid database credentials into .env and rerun this command.');

            return;
        }

        $this->comment('--------------------');
        $this->comment('Attempting to install Attendize v'.$version);
        $this->comment('--------------------');

        if (! env('APP_KEY')) {
            $this->info('Generating app key');
            Artisan::call('key:generate');
        } else {
            $this->info('App key exists -- skipping');
        }

        $this->info('Migrating database.');
        Artisan::call('migrate', ['--force' => true]);
        $this->info('Database successfully migrated.');

        if (! Timezone::count()) {
            $this->info('Seeding DB data');
            Artisan::call('db:seed', ['--force' => true]);
            $this->info('Data successfully seeded');
        } else {
            $this->info('Data already seeded.');
        }

        /*
         * If there is no account prompt the user to create one;
         */
        if (Account::count() === 0) {
            DB::beginTransaction();
            try {
                $this->comment('--------------------');
                $this->comment('Please create an admin user.');
                $this->comment('--------------------');
                //Create the first user
                $fname = $this->ask('Enter first name:');
                $lname = $this->ask('Enter last name:');
                $email = $this->ask('Enter your email:');
                $password = $this->secret('Enter a password:');

                $account_data['email'] = $email;
                $account_data['first_name'] = $fname;
                $account_data['last_name'] = $lname;
                $account_data['currency_id'] = config('attendize.default_currency');
                $account_data['timezone_id'] = config('attendize.default_timezone');
                $account = Account::create($account_data);

                $user_data['email'] = $email;
                $user_data['first_name'] = $fname;
                $user_data['last_name'] = $lname;

                $user_data['password'] = Hash::make($password);
                $user_data['account_id'] = $account->id;
                $user_data['is_parent'] = 1;
                $user_data['is_registered'] = 1;
                $user = User::create($user_data);

                DB::commit();
                $this->info('Admin User Successfully Created');
            } catch (Exception $e) {
                DB::rollBack();
                $this->error('Error Creating User');
                $this->error($e);
            }
        }

        file_put_contents(base_path('installed'), $version);

        $this->comment("
          _   _                 _ _         
     /\  | | | |               | (_)        
    /  \ | |_| |_ ___ _ __   __| |_ _______ 
   / /\ \| __| __/ _ \ '_ \ / _` | |_  / _ \
  / ____ \ |_| ||  __/ | | | (_| | |/ /  __/
 /_/    \_\__|\__\___|_| |_|\__,_|_/___\___|
        ");

        $this->comment('Success! You can now run Attendize');
    }
}
