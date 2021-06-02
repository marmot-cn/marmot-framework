<?php
namespace Marmot\Framework\Classes;

use PDO;

/**
 * @Injectable(lazy=true)
 */
class MyPdo
{
    protected $pdo = null;
    
    public $statement = null;

    public $options = array(
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES ",
    );

    /**
     * @Inject({"database.host","database.port","database.user","database.password","database.dbname", "database.charset"})
     */
    public function __construct($host, $port, $user, $pass, $dbname, $charset = "utf8", $persistent = false)
    {
        $this->options[PDO::MYSQL_ATTR_INIT_COMMAND] .= $charset;
        if ($persistent) {
            $this->options[PDO::ATTR_PERSISTENT] = true;
        }
        $dsn = "mysql:host={$host};port={$port};dbname={$dbname}";
        $this->pdo = new PDO($dsn, $user, $pass, $this->options);
    }

    protected function getPdo()
    {
        return $this->pdo;
    }

    protected function getStatement()
    {
        return $this->statement;
    }
    
    /**
     * 全局属性设置，包括：列名格式和错误提示类型    可以使用数字也能直接使用参数
     */
    public function setAttributes(array $attributes)
    {
        $pdo = $this->getPdo();

        foreach ($attributes as $key => $val) {
            $pdo->setAttribute($key, $val);
        }
        return true;
    }

    public function setAttribute($key, $value)
    {
        return $this->getPdo()->setAttribute($key, $value);
    }

    /**
     * 生成一个编译好的sql语句模版 你可以使用 ? :name 的形式
     * 返回一个statement对象
     */
    public function prepare($sql = "")
    {
        if ($sql=="") {
            return false;
        }

        $this->statement = $this->getPdo()->prepare($sql);
        return $this->statement;
    }

