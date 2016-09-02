<?php
class Biz_Menu {
    public function __construct() {
        $this->menuList = parse_ini_file(dirname(__FILE__) . '/../../../conf/menu.ini');
        $this->changeToArray();
    }

    public function changeToArray($arr = array()) {
        if (empty($arr)) {
            $arr = $this->menuList;
        }
        foreach ($arr as $key=>$v) {
            $tmp = array();
            if (strlen($key) == 2) {
                $result[$key]['name'] = $v;
                $result[$key]['id'] = $key;
            }
            if (strlen($key) == 4) {
                $parent = substr($key, 0, 2);
                $tmp['name'] = $v;
                $tmp['id'] = $key;
                $result[$parent]['child'][$key] = $tmp;
            }
            if (strlen($key) == 6) {
                $level_1 = substr($key, 0, 2);
                $level_2 = substr($key, 2, 2);
                $tmp['name'] = $v;
                $tmp['id'] = $key;
                $result[$level_1]['child'][$level_1 . $level_2]['child'][$key] = $tmp;
            }
        }
        return $result;
    }
    public function getCurrentMenu($roleid = 0) {
        //系统管理员，有所有权限
        if ($roleid == 1) {
            return $this->menuList;
        } else if ($roleid == 0 ){
            return $this->menuList;
        } else {
            $objRoleModel = new RoleModel();
            $res = $objRoleModel->fId($roleid);
            if (empty($res) || empty($res[0]) || empty($res[0]['resourceid'])) {
                $resourceids = array(4, 41, 4101, 4102);
            } else {
                $resourceids = explode(',', $res[0]['resourceid']);
                foreach ($resourceids as $v) {
                    $a = substr($v, 0, 2);
                    $b = substr($v, 2, 2);
                    $resourceids[] = $a;
                    $resourceids[] = $b;
                }
            }
        }
        return array_unique($resourceids);
    }

    public function getMenuById($ids) {
        if (is_string($ids)) {
            $ids = explode(',');
        }
        foreach ($ids as $id) {
            $return[$id] = $this->menuList[$id];
        }
        return $return;
    }
}
