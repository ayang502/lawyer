<?php
class BaseModel extends Db_Mysql {

    public function whereAnd($datas = array()) {
        if (empty($datas)) {
            return;
        }
        $this->filter($datas);
        if (empty($datas)) {
            return;
        }
        foreach ($datas as $k=>$v) {
            $query[] = "`{$k}` = '{$v}'"; 
        }
        if (!empty($this->options['where'])) {
            $this->options['where'] .= ' and ' . join(' and ', $query);
        } else {
            $this->options['where'] .= join(' and ', $query);
        }
    }

    public function whereOr($datas) {
        if (empty($datas)) {
            return;
        }
        $this->filter($datas);
        if (empty($datas)) {
            return;
        }
        foreach ($datas as $k=>$v) {
            $query[] = "`{$k}` = '{$v}'"; 
        }
        if (!empty($this->options['where'])) {
            $this->options['where'] .= ' and ' . join(' or ', $query);
        } else {
            $this->options['where'] = join(' or ', $query);
        }
    }

    public function whereLike($datas, $type="and") {
        if (empty($datas)) {
            return;
        }
        $this->filter($datas);
        if (empty($datas)) {
            return;
        }
        $type = " {$type} ";
        foreach ($datas as $k=>$v) {
            $query[] = "`{$k}` like '%{$v}%'"; 
        }
        if (!empty($this->options['where'])) {
            $this->options['where'] .= ' and ' . join($type, $query);
        } else {
            $this->options['where'] = join($type, $query);
        }
    }

    public function whereBetween($datas) {
        if (empty($datas)) {
            return;
        }
        $this->filter($datas);
        if (empty($datas)) {
            return;
        }
        foreach ($datas as $k=>$v) {
            $query[] = "`{$k}` between {$v[0]} and {$v[1]}"; 
        }
        if (!empty($this->options['where'])) {
            $this->options['where'] .= ' and ' . join(' or ', $query);
        } else {
            $this->options['where'] = join(' and ', $query);
        }
    }
}
