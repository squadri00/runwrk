<?php

namespace App\Domain\Tenancy;

use App\Models\Business;
use Closure;
use RuntimeException;

class CurrentBusiness
{
    protected ?Business $business = null;

    public function set(Business $business): void
    {
        $this->business = $business;
    }

    public function forget(): void
    {
        $this->business = null;
    }

    public function get(): ?Business
    {
        return $this->business;
    }

    public function getOrFail(): Business
    {
        return $this->business ?? throw new RuntimeException('No current business resolved.');
    }

    public function id(): ?int
    {
        return $this->business?->getKey();
    }

    public function has(): bool
    {
        return $this->business !== null;
    }

    public function run(Business $business, Closure $callback): mixed
    {
        $previous = $this->business;
        $this->business = $business;

        try {
            return $callback();
        } finally {
            $this->business = $previous;
        }
    }
}
