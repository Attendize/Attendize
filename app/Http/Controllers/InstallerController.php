<?php

namespace App\Http\Controllers;

use App\Models\Timezone;
use Artisan;
use Config;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Str;
use PhpSpec\Exception\Example\ExampleException;
use Log;

class InstallerController extends Controller
{

    private $data;

    /**
     * InstallerController constructor.
     */
    public function __construct() {
        /*
         * Path we need to make sure are writable
         */
        $this->data['paths'] = [
            storage_path('app'),
            storage_path('framework'),
            storage_path('logs'),
            public_path(config('attendize.event_images_path')),
            public_path(config('attendize.organiser_images_path')),
            public_path(config('attendize.event_pdf_tickets_path')),
            base_path('bootstrap/cache'),
            base_path('.env'),
            base_path(),
        ];

        /*
         * Required PHP extensions
         */
        $this->data['requirements'] = [
            'openssl',
            'pdo',
            'mbstring',
            'fileinfo',
            'tokenizer',
            'gd',
            'zip',
        ];

        /*
         * Optional PHP extensions
         */
        $this->data['optional_requirements'] = [
            'pdo_mysql',
            'pdo_pgsql',
        ];

        $database_default = Config::get('database.default');
        $mail_driver = Config::get('mail.default');
        $this->data['default_config'] = [
            'application_url'   => Config::get('app.url'),
            'database_type'     => $database_default,
            'database_host'     => Config::get('database.connections.' . $database_default . '.host'),
            'database_name'     => Config::get('database.connections.' . $database_default . '.database'),
            'database_username' => Config::get('database.connections.' . $database_default . '.username'),
            'database_password' => Config::get('database.connections.' . $database_default . '.password'),
            'mail_from_address' => Config::get('mail.from.address'),
            'mail_from_name'    => Config::get('mail.from.name'),
            'mail_driver'       => $mail_driver
        ];

        $mailDriverConfig = Config::get('mail.mailers.' . $mail_driver);
        foreach ($mailDriverConfig as $key => $value) {
            $this->data['default_config']['mail_' . $key] = $value;
        }
    }

    /**
     * Show the application installer
     *
     * @return mixed
     */
    public function showInstaller()
    {
        /**
         * If we're already installed display user friendly message and direct them to the appropriate next steps.
         *
         * @todo Check if DB is installed etc.
         * @todo Add some automated checks to see exactly what the state of the install is. Potentially would be nice to
         *       allow the user to restart the install process
         */
        if (file_exists(base_path('installed'))) {
            return view('Installer.AlreadyInstalled', $this->data);
        }

        return view('Installer.Installer', $this->data);
    }

