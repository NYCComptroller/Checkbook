<?php
/**
 * Created by JetBrains PhpStorm.
 * User: atorkelson
 * Date: 2/27/14
 * Time: 11:47 AM
 * To change this template use File | Settings | File Templates.
 */
namespace checkbook_advanced_search;

class Content
{
    public $fields = array();
    public $domain_name;
    public $column_type;
    public $data_source;

    public $num_left_column_fields = 0;
    public $num_right_column_fields = 0;

    public function __construct($data_source)
    {
        $this->data_source = $data_source;
    }

    public function add_field(Field $field, $column_type)
    {
        switch($column_type)
        {
            case Column::Left:
                $this->num_left_column_fields+=1;
                $field->column_index = $this->num_left_column_fields;
                break;

            case Column::Right:
                $this->num_right_column_fields+=1;
                $field->column_index = $this->num_right_column_fields;
                break;
        }
        $this->column_type = $column_type;
        $field->column_type = $column_type;
        $field->domain_name= $this->domain_name;
        $this->fields[$field->field_name] = $field;
    }
}
