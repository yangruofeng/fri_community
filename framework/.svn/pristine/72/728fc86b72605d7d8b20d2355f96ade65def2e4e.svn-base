<?php

/**
 * Created by PhpStorm.
 * User: Seven
 * Date: 2018/1/4
 * Time: 16:06
 */
class editorControl extends baseControl
{
    public function __construct()
    {
        parent::__construct();
        Tpl::setLayout("empty_layout");
        Tpl::output("html_title", "Editor");
        Tpl::setDir("editor");
    }

    /**
     * 帮助文档
     */
    public function helpOp()
    {
        $condition = array(
            "date_start" => date("Y-m-d", strtotime(dateAdd(Now(), -30))),
            "date_end" => date('Y-m-d')
        );
        Tpl::output("condition", $condition);
        Tpl::showpage('help');
    }

    /**
     * 获取帮助文档列表
     * @param $p
     * @return array
     */
    public function getHelpListOp($p)
    {
        $search_text = trim($p['search_text']);
        $type = intval($p['type']);
        $d1 = $p['date_start'];
        $d2 = dateAdd($p['date_end'], 1);
        $r = new ormReader();
        $sql = "SELECT * FROM common_cms WHERE (create_time BETWEEN '" . $d1 . "' AND '" . $d2 . "')";
        if ($search_text) {
            $sql .= ' AND (help_title like "%' . $search_text . '%")';
        }
        if ($type == 1) {
            $sql .= " AND is_system = 0";
        } elseif ($type == 2) {
            $sql .= " AND is_system = 1";
        }
        $sql .= ' ORDER BY sort DESC,uid DESC';
        $pageNumber = intval($p['pageNumber']) ?: 1;
        $pageSize = intval($p['pageSize']) ?: 20;
        $data = $r->getPage($sql, $pageNumber, $pageSize);
        $rows = $data->rows;
        $total = $data->count;
        $pageTotal = $data->pageCount;

        return array(
            "sts" => true,
            "data" => $rows,
            "total" => $total,
            "pageNumber" => $pageNumber,
            "pageTotal" => $pageTotal,
            "pageSize" => $pageSize
        );
    }

    public function editHelpOp()
    {
        $p = array_merge(array(), $_GET, $_POST);
        $uid = intval($_GET['uid']);
        $m_common_cms = M('common_cms');
        $help = $m_common_cms->find(array('uid' => $uid));
        if (!$help) {
            showMessage('Invalid Id!');
        }

        if ($p['form_submit'] == 'ok') {
            $update = array(
                'uid' => $uid,
                'category' => $p['category'],
                'help_title' => trim($p['help_title']),
                'help_content' => $p['help_content'],
                'state' => intval($p['is_show']) ? 100 : 10,
                'sort' => intval($p['sort']),
                'handler_id' => $this->user_id,
                'handler_name' => $this->user_name,
                'handle_time' => Now(),
            );
            $rt = $m_common_cms->update($update);
            if ($rt->STS) {
                showMessage('Edit Successful!', getUrl('editor', 'help', array(), false, BACK_OFFICE_SITE_URL));
            } else {
                showMessage($rt->MSG);
            }
        } else {
            $help_category = (new helpCategoryEnum())->Dictionary();
            Tpl::output('help_category', $help_category);
            Tpl::output('help', $help);
            Tpl::showpage('help.edit');
        }
    }

    public function addSystemHelpOp()
    {
        $p = array_merge(array(), $_GET, $_POST);
        if ($p['form_submit'] == 'ok') {
            $m_common_cms = M('common_cms');
            $category = $p['category'];
            $help_title = trim($p['help_title']);
            $help_content = $p['help_content'];
            $state = intval($p['is_show']) ? 100 : 10;
            $sort = intval($p['sort']);
            if (!$category || !$help_title || !$help_content) {
                showMessage('Invalid Param!');
            }

            $row = $m_common_cms->newRow();
            $row->category = $category;
            $row->help_title = $help_title;
            $row->help_content = $help_content;
            $row->handler_id = $this->user_id;
            $row->handler_name = $this->user_name;
            $row->create_time = Now();
            $row->handle_time = Now();
            $row->state = $state;
            $row->sort = $sort;
            $row->is_system = 1;
            $rt = $row->insert();

            if ($rt->STS) {
                showMessage('Add Successful!', getUrl('editor', 'help', array(), false, BACK_OFFICE_SITE_URL));
            } else {
                unset($p['form_submit']);
                showMessage($rt->MSG, getUrl('editor', 'addSystemHelp', $p, false, BACK_OFFICE_SITE_URL));
            }
        } else {
            $help_category = (new helpCategoryEnum())->Dictionary();
            Tpl::output('help_category', $help_category);
            Tpl::showpage('help.add');
        }
    }
}