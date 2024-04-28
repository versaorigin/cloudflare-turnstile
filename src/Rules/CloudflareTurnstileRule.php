<?php

namespace VersaOrigin\CloudflareTurnstile\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use VersaOrigin\CloudflareTurnstile\Facades\CloudflareTurnstile;

class CloudflareTurnstileRule implements ValidationRule
{
    /**
     * Indicates whether the rule should be implicit.
     *
     * @var bool
     */
    public $implicit = true;

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (CloudflareTurnstile::isDisabled()) {
            CloudflareTurnstile::setErrors(['disabled']);

            return;
        }

        if (empty($value)) {
            $fail('The :attribute is required.');

            return;
        }

        if (! is_string($value)) {
            $fail('The :attribute must be a string.');

            return;
        }

        $valid = CloudflareTurnstile::validate($value, request()->ip());

        if (! $valid) {
            if (CloudflareTurnstile::failed()) {
                $fail(CloudflareTurnstile::getErrorMessage());

                return;
            }

            $fail('The :attribute field is invalid.');
        }
    }
}
