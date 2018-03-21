<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2017/11/13
 * Time: 17:26
 */
class bank_appControl extends bank_apiControl
{

    /**
     * 获取APP的最新版本信息
     * @return result
     */
    public function getVersionOp()
    {
        $params = array_merge(array(),$_GET,$_POST);
        $app_name = $params['app_name'];
        $version = $params['version'];
        $version = $version?:'';
        $download_url = getConf('app_download_url');

        if( !$download_url ){
            return new result(false,'Download url config error',null,errorCodesEnum::CONFIG_ERROR);
        }

        $download_url = rtrim($download_url,'/');

        if( !$app_name ){
            return new result(false,'Lack of param',null,errorCodesEnum::DATA_LACK);
        }

        $m = new common_app_versionModel();
        $newest_version = $m->orderBy('uid desc')->getRow(array(
            'app_name' => $app_name
        ));

        if( $newest_version ){
            $newest_version->download_url = $download_url.'/'.$newest_version->download_url;
        }else{
            $newest_version = null;
        }

        $update_version = $m->orderBy('uid desc')->getRow(array(
            'app_name' => $app_name,
            'is_required' => 1
        ));

        // 没有需要更新的版本，返回最新版本
        if( !$update_version ){
            return new result(true,'success',$newest_version);
        }

        // 版本低于需要更新的版本，强制更新最新版本
        if( $version < $update_version->version ){
            $newest_version->is_required = 1;
            return new result(true,'success',$newest_version);
        }

        // 版本高于需要更新的版本，选择性更新到最新版本
        return new result(true,'success',$newest_version);

        /*return new result(true,'success',array(
            'newest_version' => $newest_version,
            'update_version' => $update_version
        ));*/
    }
}