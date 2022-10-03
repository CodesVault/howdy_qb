<?php

namespace CodesVault\Howdyqb\Api;

interface CreateInterface
{
    function column(string $column_name): self;

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

    function foreignKey(string $column, string $reference_table, string $reference_column): self;

    function execute();
}
