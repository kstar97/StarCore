<?php


namespace StarCore\Core;


use PDO;
use StarCore\Exception\DBException;
use StarCore\Exception\MainException;
use Throwable;

class DB
{
    private PDO $link;
    private SqlGen $sqlGen;

    public function __construct($host, $port, $dbname, $charset, $user, $passwd)
    {
        $connectStr = sprintf("mysql:host=%s;port=%d;dbname=%s;charset=%s", $host, $port, $dbname, $charset);
        $this->link = new PDO($connectStr, $user, $passwd);
        $this->link->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->link->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        //sql语句生成器
        $this->sqlGen = new SqlGen();
    }

    //Sql语句生成--------------------start
    public function table(string $table): DB
    {
        $this->sqlGen->table($table);
        return $this;
    }

    public function field(string ...$fields): DB
    {
        $this->sqlGen->field(...$fields);
        return $this;
    }

    public function where(string $field, string $Operator, string|int $value, string $logic = "AND"): DB
    {
        $this->sqlGen->where($field, $Operator, $value, $logic);
        return $this;
    }

    public function order(string $order): DB
    {
        $this->sqlGen->order($order);
        return $this;
    }

    public function limit(int ...$limit): DB
    {
        $this->sqlGen->limit(...$limit);
        return $this;
    }
    //Sql语句生成--------------------end

    //数据库操作：查增改删-------------start
    /**
     * @param array $data
     * @return array
     */
    public function insert(array $data): int
    {
        $sql = $this->sqlGen->insert($data);
        $this->sqlGen->reset();
        return $this->execute($sql, array_values($data));
    }

    /**
     * @return array
     */
    public function select(): array
    {
        $sql = $this->sqlGen->select();
        $where = $this->sqlGen->whereData();
        $this->sqlGen->reset();
        return $this->query($sql, $where);
    }

    /**
     * @return array|null
     */
    public function selectOne(): ?array
    {
        $sql = $this->sqlGen->select();
        $where = $this->sqlGen->whereData();
        $this->sqlGen->reset();
        $data = $this->query($sql, $where);

        return count($data) > 0 ? $data[0] : null;
    }

    /**
     * @param array $data
     * @return int
     */
    public function update(array $data): int
    {
        $sql = $this->sqlGen->update($data);
        $where = $this->sqlGen->whereData();
        $this->sqlGen->reset();
        $updateData = array_merge(array_values($data), $where);
        return $this->execute($sql, $updateData);
    }

    /**
     * @return int
     * @throws DBException
     */
    public function delete(): int
    {
        $sql = $this->sqlGen->delete();
        $where = $this->sqlGen->whereData();
        if (empty($where)) {
            throw new DBException("删除操作必须带条件Where");
        }
        $this->sqlGen->reset();
        return $this->execute($sql, $where);
    }
    //数据库操作：查增改删-------------end


    //数据库操作（含预处理防注入）------start
    /**
     * @param $sql
     * @param array $param
     * @return array
     */
    public function query($sql, array $param = []): array
    {
        $data = [];
        $pre = $this->link->prepare($sql);
        try {
            $pre->execute($param);
            $data = $pre->fetchAll();
        } catch (Throwable $exception) {
            $errorInfo = $this->errorInfo($sql, $param);
            MainException::Render($exception, $errorInfo);
        }
        return $data;
    }

    /**
     * @param $sql
     * @param array $param
     * @return int
     */
    public function execute($sql, array $param = []): int
    {
        $rows = 0;
        try {
            $pre = $this->link->prepare($sql);
            $pre->execute($param);
            $rows = $pre->rowCount();
        } catch (Throwable $exception) {
            $errorInfo = $this->errorInfo($sql, $param);
            MainException::Render($exception, $errorInfo);
        }
        return $rows;
    }

    //数据库操作（含预处理防注入）------end

    public function lastInsertId($name = null): int
    {
        return $this->link->lastInsertId($name);
    }

    private function errorInfo(string $sql, array $param): string
    {
        foreach ($param as $v) {
            $pos = strpos($sql, "?");
            if ($pos !== false) {
                $sql = substr_replace($sql, var_export($v, true), $pos, 1);
            }
        }
        return "SQL[{$sql}]";
    }
}