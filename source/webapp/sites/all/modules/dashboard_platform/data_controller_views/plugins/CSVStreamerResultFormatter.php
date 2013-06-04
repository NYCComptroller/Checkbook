<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/




class CSVStreamerResultFormatter extends AbstractResultFormatter {

    protected $view;
    protected $header;

    public function __construct($theView, $showHeader = TRUE, ResultFormatter $parent = NULL) {
        parent::__construct($parent);

        $this->view = $theView;
        $this->header = $showHeader;
    }

    public function formatRecord(&$records, $record) {
        //echo var_export($record, TRUE);

        if ($this->header) {
            //print out header
            $hfirst = TRUE;
            $hfield_ids = array_keys($this->view->field);
            foreach ($hfield_ids as $hid) {
                $hfield = $this->view->field[$hid];

                if ($hfield->options['exclude']) {
                    continue;
                }

                if (!$hfirst) {
                    echo ',';
                }

                $hfirst = FALSE;

                echo '"' . $hfield->options['label'] . '"';

            }
            echo "\n";

            //then disable header, so that it doesn't
            //get printed on subsequent rows
            $this->header = FALSE;
        }

        //convert record into drupal view row format
        foreach ($record as $key => $val) {
            $row->$key = $val;
        }

        $rendered_fields = $this->render_fields($row);

        $first = TRUE;
        $field_ids = array_keys($this->view->field);
        foreach ($field_ids as $id) {
            $field = $this->view->field[$id];

            if ($field->options['exclude']) {
                continue;
            }

            if (!$first) {
                echo ',';
            }

            $first = FALSE;

            echo '"' . str_replace('"', '""', $rendered_fields[$id]->content) . '"';
        }
        echo "\n";

        return TRUE;
    }

    function render_fields($row) {

        $field_ids = array_keys($this->view->field);
        $rendered_fields = array();
        foreach ($field_ids as $id) {
            $field = $this->view->field[$id];
            $field_is_multiple = FALSE;
            $field_raw = array();

            $field_output = $this->view->field[$field->options['id']]->advanced_render($row);
            $field_raw = (isset($this->view->field[$id]->field_alias) && isset($row->{$this->view->field[$id]->field_alias})) ? $row->{$this->view->field[$id]->field_alias} : NULL;


            if (empty($field->options['exclude']) && ($field_output != "") && !empty($field_output)) {
                $object = new stdClass();
                $object->id = $id;
                $object->content = $field_output;
                $object->raw = $field_raw;
                $object->class = views_css_safe($id);
                $object->label = check_plain($this->view->field[$id]->label());
                $object->is_multiple = $field_is_multiple;
                $rendered_fields[$id] = $object;
            }
        }

        return $rendered_fields;
    }

}
