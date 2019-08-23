<?php

namespace App\Bitbucket\Services;

class Branches
{
    private $exemptTypes = [
        'candidate',
        'release',
        'revert',
    ];

    public function type(string $branch)
    {
        $parts = explode('/', $branch);

        return strtolower($parts[0]);
    }

    public function isExempt(string $branch)
    {
        return in_array($this->type($branch), $this->exemptTypes)
            || preg_match('/^revert-pr-\d*$/', $this->type($branch));
    }
}
