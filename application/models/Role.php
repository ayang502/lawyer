<?php
/**
 * @name RoleModel
 * @desc Role数据获取类, 可以访问数据库，文件，其它系统等
 * @author yangmingfu
 */
class RoleModel extends Db_Mysql{
    public $tablename = 'roles';
    public $pk = 'id';
}
