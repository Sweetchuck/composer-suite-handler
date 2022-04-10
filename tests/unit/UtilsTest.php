<?php

declare(strict_types = 1);

namespace Sweetchuck\ComposerSuiteHandler\Tests\Unit;

use Sweetchuck\ComposerSuiteHandler\Utils;

/**
 * @covers \Sweetchuck\ComposerSuiteHandler\Utils
 */
class UtilsTest extends TestBase
{

    /**
     * @return array<mixed>
     */
    public function casesIsVector(): array
    {
        return [
            'empty' => [false, []],
            'vector' => [true, ['a', 'b']],
            'first is not zero' => [false, [1 => 'a']],
            'wrong order' => [false, [1 => 'a', 0 => 'b']],
            'associative 1' => [false, ['a' => 'b']],
            'associative 2' => [false, ['a', 'b' => 'c', 'd']],
            'associative 3' => [false, ['a', 'b' => 'c', 2 => 'd']],
        ];
    }

    /**
     * @param array<mixed> $items
     *
     * @dataProvider casesIsVector
     */
    public function testIsVector(bool $expected, array $items): void
    {
        $this->tester->assertSame($expected, Utils::isVector($items));
    }

    /**
     * @return array<mixed>
     */
    public function casesIsDefaultComposer(): array
    {
        return [
            // To make it easier the directory doesn't matter,
            // only the basename is relevant.
            'default with ./' => [true, './composer.json'],
            'default without ./' => [true, 'composer.json'],
            'default in parent' => [true, '../composer.json'],
            'default somewhere' => [true, 'a/b/composer.json'],
            'default root' => [true, '/composer.json'],
            'other 1' => [false, './composer.other.json'],
            'other 2' => [false, 'composer.other.json'],
        ];
    }

    /**
     * @dataProvider casesIsDefaultComposer
     */
    public function testIsDefaultComposer(bool $expected, string $fileName): void
    {
        $this->tester->assertSame($expected, Utils::isDefaultComposer($fileName));
    }

    public function testEncode(): void
    {
        $this->tester->assertSame(
            implode("\n", [
                '{',
                '    "name": "a/b",',
                '    "path": "c\\\\d"',
                '}',
            ]),
            Utils::encode([
                'name' => 'a/b',
                'path' => 'c\\d',
            ]),
        );
    }

    public function testDecode(): void
    {
        $string = implode("\n", [
            '{',
            '    "name": "a/b",',
            '    "path": "c\\\\d"',
            '}',
        ]);

        $this->tester->assertIsArray(Utils::decode($string));
    }
}
