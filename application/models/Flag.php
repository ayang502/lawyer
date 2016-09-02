<?php
/**
 * @name FlagModel
 * @desc Flag数据获取类, 可以访问数据库，文件，其它系统等
 * @author yangmingfu
 */
class FlagModel extends Db_Mysql{
    public $tablename = 'cases_flag';
    public $pk = 'id';
}
