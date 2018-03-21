<?php

class logApiDocument extends apiDocument {
    public function __construct()
    {
        $this->name = "log";
        $this->description = "记录页面行为日志";
        $this->url = C("entry_api_url") . "/log.php";

        $this->parameters = array();
        $this->parameters[]= new apiParameter("cookieId", "用户Cookie ID", md5('test'), true);
        $this->parameters[]= new apiParameter("url", "行为发生所在页面的url", "http://www.test.com/test.php?t=test", true);
        $this->parameters[]= new apiParameter("refurl", "当前页面的来源url", "http://www.test.com/test2.php?t=test2");
        $this->parameters[]= new apiParameter("ua", "请求头中的User-Agent", "Mozilla/5.0 (Windows NT 10.0; WOW64; rv:55.0) Gecko/20100101 Firefox/55.0");
        $this->parameters[]= new apiParameter("screenX", "用户当前设备的屏幕宽度", "1280");
        $this->parameters[]= new apiParameter("screenY", "用户当前设备的屏幕高度", "720");
        $this->parameters[]= new apiParameter("os", "用户当前设备的操作系统", "Win32");
        $this->parameters[]= new apiParameter("browser", "用户使用的浏览器", "Firefox");
        $this->parameters[]= new apiParameter("browserLang", "用户使用的语言", "zh-CN");
        $this->parameters[]= new apiParameter("title", "当前页面的标题", "Lucky Dig");
        $this->parameters[]= new apiParameter("ch", "辅助字段，一般用来标记行为产生元素", "page");
        $this->parameters[]= new apiParameter("ch1", "辅助字段1，一般用来记录行为类型", "init");
        $this->parameters[]= new apiParameter("ch2", "辅助字段2，一般用来辅助标记行为产生元素或记录行为影响的值", "");
        $this->parameters[]= new apiParameter("ch3", "辅助字段3，一般用来记录行为影响的值", "");
    }
}