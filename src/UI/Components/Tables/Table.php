<?php

namespace fvucemilo\phpmvc\Tables;

use fvucemilo\phpmvc\MVC\Models\Model;

/**
 * The Table class provides a set of methods for generating HTML tables using PHP.
 */
class Table extends BaseTable
{
    /**
     * @var Model The model object containing the data to display in the table.
     */
    public Model $model;
    /**
     * @var array The additional attributes for the table.
     */
    public array $attribute;
    /**
     * @var array The array of column names to display in the table.
     */
    protected array $columns;

    /**
     * Table constructor.
     *
     * @param Model $model The model object containing the data to display in the table.
     * @param array $columns An array of column names to display in the table.
     * @param array $attribute An optional array of additional options to add to the table element.
     */
    public function __construct(Model $model, array $columns, array $attribute = [])
    {
        $this->columns = $columns;
        parent::__construct($model, $attribute);
    }

    /**
     * Renders the HTML input element for the field.
     *
     * @return string The HTML markup for the input element.
     */
    public function renderTable(): string
    {
        $rows = $this->model->attributes();
        $html = $this->createTableWithAttributes();
        list($html) = $this->createTheTableHeaderRows($html);
        return $this->createTheTableBodyRows($html, $rows);
    }

    /**
     * Creates a string representation of additional attributes to add to the table element.
     *
     * @return string The string representation of additional attributes to add to the table element.
     */
    private function createTableWithAttributes(): string
    {
        $html = '<table';
        foreach ($this->attribute as $key => $value) $html .= sprintf(' %s="%s"', $key, $value);
        $html .= '>';
        return $html;
    }

    /**
     * Creates the HTML code for the table header row.
     *
     * @param string $html The current HTML code to which the table header row will be appended.
     *
     * @return array An array containing the updated HTML code and the last column name in the header row.
     */
    private function createTheTableHeaderRows(string $html): array
    {
        $html .= '<thead><tr>';
        foreach ($this->columns as $column) $html .= sprintf('<th>%s</th>', ucfirst($column));
        $html .= '</tr></thead>';
        return array($html);
    }

    /**
     * Creates the HTML code for the table body rows.
     *
     * @param string $html The current HTML code to which the table body rows will be appended.
     * @param array $rows The rows of data to be displayed in the table.
     *
     * @return string The updated HTML code for the table.
     */
    private function createTheTableBodyRows(string $html, array $rows): string
    {
        $html .= '<tbody>';
        foreach ($rows as $row) {
            $html .= '<tr>';
            foreach ($this->columns as $column) $html .= sprintf('<td>%s</td>', $row->{$column});
            $html .= '</tr>';
        }
        $html .= '</tbody></table>';
        return $html;
    }
}