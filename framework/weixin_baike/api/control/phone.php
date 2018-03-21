<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2017/11/1
 * Time: 14:07
 */
class phoneControl extends bank_apiControl
{

    /**
     * 获取发送短信验证码的冷却时间
     * @return result  时间是s
     */
    public function verifyCoolTimeOp()
    {

        $params = array_merge(array(),$_GET,$_POST);
        if( !$params['country_code'] || !$params['phone'] ){
            return new result(false,'Phone number error',null,errorCodesEnum::INVALID_PARAM);
        }

        $format_phone = tools::getFormatPhone($params['country_code'],$params['phone']);
        $contact_phone = $format_phone['contact_phone'];
        $m = new phone_verify_codeModel();
        $row = $m->orderBy('uid desc')->getRow(array(
            'phone_id' => $contact_phone
        ));
        if( !$row ){
            return new result(true,'success',0);  // 不在冷却中
        }

        $send_time = strtotime($row->create_time);
        $end_time = $send_time+phoneCodeCDEnum::CD;
        $c_time = time();
        if( $end_time > $c_time ){
            $cd = $end_time-$c_time;
            return new result(true,'success',$cd);
        }
        return new result(true,'success',0);

    }

    /**
     * 发送短信验证码,不处理验证问题，正常号码都可以发送，是否验证过在具体的业务处理
     * @return result
     */
    public function sendCodeOp()
    {
        // 做安全处理
       /* $rt = $this->checkAppSign();
        if( !$rt->STS ){
            return $rt;
        }*/

        $params = array_merge(array(),$_GET,$_POST);
        $country_code = $params['country_code'];
        $phone_number = $params['phone'];
        $format_phone = tools::getFormatPhone($country_code,$phone_number);
        $contact_phone = $format_phone['contact_phone'];
        // 检查合理性
        if( !isPhoneNumber($contact_phone) ){
            return new result(false,'Invalid phone',null,errorCodesEnum::INVALID_PHONE_NUMBER);
        }

        $m_phone_verify_code = M('phone_verify_code');


        // 是否在冷却时间内
        // todo 增加发送账户的冷却
        $verify_row = $m_phone_verify_code->orderBy('uid desc')->find(array(
            'phone_id' => $contact_phone,
        ));

        $last_time = 0;
        if( $verify_row && $verify_row['create_time'] ){
            $last_time = strtotime($verify_row['create_time']);
        }
        if( (time()-$last_time) < phoneCodeCDEnum::CD ){
            return new result(false,'Wrong time',null,errorCodesEnum::UNDER_COOL_TIME);
        }


        // 发送短信验证码
        $verify_code = mt_rand(100001,999999);

        $smsHandler = new smsHandler();
        $rt = $smsHandler->sendVerifyCode($contact_phone,$verify_code);
        if( !$rt->STS ){
            return new result(false,'Send code fail: '.$rt->MSG,null,errorCodesEnum::SMS_CODE_SEND_FAIL);
        }

        $sms_row = $rt->DATA;
        $new_row = $m_phone_verify_code->newRow();
        $new_row->phone_country = $country_code;
        $new_row->phone_id = $contact_phone;
        $new_row->verify_code = $verify_code;
        $new_row->create_time = Now();
        $new_row->sms_id = $sms_row->uid;
        $insert = $new_row->insert();
        if( !$insert->STS ){
            return new result(false,'Insert verify code fail',null,errorCodesEnum::DB_ERROR);
        }
        $verify_id = $insert->AUTO_ID;

        return new result(true,'success',array(
            'verify_id' => $verify_id,
            'phone_id' => $contact_phone
        ));


    }


    /**
     * 验证短信验证码
     * @return result
     */
    public function verifyCodeOp()
    {

        $params = array_merge(array(),$_GET,$_POST);
        $verify_id = $params['verify_id'];
        $verify_code = $params['verify_code'];
        if( !$verify_id || !$verify_code ){
            return new result(false,'Invalid param',null,errorCodesEnum::DATA_LACK);
        }
        $m_phone_verify_code = new phone_verify_codeModel();
        $row = $m_phone_verify_code->getRow($verify_id);
        if( !$row ){
            return new result(false,'No data',null,errorCodesEnum::UNEXPECTED_DATA);
        }

        if( $row->verify_code != $verify_code ){
            return new result(false,'Verify fail',null,errorCodesEnum::UNEXPECTED_DATA);
        }

        $create_time = strtotime($row->create_time);
        if( ($create_time+60*30) < time() ){  // 有效时间30分钟
            return new result(false,'Code expired',null,errorCodesEnum::DATA_EXPIRED);
        }

        $row->state = 1;
        $up = $row->update();
        if( !$up->STS ){
            return new result(false,'Update data fail',null,errorCodesEnum::DB_ERROR);
        }

        // 会员电话认证
        if( isset($params['is_certificate']) && $params['is_certificate'] == 1 ){
            $m_member = new memberModel();
            $member = $m_member->getRow(array(
                'phone_id' => $row->phone_id,
                'is_verify_phone' => 0
            ));
            if( $member ){
                $member->is_verify_phone = 1;
                $member->verify_phone_time = date('Y-m-d H:i:s');
                $update = $member->update();
                if( !$update->STS ){
                    return new result(false,'Certificate fail',null,errorCodesEnum::DB_ERROR);
                }

            }
        }
        return new result(true,'Verify success');

    }



    public function phoneIsRegisteredOp()
    {
        $params = array_merge(array(),$_GET,$_POST);
        $country_code = $params['country_code'];
        $phone_number = $params['phone'];
        $format_phone = tools::getFormatPhone($country_code,$phone_number);
        $contact_phone = $format_phone['contact_phone'];

        // 检查合理性
        if( !isPhoneNumber($contact_phone) ){
            return new result(false,'Invalid phone',null,errorCodesEnum::INVALID_PARAM);
        }

        // 判断是否被其他member注册过
        $is_registered = 0;
        $m_member = new memberModel();
        $row = $m_member->getRow(array(
            'phone_id' => $contact_phone,
        ));
        if( $row ){
            $is_registered = 1;
        }
        return new result(true,'success',array(
            'is_registered' => $is_registered
        ));
    }

}