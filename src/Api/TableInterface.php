<?php

namespace CodesVault\Howdyqb\Api;

interface TableInterface
{
	public function drop();

	public function dropIfExists();

	public function truncate();

	public function hasTable(string $table_name): bool;
}
