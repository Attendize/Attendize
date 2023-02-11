<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class RemoveOldTempFiles implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Log::debug('Running temporary file cleanup...');
        $directories = Storage::disk('local')->directories('tmp/docs');

        $now = time();
        $nbFilesDeleted = 0;

        foreach($directories as $dir) {
            // Those are unix timestamps
            $folder_modified = Storage::disk('local')->lastModified($dir);
            // If folder older than an hour
            if($now - $folder_modified > 3600) {
                Storage::disk('local')->deleteDirectory($dir);
                $nbFilesDeleted = $nbFilesDeleted + 1;
            }
        }

        switch($nbFilesDeleted) {
            case 0:
                Log::debug('No temporary files deleted');
            break;
            case 1:
                Log::debug('One temporary file deleted');
            break;
            default:
                Log::debug("$nbFilesDeleted temporary files deleted");
            break;
        }
    }
}
