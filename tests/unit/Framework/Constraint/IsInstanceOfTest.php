<?php declare(strict_types=1);
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PHPUnit\Framework\Constraint;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Util\ThrowableToStringMapper;
use ReflectionException;
use stdClass;

#[CoversClass(IsInstanceOf::class)]
#[Small]
final class IsInstanceOfTest extends ConstraintTestCase
{
    public function testConstraintInstanceOf(): void
    {
        $constraint = new IsInstanceOf(stdClass::class);

        $this->assertTrue($constraint->evaluate(new stdClass, '', true));
    }

    public function testConstraintFailsOnString(): void
    {
        $constraint = new IsInstanceOf(stdClass::class);

        try {
            $constraint->evaluate(stdClass::class);
        } catch (ExpectationFailedException $e) {
            $this->assertSame(
                sprintf(
                    <<<'EOT'
Failed asserting that '%s' is an instance of class "%s".

EOT
                    ,
                    stdClass::class,
                    stdClass::class
                ),
                ThrowableToStringMapper::map($e)
            );
        }
    }

    public function testCronstraintsThrowsReflectionException(): void
    {
        $this->throwException(new ReflectionException);

        $constraint = new IsInstanceOf(NotExistingClass::class);

        $this->assertSame(
            sprintf(
                'is instance of class "%s"',
                NotExistingClass::class
            ),
            $constraint->toString()
        );
    }
}
