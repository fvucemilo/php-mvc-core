<?php

namespace fvucemilo\phpmvc\Forms;

use fvucemilo\phpmvc\MVC\Models\Model;

/**
 * The BaseField abstract class provides a base class for form input fields.
 */
abstract class BaseField
{
    /**
     * The model associated with this field.
     *
     * @var Model
     */
    public Model $model;

    /**
     * The name of the attribute associated with this field.
     *
     * @var string
     */
    public string $attribute;

    /**
     * The type of the input field.
     *
     * @var string
     */
    public string $type;

    /**
     * BaseField constructor.
     *
     * @param Model $model The model associated with this field.
     * @param string $attribute The name of the attribute associated with this field.
     */
    public function __construct(Model $model, string $attribute)
    {
        $this->model = $model;
        $this->attribute = $attribute;
    }

    /**
     * Converts this field to a string representation.
     *
     * @return string The HTML representation of this field.
     */
    public function __toString(): string
    {
        return sprintf('
            <div class="form-group">
                <label>%s</label>
                %s
                <div class="invalid-feedback">
                    %s
                </div>
            </div>',
            $this->model->getLabel($this->attribute),
            $this->renderInput(),
            $this->model->getFirstError($this->attribute)
        );
    }

    /**
     * Renders the input field HTML for this form field.
     *
     * @return string The HTML representation of the input field.
     */
    abstract public function renderInput(): string;
}