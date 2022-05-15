<?php

declare(strict_types = 1);

namespace Sweetchuck\ComposerSuiteHandler;

/**
 * Compares two composer.json, and shows that what are the differences between
 * "require" and "require-dev".
 */
class RequireDiffer
{

    /**
     * @var suite-composer-json
     */
    protected array $aJson = [];

    /**
     * @var suite-composer-json
     */
    protected array $bJson = [];

    /**
     * @param suite-composer-json $a
     * @param suite-composer-json $b
     *
     * @return array<string, suite-package-diff>
     */
    public function diff(array $a, array $b): array
    {
        $this->aJson = $a;
        $this->bJson = $b;
        $result = [];
        foreach ($this->fetchPackageNames() as $name) {
            $diff = $this->diffPackage($name);
            if ($diff !== null) {
                $result[$name] = $diff;
            }
        }

        $this->aJson = [];
        $this->bJson = [];

        return $result;
    }

    /**
     * @return array<string>
     */
    protected function fetchPackageNames(): array
    {
        $filter = function (string $key): bool {
            return preg_match('@^[a-z\d_-]+/[a-z\d_-]+$@', $key) === 1;
        };

        return array_unique(array_merge(
            array_keys($this->aJson['require'] ?? []),
            array_keys($this->aJson['require-dev'] ?? []),
            array_keys($this->bJson['require'] ?? []),
            array_keys($this->bJson['require-dev'] ?? []),
            array_filter(
                array_keys($this->aJson['repositories'] ?? []),
                $filter,
            ),
            array_filter(
                array_keys($this->bJson['repositories'] ?? []),
                $filter,
            ),
        ));
    }

    /**
     * @return suite-package-diff
     */
    protected function diffPackage(string $name): ?array
    {
        $diff = null;
        $aRepo = $this->aJson['repositories'][$name] ?? null;
        $bRepo = $this->bJson['repositories'][$name] ?? null;
        if ($aRepo !== $bRepo) {
            $diff['repository'] = [
                'old' => $aRepo,
                'new' => $bRepo,
            ];
        }

        $aConstraint = $this->aJson['require'][$name] ?? $this->aJson['require-dev'][$name] ?? null;
        $bConstraint = $this->bJson['require'][$name] ?? $this->bJson['require-dev'][$name] ?? null;
        if ($aConstraint !== $bConstraint) {
            $diff['constraint'] = [
                'old' => $aConstraint,
                'new' => $bConstraint,
            ];
        }

        if (isset($this->aJson['require'][$name]) && isset($this->bJson['require-dev'][$name])) {
            $diff['moved'] = [
                'old' => 'require',
                'new' => 'require-dev',
            ];
        } elseif (isset($this->aJson['require-dev'][$name]) && isset($this->bJson['require'][$name])) {
            $diff['moved'] = [
                'old' => 'require-dev',
                'new' => 'require',
            ];
        }

        return $diff;
    }
}
