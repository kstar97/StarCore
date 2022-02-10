<?php


namespace StarCore\Core;


class SqlGen
{
    private string $table;
    private string $field;
    private string $where;
    private string $order;
    private string $limit;
    private array $whereData;

    public function __construct()
    {
        $this->reset();
    }

    public function reset()
    {
        $this->table = '';
        $this->field = '*';
        $this->where = '';
        $this->order = '';
        $this->limit = '';
        $this->whereData = [];
    }

    /**
     * @param string $table
     */
    public function table(string $table)
    {
        $this->table = "`{$table}`";
    }

    /**
     * @param string ...$fields
     */
    public function field(string ...$fields)
    {
        $this->field = '`' . implode('`,`', $fields) . '`';
    }

    /**
     * @param string $field
     * @param string $Operator
     * @param string|int $value
     * @param string $logic
     */
    public function where(string $field, string $Operator, string|int $value, string $logic = "AND")
    {
        $this->where .= $this->where == "" ? "WHERE {$field} {$Operator} ?" : " {$logic} {$field} {$Operator} ?";
        $this->whereData[] = $value;
    }

    /**
     * @param string $order
     */
    public function order(string $order)
    {
        $this->order = "ORDER BY " . $order;
    }

    /**
     * @param int ...$limit
     */
    public function limit(int ...$limit)
    {
        $this->limit = "LIMIT " . implode(',', $limit);
    }

    /**
     * @return array
     */
    public function whereData(): array
    {
        return $this->whereData;
    }

    /**
     * @param array $data
     * @return string
     */
    public function insert(array $data): string
    {
        #INSERT INTO `user` (`name`, `home`) VALUES ('xxx', 'xxx')
        $fields = '`' . implode('`,`', array_keys($data)) . '`';
        $values = implode(",", array_fill(0, count($data), '?'));
        return "INSERT INTO {$this->table} ({$fields}) VALUES ({$values})";
    }

    /**
     * @return string
     */
    public function select(): string
    {
        #SELECT *　FROM user WHERE 1=1 ORDER BY LIMIT 1
        $sql = "SELECT {$this->field} FROM {$this->table}";
        $sql .= $this->where != "" ? " " . $this->where : "";
        $sql .= $this->order != "" ? " " . $this->order : "";
        $sql .= $this->limit != "" ? " " . $this->limit : "";
        return $sql;
    }

    /**
     * @param array $data
     * @return string
     */
    public function update(array $data): string
    {
        #UPDATE `user` SET `home`='aaa' WHERE (`id`='1')
        $fields = '`' . implode('`=? ,`', array_keys($data)) . '`=?';
        $sql = "UPDATE {$this->table} SET {$fields}";
        $sql .= $this->where != "" ? " " . $this->where : "";
        return $sql;
    }

    /**
     * @param array $data
     * @return string
     */
    public function delete(): string
    {
        #DELETE FROM `user` WHERE (`id`='1')
        $sql = "DELETE FROM {$this->table}";
        $sql .= $this->where != "" ? " " . $this->where : "";
        return $sql;
    }
}