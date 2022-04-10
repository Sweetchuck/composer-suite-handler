<?php

declare(strict_types = 1);

namespace Sweetchuck\ComposerSuiteHandler;

class RequirementComparer
{

    // phpcs:disable Generic.Files.LineLength.TooLong
    const PLATFORM_PACKAGE_REGEX = '{^(?:php(?:-64bit|-ipv6|-zts|-debug)?|hhvm|(?:ext|lib)-[a-z0-9](?:[_.-]?[a-z0-9]+)*|composer(?:-(?:plugin|runtime)-api)?)$}iD';
    // phpcs:enable Generic.Files.LineLength.TooLong

    /**
     * @param string $a
     * @param string $b
     */
    public function __invoke($a, $b): int
    {
        return $this->compare($a, $b);
    }

    /**
     * @param string $a
     * @param string $b
     */
    public function compare($a, $b): int
    {
        return strnatcmp($this->prefix($a), $this->prefix($b));
    }

    protected function prefix(string $requirement): string
    {
        if (preg_match(static::PLATFORM_PACKAGE_REGEX, $requirement) === 1) {
            return preg_replace(
                [
                    '/^php/',
                    '/^hhvm/',
                    '/^ext/',
                    '/^lib/',
                    '/^\D/',
                ],
                [
                    '0-$0',
                    '1-$0',
                    '2-$0',
                    '3-$0',
                    '4-$0',
                ],
                $requirement,
            );
        }

        return "5-$requirement";
    }
}
