<?php
/**
 * Created by PhpStorm.
 * User: hh
 * Date: 2018/3/18
 * Time: 下午 2:50
 */
class weixinControl
{

    public function helloOp()
    {
        $token = getConf('weixin_token');
        $params = $_GET;
        $signature = $params['signature'];
        $timestamp = $params['timestamp'];
        $nonce = $params['nonce'];
        $echostr = $params['echostr'];

        $temp = array($token,$timestamp,$nonce);
        sort($temp,SORT_STRING);
        $tmp_str = implode('',$temp);
        $sign = sha1($tmp_str);

        if( $sign == $signature ){
            echo $echostr;
        }else{
            echo 'OK';
        }

    }
}