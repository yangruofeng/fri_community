<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2017/11/6
 * Time: 17:24
 */

class loan_productControl extends bank_apiControl
{


    /**
     * 取得贷款产品列表
     * @return result
     */
    public function getProductListOp()
    {

        // 产品少，不分页了
        $param = array_merge(array(),$_GET,$_POST);

        $m = new loan_productModel();
        $list = $m->field('uid,product_code,product_name,product_description')->orderBy('uid asc')->getRows(array(
            'state' => loanProductStateEnum::ACTIVE
        ));
        $return = array();
        foreach( $list as $v){
            // 计算产品最低月利率
            $min = loan_productClass::getMinMonthlyRate($v['uid']);
            $v['min_rate'] = $min;
            $v['min_rate_desc'] = $min.'%';
            $return[] = $v;
        }

        return new result(true,'success',$return);

    }

    public function getProductDetailInfoOp()
    {
        $param = array_merge(array(),$_GET,$_POST);
        $product_id = $param['product_id'];
        $product_detail = loan_productClass::getProductDetailInfo($product_id);
        if( !$product_detail ){
            return new result(false,'No product',null,errorCodesEnum::NO_LOAN_PRODUCT);
        }
        return new result(true,'success',$product_detail);
    }

    public function getProductDesRateListOp()
    {
        $param = array_merge(array(),$_GET,$_POST);
        $product_id = $param['product_id'];
        $currency = $param['currency'];
        $page_num = $param['page_num'];
        $page_size = $param['page_size'];
        $re = loan_productClass::getProductDescribeRateList($product_id,$page_num,$page_size,$currency);
        return new result(true,'success',$re);
    }



}