    /**
     * 执行Sql语句,一般用于增、删、更新或者设置
     * 返回影响的行数
     */
    public function exec($sql)
    {
        if ($sql=="") {
            return false;
        }

        try {
            return $this->getPdo()->exec($sql);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * 执行有返回值的查询,返回PDOStatement
     * 可以通过链式操作,可以通过这个类封装的操作获取数据
     */
    public function query($sql)
    {
        if (empty($sql)) {
            return false;
        }
        $this->statement = $this->getPdo()->query($sql);
        return $this->fetchAll();
    }

    /**
     * 开启事务
     */
    public function beginTA()
    {
        return $this->getPdo()->beginTransaction();
    }

    /**
     * 提交事务
     */
    public function commit()
    {
        return $this->getPdo()->commit();
    }

    /**
     * 事务回滚
     */
    public function rollBack()
    {
        return $this->getPdo()->rollBack();
    }

    public function lastInsertId()
    {
        return $this->getPdo()->lastInsertId();
    }
     
    //**   PDOStatement 类操作封装    **//
     
    /**
     * 让模版执行SQL语句，1、执行编译好的 2、在执行时编译
     */
    public function execute($param = "")
    {
        if (is_array($param)) {
            try {
                return $this->getStatement()->execute($param);
            } catch (Exception $e) {
                return $this->errorInfo();
            }
        }

        try {
            return $this->getStatement()->execute();
        } catch (Exception $e) {
            return $this->errorInfo();
        }
    }
     
    /**
    * 参数1说明：
    * PDO::FETCH_BOTH     也是默认的，两者都有（索引，关联）
    * PDO::FETCH_ASSOC    关联数组
    * PDO::FETCH_NUM      索引
    * PDO::FETCH_OBJ          对象
    * PDO::FETCH_COLUMN   指定列 参数2可以指定要获取的列
    * PDO::FETCH_CLASS        指定自己定义的类
    * PDO::FETCH_FUNC     自定义类 处理返回的数据
    * PDO_FETCH_BOUND 如果你需要设置bindColumn，则使用该参数
    * 参数2说明：
    * 给定要处理这个结果的类或函数
    */
    protected function fetchAll($fetchStyle = PDO::FETCH_ASSOC, $handle = '')
    {
        if ($handle!='') {
             return $this->getStatement()->fetchAll($fetchStyle, $handle);
        }
         return $this->getStatement()->fetchAll($fetchStyle);
    }

     
    /**
     * 以引用的方式绑定变量到占位符(可以只执行一次prepare,
     * 执行多次bindParam达到重复使用的效果)
     */
    public function bindParam($parameter, $variable, $dataType = PDO::PARAM_STR, $length = 6)
    {
        return $this->getStatement()->bindParam($parameter, $variable, $dataType, $length);
    }
     
    /**
    * 返回statement记录集的行数
    */
    public function rowCount()
    {
        return $this->getStatement()->rowCount();
    }

    public function count()
    {
        return $this->rowCount();
    }
     
    /**
     * 关闭编译的模版
     */
    public function close()
    {
        return $this->closeCursor();
    }

    public function closeCursor()
    {
        return $this->getStatement()->closeCursor();
    }

    /**
     * 返回错误信息也包括错误号
     */
    protected function errorInfo()
    {
        return $this->getStatement()->errorInfo();
    }
    /**
     * 返回错误号
     */
    protected function errorCode()
    {
        return $this->getStatement()->errorCode();
    }
     
    //简化操作for insert
    public function insert($table, array $data)
    {
        $cols = $colsStr = $colsArr = array();
        $vals = $valsStr = $valsArr = array();
        foreach ($data as $key => $val) {
            if (is_array($val)) {
                $colsArr[] = $key;

                $val = json_encode(Filter::stripslashesPlus($val), JSON_UNESCAPED_UNICODE);
                $val = Filter::addslashesPlus($val);

                $valsArr[] = "'".$val."'";
            } else {
                $colsStr[] = $key;
                $valsStr[] = is_string($val) ? "'".$val."'" : $val;
            }
        }
        $cols = array_merge($colsStr, $colsArr);
        $vals = array_merge($valsStr, $valsArr);

        $sql  = "INSERT INTO {$table} (";
        $sql .= implode(",", $cols).") VALUES (";
        $sql .= implode(",", $vals).")";
        
        return $this->exec($sql);
    }

    //简化操作for update
    public function update($table, array $data, $wheresqlArr = "")
    {
        $set = array();
        foreach ($data as $key => $val) {
            if (is_array($val)) {
                $val = json_encode(Filter::stripslashesPlus($val), JSON_UNESCAPED_UNICODE);
                $val = Filter::addslashesPlus($val);

                $set[] = $key."='".$val."'";
            } else {
                $val = is_string($val) ? "'".$val."'" : $val;
                $set[] = $key."=".$val;
            }
        }

        $where = $comma = '';
        if (empty($wheresqlArr)) {
            $where = '1';
        } elseif (is_array($wheresqlArr)) {
            foreach ($wheresqlArr as $key => $value) {
                $where .= $comma.'`'.$key.'`'.'=\''.$value.'\'';
                $comma = ' AND ';
            }
        } else {
            $where = $wheresqlArr;
        }
        
        $sql = "UPDATE {$table} SET ";
        $sql .= implode(",", $set);
        $sql .= " WHERE ".$where;
        return $this->exec($sql);
    }
    
    public function delete($table, $wheresqlArr = "")
    {
        $where = $comma = '';
        if (empty($wheresqlArr)) {
            $where = '1';
        } elseif (is_array($wheresqlArr)) {
            foreach ($wheresqlArr as $key => $value) {
                $where .= $comma.'`'.$key.'`'.'=\''.$value.'\'';
                $comma = ' AND ';
            }
        } else {
            $where = $wheresqlArr;
        }
        
        
        $sql = "DELETE FROM {$table} WHERE ".$where;
        return $this->exec($sql);
    }
}
