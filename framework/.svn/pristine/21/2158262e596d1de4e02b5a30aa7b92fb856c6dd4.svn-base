<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/2/7
 * Time: 11:02
 */
class officerSubmitCertResidentbookApiDocument extends apiDocument
{
    public function __construct()
    {
        $this->name = "Resident book cert";
        $this->description = "居住证认证";
        $this->url = C("bank_api_url") . "/officer.submit.cert.residentbook.php";

        $this->parameters = array();
        $this->parameters[]= new apiParameter("member_id", "会员id", 1, true);
        $this->parameters[]= new apiParameter("front_photo", "正面照，文件流", '', true);
        $this->parameters[]= new apiParameter("back_photo", "背面照，文件流", '', true);
        $this->parameters[]= new apiParameter("cert_id", "如果是编辑，需要传记录id", null);
        $this->parameters[]= new apiParameter("token", "token令牌", '', true);


        $this->return = array(
            'STS' => 'API结果状态，true/false',
            'CODE' => 'API结果代码',
            'MSG' => '错误消息（调试情况下才会出现）',
            'DATA' => array(
                'cert_result' => '基本信息',
                'extend_info' => '扩展信息'
            )
        );

    }
}