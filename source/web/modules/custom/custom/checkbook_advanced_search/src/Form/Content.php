<?php
/**
 * This file is part of the Checkbook NYC financial transparency software.
 *
 * Copyright (c) 2012 – 2023 New York City
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

namespace Drupal\checkbook_advanced_search\Form;

class Content
{
    public $fields = array();
    public $domain_name;
    public $column_type;
    public $data_source;

    public $num_left_column_fields = 0;
    public $num_right_column_fields = 0;

    public function __construct($data_source){
        $this->data_source = $data_source;
    }

    public function add_field(Field $field, $column_type){
        switch($column_type) {
          case Column::LEFT:
            $this->num_left_column_fields+=1;
            $field->column_index = $this->num_left_column_fields;
            break;

          case Column::RIGHT:
            $this->num_right_column_fields+=1;
            $field->column_index = $this->num_right_column_fields;
            break;
          default:
            //nothing to do
        }
        $this->column_type = $column_type;
        $field->column_type = $column_type;
        $field->domain_name= $this->domain_name;
        $this->fields[$field->field_name] = $field;
    }
}
