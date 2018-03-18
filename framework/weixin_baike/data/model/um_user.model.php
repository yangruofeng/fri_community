<?php

/**
 * Created by PhpStorm.
 * User: tim
 * Date: 5/31/2015
 * Time: 1:15 AM
 */
class um_userModel extends tableModelBase
{
//    public $schema = array(
//        array("Field" => "user_code", "Type" => "varchar", "Null" => "NO", "Key" => "", "Default" => "", "Extra" => ""),
//        array("Field" => "first_name", "Type" => "varchar", "Null" => "", "Key" => "", "Default" => "", "Extra" => ""),
//        array("Field" => "last_name", "Type" => "varchar", "Null" => "", "Key" => "", "Default" => "", "Extra" => ""),
//        array("Field" => "password", "Type" => "varchar", "Null" => "", "Key" => "", "Default" => "", "Extra" => ""),
//        array("Field" => "user_image", "Type" => "varchar", "Null" => "", "Key" => "", "Default" => "", "Extra" => ""),
//        array("Field" => "user_icon", "Type" => "varchar", "Null" => "", "Key" => "", "Default" => "", "Extra" => ""),
//        array("Field" => "email", "Type" => "varchar", "Null" => "", "Key" => "", "Default" => "", "Extra" => ""),
//        array("Field" => "mobile_phone", "Type" => "varchar", "Null" => "", "Key" => "", "Default" => "", "Extra" => ""),
//        array("Field" => "user_type", "Type" => "int(4)", "Null" => "", "Key" => "", "Default" => "", "Extra" => ""),
//        array("Field" => "user_status", "Type" => "int(4)", "Null" => "", "Key" => "", "Default" => "", "Extra" => ""),
//        array("Field" => "creator_id", "Type" => "int", "Null" => "", "Key" => "", "Default" => "", "Extra" => ""),
//        array("Field" => "insert_time", "Type" => "timestamp", "Null" => "", "Key" => "", "Default" => "", "Extra" => ""),
//        array("Field" => "remark", "Type" => "varchar(200)", "Null" => "", "Key" => "", "Default" => "", "Extra" => ""),
//        array("Field" => "last_login_time", "Type" => "datetime", "Null" => "", "Key" => "", "Default" => "", "Extra" => ""),
//        array("Field" => "last_login_ip", "Type" => "varchar", "Null" => "", "Key" => "", "Default" => "", "Extra" => ""),
//        array("Field" => "last_login_area", "Type" => "varchar", "Null" => "", "Key" => "", "Default" => "", "Extra" => ""),
//        array("Field" => "role_code", "Type" => "varchar", "Null" => "", "Key" => "", "Default" => "", "Extra" => ""),
//        array("Field" => "profile", "Type" => "text", "Null" => "", "Key" => "", "Default" => "", "Extra" => ""),
//    );

    public function  __construct()
    {
        parent::__construct('um_user');
    }
}
