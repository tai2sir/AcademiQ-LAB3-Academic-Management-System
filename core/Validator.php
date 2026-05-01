<?php

/**
 * Input Validator - Provides validation rules for form inputs.
 */

declare(strict_types=1);

final class Validator
{
    // Validate required field
    /**
     * @return list<string> errors
     */
    public static function required(string $value, string $field): array
    {
        return trim($value) === '' ? ["{$field} is required."] : [];
    }

    // Validate email format
    public static function email(string $value, string $field): array
    {
        return filter_var($value, FILTER_VALIDATE_EMAIL) ? [] : ["{$field} must be a valid email."];
    }

    // Validate positive integer
    public static function intPositive(?string $value, string $field): array
    {
        if ($value === null || $value === '') {
            return ["{$field} is required."];
        }
        if (!ctype_digit($value) || (int) $value < 1) {
            return ["{$field} must be a positive integer."];
        }
        return [];
    }

    // Validate non-negative decimal number
    public static function floatNonNegative(?string $value, string $field): array
    {
        if ($value === null || $value === '') {
            return ["{$field} is required."];
        }
        if (!is_numeric($value) || (float) $value < 0) {
            return ["{$field} must be a non-negative number."];
        }
        return [];
    }

    // Validate value is in allowed list
    public static function inList(string $value, array $allowed, string $field): array
    {
        return in_array($value, $allowed, true) ? [] : ["Invalid {$field}."];
    }

    // Validate minimum string length
    public static function minLength(string $value, int $min, string $field): array
    {
        return strlen($value) >= $min ? [] : ["{$field} must be at least {$min} characters."];
    }
}
