<?php

namespace CodesVault\Howdyqb\Api;

interface CreateInterface
{
    public function column(string $column_name): self;

    public function int(int $size = 255): self;

    public function bigInt(int $size = 255): self;

    public function double(int $size = 255, int $d = 2): self;

    public function boolean(): self;

    public function string(int $size = 255): self;

    public function text(int $size = 10000): self;

    public function longText(int $size): self;

    public function required(): self;

    public function nullable(): self;

    public function primary($columns = []): self;

    public function index(array $columns): self;

    public function date(): self;

    public function dateTime(): self;

    public function unsigned(): self;

    public function autoIncrement(): self;

    public function default($value): self;

    public function foreignKey(string $column, string $reference_table, string $reference_column): self;

    public function onDelete(string $action): self;

	public function getSql();

    public function execute();
}
