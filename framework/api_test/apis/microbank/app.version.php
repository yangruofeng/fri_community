<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2017/11/13
 * Time: 17:32
 */

class appVersionApiDocument extends apiDocument
{
    public function __construct()
    {
        $this->name = "App Version";
        $this->description = "获取APP的最新版本信息";
        $this->url = C("bank_api_url") . "/app.version.php";

        $this->parameters = array();
        $this->parameters[] = new apiParameter('app_name','APP名称','smarithiesak-member',true);
        $this->parameters[] = new apiParameter('version','客户端版本','1.0.0',false);

        $this->return = array(
            'STS' => 'API结果状态，true/false',
            'CODE' => 'API结果代码',
            'MSG' => '错误消息（调试情况下才会出现）',
            'DATA' => array(

                '返回数据' => '最新版本信息',
                'app_name' => 'bank_member_app',
                'version' => '2.5.3',
                'download_url' => '下载地址,bank.mekong24.com/data/bank_member_app/1.0.0/bank_member_app.apk',
                'is_required' => '是否必须下载，是1 否0',
                'remark' => '备注',
                'creator_id' => '发布人id',
                'creator_name' => '发布人名称',
                'create_time' => '发布时间',
                /*'newest_version' => array(
                    '返回数据' => '最新版本信息',
                    'app_name' => 'bank_member_app',
                    'version' => '2.5.3',
                    'download_url' => '下载地址,bank.mekong24.com/data/bank_member_app/1.0.0/bank_member_app.apk',
                    'is_required' => '是否必须下载，是1 否0',
                    'remark' => '备注',
                    'creator_id' => '发布人id',
                    'creator_name' => '发布人名称',
                    'create_time' => '发布时间'
                ),
                'update_version' => array(
                    '返回数据' => '必须更新版本信息',
                    'app_name' => 'bank_member_app',
                    'version' => '2.4.1',
                    'download_url' => '下载地址,bank.mekong24.com/data/bank_member_app/1.0.0/bank_member_app.apk',
                    'is_required' => '是否必须下载，是1 否0',
                    'remark' => '备注',
                    'creator_id' => '发布人id',
                    'creator_name' => '发布人名称',
                    'create_time' => '发布时间'
                )*/

            )
        );

    }
}