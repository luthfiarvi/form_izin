<?php

namespace App\Exceptions;

use Exception;

class PolicyViolationException extends Exception
{
    /** @var array<int,string> */
    protected array $reasons;

    /**
     * @param array<int,string> $reasons
     */
    public function __construct(array $reasons)
    {
        $this->reasons = array_values($reasons);
        parent::__construct($this->buildMessage());
    }

    /**
     * @return array<int,string>
     */
    public function reasons(): array
    {
        return $this->reasons;
    }

    private function buildMessage(): string
    {
        return 'Policy violation: '.implode('; ', $this->reasons);
    }
}

