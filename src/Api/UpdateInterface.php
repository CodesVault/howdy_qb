<?php

namespace CodesVault\Howdyqb\Api;

interface UpdateInterface extends WhereClauseInterface
{
    function getSql();

    public function execute();
}
