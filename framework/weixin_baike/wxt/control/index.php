<?php
/**
 * Created by PhpStorm.
 * User: hh
 * Date: 2018/3/18
 * Time: 下午 2:47
 */
class indexControl
{
    public function indexOp()
    {
        echo 'OK';
    }

    public function testOp()
    {
       $postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
        if( !$postStr ){
            $postStr = file_get_contents('php://input');
        }

	echo $postStr;die;
    }
}
