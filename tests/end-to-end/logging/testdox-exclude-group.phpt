--TEST--
phpunit --testdox-text php://stdout --testdox-exclude-group one ../../_files/TestDoxGroupTest.php
--XFAIL--
TestDox logging has not been migrated to events yet.
See https://github.com/sebastianbergmann/phpunit/issues/4702 for details.
--FILE--
<?php declare(strict_types=1);
$_SERVER['argv'][] = '--do-not-cache-result';
$_SERVER['argv'][] = '--no-configuration';
$_SERVER['argv'][] = '--testdox-text';
$_SERVER['argv'][] = 'php://stdout';
$_SERVER['argv'][] = '--testdox-exclude-group';
$_SERVER['argv'][] = 'one';
$_SERVER['argv'][] = \realpath(__DIR__ . '/_files/TestDoxGroupTest.php');

require_once __DIR__ . '/../../bootstrap.php';

PHPUnit\TextUI\Application::main();
--EXPECTF--
PHPUnit %s by Sebastian Bergmann and contributors.

Runtime: %s

.Dox Group (PHPUnit\TestFixture\DoxGroup)
.                                                                  2 / 2 (100%) [x] Two



Time: %s, Memory: %s

OK (2 tests, 2 assertions)
