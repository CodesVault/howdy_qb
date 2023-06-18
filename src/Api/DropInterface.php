<?php

namespace CodesVault\Howdyqb\Api;

interface DropInterface
{
    public function drop();

    public function dropIfExists();
}
