<?php

class userControl extends baseControl
{
    public function __construct()
    {
        parent::__construct();
        Tpl::setLayout("empty_layout");
        Tpl::output("html_title", "User List");
        Tpl::setDir("user");
    }

    /**
     * region list
     */
    public function roleOp()
    {
        Tpl::showPage("role");
    }

    /**
     * role列表
     * @param $p
     * @return array
     */
    public function getRoleListOp($p)
    {
        $search_text = trim($p['search_text']);
        $r = new ormReader();
        $sql = "SELECT * FROM um_role";
        if ($search_text) {
            $sql .= " WHERE role_name LIKE '%" . $search_text . "%'";
        }
        $pageNumber = intval($p['pageNumber']) ?: 1;
        $pageSize = intval($p['pageSize']) ?: 20;
        $data = $r->getPage($sql, $pageNumber, $pageSize);
        $rows = $data->rows;
        $total = $data->count;
        $pageTotal = $data->pageCount;

        if ($rows) {
            $role_ids = implode(',', array_column($rows, 'uid'));
            $sql = "SELECT role_id,auth_group_id,auth_type FROM um_role_group WHERE role_id IN ($role_ids)";
            $auth_group = $r->getRows($sql);
            $auth_group_back_office = array();
            $auth_group_counter = array();
            foreach ($auth_group as $val) {
                if ($val['auth_type'] == authTypeEnum::BACK_OFFICE) {
                    $auth_group_back_office[$val['role_id']][] = $val['auth_group_id'];
                }
                if ($val['auth_type'] == authTypeEnum::COUNTER) {
                    $auth_group_counter[$val['role_id']][] = $val['auth_group_id'];
                }
            }
            foreach ($rows as $key => $row) {
                $row['auth_group_back_office'] = $auth_group_back_office[$row['uid']];
                $row['auth_group_counter'] = $auth_group_counter[$row['uid']];
                $rows[$key] = $row;
            }
        }

        return array(
            "sts" => true,
            "data" => $rows,
            "total" => $total,
            "pageNumber" => $pageNumber,
            "pageTotal" => $pageTotal,
            "pageSize" => $pageSize,
        );
    }

    /**
     * role详情
     */
    public function showRoleDetailOp()
    {
        $uid = intval($_GET['uid']);
        $class_role = new role();
        $rt = $class_role->getRoleInfo($uid);
        if (!$rt->STS) {
            showMessage($rt->MSG, getUrl('user', 'role', array(), false, BACK_OFFICE_SITE_URL));
        }
        $role_info = $rt->DATA;
        Tpl::output('role_info', $role_info);

        $auth_list = $this->getAuthList();
        Tpl::output('auth_group_back_office', $auth_list['auth_group_back_office']);
        Tpl::output('auth_group_counter', $auth_list['auth_group_counter']);

        Tpl::showPage("role.detail");
    }

    /**
     * 添加role
     */
    public function addRoleOp()
    {
        $p = array_merge(array(), $_GET, $_POST);
        if ($p['form_submit'] == 'ok') {
            $class_role = new role();
            $p['creator_id'] = $this->user_id;
            $p['creator_name'] = $this->user_name;
            $rt = $class_role->addRole($p);
            if ($rt->STS) {
                showMessage($rt->MSG, getUrl('user', 'role', array(), false, BACK_OFFICE_SITE_URL));
            } else {
                unset($p['form_submit']);
                showMessage($rt->MSG, getUrl('user', 'addRole', $p, false, BACK_OFFICE_SITE_URL));
            }
        } else {
            $auth_list = $this->getAuthList();
            Tpl::output('auth_group_back_office', $auth_list['auth_group_back_office']);
            Tpl::output('auth_group_counter', $auth_list['auth_group_counter']);
            Tpl::showPage("role.add");
        }
    }

    /**
     * 获取auth列表
     * @return array
     */
    private function getAuthList()
    {
        $define_auth_group = authBase::getAllAuthGroup();
        $define_auth_group_counter = $define_auth_group['counter'];
        $define_auth_group_back_office = $define_auth_group['back_office'];
        $auth_group_back_office = array();
        foreach ($define_auth_group_back_office as $key => $r) {
            $role = authBase::getAuthGroup($r, authTypeEnum::BACK_OFFICE);
            if (!$role) continue;
            $auth_group_list = $role->getAuthList();
            $auth_group_key = $role->getGroupKey();
            $auth_group_back_office[$auth_group_key] = $auth_group_list;
        }

        $auth_group_counter = array();
        foreach ($define_auth_group_counter as $key => $r) {
            $role = authBase::getAuthGroup($r, authTypeEnum::COUNTER);
            if (!$role) continue;
            $auth_group_list = $role->getAuthList();
            $auth_group_key = $role->getGroupKey();
            $auth_group_counter[$auth_group_key] = $auth_group_list;
        }
        return array('auth_group_back_office' => $auth_group_back_office, 'auth_group_counter' => $auth_group_counter);
    }

    /**
     * 编辑role
     */
    public function editRoleOp()
    {
        $p = array_merge(array(), $_GET, $_POST);
        $class_role = new role();
        if ($p['form_submit'] == 'ok') {
            $rt = $class_role->editRole($p);
            if ($rt->STS) {
                showMessage($rt->MSG, getUrl('user', 'role', array(), false, BACK_OFFICE_SITE_URL));
            } else {
                unset($p['form_submit']);
                showMessage($rt->MSG, getUrl('user', 'addRole', $p, false, BACK_OFFICE_SITE_URL));
            }
        } else {
            $rt = $class_role->getRoleInfo(intval($_GET['uid']));
            if (!$rt->STS) {
                showMessage($rt->MSG, getUrl('user', 'role', array(), false, BACK_OFFICE_SITE_URL));
            }
            $role_info = $rt->DATA;
            Tpl::output('role_info', $role_info);

            $auth_list = $this->getAuthList();
            Tpl::output('auth_group_back_office', $auth_list['auth_group_back_office']);
            Tpl::output('auth_group_counter', $auth_list['auth_group_counter']);

            Tpl::showPage("role.edit");
        }
    }

