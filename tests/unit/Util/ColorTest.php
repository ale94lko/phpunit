<?php declare(strict_types=1);
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PHPUnit\Util;

use const DIRECTORY_SEPARATOR;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

#[CoversClass(Color::class)]
#[Small]
#[TestDox('Basic ANSI color highlighting support')]
final class ColorTest extends TestCase
{
    #[TestDox('Colorize with $_dataName')]
    #[DataProvider('colorizeProvider')]
    public function testColorize(string $color, string $buffer, string $expected): void
    {
        $this->assertSame($expected, Color::colorize($color, $buffer));
    }

    #[TestDox('Colorize path $path after $prevPath')]
    #[DataProvider('colorizePathProvider')]
    public function testColorizePath(?string $prevPath, string $path, bool $colorizeFilename, string $expected): void
    {
        $this->assertSame($expected, Color::colorizePath($path, $prevPath, $colorizeFilename));
    }

    #[TestDox('dim($m) and colorize(\'dim\',$m) return different ANSI codes')]
    public function testDimAndColorizeDimAreDifferent(): void
    {
        $buffer = 'some string';
        $this->assertNotSame(Color::dim($buffer), Color::colorize('dim', $buffer));
    }

    #[DataProvider('whitespacedStringProvider')]
    #[TestDox('Visualize all whitespace characters in $actual')]
    public function testVisibleWhitespace(string $actual, string $expected): void
    {
        $this->assertSame($expected, Color::visualizeWhitespace($actual, true));
    }

    #[TestDox('Visualize whitespace but ignore EOL')]
    public function testVisualizeWhitespaceButIgnoreEol(): void
    {
        $string = "line1\nline2\n";
        $this->assertSame($string, Color::visualizeWhitespace($string, false));
    }

    #[DataProvider('unnamedDataSetProvider')]
    public function testPrettifyUnnamedDataprovider(int $value): void
    {
        $this->assertSame($value, $value);
    }

    #[DataProvider('namedDataSetProvider')]
    public function testPrettifyNamedDataprovider(int $value): void
    {
        $this->assertSame($value, $value);
    }

    #[DataProvider('namedDataSetProvider')]
    #[TestDox('TestDox shows name of data set $_dataName with value $value')]
    public function testTestdoxDatanameAsParameter(int $value): void
    {
        $this->assertSame($value, $value);
    }

    public function colorizeProvider(): array
    {
        return [
            'no color'                 => ['', 'string', 'string'],
            'one color'                => ['fg-blue', 'string', "\x1b[34mstring\x1b[0m"],
            'multiple colors'          => ['bold,dim,fg-blue,bg-yellow', 'string', "\x1b[1;2;34;43mstring\x1b[0m"],
            'invalid color'            => ['fg-invalid', 'some text', 'some text'],
            'valid and invalid colors' => ['fg-invalid,bg-blue', 'some text', "\e[44msome text\e[0m"],
        ];
    }

    public function colorizePathProvider(): array
    {
        $sep    = DIRECTORY_SEPARATOR;
        $sepDim = Color::dim($sep);

        return [
            'null previous path' => [
                null,
                $sep . 'php' . $sep . 'unit' . $sep . 'test.phpt',
                false,
                $sepDim . 'php' . $sepDim . 'unit' . $sepDim . 'test.phpt',
            ],
            'empty previous path' => [
                '',
                $sep . 'php' . $sep . 'unit' . $sep . 'test.phpt',
                false,
                $sepDim . 'php' . $sepDim . 'unit' . $sepDim . 'test.phpt',
            ],
            'from root' => [
                $sep,
                $sep . 'php' . $sep . 'unit' . $sep . 'test.phpt',
                false,
                $sepDim . 'php' . $sepDim . 'unit' . $sepDim . 'test.phpt',
            ],
            'partial part' => [
                $sep . 'php' . $sep,
                $sep . 'php' . $sep . 'unit' . $sep . 'test.phpt',
                false,
                Color::dim($sep . 'php' . $sep) . 'unit' . $sepDim . 'test.phpt',
            ],
            'colorize filename' => [
                '',
                $sep . '_d-i.r' . $sep . 't-e_s.t.phpt',
                true,
                $sepDim . '_d-i.r' . $sepDim . 't' . Color::dim('-') . 'e' . Color::dim('_') . 's' . Color::dim('.') . 't' . Color::dim('.phpt'),
            ],
        ];
    }

    public function whitespacedStringProvider(): array
    {
        return [
            ['no-spaces',
                'no-spaces',
            ],
            [
                ' space   invaders ',
                "\e[2m·\e[22mspace\e[2m···\e[22minvaders\e[2m·\e[22m",
            ],
            [
                "\tindent, space and \\n\n\\r\r",
                "\e[2m⇥\e[22mindent,\e[2m·\e[22mspace\e[2m·\e[22mand\e[2m·\e[22m\\n\e[2m↵\e[22m\\r\e[2m⟵\e[22m",
            ],
        ];
    }

    public function unnamedDataSetProvider(): array
    {
        return [
            [1],
            [2],
        ];
    }

    public function namedDataSetProvider(): array
    {
        return [
            'one' => [1],
            'two' => [2],
        ];
    }
}
