<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/3/15
 * Time: 15:01
 */


require_once(dirname(__FILE__).'/../include_common.php');

$url = SCRIPT_SITE_URL.'/index.php?act=loan&op=exec_disbursement_schema';

while( true ){
    $re = @file_get_contents($url);
    echo date('Y-m-d H:i:s')."\n";
    sleep(10);
}