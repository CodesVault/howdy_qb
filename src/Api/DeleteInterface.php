<?php

namespace CodesVault\Howdyqb\Api;

interface DeleteInterface extends WhereClauseInterface
{
    public function drop();

    public function dropIfExists();

    public function getSql();

    public function execute();
}
