<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/3/13
 * Time: 10:14
 */

/** 储蓄账户操作类
 * Class savingsControl
 */
class savingsControl extends bank_apiControl
{

    public function requestWithdrawOp()
    {

        $re = $this->checkToken();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge($_GET,$_POST);
        $member_id = $params['member_id'];
        $amount = round($params['amount'],2);
        $currency = $params['currency'];
        $handler_id = $params['handler_id'];
        $trading_password = $params['trading_password'];
        $remark = $params['remark'];
        if( !$member_id || $amount<=0 || !$currency ||  !$handler_id ){
            return new result(false,'Invalid param',null,errorCodesEnum::INVALID_PARAM);
        }
        $m_member = new memberModel();
        $member = $m_member->getRow($member_id);
        if( !$member ){
            return new result(false,'Invalid param',null,errorCodesEnum::INVALID_PARAM);
        }
        $m_handler = new member_account_handlerModel();
        $handler = $m_handler->getRow($handler_id);
        if( !$handler ){
            return new result(false,'Invalid param',null,errorCodesEnum::INVALID_PARAM);
        }
        $memberObject = new objectMemberClass($member_id);
        $re = $memberObject->savingsRequestWithdraw($amount,$currency,$handler_id,$remark,$trading_password);
        return $re;

    }

    public function requestDepositOp()
    {
        $re = $this->checkToken();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge($_GET,$_POST);
        $member_id = $params['member_id'];
        $amount = round($params['amount'],2);
        $currency = $params['currency'];
        $handler_id = $params['handler_id'];
        $remark = $params['remark'];
        if( !$member_id || $amount<=0 || !$currency ||  !$handler_id ){
            return new result(false,'Invalid param',null,errorCodesEnum::INVALID_PARAM);
        }
        $m_member = new memberModel();
        $member = $m_member->getRow($member_id);
        if( !$member ){
            return new result(false,'Invalid param',null,errorCodesEnum::INVALID_PARAM);
        }
        $memberObject = new objectMemberClass($member_id);
        $re = $memberObject->savingsRequestDeposit($amount,$currency,$handler_id,$remark);
        return $re;
    }


    public function getMemberBillListOp()
    {

        $re = $this->checkToken();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge($_GET,$_POST);
        $member_id = $params['member_id'];
        $currency = $params['currency'];
        $page_num = $params['page_num']?:1;
        $page_size = $params['page_size']?:100000;

        $m_member = new  memberModel();
        $member = $m_member->getRow($member_id);
        if( !$member ){
            return new result(false,'Member not exist',null,errorCodesEnum::MEMBER_NOT_EXIST);
        }

        $passbook = passbookClass::getSavingsPassbookOfMemberGUID($member->obj_guid);
        $book_id = $passbook->getBookId();

        $where = " acc.book_id='$book_id' ";
        if( $currency ){
            $where .= " and acc.currency='$currency' ";
        }

        if( $params['min_amount'] ){
            $min_amount = round($params['min_amount'],2);
            $where .= " and ( (af.credit+af.debit) >= '$min_amount' ) ";
        }

        if( $params['max_amount'] ){
            $max_amount = round($params['max_amount'],2);
            $where .= " and ( (af.credit+af.debit) <= '$max_amount' ) ";
        }

        if( $params['start_date'] ){
            $start_date = date('Y-m-d 00:00:00',strtotime($params['start_date']));
            $where .= " and af.create_time>='$start_date' ";
        }

        if( $params['end_date'] ){
            $end_date = date('Y-m-d 23:59:59',strtotime($params['end_date']));
            $where .= " and af.create_time<='$end_date' ";
        }


        $r = new ormReader();
        $sql = "select af.*,date_format(af.create_time,'%Y-%m') date_month,acc.currency,t.category,t.trading_type,t.subject from passbook_account_flow af left join passbook_trading t on t.uid=af.trade_id  
        left join passbook_account acc on acc.uid=af.account_id where  $where  order by af.create_time desc ";

        $page = $r->getPage($sql,$page_num,$page_size);
        $list = $page->rows;

        // 统计各月的合计,去除分页的bug
        $sum_sql = "select sum(af.credit) total_credit,sum(af.debit) total_debit,date_format(af.create_time,'%Y-%m') date_month from passbook_account_flow af left join passbook_trading t on t.uid=af.trade_id  
        left join passbook_account acc on acc.uid=af.account_id where  $where group by date_format(af.create_time,'%Y-%m')  ";
        $month_list = $r->getRows($sum_sql);
        $month_total = array();
        foreach( $month_list as $v ){
            $month_total[$v['date_month']] = $v;
        }


        $f_list = array();
        $trading_type_lang = enum_langClass::getPassbookTradingTypeLang();

        foreach( $list as $item ){

            $item['trading_type'] = ($trading_type_lang[$item['trading_type']])?:$item['trading_type'];

            $month = $item['date_month'];

            if( $f_list[$month] ){
                    $f_list[$month]['summary']['credit'] += $item['credit'];
                    $f_list[$month]['summary']['debit'] += $item['debit'];
                    $f_list[$month]['list'][] = $item;
            }else{
                $f_list[$month] = array(
                    'month' => $month,
                    'summary' => array(
                        'credit' => $item['credit'],
                        'debit' => $item['debit']
                    ),
                    'list' => array($item)
                );
            }
        }

        foreach( $f_list as $month=>$v ){
            $f_list[$month]['summary'] = array(
                'credit' => $month_total[$month]['total_credit'],
                'debit' => $month_total[$month]['total_debit']
            );
        }


        // 去掉键值
        $f_list = array_values($f_list);


        $data = array(
            'total_num' => $page->count,
            'total_pages' => $page->pageCount,
            'current_page' => $page_num,
            'page_size' => $page_size,
            'list' => $f_list
        );

        return new result(true,'success',$data);

    }

    public function getBillDetailOp()
    {
        $re = $this->checkToken();
        if( !$re->STS ){
            return $re;
        }
        $params = array_merge($_GET,$_POST);
        $bill_id = $params['bill_id'];

        $m = new passbook_accountModel();

        $sql = "select f.*,t.category,t.trading_type,t.subject from passbook_account_flow f left join passbook_trading t on t.uid=f.trade_id 
        where f.uid='$bill_id' ";

        $flow_info = $m->reader->getRow($sql);
        if( !$flow_info ){
            return new result(false,'No bill',null,errorCodesEnum::BILL_NOT_EXIST);
        }

        $trading_type_lang = enum_langClass::getPassbookTradingTypeLang();
        $flow_info['trading_type'] = ($trading_type_lang[$flow_info['trading_type']])?:$flow_info['trading_type'];

        $account_info = $m->getRow($flow_info['account_id']);

        return new result(true,'success',array(
            'bill_detail' => $flow_info,
            'account_info' => $account_info
        ));
    }





}