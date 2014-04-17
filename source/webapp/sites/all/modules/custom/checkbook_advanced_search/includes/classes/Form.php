<?php
/**
 * Created by JetBrains PhpStorm.
 * User: atorkelson
 * Date: 2/27/14
 * Time: 11:48 AM
 * To change this template use File | Settings | File Templates.
 */
namespace checkbook_advanced_search;


class Form
{
    public $domain_name;
    public $data_source;
    public $contents = array();

    public function __construct($domain_name)
    {
        $this->domain_name=$domain_name;
        //$this->data_source=$data_source;
    }

    public function add_content(Content $content)
    {
        $content->domain_name=$this->domain_name;
        $this->contents[$content->data_source] = $content;
    }

    public function generate_form($form)
    {
        $form->domain_name=$this->domain_name;
        $form = $this->generate_header($form);
        //$form = $this->generate_filter($form);
        return $this->generate_content($form);
    }

    private function generate_header($form)
    {
        $domain = $this->domain_name;
        $domain_field = "{$this->domain_name}_filter";

        $div_id = "$this->domain_name-advanced-search";

        $form[$domain][$domain_field]['#prefix'] = '<h3><a href="#">'.$this->domain_name.'</a></h3><div id="'.$div_id.'">';

        return $form;
    }

//    private function generate_filter($form)
//    {
//        $domain = $this->domain_name;
//        $data_source = $this->data_source;
//        $domain_field = 'advanced_search_'.$domain.'_filter';
//
//        $form[$domain][$domain_field]['#type'] = 'radios';
//        //$form[$domain][$domain_field]['#id'] = 'edit-advanced-search-spending-filter-checkbook';
//        $form[$domain][$domain_field]['#options'] = array('checkbook' => 'Citywide Agencies', 'checkbook_oge' => 'Other Government Entities');
//        $form[$domain][$domain_field]['#default_value'] = !isset($data_source) ? 'checkbook' : $data_source;
//        $form[$domain][$domain_field]['#prefix'] = '<div id="advanced_search_'.$this->domain_name.'_dynamic_fields">';
//        $form[$domain][$domain_field]['#ajax'] = array(
//            'event' => 'change',
//            'callback' => 'advanced_search_'.$domain.'_filter_ajax',
//            'method' => 'replace',
//            'attributes' => array('new_data_source'=> $this->data_source)
//        );
//
//        return $form;
//    }

    private function generate_content($form)
    {
        foreach ($this->contents as $content) {
            foreach ($content->fields as $field) {
                switch($field->column_type)
                {
                    case Column::Left:
                        if($field->column_index == 1)
                            $field->field_coordinates = 1;
                        else if ($field->column_index == $content->num_left_column_fields)
                            $field->field_coordinates = 2;
                        break;

                    case Column::Right:
                        if($field->column_index == 1)
                            $field->field_coordinates = 3;
                        else if ($field->column_index == $content->num_right_column_fields)
                            $field->field_coordinates = 4;
                        break;
                }

                $form = $this->generate_field($field,$form,$content->data_source);
            }
        }
        return $form;
    }

