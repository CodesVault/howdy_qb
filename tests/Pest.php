<?php

declare(strict_types=1);

// Include Composer autoloader
require_once __DIR__ . '/../vendor/autoload.php';

use CodesVault\Howdyqb\Tests\TestCase;


uses(TestCase::class)->in('Feature');
uses(TestCase::class)->in('Unit');
