<?php

namespace fvucemilo\phpmvc\Tables;

use fvucemilo\phpmvc\MVC\Models\Model;

abstract class BaseTable
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
     * @var array
     */
    public array $attribute;

    /**
     * BaseTable constructor.
     *
     * @param Model $model The model associated with this table.
     * @param array $attribute The name of the attribute associated with this table.
     */
    public function __construct(Model $model, array $attribute)
    {
        $this->model = $model;
        $this->attribute = $attribute;
    }

    /**
     * Renders the table HTML.
     *
     * @return string The HTML representation of the table.
     */
    abstract public function renderTable(): string;
}