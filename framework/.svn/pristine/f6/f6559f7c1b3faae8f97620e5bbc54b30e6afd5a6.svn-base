<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2017/12/26
 * Time: 16:26
 */

require_once(dirname(__FILE__).'/../include_common.php');


$url = SCRIPT_SITE_URL.'/index.php?act=script_cert&op=updateExpireCertList';
while( true ){
    $re = @file_get_contents($url);
    print_r($re);
    sleep(12*3600);
}