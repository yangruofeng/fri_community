<?php

/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2017/11/1
 * Time: 10:44
 */
class helpControl extends bank_apiControl
{

    public function getHelpCategoryOp()
    {
        $category = (new helpCategoryEnum())->Dictionary();
        return new result(true, '', $category);
    }

    public function getHelpListOp()
    {
        $params = array_merge(array(), $_GET, $_POST);
        $m_common_cms = M('common_cms');

        $re = $m_common_cms->getHelpList($params);
        return $re;
    }

    public function helpDetailOp()
    {
        $params = array_merge(array(), $_GET, $_POST);
        $m_common_cms = M('common_cms');
        $uid = intval($params['uid']);
        $re = $m_common_cms->getHelpDetail($uid);
        return $re;
    }

}
