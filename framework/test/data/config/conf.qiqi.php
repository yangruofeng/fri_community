<?php
$config['db_conf']=array(
    "db_loan"=>array(
        "db_type"=>"mysql",
        "db_host"=>"localhost",
        "db_user"=>"root",
        "db_pwd"=>"",
        "db_name"=>"pos",
        "db_port"=>3306
    )
);

$config['global_resource_site_url'] = "http://localhost/microbank/resource";
$config['project_resource_site_url'] = "http://localhost/microbank/entry/resource";
$config['debug']=true;

$config['entry_api_url'] = "http://localhost/microbank/entry/api/v1";
