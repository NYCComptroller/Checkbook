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
    public $value;
    public $disabled;
    public $required;
    public $ajax;
    public $id;

    public $prefix;
    public $suffix;

    public function __construct($field_name, $field_type, $attributes, $disabled = FALSE)
    {
        $this->field_name = $field_name;
        $this->field_type = $field_type;
        $this->disabled = $disabled;


        if (!(is_null($attributes))) {

            if (array_key_exists('id', $attributes))
              $this->id = str_replace('{domain}',$this->domain_name,$attributes['id']);

            if (array_key_exists('attributes', $attributes))
                $this->attributes = $attributes['attributes'];

            if (array_key_exists('data_source', $attributes))
                $this->data_source = $attributes['data_source'];

            if (array_key_exists('default_value', $attributes))
                $this->default_value = $attributes['default_value'];

            if (array_key_exists('domain_name', $attributes))
                $this->domain_name = $attributes['domain_name'];

            if (array_key_exists('field_name', $attributes))
                $this->field_name = $attributes['field_name'];

            if (array_key_exists('maxlength', $attributes))
                $this->maxlength = $attributes['maxlength'];

            if (array_key_exists('option_attributes', $attributes))
                $this->option_attributes = $attributes['option_attributes'];

            if (array_key_exists('options', $attributes))
                $this->options = $attributes['options'];

            if (array_key_exists('required', $attributes))
                $this->required = $attributes['required'];

            if (array_key_exists('size', $attributes))
                $this->size = $attributes['size'];

            if (array_key_exists('title', $attributes))
                $this->title = $attributes['title'];

            if (array_key_exists('value', $attributes))
                $this->value = $attributes['value'];

            if (array_key_exists('ajax', $attributes))
                $this->ajax = $attributes['ajax'];
        }
    }

    public function getFieldTitle()
    {
        if (!(is_null($this->title)))
            $field_title = $this->title;
        else
            $field_title = ucwords(str_replace('_', ' ', $this->field_name));
        return str_replace(' Id', ' ID', $field_title);
    }

    public function getDropDownOptions()
    {
        if (!(is_null($this->options)))
            return $this->options;
        else
            return array('Select ' . $this->getFieldTitle());
    }

    public function getDropDownDefault()
    {
        if (isset($this->options[0]) && !(is_null($this->options[0])))
            return $this->options[0];
        else
            return 'Select ' . $this->getFieldTitle();
    }
}