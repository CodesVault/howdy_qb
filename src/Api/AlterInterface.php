<?php

namespace CodesVault\Howdyqb\Api;

interface AlterInterface
{
	function add(string $column): self;

	function modify(string $old_column, string $new_column = ''): self;

	function drop(string $column): self;

    function int(int $size = 255): self;

    function bigInt(int $size = 255): self;

    function double(): self;

    function boolean(): self;

    function string(int $size = 255): self;

    function text(int $size = 10000): self;

    function longText(int $size): self;

	public function json(): self;

    function required(): self;

    function primary($columns = []): self;

    function index(array $columns): self;

    function date(): self;

    function dateTime(): self;

	public function timestamp($default = null, $on_update = null): self;

    function unsigned(): self;

    function autoIncrement(): self;

    function default($value): self;

	public function enum(array $allowed): self;

	function nullable(): self;

	public function decimal(int $precision = 8, int $scale = 2): self;

	public function float(): self;

	public function foreignKey(string $column, string $reference_table, string $reference_column): self;

	public function onDelete(string $action): self;

    function getSql();

    function execute();
}
