<?php

namespace CodesVault\WPqb\Api;

interface CreateInterface
{
    function column(string $column_name): self;

    function int(int $size): self;

    function bigInt(int $size): self;

    function double(int $size, int $d): self;

    function boolean(): self;

    function string(int $size): self;

    function text(int $size): self;

    function longText(int $size): self;

    function required(): self;

    function primary($columns): self;

    function date(): self;

    function dateTime(): self;

    function unsigned(): self;

    function autoIncrement(): self;

    function default($value): self;

    function execute();
}