    /**
     * Attempts to install the system
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|array
     */
    public function postInstaller(Request $request)
    {
        if (file_exists(base_path('installed'))) {
            abort(404);
        }

        set_time_limit(300);

        $database['type'] = $request->get('database_type');
        $database['host'] = $request->get('database_host');
        $database['name'] = $request->get('database_name');
        $database['username'] = $request->get('database_username');
        $database['password'] = $request->get('database_password');

        try {
            $this->validate($request, [
                'database_type' => 'required',
                'database_host' => 'required',
                'database_name' => 'required',
                'database_username' => 'required',
                'database_password' => 'required'
            ]);
            $connectionDetailsValid = true;
        } catch (\Exception $e) {
            Log::error('Please enter all app settings. ' . $e->getMessage());
            $connectionDetailsValid = false;
        }

        if (!$connectionDetailsValid) {

            if ($request->get('test') === 'db') {
                return [
                    'status'  => 'error',
                    'message' => trans("Installer.connection_failure"),
                    'test'    => 1,
                ];
            }
            return view('Installer.Installer', $this->data);
        }


        $mail['driver'] = $request->get('mail_driver');
        $mail['port'] = $request->get('mail_port');
        $mail['username'] = $request->get('mail_username');
        $mail['password'] = $request->get('mail_password');
        $mail['encryption'] = $request->get('mail_encryption');
        $mail['from_address'] = $request->get('mail_from_address');
        $mail['from_name'] = $request->get('mail_from_name');
        $mail['host'] = $request->get('mail_host');

        $app_url = $request->get('app_url');
        $app_key = Str::random(32);
        $version = file_get_contents(base_path('VERSION'));

        if ($request->get('test') === 'db') {
            $db_valid = self::testDatabase($database);
            if ($db_valid) {
                return [
                    'status'  => 'success',
                    'message' => trans("Installer.connection_success"),
                    'test'    => 1,
                ];
            }

            return response()->json([
                'status'  => 'error',
                'message' => trans("Installer.connection_failure"),
                'test'    => 1,
            ]);
        }

        //if a user doesn't use the default database details, enters incorrect values in the form, and then proceeds
        //the installation fails miserably. Rather check if the database connection details are valid and fail
        //gracefully
        $db_valid = self::testDatabase($database);
        if (!$db_valid) {
            return view('Installer.Installer', $this->data)->withErrors(
                new MessageBag(['Database connection failed. Please check the details you have entered and try again.']));
        }


        $config_string = file_get_contents(base_path() . '/.env.example');
        $config_temp = explode("\n", $config_string);
        foreach($config_temp as $key=>$row)
            $config_temp[$key] = explode("=", $row, 2);
        $config = [
            "APP_ENV" => "production",
            "APP_DEBUG" => "false",
            "APP_URL" => $app_url,
            "APP_KEY" => $app_key,
            "DB_TYPE" => $database['type'],
            "DB_HOST" => $database['host'],
            "DB_DATABASE" => $database['name'],
            "DB_USERNAME" => $database['username'],
            "DB_PASSWORD" => $database['password'],
            "MAIL_MAILER" => $mail['driver'],
            "MAIL_PORT" => $mail['port'],
            "MAIL_ENCRYPTION" => $mail['encryption'],
            "MAIL_HOST" => $mail['host'],
            "MAIL_USERNAME" => $mail['username'],
            "MAIL_FROM_NAME" => $mail['from_name'],
            "MAIL_FROM_ADDRESS" => $mail['from_address'],
            "MAIL_PASSWORD" => $mail['password'],
        ];

        foreach($config as $key => $val) {
            $set = false;
            foreach($config_temp as $rownum=>$row) {
                if($row[0]==$key) {
                    $config_temp[$rownum][1] = $val;
                    $set = true;
                }
            }
            if(!$set)
                $config_temp[] = [$key, $val];
        }
        $config_string = "";
        foreach($config_temp as $row)
            if(count($row)>1)
                $config_string .= implode("=", $row)."\n";
            else
                $config_string .= implode("", $row)."\n";

        $fp = fopen(base_path() . '/.env', 'w');
        fwrite($fp, $config_string);
        fclose($fp);

        Config::set('database.default', $database['type']);
        Config::set("database.connections.{$database['type']}.host", $database['host']);
        Config::set("database.connections.{$database['type']}.database", $database['name']);
        Config::set("database.connections.{$database['type']}.username", $database['username']);
        Config::set("database.connections.{$database['type']}.password", $database['password']);

        DB::reconnect();

        //force laravel to regenerate a new key (see key:generate sources)
        Config::set('app.key', $app_key);
        Artisan::call('key:generate', ['--force' => true]);
        Artisan::call('migrate', ['--force' => true]);
        if (Timezone::count() == 0) {
            Artisan::call('db:seed', ['--force' => true]);
        }

        $fp = fopen(base_path() . '/installed', 'w');
        fwrite($fp, $version);
        fclose($fp);

        return redirect()->route('showSignup', ['first_run' => 'yup']);
    }

    private function testDatabase($database)
    {
        Config::set('database.default', $database['type']);
        Config::set("database.connections.{$database['type']}.host", $database['host']);
        Config::set("database.connections.{$database['type']}.database", $database['name']);
        Config::set("database.connections.{$database['type']}.username", $database['username']);
        Config::set("database.connections.{$database['type']}.password", $database['password']);

        $databaseConnectionValid = FALSE;

        try {
            DB::reconnect();
            $pdo = DB::connection()->getPdo();
            if(!empty($pdo)) {
                $databaseConnectionValid = TRUE;
            }

        } catch (\Exception $e) {
            Log::error('Database connection details invalid' . $e->getMessage());
        }

        return $databaseConnectionValid;
    }
}
