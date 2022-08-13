<?php

namespace CodesVault\Howdyqb\Tests;

use PHPUnit\Framework\TestCase;
use CodesVault\Howdyqb\Tests\Statement\UpdateApi;

class UpdateTest extends TestCase
{
    public function testUpdate()
    {
        $sql = 'UPDATE wp_querybuilders SET name=?, email=?';
        $query =
            UpdateApi::update('querybuilders', [
                'name' => 'Keramot UL',
                'email' => 'keramotul.islam@gmail.com'
            ])
            ->getSql();

        $this->assertEquals($sql, $query);
    }

    public function testWhere()
    {
        $sql = 'UPDATE wp_querybuilders SET name=?, email=? WHERE ID = ?';
        $query =
            UpdateApi::update('querybuilders', [
                'name' => 'Keramot UL',
                'email' => 'keramotul.islam@gmail.com'
            ])
            ->where('ID', '=', 1)
            ->getSql();

        $this->assertEquals($sql, $query);
    }

    public function testParams()
    {
        $params = ['Keramot UL', 'keramotul.islam@gmail.com', 1];
        $query =
            UpdateApi::update('querybuilders', [
                'name' => 'Keramot UL',
                'email' => 'keramotul.islam@gmail.com'
            ])
            ->where('ID', '=', 1)
            ->getParams();

        $this->assertEquals($params, $query);
    }
}
