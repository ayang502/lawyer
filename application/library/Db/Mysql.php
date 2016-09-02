<?php
class Db_Mysql{
    protected $db;
    public $tablename;
    public $pk = 'id';
    public $options = array();
    static $instance = array();
    protected $_config;
    public $error = array();
    protected $_lock = '';
    private $_begin_transaction = false;
    function __construct($pConfig = 'default') {
        $this->_config = $pConfig;
        $this->tablename || $this->tablename = strtolower(substr(get_class($this), 0, -5));
    }

    /**
     *  特殊方法实现
     * @param string $pMethod
     * @param array $pArgs
     * @return mixed
     */
    function __call($pMethod, $pArgs) {
        if (in_array($pMethod, array('field', 'table', 'order', 'limit', 'page', 'having', 'group', 'distinct', 'where'), true)) {
            $this->options[$pMethod] = $pArgs[0];
            return $this;
        }
        if (in_array($pMethod, array('count', 'sum', 'min', 'max', 'avg'))) {
            $field = isset($pArgs[0])? $pArgs[0]: '*';
            return $this->fOne("$pMethod($field)");
        }
        if ('ff' == substr($pMethod, 0, 2)) {
            return $this->where(strtolower(substr($pMethod, 2)) . "='{$pArgs[0]}'")->fRow();
        }
    }

