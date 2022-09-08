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

use function fclose;
use function fopen;
use function is_resource;
use function preg_replace;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Util\ThrowableToStringMapper;
use stdClass;

#[CoversClass(IsType::class)]
#[Small]
final class IsTypeTest extends ConstraintTestCase
{
    public function testConstraintIsType(): void
    {
        $constraint = Assert::isType('string');

        $this->assertFalse($constraint->evaluate(0, '', true));
        $this->assertTrue($constraint->evaluate('', '', true));
        $this->assertEquals('is of type "string"', $constraint->toString());
        $this->assertCount(1, $constraint);

        try {
            $constraint->evaluate(new stdClass);
        } catch (ExpectationFailedException $e) {
            $this->assertStringMatchesFormat(
                sprintf(
                    <<<'EOF'
Failed asserting that %s Object #%%d () is of type "string".

EOF
                    ,
                    stdClass::class
                ),
                $this->trimnl(ThrowableToStringMapper::map($e))
            );

            return;
        }

        $this->fail();
    }

    public function testConstraintIsType2(): void
    {
        $constraint = Assert::isType('string');

        try {
            $constraint->evaluate(new stdClass, 'custom message');
        } catch (ExpectationFailedException $e) {
            $this->assertStringMatchesFormat(
                sprintf(
                    <<<'EOF'
custom message
Failed asserting that %s Object #%%d () is of type "string".

EOF
                    ,
                    stdClass::class
                ),
                $this->trimnl(ThrowableToStringMapper::map($e))
            );

            return;
        }

        $this->fail();
    }

    #[DataProvider('resources')]
    public function testConstraintIsResourceTypeEvaluatesCorrectlyWithResources($resource): void
    {
        $constraint = Assert::isType('resource');

        $this->assertTrue($constraint->evaluate($resource, '', true));

        if (is_resource($resource)) {
            @fclose($resource);
        }
    }

    public function resources(): array
    {
        $fh = fopen(__FILE__, 'r');
        fclose($fh);

        return [
            'open resource'   => [fopen(__FILE__, 'r')],
            'closed resource' => [$fh],
        ];
    }

    public function testIterableTypeIsSupported(): void
    {
        $constraint = Assert::isType('iterable');

        $this->assertFalse($constraint->evaluate('', '', true));
        $this->assertTrue($constraint->evaluate([], '', true));
        $this->assertEquals('is of type "iterable"', $constraint->toString());
    }

    public function testTypeCanBeNull(): void
    {
        $constraint = Assert::isType('null');

        $this->assertNull($constraint->evaluate(null));
        $this->assertEquals('is of type "null"', $constraint->toString());
    }

    public function testTypeCanNotBeAnUndefinedOne(): void
    {
        try {
            Assert::isType('diverse');
        } catch (\PHPUnit\Framework\Exception $e) {
            $this->assertEquals(
                <<<EOF
PHPUnit\Framework\Exception: Type specified for PHPUnit\Framework\Constraint\IsType <diverse> is not a valid type.

EOF
                ,
                ThrowableToStringMapper::map($e)
            );
        }
    }

    /**
     * Removes spaces in front of newlines.
     */
    private function trimnl(string $string): string
    {
        return preg_replace('/[ ]*\n/', "\n", $string);
    }
}
