<?php

namespace App\Console\Traits;

trait Skippable
{
    /**
     * @param  int    $i
     * @return boolean
     */
    public function shouldSkip(int $i)
    {
        return !$this->option('skip') || $i <= $this->option('skip');
    }
}
