<?php
declare(strict_types=1);

namespace App;

class Validator
{
    public static function sanitize(string $value): string
    {
        return trim(strip_tags($value));
    }

    public static function required(array $data, array $fields): array
    {
        $errors = [];
        foreach ($fields as $field) {
            if (!isset($data[$field]) || trim((string)$data[$field]) === '') {
                $errors[] = ucfirst(str_replace('_', ' ', $field)) . ' is required.';
            }
        }
        return $errors;
    }

    public static function email(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    public static function phone(string $phone): bool
    {
        return preg_match('/^\+?[0-9\s\-()]{7,20}$/', $phone) === 1;
    }

    public static function date(string $date): bool
    {
        return (bool)strtotime($date);
    }
}
