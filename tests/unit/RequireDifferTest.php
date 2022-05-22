<?php

declare(strict_types = 1);

namespace Sweetchuck\ComposerSuiteHandler\Tests\Unit;

use Sweetchuck\ComposerSuiteHandler\RequireDiffer;

/**
 * @covers \Sweetchuck\ComposerSuiteHandler\RequireDiffer
 */
class RequireDifferTest extends TestBase
{

    /**
     * @return array<string, mixed>
     */
    public function casesDiff(): array
    {
        return [
            'empty' => [
                [],
                [],
                [],
            ],
            'package added' => [
                [
                    'a/a' => [
                        'constraint' => [
                            'old' => null,
                            'new' => '^1.0',
                        ],
                    ],
                ],
                [],
                [
                    'require' => [
                        'a/a' => '^1.0'
                    ],
                ],
            ],
            'constraint changed' => [
                [
                    'a/a' => [
                        'constraint' => [
                            'old' => '^1.0',
                            'new' => '1.x-dev',
                        ],
                    ],
                ],
                [
                    'require' => [
                        'a/a' => '^1.0'
                    ],
                ],
                [
                    'require' => [
                        'a/a' => '1.x-dev'
                    ],
                ],
            ],
            'package removed' => [
                [
                    'a/a' => [
                        'constraint' => [
                            'old' => '^1.0',
                            'new' => null,
                        ],
                    ],
                ],
                [
                    'require' => [
                        'a/a' => '^1.0'
                    ],
                ],
                [],
            ],
            'package moved to require-dev' => [
                [
                    'a/a' => [
                        'moved' => [
                            'old' => 'require',
                            'new' => 'require-dev',
                        ],
                    ],
                ],
                [
                    'require' => [
                        'a/a' => '^1.0'
                    ],
                ],
                [
                    'require-dev' => [
                        'a/a' => '^1.0'
                    ],
                ],
            ],
            'package moved to require' => [
                [
                    'a/a' => [
                        'moved' => [
                            'old' => 'require-dev',
                            'new' => 'require',
                        ],
                    ],
                ],
                [
                    'require-dev' => [
                        'a/a' => '^1.0'
                    ],
                ],
                [
                    'require' => [
                        'a/a' => '^1.0'
                    ],
                ],
            ],
            'repository same' => [
                [],
                [
                    'repositories' => [
                        'a/a' => [
                            'type' => 'git',
                        ],
                    ],
                    'require' => [
                        'a/a' => '^1.0'
                    ],
                ],
                [
                    'repositories' => [
                        'a/a' => [
                            'type' => 'git',
                        ],
                    ],
                    'require' => [
                        'a/a' => '^1.0'
                    ],
                ],
            ],
            'repository changed' => [
                [
                    'a/a' => [
                        'repository' => [
                            'old' => [
                                'type' => 'git',
                                'url' => 'git://localhost/foo/bar.git',
                            ],
                            'new' => [
                                'type' => 'path',
                                'url' => '../../foo/bar',
                            ],
                        ],
                    ],
                ],
                [
                    'repositories' => [
                        'a/a' => [
                            'type' => 'git',
                            'url' => 'git://localhost/foo/bar.git',
                        ],
                    ],
                    'require' => [
                        'a/a' => '^1.0'
                    ],
                ],
                [
                    'repositories' => [
                        'a/a' => [
                            'type' => 'path',
                            'url' => '../../foo/bar',
                        ],
                    ],
                    'require' => [
                        'a/a' => '^1.0'
                    ],
                ],
            ],
            'indirect dependency from custom repository' => [
                [
                    'a/a' => [
                        'repository' => [
                            'old' => null,
                            'new' => [
                                'type' => 'path',
                                'uri' => 'git://localhost/foo/bar.git',
                            ],
                        ],
                    ],
                ],
                [],
                [
                    'repositories' => [
                        'a/a' => [
                            'type' => 'path',
                            'uri' => 'git://localhost/foo/bar.git',
                        ],
                        'foo' => [
                            'type' => 'composer',
                        ],
                    ],
                ],
            ],
            // @todo more test cases with multiple packages.
        ];
    }

    /**
     * @param array<string, suite-package-diff> $expected
     * @param suite-composer-json $a
     * @param suite-composer-json $b
     *
     * @dataProvider casesDiff
     */
    public function testDiff(array $expected, array $a, array $b): void
    {
        $differ = new RequireDiffer();
        $this->tester->assertSame($expected, $differ->diff($a, $b));
    }
}
