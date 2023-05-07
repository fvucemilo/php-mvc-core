<?php

namespace fvucemilo\phpmvc\Forms;

/**
 * The TextareaField class provides a set of methods for rendering a textarea input field in an HTML form.
 */
class TextareaField extends BaseField
{
    /**
     * Renders the HTML input element for the textarea field.
     *
     * @return string string The HTML markup for the textarea input element.
     */
    public function renderInput(): string
    {
        return sprintf('<textarea class="form-control%s" name="%s">%s</textarea>',
            $this->model->hasError($this->attribute) ? ' is-invalid' : '',
            $this->attribute,
            $this->model->{$this->attribute},
        );
    }
}