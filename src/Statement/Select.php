<?php

namespace CodesVault\Howdyqb\Statement;

use CodesVault\Howdyqb\Api\SelectInterface;
use CodesVault\Howdyqb\Clause\JoinClause;
use CodesVault\Howdyqb\Clause\SqlCore;
use CodesVault\Howdyqb\Clause\WhereClause;
use CodesVault\Howdyqb\SqlGenerator;
use CodesVault\Howdyqb\Utilities;

class Select implements SelectInterface
{
	// bring all SQL Clause
	use SqlCore, WhereClause, JoinClause;

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