    /**
     * role列表
     * @param $p
     * @return array
     */
    public function getUserListByRoleOp($p)
    {
        $r = new ormReader();
        $sql = "SELECT uu.*,sb.branch_name,sd.depart_name FROM um_user uu"
            . " INNER JOIN um_user_role uur ON uu.uid = uur.user_id"
            . " LEFT JOIN site_depart sd ON uu.depart_id = sd.uid"
            . " LEFT JOIN site_branch sb ON sd.branch_id = sb.uid"
            . " WHERE 1 = 1";
        $role_id = intval($p['role_id']);
        if ($role_id) {
            $sql .= " AND uur.role_id =  $role_id";
        }

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
            "pageSize" => $pageSize,
        );
    }

    /**
     * 删除role
     */
    public function deleteRoleOp()
    {
        $uid = intval($_GET['uid']);
        $class_role = new role();
        $rt = $class_role->deleteRole($uid);
        showMessage($rt->MSG);
    }

    /**
     * user list
     */
    public function userOp()
    {
        Tpl::showPage("user");
    }

    /**
     * role列表
     * @param $p
     * @return array
     */
    public function getUserListOp($p)
    {
        $r = new ormReader();
        $sql = "SELECT uu.*,sb.branch_name,sd.depart_name FROM um_user uu"
            . " LEFT JOIN site_depart sd ON uu.depart_id = sd.uid"
            . " LEFT JOIN site_branch sb ON sd.branch_id = sb.uid"
            . " WHERE 1 = 1";
        $search_text = trim($p['search_text']);
        if ($search_text) {
            $sql .= " AND uu.user_code LIKE '%" . $search_text . "%' OR uu.user_name LIKE '%" . $search_text . "%'";
        }
        $sql .= " ORDER by uu.uid DESC";
        $pageNumber = intval($p['pageNumber']) ?: 1;
        $pageSize = intval($p['pageSize']) ?: 20;
        $data = $r->getPage($sql, $pageNumber, $pageSize);
        $rows = $data->rows;
        $total = $data->count;
        $pageTotal = $data->pageCount;

        if ($rows) {
            $user_ids = implode(',', array_column($rows, 'uid'));
            $sql = "SELECT uur.user_id,ur.role_name FROM um_user_role uur LEFT JOIN um_role ur ON uur.role_id = ur.uid WHERE user_id IN ($user_ids)";
            $role_arr = $r->getRows($sql);
            $role_arr_new = array();
            foreach ($role_arr as $val) {
                $role_arr_new[$val['user_id']][] = $val['role_name'];
            }
            foreach ($rows as $key => $row) {
                $row['role_group'] = $role_arr_new[$row['uid']];
                unset($row['password']);
                $rows[$key] = $row;
            }
        }

        return array(
            "sts" => true,
            "data" => $rows,
            "total" => $total,
            "pageNumber" => $pageNumber,
            "pageTotal" => $pageTotal,
            "pageSize" => $pageSize,
        );
    }

    /**
     * 添加user
     */
    public function addUserOp()
    {
        $p = array_merge(array(), $_GET, $_POST);
        if ($p['form_submit'] == 'ok') {
            $class_user = new userClass();
            $p['creator_id'] = $this->user_id;
            $p['creator_name'] = $this->user_name;
            $rt = $class_user->addUser($p);
            if ($rt->STS) {
                showMessage($rt->MSG, getUrl('user', 'user', array(), false, BACK_OFFICE_SITE_URL));
            } else {
                unset($p['form_submit']);
                showMessage($rt->MSG, getUrl('user', 'addUser', $p, false, BACK_OFFICE_SITE_URL));
            }
        } else {
            $auth_list = $this->getAuthList();
            Tpl::output('auth_group_back_office', $auth_list['auth_group_back_office']);
            Tpl::output('auth_group_counter', $auth_list['auth_group_counter']);

            $class_role = new role();
            $rt = $class_role->getRoleList();
            if (!$rt->STS) {
                showMessage($rt->MSG, getUrl('user', 'role', array(), false, BACK_OFFICE_SITE_URL));
            }
            $role_list = $rt->DATA;
            Tpl::output('role_list', $role_list);

            $m_site_branch = M('site_branch');
            $branch_list = $m_site_branch->select(array('status' => 1));
            Tpl::output('branch_list', $branch_list);

            $user_position = (new userPositionEnum)->Dictionary();
            Tpl::output('user_position', $user_position);

            Tpl::showPage("user.add");
        }
    }

    /**
     * 编辑user
     */
    public function editUserOp()
    {
        $p = array_merge(array(), $_GET, $_POST);
        $class_user = new userClass();
        if ($p['form_submit'] == 'ok') {
            $rt = $class_user->editUser($p);
            if ($rt->STS) {
                showMessage($rt->MSG, getUrl('user', 'user', array(), false, BACK_OFFICE_SITE_URL));
            } else {
                unset($p['form_submit']);
                showMessage($rt->MSG, getUrl('user', 'editUser', $p, false, BACK_OFFICE_SITE_URL));
            }
        } else {
            $auth_list = $this->getAuthList();
            Tpl::output('auth_group_back_office', $auth_list['auth_group_back_office']);
            Tpl::output('auth_group_counter', $auth_list['auth_group_counter']);

            $class_role = new role();
            $rt = $class_role->getRoleList();
            if (!$rt->STS) {
                showMessage($rt->MSG, getUrl('user', 'role', array(), false, BACK_OFFICE_SITE_URL));
            }
            $role_list = $rt->DATA;
            Tpl::output('role_list', $role_list);

            $rt = $class_user->getUserInfo(intval($_GET['uid']));
            if (!$rt->STS) {
                showMessage($rt->MSG, getUrl('user', 'role', array(), false, BACK_OFFICE_SITE_URL));
            }
            Tpl::output('user_info', $rt->DATA);

            $m_site_branch = M('site_branch');
            $branch_list = $m_site_branch->select(array('status' => 1));
            Tpl::output('branch_list', $branch_list);

            $m_site_depart = M('site_depart');
            $depart_list = $m_site_depart->select(array('branch_id' => $rt->DATA['branch_id']));
            Tpl::output('depart_list', $depart_list);

            $user_position = (new userPositionEnum)->Dictionary();
            Tpl::output('user_position', $user_position);

            Tpl::showPage("user.edit");
        }
    }

    /**
     * 部门列表
     * @param $p
     * @return array
     */
    public function getDepartListOp($p)
    {
        $branch_id = intval($p['branch_id']);
        $m_site_depart = M('site_depart');
        $depart_list = $m_site_depart->getRows(array('branch_id' => $branch_id));
        return array(
            "sts" => true,
            "data" => $depart_list
        );
    }

    /**
     * 删除user
     */
    public function deleteUserOp()
    {
        $uid = intval($_GET['uid']);
        $class_user = new userClass();
        $rt = $class_user->deleteUser($uid);
        showMessage($rt->MSG);
    }

    /**
     * user详情
     */
    public function showUserDetailOp()
    {
        $uid = intval($_GET['uid']);

        $auth_list = $this->getAuthList();
        $auth_group_back_office = $auth_list['auth_group_back_office'];
        $auth_group_counter = $auth_list['auth_group_counter'];

        $class_user = new userClass();
        $rt = $class_user->getUserInfo($uid);
        if (!$rt->STS) {
            showMessage($rt->MSG);
        }
        $user_info = $rt->DATA;

        $class_role = new role();
        $rt = $class_role->getRoleList();
        if (!$rt->STS) {
            showMessage($rt->MSG, getUrl('user', 'role', array(), false, BACK_OFFICE_SITE_URL));
        }
        $role_list = $rt->DATA;

        Tpl::output('user_info', $user_info);
        Tpl::output('role_list', $role_list);

        $allow_auth_back_office = array();
        $limit_auth_back_office = array();
        foreach ($auth_group_back_office as $group) {
            foreach ($group as $auth) {
                if (in_array($auth, $user_info['back_office_auth'])) {
                    $allow_auth_back_office[] = L('auth_' . strtolower($auth));
                } else {
                    $limit_auth_back_office[] = L('auth_' . strtolower($auth));
                }
            }
        }
        $allow_auth_counter = array();
        $limit_auth_counter = array();
        foreach ($auth_group_counter as $group) {
            foreach ($group as $auth) {
                if (in_array($auth, $user_info['counter_auth'])) {
                    $allow_auth_counter[] = L('auth_counter_' . strtolower($auth));
                } else {
                    $limit_auth_counter[] = L('auth_counter_' . strtolower($auth));
                }
            }
        }
        Tpl::output('allow_auth_back_office', $allow_auth_back_office);
        Tpl::output('limit_auth_back_office', $limit_auth_back_office);
        Tpl::output('allow_auth_counter', $allow_auth_counter);
        Tpl::output('limit_auth_counter', $limit_auth_counter);

        Tpl::showPage("user.detail");
    }

    /**
     * 登录日志
     */
    public function logOp()
    {
        Tpl::showPage("log");
    }

    /**
     * 获取用户登录日志
     * @param $p
     * @return array
     */
    public function getLogListOp($p)
    {
        $r = new ormReader();
        $sql = "SELECT uul.*,uu.user_code FROM um_user_log uul LEFT JOIN um_user uu ON uul.user_id = uu.uid";
        $search_text = trim($p['search_text']);
        if (intval($p['uid'])) {
            $sql .= " WHERE uu.uid = " . intval($p['uid']);
        } elseif ($search_text) {
            $sql .= " WHERE uu.user_code LIKE '%" . $search_text . "%' OR uu.user_name LIKE '%" . $search_text . "%' OR uul.client_type like '%" . $search_text . "%'";
        }
        $sql .= " ORDER BY uul.uid DESC";
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
            "pageSize" => $pageSize,
        );
    }

    /**
     * 用户修改密码
     */
    public function changePasswordOp()
    {
        Tpl::showPage("user.change_pwd");
    }

    /**
     * 修改密码
     * @param $p
     * @return result
     */
    public function apiChangePasswordOp($p)
    {
        $p['user_id'] = $this->user_id;
        if (trim($p['new_password'] != trim($p['verify_password']))) {
            return new result(false, 'Verify password error!');
        }
        $class_user = new userClass();
        $rt = $class_user->changePassword($p);
        return $rt;
    }

    /**
     * 用户信息
     */
    public function myProfileOp()
    {
        $class_user = new userClass();
        $rt = $class_user->getUserInfo($this->user_id);
        Tpl::output("user_info", $rt->DATA);
        Tpl::showPage("user.profile");
    }

    /**
     * 更新user信息
     * @param $p
     * @return result
     */
    public function updateProfileOp($p)
    {
        $user_name = trim($p['user_name']);
        $mobile_phone = trim($p['mobile_phone']);
        $email = trim($p['email']);
        if (empty($user_name)) {
            return new result(false, 'The user name cannot be empty!');
        }
        $m = M("um_user");
        $user = $m->getRow($this->user_id);
        $user->user_name = $user_name;
        $user->mobile_phone = $mobile_phone;
        $user->email = $email;
        $user->update_time = Now();
        $rt = $user->update();
        if ($rt->STS) {
            return new result(true, 'Update successful!');
        } else {
            return new result(false, 'Update failure!');
        }
    }

    /**
     * 头像
     */
    public function userIconOp()
    {
        $class_user = new userClass();
        $rt = $class_user->getUserInfo($this->user_id);
        Tpl::output("user_info", $rt->DATA);
        Tpl::showPage("user.icon");
    }

    /**
     * 更改头像
     * @param $p
     * @return result
     */
    public function updateUserIconOp($p)
    {
        $srcImg = $p['src_img'];
        if (!$srcImg) return new result(false, "Source Image is Emptry");
        $user_id = $this->user_id;

        if (!$user_id) return new result(false, "Invalid Session,Please Login Again");
        //把图片从draft移动到avatar目录

        $avatar_path = _UPLOAD_ . "/avatar";
        if (!is_dir($avatar_path)) {
            if (!@mkdir($avatar_path, 0755)) {
                return new result(false, "Make Folder Failed");
            }
        }
        $src_img = $avatar_path . "/" . $srcImg;
        $draft_img = _UPLOAD_ . "/draft/" . $srcImg;
        if (file_exists($draft_img)) {
            rename($draft_img, $src_img);
            @chmod($src_img, 0755);
            @unlink($draft_img);
        }
        $file = pathinfo($src_img);
        $ext = $file['extension'];
        //剪切缩略图
        $args = array();
        $args['src'] = $src_img;
        $iconImg = getUniqueNumber() . "." . $ext;
        $args['dst'] = $avatar_path . "/" . $iconImg;
        $args['x1'] = $p['cords_x1'];
        $args['x2'] = $p['cords_x2'];
        $args['y1'] = $p['cords_y1'];
        $args['y2'] = $p['cords_y2'];
        $args['w'] = $p['cords_w'];
        $args['h'] = $p['cords_h'];
        $args['src_max_w'] = 550;
        $result = imageHandler::cutImage($args);
        if ($result->STS) {
            //保存数据库参数
            $m = M("um_user");
            $user = $m->getRow($user_id);
            $user->user_image = $srcImg;
            $user->user_icon = $iconImg;
            $user->update_time = Now();
            $profile = my_json_decode($user->profile);
            $profile['cords'] = array(
                "x" => $p['cords_x1'],
                "x2" => $p['cords_x2'],
                "y" => $p['cords_y1'],
                "y2" => $p['cords_y2'],
                "w" => $p['cords_w'],
                "h" => $p['cords_h'],
            );
            $user->profile = my_json_encode($profile);
            $rt = $user->update();
            if ($rt->STS) {
                setSessionVar("user_info", $user->toArray());
            }
            return new result(true, '', array('icon' => getUserIcon($iconImg)));
        } else {
            return $result;
        }
    }

    /**
     * 分行
     */
    public function branchOp()
    {
        Tpl::showPage("branch");
    }

    /**
     * 获取branch列表
     * @param $p
     * @return array
     */
    public function getBranchListOp($p)
    {
        $search_text = trim($p['search_text']);
        $r = new ormReader();
        $sql = "SELECT sb.*,uu.user_code FROM site_branch sb LEFT JOIN um_user uu ON sb.manager = uu.uid ";
        if ($search_text) {
            $sql .= " WHERE sb.branch_code LIKE '%" . $search_text . "%' OR sb.branch_name LIKE '%" . $search_text . "%'";
        }
        $pageNumber = intval($p['pageNumber']) ?: 1;
        $pageSize = intval($p['pageSize']) ?: 20;
        $data = $r->getPage($sql, $pageNumber, $pageSize);
        $rows = $data->rows;
        $total = $data->count;
        $pageTotal = $data->pageCount;

        if ($rows) {
            $branch_ids = array_column($rows, 'uid');
            $branch_id_str = '(' . implode(',', $branch_ids) . ')';
            $sql = 'SELECT * FROM site_branch_limit WHERE branch_id IN ' . $branch_id_str;
            $limit_list = $r->getRows($sql);
            $limit_arr = array();
            foreach ($limit_list as $limit) {
                $limit_arr[$limit['branch_id']][$limit['limit_key']] = array(
                    'max_per_day' => $limit['max_per_day'],
                    'max_per_time' => $limit['max_per_time']
                );
            }

            foreach ($rows as $key => $row) {
                $limit = $limit_arr[$row['uid']];
                $rows[$key]['limit_arr'] = $limit;
            }

        }

        return array(
            "sts" => true,
            "data" => $rows,
            "total" => $total,
            "pageNumber" => $pageNumber,
            "pageTotal" => $pageTotal,
            "pageSize" => $pageSize,
        );
    }

    /**
     * 添加role
     */
    public function addBranchOp()
    {
        $p = array_merge(array(), $_GET, $_POST);
        if ($p['form_submit'] == 'ok') {
            $p['creator_id'] = $this->user_id;
            $p['creator_name'] = $this->user_name;
            $m_site_branch = new branchClass();
            $rt = $m_site_branch->addBranch($p);
            if ($rt->STS) {
                showMessage($rt->MSG, getUrl('user', 'branch', array(), false, BACK_OFFICE_SITE_URL));
            } else {
                unset($p['form_submit']);
                showMessage($rt->MSG, getUrl('user', 'addBranch', $p, false, BACK_OFFICE_SITE_URL));
            }
        } else {
            $user_list = M('um_user')->select(array('user_status' => 1));
            Tpl::output('user_list', $user_list);

            $bank_list = M('site_bank')->select(array('account_state' => 1));
            Tpl::output('bank_list', $bank_list);

            Tpl::showPage("branch.add");
        }
    }

    /**
     * 编辑user
     */
    public function editBranchOp()
    {
        $p = array_merge(array(), $_GET, $_POST);
        $m_site_branch = new branchClass();
        if ($p['form_submit'] == 'ok') {
            $rt = $m_site_branch->editBranch($p);
            if ($rt->STS) {
                showMessage($rt->MSG, getUrl('user', 'branch', array(), false, BACK_OFFICE_SITE_URL));
            } else {
                unset($p['form_submit']);
                showMessage($rt->MSG, getUrl('user', 'editBranch', $p, false, BACK_OFFICE_SITE_URL));
            }
        } else {
            $branch_info = $m_site_branch->getBranchInfo(intval($p['uid']));
            if (!$branch_info) {
                showMessage('Invalid Id');
            } else {
                Tpl::output('branch_info', $branch_info);
            }
            $address_id = $branch_info['address_id'];
            $m_core_tree = M('core_tree');
            $region_list = $m_core_tree->getParentAndBrotherById($address_id, 'region');
            Tpl::output('region_list', $region_list);
            $user_list = M('um_user')->select(array('user_status' => 1));
            Tpl::output('user_list', $user_list);

            $bank_list = M('site_bank')->select(array('account_state' => 1));
            Tpl::output('bank_list', $bank_list);

            Tpl::showPage("branch.edit");
        }
    }

    /**
     * 删除branch
     */
    public function deleteBranchOp()
    {
        $uid = intval($_GET['uid']);
        $m_site_branch = new branchClass();
        $rt = $m_site_branch->deleteBranch($uid);
        showMessage($rt->MSG);
    }

    /**
     * 部门列表
     */
    public function departmentOp()
    {
        Tpl::output('branch_id', $_GET['uid']);
        Tpl::showPage("department");
    }

    /**
     * 获取部门列表
     * @param $p
     * @return array
     */
    public function getDepartmentListOp($p)
    {
        $branch_id = intval($p['branch_id']);
        $search_text = trim($p['search_text']);
        $r = new ormReader();
        $sql = "SELECT sd.*,sb.branch_name,uu1.user_name leader_name,uu2.user_name assistant_name FROM site_depart sd "
            . " INNER JOIN site_branch sb ON sd.branch_id = sb.uid"
            . " LEFT JOIN um_user uu1 ON sd.leader = uu1.uid"
            . " LEFT JOIN um_user uu2 ON sd.assistant = uu2.uid"
            . " WHERE sb.uid = $branch_id";
        if ($search_text) {
            $sql .= " AND (sd.depart_code LIKE '%" . $search_text . "%' OR sd.depart_name LIKE '%" . $search_text . "%')";
        }
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
            "pageSize" => $pageSize,
        );
    }

    /**
     * 添加部门
     */
    public function addDepartmentOp()
    {
        $p = array_merge(array(), $_GET, $_POST);
        if ($p['form_submit'] == 'ok') {
            $m_site_depart = M('site_depart');
            $p['creator_id'] = $this->user_id;
            $p['creator_name'] = $this->user_name;
            $rt = $m_site_depart->addDepart($p);
            if ($rt->STS) {
                showMessage($rt->MSG, getUrl('user', 'department', array('uid' => $p['branch_id']), false, BACK_OFFICE_SITE_URL));
            } else {
                unset($p['form_submit']);
                showMessage($rt->MSG, getUrl('user', 'addDepartment', $p, false, BACK_OFFICE_SITE_URL));
            }
        } else {
            $m_site_branch = M('site_branch');
            $branch_list = $m_site_branch->getRows(array('status' => 1));
            Tpl::output('branch_list', $branch_list);
            $user_list = M('um_user')->select(array('user_status' => 1));
            Tpl::output('user_list', $user_list);
            Tpl::showPage("department.add");
        }
    }

    /**
     * 修改部门
     */
    public function editDepartmentOp()
    {
        $p = array_merge(array(), $_GET, $_POST);
        $m_site_depart = M('site_depart');
        if ($p['form_submit'] == 'ok') {
            $rt = $m_site_depart->editDepart($p);
            if ($rt->STS) {
                showMessage($rt->MSG, getUrl('user', 'department', array('uid' => $p['branch_id']), false, BACK_OFFICE_SITE_URL));
            } else {
                unset($p['form_submit']);
                showMessage($rt->MSG, getUrl('user', 'addDepartment', $p, false, BACK_OFFICE_SITE_URL));
            }
        } else {
            $depart_info = $m_site_depart->find(array('uid' => $p['uid']));
            if (!$depart_info) {
                showMessage('Invalid Id');
            } else {
                Tpl::output('depart_info', $depart_info);
            }
            $m_site_branch = M('site_branch');
            $branch_list = $m_site_branch->getRows(array('status' => 1));
            Tpl::output('branch_list', $branch_list);
            $user_list = M('um_user')->select(array('user_status' => 1));
            Tpl::output('user_list', $user_list);
            Tpl::showPage("department.edit");
        }
    }

    /**
     * 删除部门
     */
    public function deleteDepartmentOp()
    {
        $uid = intval($_GET['uid']);
        $m_site_depart = M('site_depart');
        $rt = $m_site_depart->deleteDepart($uid);
        showMessage($rt->MSG);
    }

    /************************************** point *************************************/

    /**
     * event
     */
    public function pointEventOp()
    {
        Tpl::showPage("point.event");
    }

    /**
     * @param $p
     * @return array
     */
    public function getPointEventListOp($p)
    {
        $search_text = trim($p['search_text']);
        $is_system = intval($p['is_system']);
        $r = new ormReader();
        $sql = "SELECT * FROM hr_point_event WHERE is_system = $is_system";
        if ($search_text) {
            $sql .= " AND (event_code LIKE '%" . $search_text . "%' OR description LIKE '%" . $search_text . "%')";
        }
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
            "pageSize" => $pageSize,
            "is_system" => $is_system,
        );
    }

    /**
     * 添加event
     * @param $p
     * @return result
     */
    public function addEventOp($p)
    {
        $p['creator_id'] = $this->user_id;
        $p['creator_name'] = $this->user_name;
        $class_user = new userClass();
        return $class_user->addEvent($p);
    }

    /**
     * 修改event
     * @param $p
     * @return result
     */
    public function editEventOp($p)
    {
        $class_user = new userClass();
        return $class_user->editEvent($p);
    }

    /**
     * 移除事件
     * @return result
     */
    public function deleteEventOp()
    {
        $uid = intval($_GET['uid']);
        $class_user = new userClass();
        $rt = $class_user->deleteEvent($uid);
        if ($rt->STS) {
            showMessage('Delete successful!');
        } else {
            showMessage('Delete failed!');
        }
    }

    /**
     * point期间
     */
    public function pointPeriodOp()
    {
        $r = new ormReader();
        $sql = "SELECT MAX(end_date) end_date FROM hr_point_period";
        $end_date = $r->getOne($sql);
        if ($end_date) {
            $new_start_date = date('Y-m-d', strtotime("$end_date +1 day"));
            Tpl::output('new_start_date', $new_start_date);
        }
        Tpl::showpage("point.period");
    }

    /**
     * 期间列表
     * @param $p
     * @return array
     */
    public function getPeriodListOp($p)
    {
        $search_text = trim($p['search_text']);
        $r = new ormReader();
        $sql = "SELECT * FROM hr_point_period";
        if ($search_text) {
            $sql .= " WHERE period LIKE '%" . $search_text . "%'";
        }
        $sql .= " ORDER BY uid DESC";
        $pageNumber = intval($p['pageNumber']) ?: 1;
        $pageSize = intval($p['pageSize']) ?: 20;
        $data = $r->getPage($sql, $pageNumber, $pageSize);
        $rows = $data->rows;
        $total = $data->count;
        $pageTotal = $data->pageCount;

        if ($rows) {
            $sql = "SELECT max(uid) max_uid FROM hr_point_period";
            $max_uid = $r->getOne($sql);
            $rows[0]['is_new'] = $rows[0]['uid'] == $max_uid;

            $m_hr_point_depart = M('hr_point_depart');
            foreach ($rows as $key => $row) {
                if ($row['status'] == 0 && $row['start_date'] <= Now()) {
                    $closed = array();
                    $not_close = array();
                    $sql = "SELECT hpd.*,sd.depart_name,sb.branch_name FROM hr_point_depart hpd LEFT JOIN site_depart sd ON hpd.depart_id = sd.uid LEFT JOIN site_branch sb ON sd.branch_id = sb.uid WHERE hpd.period_id = " . $row['uid'];
                    $point_depart = $r->getRows($sql);
                    foreach ($point_depart as $val) {
                        if ($val['status'] == 100) {
                            $closed[] = $val['branch_name'] . ' - ' . $val['depart_name'];
                        } else {
                            $not_close[] = $val['branch_name'] . ' - ' . $val['depart_name'];
                        }
                    }
                    $row['closed'] = $closed;
                    $row['not_close'] = $not_close;
                    if (!count($not_close)) {
                        $row['is_close'] = true;
                    }
                }
                $rows[$key] = $row;
            }
        }

        return array(
            "sts" => true,
            "data" => $rows,
            "total" => $total,
            "pageNumber" => $pageNumber,
            "pageTotal" => $pageTotal,
            "pageSize" => $pageSize,
        );
    }

    /**
     * 增加期间
     * @param $p
     * @return result
     */
    public function addPeriodOp($p)
    {
        $p['creator_id'] = $this->user_id;
        $p['creator_name'] = $this->user_name;
        $class_user = new userClass();
        return $class_user->addPeriod($p);
    }

    /**
     * 修改期间
     * @param $p
     * @return result
     */
    public function editPeriodOp($p)
    {
        $class_user = new userClass();
        return $class_user->editPeriod($p);
    }

    /**
     * 删除区间
     */
    public function deletePeriodOp()
    {
        $uid = intval($_GET['uid']);
        $class_user = new userClass();
        $rt = $class_user->deletePeriod($uid);
        showMessage($rt->MSG);
    }

    /**
     * 关闭区间
     */
    public function closePeriodOp()
    {
        $uid = intval($_GET['uid']);
        $m_hr_point_period = M('hr_point_period');
        $row = $m_hr_point_period->getRow($uid);
        if ($row['status'] == 100 || $row['start_date'] > Now()) {
            showMessage('Param Error!');
        }

        $m_hr_point_depart = M('hr_point_depart');
        $chk_close = $m_hr_point_depart->find(array('period_id' => $row['uid'], 'status' => 0));
        if ($chk_close) {
            showMessage('Department not closed!');
        }

        $row->status = 100;
        $row->handler_id = $this->user_id;
        $row->handler_name = $this->user_name;
        $row->update_time = Now();
        $rt = $row->update();
        if ($rt->STS) {
            showMessage('Close Successful!');
        } else {
            showMessage('Close Failed!');
        }

    }

    /**
     * 积分报表
     */
    public function periodReportOp()
    {
        $uid = intval($_GET['uid']);
        $r = new ormReader();
        $user_id = $this->user_id;
        $sql = "select sd.*,sb.branch_name from site_depart sd LEFT JOIN site_branch sb ON sd.branch_id = sb.uid WHERE leader = $user_id OR assistant = $user_id ORDER BY sd.branch_id ASC";
        $rows = $r->getRows($sql);

        $depart_list = array();
        foreach ($rows as $row) {
            $depart_list[$row['branch_id']][] = $row;
            Tpl::output('depart_list', $depart_list);
        }
        $m_hr_point_period = M('hr_point_period');
        $point_period = $m_hr_point_period->find(array('uid' => $uid));
        Tpl::output('uid', $uid);
        Tpl::output('period', $point_period);
        Tpl::showpage("point.period.report");
    }

    /**
     *
     * @param $p
     * @return array
     */
    public function getPeriodReportListOp($p)
    {
        $uid = intval($p['uid']);
        $branch_id = intval($p['branch_id']);
        $depart_id = intval($p['depart_id']);
        $search_text = trim($p['search_text']);
        $pageNumber = intval($p['pageNumber']) ?: 1;
        $pageSize = intval($p['pageSize']) ?: 20;

        $r = new ormReader();
        if ($depart_id) {
            $sql = "SELECT * FROM hr_point_depart WHERE depart_id = $depart_id AND period_id = $uid";
            $point_depart = $r->getRow($sql);
            $point_depart_id = $point_depart['uid'];
            $point_depart_str = "($point_depart_id)";
            $where = " AND uu.depart_id = $depart_id";
        } elseif ($branch_id) {
            $sql = "SELECT hpd.* FROM hr_point_depart hpd INNER JOIN site_depart sd ON hpd.depart_id = sd.uid WHERE sd.branch_id = $branch_id AND hpd.period_id = $uid";
            $point_depart = $r->getRows($sql);
            $point_depart_str = "(" . implode(',', array_column($point_depart, 'uid')) . ")";
            $where = " AND sb.uid = $branch_id";
        } elseif ($search_text) {
            $where = "";
        } else {
            return array(
                "sts" => true,
                "data" => array(),
                "total" => 0,
                "pageNumber" => $pageNumber,
                "pageTotal" => 0,
                "pageSize" => $pageSize,
            );
        }

        $sql = "SELECT uu.*,sb.branch_name,sd.depart_name " .
            "FROM um_user uu INNER JOIN site_depart sd ON uu.depart_id = sd.uid " .
            "INNER JOIN site_branch sb ON sd.branch_id = sb.uid " .
            "WHERE uu.user_status = 1" . $where;
        if ($search_text) {
            $sql .= " AND uu.user_name LIKE '%" . $search_text . "%'";
        }
        $sql .= " ORDER BY sb.uid ASC,sd.uid ASC";
        $data = $r->getPage($sql, $pageNumber, $pageSize);
        $rows = $data->rows;
        $total = $data->count;
        $pageTotal = $data->pageCount;

        if ($rows) {
            $user_ids = array_column($rows, 'uid');
            $sql = "SELECT hpu.*,hpe.event_code,hpe.is_system " .
                "FROM hr_point_user hpu LEFT JOIN hr_point_event hpe ON hpu.point_event_id = hpe.uid " .
                "WHERE hpu.user_id IN (" . implode(',', $user_ids) . ") ";
            if ($point_depart_str) $sql .= "AND hpu.point_depart_id IN $point_depart_str ";
            $sql .= "ORDER BY hpu.user_id ASC,hpu.point_event_id ASC";
            $point_user = $r->getRows($sql);
            $new_point_user = array();
            foreach ($point_user as $value) {
                $new_point_user[$value['user_id']][$value['point_event_id']] = $value;
            }

            foreach ($rows as $key => $row) {
                $point = 0;
                $user_event_arr = $new_point_user[$row['uid']];
                foreach ($user_event_arr as $point_val) {
                    $point += $point_val['point'];
                }
                $row['point_list'] = $user_event_arr;
                $row['point'] = $point;
                $rows[$key] = $row;
            }
        }

        return array(
            "sts" => true,
            "data" => $rows,
            "total" => $total,
            "pageNumber" => $pageNumber,
            "pageTotal" => $pageTotal,
            "pageSize" => $pageSize,
        );
    }

    /**
     * 积分详情
     */
    public function departmentPointOp()
    {
        $r = new ormReader();
        $user_id = $this->user_id;
        $sql = "select sd.*,sb.branch_name from site_depart sd LEFT JOIN site_branch sb ON sd.branch_id = sb.uid WHERE leader = $user_id OR assistant = $user_id ORDER BY sd.branch_id ASC";
        $rows = $r->getRows($sql);

        if (count($rows) == 0) {
            Tpl::setDir("widget");
            Tpl::output('msg', 'No access to the page!');
            Tpl::showpage("msg");
        }

        if (count($rows) == 1) {
            Tpl::output('depart', $rows[0]);
        } else {
            Tpl::output('depart_list', $rows);
//            $depart_list = array();
//            foreach ($rows as $row) {
//                $depart_list[$row['branch_id']][] = $row;
//                Tpl::output('depart_list', $depart_list);
//            }
        }

        Tpl::showpage("point.depart.period");
    }

    /**
     * 获取部门打分区间
     * @param $p
     * @return array
     */
    public function getDepartPeriodListOp($p)
    {
        $depart_id = intval($p['depart_id']);
        $pageNumber = intval($p['pageNumber']) ?: 1;
        $pageSize = intval($p['pageSize']) ?: 20;
        $search_text = trim($p['search_text']);

        $r = new ormReader();
        $sql = "SELECT hpp.*,hpd.status depart_status,hpd.uid uuid FROM hr_point_depart hpd INNER JOIN hr_point_period hpp ON hpd.period_id = hpp.uid WHERE hpd.depart_id = $depart_id";
        if ($search_text) {
            $sql .= " AND hpp.period LIKE '%" . $search_text . "%'";
        }
        $sql .= " ORDER BY hpd.period_id DESC";
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
            "pageSize" => $pageSize,
        );
    }

    /**
     * 开启区间
     * @param $p
     * @return result
     */
    public function activeDepartPeriodOp($p)
    {
        $p['handler_id'] = $this->user_id;
        $p['handler_name'] = $this->user_name;
        $class_user = new userClass();
        return $class_user->activeDepartPeriod($p);

    }

    /**
     * 积分评分
     */
    public function handleDepartPeriodOp()
    {
        $uid = intval($_GET['uid']);
        $r = new ormReader();
        $sql = "SELECT hpd.*,hpp.period,hpp.start_date,hpp.end_date,sd.depart_name,sd.leader,sd.assistant,sb.branch_name FROM hr_point_depart hpd INNER JOIN hr_point_period hpp ON hpd.period_id = hpp.uid INNER JOIN site_depart sd ON hpd.depart_id = sd.uid INNER JOIN site_branch sb ON sd.branch_id = sb.uid WHERE hpd.uid = $uid";
        $row = $r->getRow($sql);
        if ($row['leader'] != $this->user_id && $row['assistant'] != $this->user_id) {
            showMessage('The user is not the head or assistant of the department.');
        }

        if ($row['status'] != 0) {
            showMessage('Closed.');
        }

        if ($row['start_date'] > Now()) {
            showMessage('No marking time.');
        }

        Tpl::output('row', $row);
        Tpl::showpage('point.depart.period.handle');
    }

    /**
     * 积分列表
     * @param $p
     * @return array|result
     */
    public function getDepartUserListOp($p)
    {
        $uid = intval($p['uid']);
        $pageNumber = intval($p['pageNumber']) ?: 1;
        $pageSize = intval($p['pageSize']) ?: 20;
        $search_text = trim($p['search_text']);

        $m_hr_point_depart = M('hr_point_depart');
        $point_depart = $m_hr_point_depart->find(array('uid' => $uid));
        if (!$point_depart) {
            return new result(false, 'Invalid Id');
        }

        $r = new ormReader();
        $sql = "SELECT * FROM um_user WHERE user_status = 1 AND depart_id = " . $point_depart['depart_id'];
        if ($search_text) {
            $sql .= " AND user_name LIKE '%" . $search_text . "%'";
        }
        $data = $r->getPage($sql, $pageNumber, $pageSize);
        $rows = $data->rows;
        $total = $data->count;
        $pageTotal = $data->pageCount;

        if ($rows) {
            $user_ids = array_column($rows, 'uid');
            $sql = "SELECT * FROM hr_point_user WHERE user_id IN (" . implode(',', $user_ids) . ") AND point_depart_id = $uid ORDER BY user_id ASC,point_event_id ASC";
            $point_user = $r->getRows($sql);
            $new_point_user = array();
            foreach ($point_user as $value) {
                $new_point_user[$value['user_id']][$value['point_event_id']] = $value;
            }
        }

        $sql = "SELECT * FROM hr_point_event WHERE status = 100";
        $point_event = $r->getRows($sql);

        foreach ($rows as $key => $row) {
            $point = 0;
            foreach ($point_event as $event) {
                $user_event = $new_point_user[$row['uid']][$event['uid']];
                if ($user_event['point'] > 0) $point += $user_event['point'];
                $row['point_list'][$event['uid']] = array_merge(array(), $event, $user_event ?: array());
            }
            $row['point'] = $point;
            $rows[$key] = $row;
        }

        return array(
            "sts" => true,
            "data" => $rows,
            "total" => $total,
            "pageNumber" => $pageNumber,
            "pageTotal" => $pageTotal,
            "pageSize" => $pageSize,
        );

    }

    /**
     * 计算user系统积分
     * @param $p
     * @return result
     */
    public function calculateSystemPointOp($p)
    {
        $p['handler_id'] = $this->user_id;
        $p['handler_name'] = $this->user_name;
        $class_user = new userClass();
        return $class_user->calculateSystemPoint($p);
    }

    /**
     * 自定义项评分
     * @param $p
     * @return result
     */
    public function evaluateUserPointOp($p)
    {
        $p['handler_id'] = $this->user_id;
        $p['handler_name'] = $this->user_name;
        $class_user = new userClass();
        return $class_user->evaluateUserPoint($p);
    }

    /**
     * 关闭
     * @param $p
     * @return result
     */
    public function closeDepartPeriodOp($p)
    {
        $p['handler_id'] = $this->user_id;
        $p['handler_name'] = $this->user_name;
        $class_user = new userClass();
        return $class_user->closeDepartPeriod($p);
    }

    /**
     * 部门报表
     */
    public function handleDepartPeriodReportOp()
    {
        $uid = intval($_GET['uid']);
        $r = new ormReader();
        $sql = "SELECT hpd.*,hpp.period,hpp.start_date,hpp.end_date,sd.depart_name,sd.leader,sd.assistant,sb.branch_name FROM hr_point_depart hpd INNER JOIN hr_point_period hpp ON hpd.period_id = hpp.uid INNER JOIN site_depart sd ON hpd.depart_id = sd.uid INNER JOIN site_branch sb ON sd.branch_id = sb.uid WHERE hpd.uid = $uid";
        $row = $r->getRow($sql);
        if ($row['leader'] != $this->user_id && $row['assistant'] != $this->user_id) {
            showMessage('The user is not the head or assistant of the department.');
        }

        if ($row['status'] != 100) {
            showMessage('No Closed.');
        }

        Tpl::output('row', $row);
        Tpl::showpage('point.depart.period.report');
    }

    /*************************************************************/
}
