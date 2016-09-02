<?php
/**
 * @name DutiesModel
 * @desc Duties数据获取类, 可以访问数据库，文件，其它系统等
 * @author yangmingfu
 */
class DutiesModel extends Db_Mysql{
    public $tablename = 'duties';
    public $pk = 'id';
}