    private function generate_field(Field $field,$form,$data_source)
    {
        $domain = $data_source . '_' . $this->domain_name;

        $year_range = "'-" . (date("Y") - 1900) . ":+" . (2500 - date("Y")) . "'";
        $column_left_class = "column column-left ".str_replace('_','-',$data_source);
        $column_right_class = "column column-right ".str_replace('_','-',$data_source);

        switch($field->field_coordinates)
        {
            case 1:
                $field->prefix = '<div class="' . $column_left_class . '">';
                break;
            case 2:
                $field->suffix = '</div>';
                break;
            case 3:
                $field->prefix = '<div class="' . $column_right_class . '">';
                break;
            case 4:
                $field->suffix = '</div>';
                break;
        }

        switch($field->field_type)
        {
            case FieldType::DateFilter:

                //date_filter
                $domain_field = 'date_filter';

                $form[$domain][$domain_field]['#type'] = FieldType::RadioButtons;
                $form[$domain][$domain_field]['#title'] = t('Date Filter');
                $form[$domain][$domain_field]['#options'] = array('Year','Issue Date');
                $form[$domain][$domain_field]['#prefix'] = $field->prefix . '<div class="datafield datefilter clearfix">';
                $form[$domain][$domain_field]['#suffix'] = '</div>';
                $form[$domain][$domain_field]['#attributes'] = array('class' => array('watch'), 'name' => $data_source.'_date_filter');
                $form[$domain][$domain_field]['#default_value'] = '';

                //year_filter_start
                $domain_field = 'year_filter_start';

                $form[$domain][$domain_field]['#markup'] = '<div class="datafield year-filters last-item">';

                //issue_date_from
                $domain_field = "{$this->domain_name}_issue_date_from";

                $form[$domain][$domain_field]['#type'] = FieldType::DatePopup;
                $form[$domain][$domain_field]['#date_format'] = 'Y-m-d';
                $form[$domain][$domain_field]['#date_year_range'] = $year_range;
                $form[$domain][$domain_field]['#prefix'] = '<div class="datafield datarange issueddate"><div class="ranges">';
                $form[$domain][$domain_field]['#default_value'] = '';

                //Hidden Field
                $domain_field = "{$data_source}_{$this->domain_name}_issue_date_from_exact";
                $form[$domain][$domain_field]['#type'] = FieldType::HiddenField;
                $form[$domain][$domain_field]['#name'] = $domain_field;

                //issue_date_to
                $domain_field = "{$this->domain_name}_issue_date_to";

                $form[$domain][$domain_field]['#type'] = FieldType::DatePopup;
                $form[$domain][$domain_field]['#title'] = t('to');
                $form[$domain][$domain_field]['#date_format'] = 'Y-m-d';
                $form[$domain][$domain_field]['#date_year_range'] = $year_range;
                $form[$domain][$domain_field]['#suffix'] = '</div></div>';
                $form[$domain][$domain_field]['#default_value'] = '';

                //Hidden Field
                $domain_field = "{$data_source}_{$this->domain_name}_issue_date_to_exact";

                $form[$domain][$domain_field]['#type'] = FieldType::HiddenField;
                $form[$domain][$domain_field]['#name'] = $domain_field;

                //fiscal_year
                $domain_field = "{$this->domain_name}_fiscal_year";

                $form[$domain][$domain_field]['#type'] = FieldType::DropDown;
                $form[$domain][$domain_field]['#options'] = _checkbook_advanced_search_get_year($this->domain_name, $data_source);
                $form[$domain][$domain_field]['#default_value'] = 'fy~' . _getCurrentYearID();
                $form[$domain][$domain_field]['#attributes'] = array('class' => array('watch'), 'default_selected_value' => 'fy~' . _getCurrentYearID());
                $form[$domain][$domain_field]['#prefix'] = '<div class="datafield year">';
                $form[$domain][$domain_field]['#suffix'] = '</div>';

                //year_filter_end
                $domain_field = 'year_filter_end';

                if(!(is_null($field->suffix)))
                    $form[$domain][$domain_field]['#markup'] = '</div>' . $field->suffix;
                else
                    $form[$domain][$domain_field]['#markup'] = '</div>';

                break;

            case FieldType::DropDown:

                $domain_field = "{$data_source}_{$this->domain_name}_{$field->field_name}";

                if(!(is_null($field->prefix)))
                    $form[$domain][$domain_field]['#prefix'] = $field->prefix;
                if(!(is_null($field->suffix)))
                    $form[$domain][$domain_field]['#suffix'] = $field->suffix;
                $form[$domain][$domain_field]['#type'] = $field->field_type;
                $form[$domain][$domain_field]['#title'] = $field->getFieldTitle();
                $form[$domain][$domain_field]['#default_value'] = $field->getDropDownDefault();
                $form[$domain][$domain_field]['#options'] = $field->getDropDownOptions();
                $form[$domain][$domain_field]['#validated'] = true;
                if(!(is_null($field->disabled)))
                    $form[$domain][$domain_field]['#disabled'] = $field->disabled;
                if(!(is_null($field->option_attributes)))
                    $form[$domain][$domain_field]['#option_attributes'] = $field->option_attributes;

                break;

            case FieldType::RadioButtons:

                $domain_field = "{$this->domain_name}_{$field->field_name}";

                if(!(is_null($field->prefix)))
                    $form[$domain][$domain_field]['#prefix'] = $field->prefix;
                $form[$domain][$domain_field]['#type'] = FieldType::RadioButtons;
                if(!(is_null($field->default_value)))
                    $form[$domain][$domain_field]['#default_value'] = $field->default_value;
                if(!(is_null($field->options)))
                    $form[$domain][$domain_field]['#options'] = $field->options;

                break;

            case FieldType::RangeField:

                //From
                $domain_field = "{$data_source}_{$this->domain_name}_{$field->field_name}_from";
                $css_name = str_replace('_','-',$field->field_name);

                $form[$domain][$domain_field]['#type'] = FieldType::TextField;
                if(!(is_null($field->size)))
                    $form[$domain][$domain_field]['#size'] = $field->size;
                else
                    $form[$domain][$domain_field]['#size'] = 30; //Default
                if(!(is_null($field->maxlength)))
                    $form[$domain][$domain_field]['#maxlength'] = $field->maxlength;
                else
                    $form[$domain][$domain_field]['#maxlength'] = 32; //Default
                if(!(is_null($field->prefix)))
                    $form[$domain][$domain_field]['#prefix'] = $field->prefix . '<div class="form-item form-item-'.$css_name.'"><label>' . $field->getFieldTitle() . '</label><div class="ranges">';
                else
                    $form[$domain][$domain_field]['#prefix'] = '<div class="form-item form-item-'.$css_name.'"><label>' . $field->getFieldTitle() . '</label><div class="ranges">';

                //To
                $domain_field = "{$data_source}_{$this->domain_name}_{$field->field_name}_to";
                $form[$domain][$domain_field]['#type'] = FieldType::TextField;
                $form[$domain][$domain_field]['#title'] = t('TO');
                if(!(is_null($field->size)))
                    $form[$domain][$domain_field]['#size'] = $field->size;
                else
                    $form[$domain][$domain_field]['#size'] = 30; //Default
                if(!(is_null($field->maxlength)))
                    $form[$domain][$domain_field]['#maxlength'] = $field->maxlength;
                else
                    $form[$domain][$domain_field]['#maxlength'] = 32; //Default
                if(!(is_null($field->suffix)))
                    $form[$domain][$domain_field]['#suffix'] = '</div></div>' . $field->suffix;
                else
                    $form[$domain][$domain_field]['#suffix'] = '</div></div>';

                break;

            case FieldType::RangeDateField:

                //From
                $domain_field = "{$data_source}_{$this->domain_name}_{$field->field_name}_from";
                $css_name = str_replace('_','-',$field->field_name);

                $form[$domain][$domain_field]['#type'] = FieldType::DatePopup;
                $form[$domain][$domain_field]['#date_format'] = 'Y-m-d';
                $form[$domain][$domain_field]['#date_year_range'] = $year_range;
                if(!(is_null($field->prefix)))
                    $form[$domain][$domain_field]['#prefix'] = $field->prefix . '<div class="form-item form-item-'.$css_name.'"><label>' . $field->getFieldTitle() . '</label><div class="ranges">';
                else
                    $form[$domain][$domain_field]['#prefix'] = '<div class="form-item form-item-'.$css_name.'"><label>' . $field->getFieldTitle() . '</label><div class="ranges">';

                //To
                $domain_field = "{$data_source}_{$this->domain_name}_{$field->field_name}_to";
                $form[$domain][$domain_field]['#type'] = FieldType::DatePopup;
                $form[$domain][$domain_field]['#date_format'] = 'Y-m-d';
                $form[$domain][$domain_field]['#title'] = t('TO');
                if(!(is_null($field->suffix)))
                    $form[$domain][$domain_field]['#suffix'] = '</div></div>' . $field->suffix;
                else
                    $form[$domain][$domain_field]['#suffix'] = '</div></div>';

                break;

            case FieldType::SubmitField:

                //Submit Button
                $domain_field = "{$this->domain_name}_submit";

                $submit_class = "$this->domain_name-submit ".str_replace('_','-',$data_source);

                $form[$domain][$domain_field]['#type'] = $field->field_type;
                $form[$domain][$domain_field]['#name'] = $domain_field;
                $form[$domain][$domain_field]['#value'] = t('Submit');
                $form[$domain][$domain_field]['#prefix'] = t('<div class="'.$submit_class.'">');

                //Clear All Button
                $domain_field = "{$this->domain_name}_clear";

                $form[$domain][$domain_field]['#type'] = $field->field_type;
                $form[$domain][$domain_field]['#value'] = t('Clear All');

                end($this->contents);
                $endKey = key($this->contents);
                if($data_source == $endKey)
                    $form[$domain][$domain_field]['#suffix'] = '</div></div>';
                else
                    $form[$domain][$domain_field]['#suffix'] = '</div>';

                break;

            case FieldType::TextField:

                $domain_field = "{$data_source}_{$this->domain_name}_{$field->field_name}";

                $form[$domain][$domain_field]['#type'] = FieldType::TextField;
                $form[$domain][$domain_field]['#title'] = $field->getFieldTitle();
                if(!(is_null($field->size)))
                    $form[$domain][$domain_field]['#size'] = $field->size;
                else
                    $form[$domain][$domain_field]['#size'] = 30; //Default
                if(!(is_null($field->maxlength)))
                    $form[$domain][$domain_field]['#maxlength'] = $field->maxlength;
                else
                    $form[$domain][$domain_field]['#maxlength'] = 32; //Default
                if(!(is_null($field->prefix)))
                    $form[$domain][$domain_field]['#prefix'] = $field->prefix;

                //Hidden
                $domain_field = "{$data_source}_{$this->domain_name}_{$field->field_name}_exact";
                $form[$domain][$domain_field]['#type'] = FieldType::HiddenField;
                $form[$domain][$domain_field]['#name'] = $domain_field;
                if(!(is_null($field->suffix)))
                    $form[$domain][$domain_field]['#suffix'] = $field->suffix;

                break;
        }

        return $form;
    }
}