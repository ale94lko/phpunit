<?php declare(strict_types=1);
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PHPUnit\TextUI\Output;

use PHPUnit\TestRunner\TestResult\TestResult;
use PHPUnit\Util\Printer;

/**
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 */
abstract class ResultPrinter
{
    private Printer $printer;
    private bool $colors;

    public function __construct(Printer $printer, bool $colors)
    {
        $this->printer = $printer;
        $this->colors  = $colors;
    }

    public function flush(): void
    {
        $this->printer->flush();
    }

    abstract public function printResult(TestResult $result): void;

    protected function printer(): Printer
    {
        return $this->printer;
    }

    protected function colors(): bool
    {
        return $this->colors;
    }
}
