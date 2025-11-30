<?php

namespace App\Validation;

class RequiredValidation
{
    /**
     * Checks if the value is present when another field has a specific value.
     * @param string|null $value
     * @param string $params
     * @param array $data
     * @return bool
     */
     public function required_if_value(?string $value, string $params, array $data): bool
     {
            // Split the parameters to get the other field and its expected value
            [$otherField, $expectedValue] = explode(',', $params);

            // Check if the other field exists in the data
            if (!isset($data[$otherField])) {
                return false;
            }

            // If the other field's value matches the expected value, check if the current value is present
            if ($data[$otherField] === $expectedValue) {
                return !is_null($value) && $value !== '';
            }

            // If the condition is not met, return true (not required)
            return true;
     }

    /**
     * Validates a Kenyan phone number.
     * @param string|null $value
     * @param string|null $error
     * @return bool
     */
    public function valid_ke_phone(?string $value, ?string &$error = null): bool
    {
        if (is_null($value) || $value === '') {
            return true; // Consider empty value as valid, use 'required' rule to enforce presence
        }

        // Remove any non-digit characters
        $cleanedValue = preg_replace('/\D/', '', $value);

        // Kenyan phone number patterns
        $patterns = [
            '/^07\d{8}$/',      // Local format starting with 07
            '/^2547\d{8}$/',    // International format starting with 2547
            '/^\+2547\d{8}$/',   // International format starting with +2547
            // We'll also consider numbers starting with 01 for landlines
            '/^01\d{8}$/',      // Local landline format starting with 01
            '/^2541\d{8}$/',    // International landline format starting with 2541
            '/^\+2541\d{8}$/',   // International landline format starting with +2541
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $cleanedValue)) {
                return true;
            }
        }

        $error = 'The {field} field must contain a valid Kenyan phone number.';

        return false;
    }
}
