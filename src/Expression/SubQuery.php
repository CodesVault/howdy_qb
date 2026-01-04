<?php

namespace CodesVault\Howdyqb\Expression;

use CodesVault\Howdyqb\Statement\Select;

class SubQuery extends Select
{
	public function __construct($db)
	{
		$this->db = $db;
	}

	public function select(...$columns)
	{
		$this->columns(...$columns);
		return $this;
	}

	public function columnAlias()
	{
		return $this->sql['alias'] ?? null;
	}
}
