<?php

namespace fvucemilo\phpmvc\Forms;

use fvucemilo\phpmvc\MVC\Models\Model;

/**
 * The Field class provides a set of methods for rendering input fields in an HTML form.
 */
class Field extends BaseField
{
    /**
     * The text input type.
     */
    const TYPE_TEXT = 'text';

    /**
     * The password input type.
     */
    const TYPE_PASSWORD = 'password';

    /**
     * The file input type.
     */
    const TYPE_FILE = 'file';

    /**
     * Field constructor.
     *
     * @param Model $model The model object to which the field is associated.
     * @param string $attribute The name of the attribute to which the field is associated.
     */
    public function __construct(Model $model, string $attribute)
    {
        $this->type = self::TYPE_TEXT;
        parent::__construct($model, $attribute);
    }

    /**
     * Renders the HTML input element for the field.
     *
     * @return string The HTML markup for the input element.
     */
    public function renderInput(): string
    {
        return sprintf('<input type="%s" class="form-control%s" name="%s" value="%s">',
            $this->type,
            $this->model->hasError($this->attribute) ? ' is-invalid' : '',
            $this->attribute,
            $this->model->{$this->attribute},
        );
    }

    /**
     * Sets the input type to 'password'.
     *
     * @return Field The current instance of the Field class.
     */
    public function passwordField(): Field
    {
        $this->type = self::TYPE_PASSWORD;
        return $this;
    }

    /**
     * Sets the input type to 'file'.
     *
     * @return Field The current instance of the Field class.
     */
    public function fileField(): Field
    {
        $this->type = self::TYPE_FILE;
        return $this;
    }
}