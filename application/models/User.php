<?php
/**
 * @name UserModel
 * @desc User数据获取类, 可以访问数据库，文件，其它系统等
 * @author yangmingfu
 */
class UserModel extends BaseModel {
    public $tablename = 'users';
    public $pk = 'id';
}
