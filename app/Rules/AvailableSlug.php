<?php

namespace App\Rules;

use App\Models\Business;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class AvailableSlug implements ValidationRule
{
    public function __construct(private ?int $ignoreId = null) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $slug = (string) $value;

        if (! preg_match('/^[a-z0-9]+(-[a-z0-9]+)*$/', $slug)) {
            $fail('Use lowercase letters, numbers and single hyphens only.');

            return;
        }

        if (in_array($slug, config('runwrk.reserved_paths'), true)) {
            $fail('That name is reserved.');

            return;
        }

        $taken = Business::withTrashed()->where('slug', $slug)
            ->when($this->ignoreId, fn ($q) => $q->where('id', '!=', $this->ignoreId))
            ->exists();

        if ($taken) {
            $fail('That name is already taken.');
        }
    }
}
