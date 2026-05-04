<?php

namespace App\Rules;

use App\Services\SafeBrowsingService;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class SafeUrl implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $safeBrowsing = app(SafeBrowsingService::class);

        if (!$safeBrowsing->isSafe($value)) {
            $fail('Esta URL foi identificada como perigosa pelo Google Safe Browsing e nao pode ser encurtada.');
        }
    }
}
