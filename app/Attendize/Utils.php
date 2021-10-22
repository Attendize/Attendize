<?php

namespace App\Attendize;

use Auth;
use PhpSpec\Exception\Exception;

class Utils
{
    /**
     * Check if the current user is registered
     *
     * @return bool
     */
    public static function isRegistered()
    {
        return Auth::check() && Auth::user()->is_registered;
    }

    /**
     * Check if the current user is confirmed
     *
     * @return bool
     */
    public static function isConfirmed()
    {
        return Auth::check() && Auth::user()->is_confirmed;
    }

    /**
     * Check if the DB has been set up
     *
     * @return bool
     */
    public static function isDatabaseSetup()
    {
        try {
            if (Schema::hasTable('accounts')) {
                return true;
            }
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Are we the cloud version of attendize or in dev enviornment?
     *
     * @return bool
     */
    public static function isAttendize()
    {
        return self::isAttendizeCloud() || self::isAttendizeDev();
    }

    /**
     * Are we the cloud version of Attendize?
     *
     * @return bool
     */
    public static function isAttendizeCloud()
    {
        return isset($_ENV['ATTENDIZE_CLOUD']) && $_ENV['ATTENDIZE_CLOUD'] == 'true';
    }

    /**
     * Are we in a dev enviornment?
     *
     * @return bool
     */
    public static function isAttendizeDev()
    {
        return isset($_ENV['ATTENDIZE_DEV']) && $_ENV['ATTENDIZE_DEV'] == 'true';
    }

    public static function isDownForMaintenance()
    {
        return file_exists(storage_path().'/framework/down');
    }

    /**
     * Check if a user has admin access to events etc.
     *
     * @todo - This is a temp fix until user roles etc. are implemented
     * @param $object
     * @return bool
     */
    public static function userOwns($object)
    {
        if (! Auth::check()) {
            return false;
        }

        try {
            if (Auth::user()->account_id === $object->account_id) {
                return true;
            }
        } catch (Exception $e) {
            return false;
        }

        return false;
    }

    /**
     * Determine max upload size
     *
     * @return float|int
     */
    public static function file_upload_max_size()
    {
        static $max_size = -1;

        if ($max_size < 0) {
            // Start with post_max_size.
            $max_size = self::parse_size(ini_get('post_max_size'));

            // If upload_max_size is less, then reduce. Except if upload_max_size is
            // zero, which indicates no limit.
            $upload_max = self::parse_size(ini_get('upload_max_filesize'));
            if ($upload_max > 0 && $upload_max < $max_size) {
                $max_size = $upload_max;
            }
        }

        return $max_size;
    }

    /**
     * Parses the given size
     *
     * @param $size
     * @return float
     */
    public static function parse_size($size)
    {
        $unit = preg_replace('/[^bkmgtpezy]/i', '', $size); // Remove the non-unit characters from the size.
        $size = preg_replace('/[^0-9\.]/', '', $size); // Remove the non-numeric characters from the size.
        if ($unit) {
            // Find the position of the unit in the ordered string which is the power of magnitude to multiply a kilobyte by.
            return round($size * pow(1024, stripos('bkmgtpezy', $unit[0])));
        } else {
            return round($size);
        }
    }

    /**
     * Check if Attendize is installed
     *
     * @return bool
     */
    public static function installed(): bool
    {
        return file_exists(base_path('installed'));
    }

    /**
     * Safely parse a version number from a string
     *
     * @return string
     */
    public static function parse_version($string): string
    {
        if (preg_match('/(\d+\.?\d+\.?\d+)/', $string, $matches) === 1) {
            return $matches[0];
        }

        return '';
    }
}
