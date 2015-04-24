<?php
/**
 * Copyright 2015 Goracash
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace Goracash;

/**
 * Collection of static utility methods used for convenience across
 * the client library.
 */
class Utils
{
    /**
     * Normalize all keys in an array to lower-case.
     * @param array $arr
     * @return array Normalized array.
     */
    public static function normalize($arr)
    {
        if (!is_array($arr)) {
            return array();
        }

        $normalized = array();
        foreach ($arr as $key => $val) {
            $normalized[strtolower($key)] = $val;
        }
        return $normalized;
    }

    /**
     * @param $date
     * @return bool
     */
    public static function is_system_date($date)
    {
        if (!isset($date) || $date == '') {
            return false;
        }
        list($year, $month, $day) = explode('-', $date, 3);
        if ((int)$year == 0 || (int)$month == 0 || (int)$day == 0) {
            return false;
        }
        return checkdate((int)$month, (int)$day, (int)$year);
    }

    /**
     * @param $datetime
     * @return bool
     */
    public static function is_system_datetime($datetime)
    {
        if (!isset($datetime) || $datetime == '') {
            return false;
        }
        list($date, $time) = explode(' ', $datetime, 2);

        $is_date = self::is_system_date($date);
        $is_time = self::is_valid_hour($time);

        return ($is_date && $is_time);
    }

    /**
     * Check for time validity
     * @param string $time in H:i format
     * @return boolean
     */
    public static function is_valid_hour($time)
    {
        $result = preg_match('/^([0-9]{1,2}):([0-9]{1,2})(?::([0-9]{1,2}))?$/', $time, $regs);
        if ($result) {
            $hour    = $regs[1];
            $minutes = $regs[2];
            $result  = ($hour >= 0 && $hour < 24) && ($minutes >= 0 && $minutes < 60);
            if ($result && isset($regs[3])) {
                $seconds = $regs[3];
                $result = $seconds >= 0 && $seconds < 60;
            }
        }
        return $result;
    }

    public static function is_out_of_limit($start_date, $end_date, $limit)
    {
        $start_epoch = strtotime($start_date);
        $limit_epoch = strotime("+{$limit}", $start_epoch);
        $end_epoch = strtotime($end_date);
        return $end_epoch > $limit_epoch;
    }

    /**
     * Concatenate paths, making sure there is no double path separator
     **/
    public static function concat_path()
    {
        $args = func_get_args();
        $len = count($args);
        if ($len == 0) {
            return '';
        }
        $path = $args[0];
        for ($i = 1; $i < $len; $i++) {
            $path = rtrim($path, '/') . '/' . ltrim($args[$i], '/');
        }
        return $path;
    }

    public static function now()
    {
        return date('Y-m-d H:i:s');
    }
}