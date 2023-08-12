<?php

namespace CodesVault\Howdyqb\Statement;

use CodesVault\Howdyqb\Api\SelectInterface;
use CodesVault\Howdyqb\Connect;
use CodesVault\Howdyqb\QueryFactory;
use CodesVault\Howdyqb\SqlGenerator;
use CodesVault\Howdyqb\Utilities;

class Select implements SelectInterface
{
    protected $db;
    protected $sql = [];
    protected $params = [];
    protected $table_name;
    private $row_count = 0;

    public function __construct($db)
    {
        $this->db = $db;
    }

    protected function start()
    {
        $this->sql['start']['select'] = 'SELECT';
    }

    protected function setStartExpression()
    {
        $sql = '';
        if (isset($this->sql['start']['select'])) {
            $sql .= 'SELECT ';
        }
        if (isset($this->sql['start']['distinct'])) {
            $sql .= 'DISTINCT ';
        }
        if (isset($this->sql['columns'])) {
            $sql .= $this->sql['columns'];
            unset($this->sql['columns']);
        }
        if (isset($this->sql['start']['count'])) {
            $sql .= ', ' . $this->sql['start']['count'];
        }
        return $this->sql['start'] = $sql;
    }

    public function distinct(): self
    {
        $this->sql['start']['distinct'] = 'DISTINCT';
        return $this;
    }

    public function columns(...$columns): self
    {
        $this->sql['columns'] = implode(', ', $columns);
        return $this;
    }

    public function alias(string $name): self
    {
        $this->sql['alias'] = 'AS ' . $name;
        return $this;
    }

    public function from(string $table_name): self
    {
        $this->sql['table_name'] = 'FROM ' . Utilities::get_db_configs()->prefix . $table_name;
        return $this;
    }

    public function where($column, ?string $operator = null, $value = null): self
    {
        if ( is_callable( $column ) ) {
            call_user_func( $column, $this );
            return $this;
        }
        $this->sql['where'] = 'WHERE ' . $column . ' ' . $operator . ' ' . Utilities::get_placeholder($this->db, $value);
        $this->params[] = $value;
        return $this;
    }

    public function andWhere(string $column, string $operator = null, $value = null): self
    {
        $this->sql['andWhere'] = 'AND ' . $column . ' ' . $operator . ' ' . Utilities::get_placeholder($this->db, $value);
        $this->params[] = $value;
        return $this;
    }

    public function orWhere(string $column, string $operator = null, $value = null): self
    {
        $this->sql['orWhere'] = 'OR ' . $column . ' ' . $operator . ' ' . Utilities::get_placeholder($this->db, $value);
        $this->params[] = $value;
        return $this;
    }

    public function whereNot(string $column, string $operator = null, $value = null): self
    {
        $this->sql['whereNot'] = 'WHERE NOT ' . $column . ' ' . $operator . ' ' . Utilities::get_placeholder($this->db, $value);
        $this->params[] = $value;
        return $this;
    }

    public function andNot(string $column, string $operator = null, $value = null): self
    {
        $this->sql['andNot'] = 'AND NOT ' . $column . ' ' . $operator . ' ' . Utilities::get_placeholder($this->db, $value);
        $this->params[] = $value;
        return $this;
    }

    public function whereIn(string $column, ...$value): self
    {
        $this->sql['whereIn'] = 'WHERE ' . $column . ' IN (' . implode( ', ', $value ) . ')';
        return $this;
    }

    public function orderBy($column, string $sort_type): self
    {
        $col = is_array( $column ) ? implode( ', ', $column ) : $column;
        $this->sql['orderBy'] = 'ORDER BY ' . $col . ' ' . $sort_type;
        return $this;
    }

    public function groupBy($column): self
    {
        $col = is_array( $column ) ? implode( ', ', $column ) : $column;
        $this->sql['groupBy'] = 'GROUP BY ' . $col;
        return $this;
    }

    public function limit(int $count): self
    {
        $this->sql['limit'] = 'LIMIT ' . $count;
        return $this;
    }

    public function offset(int $count): self
    {
        $this->sql['offset'] = 'OFFSET ' . $count;
        return $this;
    }

    public function count(string $column, string $alias = ''): self
    {
        $alias = $alias ? ' ' . $alias : '';
        $this->sql['start']['count'] = 'COUNT(' . $column . ')' . $alias;
        return $this;
    }

    private function setJoin($table_name, string $col1 = null, string $col2 = null, string $joinType = 'JOIN'): self
    {
        $table_names = [];
        if (is_array($table_name)) {
            foreach ($table_name as $table) {
                $table_names[] = Utilities::get_db_configs()->prefix . $table;
            }
        } else {
            $table_names[] = Utilities::get_db_configs()->prefix . $table_name;
        }

        $table = '';
        if (count($table_names) > 1) {
            $table = '(' . implode(',', $table_names) . ')';
        } else {
            $table = $table_names[0];
        }

        $this->sql['join'] = $joinType . ' ' . $table;
        if ($col1 && $col2) {
            $this->sql['join'] .= ' ON ' . $col1 . ' = ' . $col2;
        }
        return $this;
    }

    public function join($table_name, string $col1 = null, string $col2 = null): self
    {
        return $this->setJoin($table_name, $col1, $col2);
    }

    public function innerJoin($table_name, string $col1 = null, string $col2 = null): self
    {
        return $this->setJoin($table_name, $col1, $col2, 'INNER JOIN');
    }

    public function leftJoin($table_name, string $col1 = null, string $col2 = null): self
    {
        return $this->setJoin($table_name, $col1, $col2, 'LEFT JOIN');
    }

    public function rightJoin($table_name, string $col1 = null, string $col2 = null): self
    {
        return $this->setJoin($table_name, $col1, $col2, 'RIGHT JOIN');
    }

    public function raw(string $sql): self
    {
        $this->sql['raw_'. $this->row_count++] = $sql;
        return $this;
    }

    protected function setAlias()
    {
        if (! isset($this->sql['alias'])) return;

        $this->sql['table_name'] .= ' ' . $this->sql['alias'];
        unset($this->sql['alias']);
    }

    private function fetch($query, array $args = [])
    {
        try {
            return $this->driver_execute($query, $args);
        } catch (\Exception $exception) {
            Utilities::throughException($exception);
        }
    }

    private function driver_execute($sql, $placeholders)
    {
        $driver = $this->db;
        if (class_exists('wpdb') && $driver instanceof \wpdb) {
            if (empty($placeholders)) {
                return $driver->get_results($sql, ARRAY_A);
            }

            return $driver->get_results(
                $driver->prepare($sql, $placeholders),
                ARRAY_A
            );
        }

        $data = $driver->prepare($sql);
        $data->execute($placeholders);
        return $data->fetchAll(\PDO::FETCH_ASSOC);
    }

    // get only sql query string
    public function getSql()
    {
        $this->start();
        $this->setAlias();
        $this->setStartExpression();
        $query = [
            'query' => SqlGenerator::select($this->sql),
            'params' => $this->params
        ];
        return $query;
    }

    // get data from database
    public function get()
    {
        $this->start();
        $this->setAlias();
        $this->setStartExpression();
        $query = SqlGenerator::select($this->sql);

        $data = $this->fetch($query, $this->params);
        return $data;
    }
}
