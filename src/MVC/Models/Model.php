<?php

namespace fvucemilo\phpmvc\MVC\Models;

use fvucemilo\phpmvc\Application;
use fvucemilo\phpmvc\Constants\RuleValidation;

/**
 * Base model class for the MVC framework.
 */
class Model
{
    /**
     * Validation rule constants.
     */
    const RULE_REQUIRED = 'required';
    const RULE_EMAIL = 'email';
    const RULE_MIN = 'min';
    const RULE_MAX = 'max';
    const RULE_MATCH = 'match';
    const RULE_UNIQUE = 'unique';

    /**
     * @var array Array of validation errors.
     */
    public array $errors = [];

    /**
     * Load model attributes with data from an array.
     *
     * @param array $data The data to load.
     *
     * @return void
     */
    public function loadData(array $data): void
    {
        foreach ($data as $key => $value) if (property_exists($this, $key)) $this->{$key} = $value;
    }

    /**
     * Get an array of attributes for the model.
     *
     * @return array An array of attributes for the model.
     */
    public function attributes(): array
    {
        return [];
    }

    /**
     * Get the label for an attribute.
     *
     * @param string $attribute The attribute to get the label for.
     * @return string The label for the attribute.
     */
    public function getLabel(string $attribute): string
    {
        return $this->labels()[$attribute] ?? $attribute;
    }

    /**
     * Get an array of attribute labels for the model.
     *
     * @return array An array of attribute labels for the model.
     */
    public function labels(): array
    {
        return [];
    }

    /**
     * Validate the model.
     *
     * @return bool Whether the model is valid.
     */
    public function validate(): bool
    {
        foreach ($this->rules() as $attribute => $rules) {
            $value = $this->{$attribute};
            foreach ($rules as $rule) {
                $ruleName = is_array($rule) ? $rule[0] : $rule;
                switch ($ruleName) {
                    case self::RULE_REQUIRED:
                        if (!$value) $this->addErrorByRule($attribute, self::RULE_REQUIRED);
                    case self::RULE_EMAIL:
                        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) $this->addErrorByRule($attribute, self::RULE_EMAIL);
                    case self::RULE_MIN:
                        if (strlen($value) < $rule['min']) $this->addErrorByRule($attribute, self::RULE_MIN, ['min' => $rule['min']]);
                    case self::RULE_MAX:
                        if (strlen($value) > $rule['max']) $this->addErrorByRule($attribute, self::RULE_MAX);
                    case self::RULE_MATCH:
                        if ($value !== $this->{$rule['match']}) $this->addErrorByRule($attribute, self::RULE_MATCH, ['match' => $rule['match']]);
                    case self::RULE_UNIQUE:
                        $className = $rule['class'];
                        $uniqueAttr = $rule['attribute'] ?? $attribute;
                        $tableName = $className::tableName();
                        $statement = Application::$app->db->prepare("SELECT * FROM $tableName WHERE $uniqueAttr = :$uniqueAttr");
                        $statement->bindValue(":$uniqueAttr", $value);
                        $statement->execute();
                        $record = $statement->fetchObject();
                        if ($record) $this->addErrorByRule($attribute, self::RULE_UNIQUE);
                }
            }
        }
        return empty($this->errors);
    }

    /**
     * Get an array of validation rules for the model.
     *
     * @return array An array of validation rules for the model.
     */
    public function rules(): array
    {
        return [];
    }

    /**
     * Add an error for a validation rule to the errors array.
     *
     * @param string $attribute The attribute that the error occurred for.
     * @param string $rule The validation rule that caused the error.
     * @param array $params An array of parameters to use in the error message.
     *
     * @return void
     */
    protected function addErrorByRule(string $attribute, string $rule, array $params = []): void
    {
        $params['field'] ??= $attribute;
        $errorMessage = $this->errorMessage($rule);
        foreach ($params as $key => $value) $errorMessage = str_replace("{{$key}}", $value, $errorMessage);
        $this->errors[$attribute][] = $errorMessage;
    }

    /**
     * Get the error message for a validation rule.
     *
     * @param string $rule The validation rule to get the error message for.
     *
     * @return string The error message for the validation rule.
     */
    public function errorMessage(string $rule): string
    {
        return $this->errorMessages()[$rule];
    }

    /**
     * Get an array of error messages for validation rules.
     *
     * @return array An array of error messages for validation rules.
     */
    public function errorMessages(): array
    {
        return [
            self::RULE_REQUIRED => RuleValidation::RULE_REQUIRED_ERROR_MESSAGE,
            self::RULE_EMAIL => RuleValidation::RULE_EMAIL_ERROR_MESSAGE,
            self::RULE_MIN => RuleValidation::RULE_MIN_ERROR_MESSAGE,
            self::RULE_MAX => RuleValidation::RULE_MAX_ERROR_MESSAGE,
            self::RULE_MATCH => RuleValidation::RULE_MATCH_ERROR_MESSAGE,
            self::RULE_UNIQUE => RuleValidation::RULE_UNIQUE_ERROR_MESSAGE,
        ];
    }

    /**
     * Add an error message to the errors array.
     *
     * @param string $attribute The attribute that the error occurred for.
     * @param string $message The error message.
     *
     * @return void
     */
    public function addError(string $attribute, string $message): void
    {
        $this->errors[$attribute][] = $message;
    }

    /**
     * Check if there are any errors for an attribute.
     *
     * @param mixed $attribute The attribute to check for errors.
     *
     * @return mixed False if there are no errors, otherwise an array of error messages.
     */
    public function hasError(mixed $attribute): mixed
    {
        return $this->errors[$attribute] ?? false;
    }

    /**
     * Get the first error message for an attribute.
     *
     * @param mixed $attribute The attribute to get the error message for.
     *
     * @return mixed The first error message for the attribute, or an empty string if there are no errors.
     */
    public function getFirstError(mixed $attribute): mixed
    {
        $errors = $this->errors[$attribute] ?? [];
        return $errors[0] ?? '';
    }
}