<?php

/**
 * Created by PhpStorm.
 * User: Seven
 * Date: 2017/11/7
 * Time: 9:35
 */
class common
{
    private static $date_format = 1;

    function __construct()
    {
        $m_core_dictionary = M('core_dictionary');
        $data = $m_core_dictionary->getDictionary('global_settings');
        if ($data) {
            $global_settings = my_json_decode($data['dict_value']);
            $date_format = $global_settings['date_format'];
        } else {
            $date_format = 1;
        }
        self::$date_format = $date_format;
    }

    /**
     * 格式化日期
     * @param $date
     * @return bool|string
     */
    public static function dateFormat($date)
    {
        if (self::$date_format == 1) {
            return $date ? date('d/m/Y', strtotime($date)) : '';
        } else {
            return $date ? date('Y-m-s', strtotime($date)) : '';
        }
    }

    public static function timeFormat($time)
    {
        if (self::$date_format == 1) {
            return $time ? date('d/m/Y H:i:s', strtotime($time)) : '';
        } else {
            return $time ? date('Y-m-s H:i:s', strtotime($time)) : '';
        }
    }

}