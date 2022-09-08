--TEST--
phpunit -c ../_files/configuration_stop_on_incomplete.xml ./tests/_files/StopOnErrorTestSuite.php
--FILE--
<?php declare(strict_types=1);
$_SERVER['argv'][] = '--do-not-cache-result';
$_SERVER['argv'][] = '-c';
$_SERVER['argv'][] = \realpath(__DIR__ . '/../../_files/configuration_stop_on_incomplete.xml');
$_SERVER['argv'][] = \realpath(__DIR__ . '/../../_files/StopOnErrorTestSuite.php');

require_once __DIR__ . '/../../bootstrap.php';

PHPUnit\TextUI\Application::main();
--EXPECTF--
PHPUnit %s by Sebastian Bergmann and contributors.

Runtime: %s
Configuration: %s

I

Time: %s, Memory: %s

OK, but some tests have issues!
Tests: 1, Assertions: 0, Incomplete: 1.
