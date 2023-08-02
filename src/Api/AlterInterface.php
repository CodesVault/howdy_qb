<?php

namespace CodesVault\Howdyqb\Api;

interface AlterInterface
{
	function add(string $column): self;

	function modify(string $old_column, string $new_column = ''): self;

	function drop(string $column): self;

    function int(int $size = 255): self;

    function bigInt(int $size = 255): self;

    function double(int $size = 255, int $d = 2): self;

    function boolean(): self;

    function string(int $size = 255): self;

    function text(int $size = 10000): self;

    function longText(int $size): self;

    function required(): self;

    function primary($columns = []): self;

    function index(array $columns): self;

    function date(): self;

    function dateTime(): self;

    function unsigned(): self;

    function autoIncrement(): self;

    function default($value): self;

	function nullable(): self;

	public function foreignKey(string $column, string $reference_table, string $reference_column): self;

	public function onDelete(string $action): self;

    function getSql();

    function execute();
}
