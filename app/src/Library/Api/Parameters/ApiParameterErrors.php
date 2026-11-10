<?php declare(strict_types=1);

namespace App\Library\Api\Parameters;

class ApiParameterErrors {
    public const string REQUIRED                 = 'required';
    public const string NOT_NULLABLE             = 'not_nullable';
    public const string INVALID_VALUE            = 'invalid_value';
    public const string INVALID_STRING           = 'invalid_string';
    public const string INVALID_FLOAT            = 'invalid_float';
    public const string INVALID_INTEGER          = 'invalid_integer';
    public const string INVALID_BOOLEAN          = 'invalid_boolean';
    public const string INVALID_DATETIME         = 'invalid_datetime';
    public const string INVALID_DATETIME_FORMAT  = 'invalid_datetime_format';
    public const string CANNOT_BE_PAST           = 'cannot_be_past';
    public const string INVALID_EMAIL            = 'invalid_email';

    public const string MIN_VALUE  = 'min_value';
    public const string MAX_VALUE  = 'max_value';
    public const string MIN_LENGTH = 'min_length';
    public const string MAX_LENGTH = 'max_length';
    public const string MIN_DATE   = 'min_date';
    public const string MAX_DATE   = 'max_date';
}
