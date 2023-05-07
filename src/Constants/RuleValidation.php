<?php

namespace fvucemilo\phpmvc\Constants;

/**
 * The RuleValidation class provides constants for commonly used validation rules and their associated error messages.
 */
class RuleValidation
{
    /**
     * @var string Constant representing the error message for the "required" validation rule.
     */
    public const RULE_REQUIRED_ERROR_MESSAGE = 'This field is required';

    /**
     * @var string Constant representing the error message for the "email" validation rule.
     */
    public const RULE_EMAIL_ERROR_MESSAGE = 'This field must be a valid email address';

    /**
     * @var string Constant representing the error message for the "min" validation rule.
     */
    public const RULE_MIN_ERROR_MESSAGE = 'Minimum length of this field must be {min}';

    /**
     * @var string Constant representing the error message for the "max" validation rule.
     */
    public const RULE_MAX_ERROR_MESSAGE = 'Maximum length of this field must be {max}';

    /**
     * @var string Constant representing the error message for the "match" validation rule.
     */
    public const RULE_MATCH_ERROR_MESSAGE = 'This field must be the same as {match}';

    /**
     * @var string Constant representing the error message for the "unique" validation rule.
     */
    public const RULE_UNIQUE_ERROR_MESSAGE = 'A record with this {field} already exists';
}