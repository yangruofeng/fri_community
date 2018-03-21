<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2017/11/1
 * Time: 9:56
 */

$config['db_conf']=array(
    "db_loan"=>array(
        "db_type"=>"mysql",
        "db_host"=>"127.0.0.1",
        "db_user"=>"demo",
        "db_pwd"=>"demo-2017",
        "db_name"=>"weixin_baike",
        "db_port"=>3306
    )
);

// 暂时使用
$config['session'] = array(
    'save_handler' => 'files',
    'save_path' => BASE_DATA_PATH.'/session'
);

$config['weixin_url_init'] = 0;

$config['debug']=true;
$config['site_root'] = 'http://www.iruofeng.cn/fri_community/framework';
$config['global_resource_site_url'] = "http://www.iruofeng.cn/fri_community/framework/resource";
$config['project_site_url'] = "http://www.iruofeng.cn/fri_community/weixin_baike";
$config['entry_api_url'] = "http://www.iruofeng.cn/fri_community/weixin_baike/api/v1";



