<?php
/**
 * Created by JetBrains PhpStorm.
 * User: atorkelson
 * Date: 2/27/14
 * Time: 11:46 AM
 * To change this template use File | Settings | File Templates.
 */
namespace checkbook_advanced_search;

class Field
{
    public $domain_name;
    public $field_name;
    public $data_source;
    public $field_type;
    public $column_type;
    public $field_coordinates;
    public $column_index;

    public $options;
    public $title;
    public $option_attributes;
    public $default_value;
    public $size;
    public $maxlength;
    public $attributes;
    public $name;
    public $value;
    public $disabled;
    public $required;

    public $prefix;
    public $suffix;

    public function __construct($field_name, $field_type, $attributes,$disabled = FALSE)
    {
        $this->field_name = $field_name;
        $this->field_type = $field_type;

        if(!(is_null($attributes['attributes'])))
            $this->attributes = $attributes['attributes'];

        if(!(is_null($attributes['data_source'])))
            $this->data_source = $attributes['data_source'];

        if(!(is_null($attributes['default_value'])))
            $this->default_value = $attributes['default_value'];

        if(!(is_null($attributes['domain_name'])))
            $this->domain_name = $attributes['domain_name'];

        if(!(is_null($attributes['field_name'])))
            $this->field_name = $attributes['field_name'];

        if(!(is_null($attributes['maxlength'])))
            $this->maxlength = $attributes['maxlength'];

        if(!(is_null($attributes['option_attributes'])))
            $this->option_attributes = $attributes['option_attributes'];

        if(!(is_null($attributes['options'])))
            $this->options = $attributes['options'];

        if(!(is_null($attributes['required'])))
            $this->required = $attributes['required'];

        if(!(is_null($attributes['size'])))
            $this->size = $attributes['size'];

        if(!(is_null($attributes['title'])))
            $this->title = $attributes['title'];

        if(!(is_null($attributes['value'])))
            $this->value = $attributes['value'];
    }

    public function getFieldTitle()
    {
        if(!(is_null($this->title)))
            $field_title = $this->title;
        else
            $field_title = ucwords(str_replace('_',' ',$this->field_name));
        return str_replace(' Id',' ID',$field_title);
    }

    public function getDropDownOptions()
    {
        if(!(is_null($this->options)))
            return $this->options;
        else
            return array('Select ' . $this->getFieldTitle());
    }

    public function getDropDownDefault()
    {
        if(!(is_null($this->options[0])))
            return $this->options[0];
        else
            return 'Select ' . $this->getFieldTitle();
    }
}