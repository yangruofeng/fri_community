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
        ob_get_clean();  // 框架原因，必须添加
        ob_clean();
        ob_end_clean();//清除缓冲区,Bom头，避免乱码和不能识别的文件类型

        // http://www.iruofeng.cn/fri_community/framework/weixin_baike/wxt/index.php?act=weixin&op=hello
        if( $_GET['echostr'] ){

            $this->validOp();

        }else{

            $this->responseMsgOp();
        }


    }

    public function validOp()
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

    public function responseMsgOp()
    {
        wechatCallbackClass::responseMsg();
    }
}