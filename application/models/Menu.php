<?php
/**
 * @name MenuModel
 * @desc Menu数据获取类, 可以访问数据库，文件，其它系统等
 * @author yangmingfu
 */
class MenuModel extends Db_Mysql{
    public $tablename = 'resources';
    public $pk = 'id';
}
