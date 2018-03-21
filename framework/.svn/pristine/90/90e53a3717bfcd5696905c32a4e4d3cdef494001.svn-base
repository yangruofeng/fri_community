<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/2/5
 * Time: 10:28
 */
class loan_productClass
{
    function __construct()
    {
    }


    public static function getAllProductList()
    {
        $m = new loan_productModel();
        $sql = "select * from loan_product where state='".loanProductStateEnum::ACTIVE."' ";
        $list = $m->reader->getRows($sql);
        return $list;
    }


    public static function getProductDetailInfo($product_id)
    {
        $m_loan_product = new loan_productModel();
        $product_info = $m_loan_product->find(array(
            'uid' => $product_id
        ));
        if( !$product_info ){
            return null;
        }

        // 处理JAVA不可解析的问题
        $product_info['product_description'] = rawurlencode($product_info['product_description']);
        $product_info['product_qualification'] = rawurlencode($product_info['product_qualification']);
        $product_info['product_feature'] = rawurlencode($product_info['product_feature']);
        $product_info['product_required'] = rawurlencode($product_info['product_required']);
        $product_info['product_notice'] = rawurlencode($product_info['product_notice']);

        $re = self::getProductDescribeRateList($product_id,1,100000);
        $rate_list = $re['list'];

        return array(
            'product_info' => $product_info,
            'rate_list' => $rate_list,
        );
    }

    public static function getProductRateList($product_id=0,$page_num,$page_size,$currency=null)
    {
        $page_num  = $page_num?:1;
        $page_size = $page_size?:100000;

        $r = new ormReader();
        $where = '';
        if( $currency ){
            $where  = " and currency='$currency' ";
        }
        $sql = "select * from loan_product_size_rate where product_id='$product_id' $where group by product_id,currency,loan_size_min,loan_size_max,min_term_days,max_term_days,interest_payment,interest_rate_period 
        order by loan_size_min asc,loan_size_max asc,interest_payment asc,max_term_days asc ";
        $re = $r->getPage($sql,$page_num,$page_size);

        $rows = $re->rows;

        $return = array(
            'total_num' => $re->count,
            'total_pages' => $re->pageCount,
            'current_page' => $page_num,
            'page_size' => $page_size,
            'list' => $rows
        );

        return $return;
    }

    public static function getProductDescribeRateList($product_id,$page_num,$page_size,$currency=null)
    {
        $page_num  = $page_num?:1;
        $page_size = $page_size?:100000;
        $rate_re = self::getProductRateList($product_id,$page_num,$page_size,$currency);
        $rate_list = $rate_re['list'];
        $return = array();
        foreach( $rate_list as $v ){

            // 日期转换
            if( $v['max_term_days'] >=30 ){
                $min = floor($v['min_term_days']/30);
                $max = ceil($v['max_term_days']/30);
                $v['loan_term_time'] = $min.'-'.$max.'M';

            }else{
                $v['loan_term_time'] = intval($v['min_term_days']).'-'.intval($v['max_term_days']).'D';
            }

            $v['interest_rate_des'] = $v['interest_rate'].'%';
            $v['operation_fees_des'] = $v['operation_fee'].'%';

            // 合并利率（interest+operate_fee）
            // todo 现在只有设置百分比
            $re = loan_baseClass::interestRateConversion($v['operation_fee'],$v['operation_fee_unit'],$v['interest_rate_unit']);
            $interest_sum = $v['interest_rate'];
            if( $re->STS ){
                $interest_sum += $re->DATA;
            }

            $v['total_rate_des_value'] = $interest_sum.'%';


            if($v['admin_fee_type'] == 1){
                $v['admin_fee_des_value'] = $v['admin_fee'];
            }else{
                $v['admin_fee_des_value'] = $v['admin_fee'].'%';
            }


            if($v['loan_fee_type'] == 1){
                $v['loan_fee_des_value'] = $v['loan_fee'];
            }else{
                $v['loan_fee_des_value'] = $v['loan_fee'].'%';
            }

            $item = $v;
            $item['repayment_type'] = $v['interest_payment'];
            $item['repayment_period'] = $v['interest_rate_period'];
            $item['loan_term_time'] = $v['loan_term_time'];
            $item['interest_rate_des_value'] = $v['interest_rate_des_value'];


            $return[] = $item;
        }

        $rate_re['list'] = $return;
        return $rate_re;
    }


    public static function getMinMonthlyRate($product_id)
    {
        $m_rate = new loan_product_size_rateModel();
        $rates = $m_rate->getRows(array(
            'product_id' => $product_id
        ));
        if( count($rates) < 1 ){
            return 0.00;
        }
        $rate_array = array();
        foreach( $rates as $rate ){
            $interest_rate = $rate['interest_rate'];
            $in_re = loan_baseClass::interestRateConversion($interest_rate,$rate['interest_rate_unit'],interestRatePeriodEnum::MONTHLY);
            if( $in_re->STS ){
                $interest_rate = $in_re->DATA;
            }

            $o_rate = $rate['operation_fee'];
            $o_re = loan_baseClass::interestRateConversion($o_rate,$rate['operation_fee_unit'],interestRatePeriodEnum::MONTHLY);
            if( $o_re->STS ){
                $o_rate = $o_re->DATA;
            }
            $total_rate = round($interest_rate+$o_rate,2);
            $rate_array[] = $total_rate;
        }
        asort($rate_array,SORT_NUMERIC );
        $min = reset($rate_array);
        return $min;
    }

}