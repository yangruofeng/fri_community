<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/3/14
 * Time: 15:58
 */
abstract class bizBaseClass
{
    public $biz_code;  // 统一业务code格式
    public $scene_code;  // 统一场景code格式

    abstract function checkBizOpen($scene_code);  // 统一格式检查业务功能是否开启
}