<?php

namespace CodesVault\Howdyqb\Tests;

use PHPUnit\Framework\TestCase;
use CodesVault\Howdyqb\Tests\Statement\CreateApi;

class CreateTest extends TestCase
{
    public function testCreateTable()
    {
        $sql = "CREATE TABLE wp_test (ID BIGINT(255) UNSIGNED AUTO_INCREMENT PRIMARY KEY NOT NULL, name VARCHAR(255) NOT NULL, email VARCHAR(255) DEFAULT 'nil', INDEX (ID) )";

        $query =
            CreateApi::create('test')
            ->column('ID')->bigInt()->unsigned()->autoIncrement()->primary()->required()
            ->column('name')->string(255)->required()
            ->column('email')->string(255)->default('nil')
            ->index(['ID'])
            ->getSql();

        $this->assertSame($sql, $query);
    }
}
