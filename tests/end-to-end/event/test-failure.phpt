--TEST--
The right events are emitted in the right order for a failed test
--SKIPIF--
<?php declare(strict_types=1);
if (DIRECTORY_SEPARATOR === '\\') {
    print "skip: this test does not work on Windows / GitHub Actions\n";
}
--FILE--
<?php declare(strict_types=1);
$traceFile = tempnam(sys_get_temp_dir(), __FILE__);

$_SERVER['argv'][] = '--do-not-cache-result';
$_SERVER['argv'][] = '--no-configuration';
$_SERVER['argv'][] = '--no-output';
$_SERVER['argv'][] = '--log-events-text';
$_SERVER['argv'][] = $traceFile;
$_SERVER['argv'][] = __DIR__ . '/_files/FailureTest.php';

require __DIR__ . '/../../bootstrap.php';

PHPUnit\TextUI\Application::main(false);

print file_get_contents($traceFile);

unlink($traceFile);
--EXPECTF--
Test Runner Started (PHPUnit %s using %s)
Test Runner Configured
Test Suite Loaded (1 test)
Test Suite Sorted
Event Facade Sealed
Test Runner Execution Started (1 test)
Test Suite Started (PHPUnit\TestFixture\Event\FailureTest, 1 test)
Test Preparation Started (PHPUnit\TestFixture\Event\FailureTest::testFailure)
Test Prepared (PHPUnit\TestFixture\Event\FailureTest::testFailure)
Assertion Failed (Constraint: is true, Value: false)
Test Failed (PHPUnit\TestFixture\Event\FailureTest::testFailure)
Failed asserting that false is true.
Test Finished (PHPUnit\TestFixture\Event\FailureTest::testFailure)
Test Suite Finished (PHPUnit\TestFixture\Event\FailureTest, 1 test)
Test Runner Execution Finished
Test Runner Finished