    /**
     *  数据库连接
     * @param string $pConfig 配置
     * @return PDO
     */
    static function instance($pConfig = 'default') {
        if (empty(self::$instance[$pConfig])) {
            $config = new Yaf_Config_Ini(dirname(__FILE__) . '/../../../conf/config.ini');
            $tDB = $config['mysql'];
            try {
                self::$instance[$pConfig] = new PDO($tDB['dsn'], $tDB['user'], $tDB['passwd'], array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
            } catch(Exception $e) {
                throw Exception($e->getMessage);
            }
        }
        return self::$instance[$pConfig];
    }
    /**
     *  过滤数据
     * @param array $datas 过滤数据
     * @return bool
     */
    public function filter(&$datas) {
        if (empty($datas)) {
            return $datas;
        }
        $tFields = $this->getFields();
        foreach($datas as $k1 => &$v1) {
            if (isset($tFields[$k1])) {
                $v1 = strtr($v1, array('\\' => '', "'" => "'"));
            } else {
                unset($datas[$k1]);
            }
        }
        return $datas? true: false;
    }
    /**
     *  查询条件
     * @param array $pOpt 条件
     * @return array
     */
    private function _options($pOpt = array()) {
        $tOpt = $pOpt? array_merge($this->options, $pOpt): $this->options;
        $this->options = array();
        empty($tOpt['table']) && $tOpt['table'] = $this->tablename;
        empty($tOpt['field']) && $tOpt['field'] = '*';
        return $tOpt;
    }

    /**
     *  执行SQL
     * @param string $sql 查询语句
     * @return int
     */
    function exec($sql) {
        $this->db || $this->db = self::instance($this->_config);
        if ($tReturn = $this->db->exec($sql)) {
            $this->error = array();
        }
        else{
            $this->error = $this->db->errorInfo();
            isset($this->error[1]) || $this->error = array();
        }
        return $tReturn;
    }

    /**
     * 设置出错信息
     * @param string $msg 信息
     * @param int $code 错误码
     * @param string $state 状态码
     * @return bool
     */
    function setError($msg, $code = 1, $state = 'UNKNOW') {
        $this->error = array($state, $code, $msg);
        return false;
    }

    /**
     * 执行SQL，并返回结果数据
     * @param string $sql 查询语句
     * @return array
     */
    function query($sql) {
        $this->db || $this->db = self::instance($this->_config);
        if ($this->_lock) {
            $sql.= ' '.$this->_lock;
            $this->_lock = '';
        }
        if (!$query = $this->db->query($sql)) {
            $this->error = $this->db->errorInfo();
            isset($this->error[1]) || $this->error = array();
            return array();
        }
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * 添加记录
     */
    function insert($datas, $pReplace = false) {
        if ($this->filter($datas)) {
            if ($this->exec(($pReplace? "REPLACE": "INSERT") . " INTO `$this->tablename`(`".join('`,`', array_keys($datas))."`) VALUES ('".join("','", $datas)."')")) {
                return $this->db->lastInsertId();
            }
        }
        return 0;
    }

    /**
     * 更新记录
     */
    function update($datas) {
        if (!$this->filter($datas)) return false;
        $tOpt = array();
        if (isset($datas[$this->pk])) {
            $tOpt = array('where' => "$this->pk='{$datas[$this->pk]}'");
        }
        $tOpt = $this->_options($tOpt);
        if ($datas && !empty($tOpt['where'])) {
            foreach($datas as $k1 => $v1) $tSet[] = "`$k1`='$v1'";
            return $this->exec("UPDATE `" . $tOpt['table'] . "` SET " . join(',', $tSet) . " WHERE " . $tOpt['where']);
        }
        return false;
    }

    /**
     * 删除记录
     */
    function del() {
        if ($tArgs = func_get_args()) {
            $tSql = "DELETE FROM `$this->tablename` WHERE ";
            if (intval($tArgs[0]) || count($tArgs) > 1) {
                return $this->exec($tSql . $this->pk . ' IN(' . join(',', array_map("intval", $tArgs)) . ')');
            }
            return $this->exec($tSql . $tArgs[0]);
        }
        $tOpt = $this->_options();
        if (empty($tOpt['where'])) return false;
        return $this->exec("DELETE FROM `" . $tOpt['table'] . "` WHERE " . $tOpt['where']);
    }

    /**
     * 查找一条
     */
    function fRow($pId = 0) {
        if (false === stripos($pId, 'SELECT')) {
            $tOpt = $pId? $this->_options(array('where' => $this->pk . '=' . abs($pId))): $this->_options();
            $tOpt['where'] = empty($tOpt['where'])? '': ' WHERE ' . $tOpt['where'];
            $tOpt['order'] = empty($tOpt['order'])? '': ' ORDER BY ' . $tOpt['order'];
            $tSql = "SELECT {$tOpt['field']} FROM `{$tOpt['table']}` {$tOpt['where']} {$tOpt['order']}  LIMIT 0,1";
        }
        else{
            $tSql = & $pId;
        }
        $tResult = $this->query($tSql);
        if ($tResult) {
            return $tResult[0];
        }
        return array();
    }

    /**
     * 查找一字段 ( 基于 fRow )
     *
     * @param string $pField
     * @return string
     */
    function fOne($pField) {
        $this->field($pField);
        if (($tRow = $this->fRow()) && isset($tRow[$pField])) {
            return $tRow[$pField];
        }
        return false;
    }

    /**
     * 查找多条
     */
    function fList($pOpt = array()) {
        if (!is_array($pOpt)) {
            $pOpt = array('where' => $this->pk . (strpos($pOpt, ',')? ' IN(' . $pOpt . ')': '=' . $pOpt));
        }
        $tOpt = $this->_options($pOpt);
        $tSql = "SELECT {$tOpt['field']} FROM  `{$tOpt['table']}`";
        $this->join && $tSql .= implode(' ', $this->join);
        empty($tOpt['where']) || $tSql .= ' WHERE ' . $tOpt['where'];
        empty($tOpt['group']) || $tSql .= ' GROUP BY ' . $tOpt['group'];
        empty($tOpt['order']) || $tSql .= ' ORDER BY ' . $tOpt['order'];
        empty($tOpt['having']) || $tSql .= ' HAVING ' . $tOpt['having'];
        empty($tOpt['limit']) || $tSql .= ' LIMIT ' . $tOpt['limit'];
        return $this->query($tSql);
    }

    /**
     * 查询并处理为哈西数组 ( 基于 fList )
     *
     * @param string $pField
     * @return array
     */
    function fHash($pField) {
        $this->field($pField);
        $tList = array();
        $tField = explode(',', $pField);
        if (2 == count($tField)) {
            foreach($this->fList() as $v1) {
                $tList[$v1[$tField[0]]] = $v1[$tField[1]];
            }
        }
        else {
            foreach($this->fList() as $v1) {
                $tList[$v1[$tField[0]]] = $v1;
            }
        }
        return $tList;
    }

    /**
     * 数据表名
     * @return array
     */
    function getTables() {
        $this->db || $this->db = self::instance($this->_config);
        return $this->db->query("SHOW TABLES")->fetchAll(3);
    }

    /**
     *  数据表字段
     * @param string $table 表名
     * @return mixed
     */
    function getFields($table = '') {
        static $fields = array();
        $table || $table = $this->tablename;
        if (empty($fields[$table])) {
            if (is_file($tFile = APPLICATION_PATH.'/cache/db/fields/'.$table)) {
                $fields[$table] = unserialize(file_get_contents($tFile, true));
            }
            else {
                $fields[$table] = array();
                $this->db || $this->db = self::instance($this->_config);
                if ($tQuery = $this->db->query("SHOW FULL FIELDS FROM `$table`")) {
                    foreach($tQuery->fetchAll(2) as $v1) {
                        $fields[$table][$v1['Field']] = array('type' => $v1['Type'], 'key' => $v1['Key'], 'null' => $v1['Null'], 'default' => $v1['Default'], 'comment' => $v1['Comment']);
                    }
                    file_put_contents($tFile, serialize($fields[$table]));
                }
            }
        }
        return $fields[$table];
    }

    /**
     *  联表语句
     * @var array
     */
    public $join = array();

    /**
     * 联表查询
     * @param string $table 联表名
     * @param string $where 联表条件
     * @param string $prefix INNER|LEFT|RIGHT 联表方式
     * @return $this
     */
    function join($table, $where, $prefix = '') {
        $this->join[] = " $prefix JOIN `$table` ON $where ";
        return $this;
    }

    /**
     * 事务开始
     */
    function begin() {
        $this->db || $this->db = self::instance($this->_config);
        $this->back();
        if (!$this->db->beginTransaction()) {
            return false;
        }
        return $this->_begin_transaction = true;
    }

    /**
     * 事务提交
     */
    function commit() {
        if ($this->_begin_transaction) {
            $this->_begin_transaction = false;
            $this->db->commit();
        }
        return true;
    }
    /**
     * 事务回滚
     */
    function back() {
        if ($this->_begin_transaction) {
            $this->_begin_transaction = false;
            $this->db->rollback();
        }
        return false;
    }
    /**
     * 锁表
     */
    function lock($sql = 'FOR UPDATE') {
        $this->_lock = $sql;
        return $this;
    }
}
