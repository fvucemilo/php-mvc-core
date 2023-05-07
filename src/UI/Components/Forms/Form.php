<?php

namespace fvucemilo\phpmvc\Forms;

use fvucemilo\phpmvc\MVC\Models\Model;

/**
 * The Form class provides a set of methods for generating HTML forms using PHP.
 */
class Form
{
    /**
     * Creates a new HTML form.
     *
     * @param string $action The URL to which the form data will be submitted.
     * @param string $method The HTTP method used to submit the form (e.g. 'get' or 'post').
     * @param array $options An optional array of additional attributes to add to the form element.
     *
     * @return Form A new instance of the Form class.
     */
    public static function begin(string $action, string $method, array $options = []): Form
    {
        $attributes = [];
        foreach ($options as $key => $value) {
            $attributes[] = "$key=\"$value\"";
        }
        echo sprintf('<form action="%s" method="%s" %s>', $action, $method, implode(" ", $attributes));
        return new Form();
    }

    /**
     * Closes an HTML form.
     *
     * @return void
     */
    public static function end(): void
    {
        echo '</form>';
    }

    /**
     * Creates a new HTML form field.
     *
     * @param Model $model The model object to which the field is associated.
     * @param string $attribute The name of the attribute to which the field is associated.
     *
     * @return Field A new instance of the Field class.
     */
    public function field(Model $model, string $attribute): Field
    {
        return new Field($model, $attribute);
    }
}