<?php
/**
 * Created by PhpStorm.
 * User: hh
 * Date: 2018/3/18
 * Time: 下午 4:17
 */
class wechatCallbackClass
{

    public static function responseMsg()
    {

        $postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
        if( !$postStr ){
            $postStr = file_get_contents('php://input');
        }
       
       //	file_put_contents(_LOG_.'/'.'WEIXIN-MSG-'.date('Y-m-d').'.xml',$result,FILE_APPEND);
       // Logger::record('responseMsg',Now().'  '.$postStr."\n");


        if( $postStr ){

            $postObj = simplexml_load_string($postStr,'SimpleXMLElement',LIBXML_NOCDATA);

            $MsgType = $postObj->MsgType;


            switch( $MsgType ){
                case 'text':
                    self::responseTextMsg($postObj);
                    break;
                case 'image':
                    self::responseImageMsg($postObj);
                    break;
                case 'event':
                    $Event = $postObj->Event;
                    if( $Event == 'subscribe'){
                        self::responseSubscribeMsg($postObj);
                    }
                    break;
                default:
                    echo 'Hello';
                    die;
            }




        }else{
            echo 'OK';die;
        }
    }


    public static function responseTextMsg($msg_obj)
    {
        $MsgType = $msg_obj->MsgType;
        $from_username = $msg_obj->FromUserName;
        $to_username = $msg_obj->ToUserName;
        $CreateTime = $msg_obj->CreateTime;
        $Content = $msg_obj->Content;
        $MsgId = $msg_obj->MsgId;

        $textTpl = "<xml> <ToUserName>< ![CDATA[%s] ]></ToUserName> <FromUserName>< ![CDATA[%s] ]></FromUserName> <CreateTime>%s</CreateTime> <MsgType>< ![CDATA[text] ]></MsgType> <Content>< ![CDATA[%s] ]></Content> </xml>";

        $res = sprintf($textTpl,$from_username,$to_username,time(),$Content);
        echo $res;
        die;

    }

    public static function responseImageMsg($msg_obj)
    {

        $MsgType = $msg_obj->MsgType;
        $from_username = $msg_obj->FromUserName;
        $MsgType = $msg_obj->MsgType;
        $to_username = $msg_obj->ToUserName;
        $CreateTime = $msg_obj->CreateTime;
        $MsgId = $msg_obj->MsgId;
        $MediaId = $msg_obj->MediaId;
        $PicUrl = $msg_obj->PicUrl;

        $tpl = "<xml><ToUserName>< ![CDATA[%s] ]></ToUserName><FromUserName>< ![CDATA[%s] ]></FromUserName><CreateTime>%s</CreateTime><MsgType>< ![CDATA[image] ]></MsgType><Image><MediaId>< ![CDATA[%s] ]></MediaId></Image></xml>";

        $res = sprintf($tpl,$from_username,$to_username,time(),$MediaId);
        echo $res;
        die;

    }



    public static function responseSubscribeMsg($msg_obj)
    {

        $MsgType = $msg_obj->MsgType;
        $from_username = $msg_obj->FromUserName;
        $to_username = $msg_obj->ToUserName;
        $CreateTime = $msg_obj->CreateTime;
        $event = $msg_obj->Event;

        $textTpl = "<xml> <ToUserName>< ![CDATA[%s] ]></ToUserName> <FromUserName>< ![CDATA[%s] ]></FromUserName> <CreateTime>%s</CreateTime> <MsgType>< ![CDATA[text] ]></MsgType> <Content>< ![CDATA[%s] ]></Content> </xml>";

        $res = sprintf($textTpl,$from_username,$to_username,time(),'欢迎您！');
        echo $res;
        die;
    }


}
