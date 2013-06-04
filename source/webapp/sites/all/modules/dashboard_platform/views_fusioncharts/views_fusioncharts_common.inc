<?php



function views_fusioncharts_generate_font_options($section, $caption, $options) {
    $fields = array();
    $fields[$section] = array(
        '#type' => 'fieldset',
        '#title' => $caption,
        '#tree' => TRUE,
        '#collapsible' => TRUE,
        '#collapsed' => TRUE
    );
    $fields[$section]['font'] = array(
        '#type' => 'textfield',
        '#title' => t('Font Family'),
    );
    if (isset($options['font']) &&  drupal_strlen($options['font']) > 1) {
        $fields[$section]['font']['#default_value'] = $options['font'];
        $fields[$section]['#collapsed'] = FALSE;
    }

    $fields[$section]['size'] = array(
        '#type' => 'textfield',
        '#title' => t('Font size in pixels'),
    );
    if (isset($options['size']) && intval($options['size']) > 0) {
        $fields[$section]['size']['#default_value'] = floatval($options['size']);
        $fields[$section]['#collapsed'] = FALSE;
    }

    $fields[$section]['color'] = array(
        '#type' => 'textfield',
        '#title' => t('Text color (as a hex triplet)'),
    );
    if (isset($options['color']) && preg_match('!^#?([0-9A-F]{6})$!', drupal_strtoupper($options['color']), $matches)) {
        $fields[$section]['color']['#default_value'] = $matches[1];
        $fields[$section]['#collapsed'] = FALSE;
    }

    $fields[$section]['bold'] = array(
        '#type' => 'checkbox',
        '#title' => t('Bold'),
        '#default_value' => FALSE
    );
    if (isset($options['bold']) && $options['bold']) {
        $fields[$section]['bold']['#default_value'] = TRUE;
        $fields[$section]['#collapsed'] = FALSE;
    }

    $fields[$section]['italic'] = array(
        '#type' => 'checkbox',
        '#title' => t('Italic'),
        '#default_value' => FALSE
    );
    if (isset($options['italic']) && $options['italic']) {
        $fields[$section]['italic']['#default_value'] = TRUE;
        $fields[$section]['#collapsed'] = FALSE;
    }

    $fields[$section]['underline'] = array(
        '#type' => 'checkbox',
        '#title' => t('Underlined'),
        '#default_value' => FALSE
    );
    if (isset($options['underline']) && $options['underline']) {
        $fields[$section]['underline']['#default_value'] = TRUE;
        $fields[$section]['#collapsed'] = FALSE;
    }
    return $fields;
}

function views_fusioncharts_views_render_fields($view, $row) {
    $field_ids = array_keys($view->field);

    foreach ($field_ids as $id) {
        $field = $view->field[$id];

        $field_output = $view->field[$field->options['id']]->advanced_render($row);
        if (empty($field->options['exclude']) && isset($field_output)) {
            //$fname = $field->field_alias;
            $rendered_fields->$id = $field_output;
        }
    }
    return $rendered_fields;
}

function views_fusioncharts_get_field_alias($fieldoption, $fields) {
    $fieldname = str_replace('.', '_', $fieldoption);
    if (!isset($fields[$fieldname])) {
        return NULL;
    }
    return $fieldname;
    //return $fields[$fieldname]->field_alias;
}

function views_fusioncharts_add_style($options, $section, DOMElement &$definition, DOMElement &$application) {
    $sectoptions = $options['fonts'][$section];
    $document = $definition->ownerDocument;
    $style = $document->createElement('style');
    $style->setAttribute('name', $section . '_style');
    $style->setAttribute('type', 'font');
    $hasStyle = FALSE;
    if (isset($sectoptions['font']) &&  drupal_strlen($sectoptions['font']) > 1) {
        $style->setAttribute('font', $sectoptions['font']);
        $hasStyle = TRUE;
    }
    if (isset($sectoptions['size']) && intval($sectoptions['size']) > 0) {
        $style->setAttribute('size', floatval($sectoptions['size']));
        $hasStyle = TRUE;
    }
    if (isset($sectoptions['color']) && preg_match('!^#?[0-9A-Fa-f]$!', $sectoptions['color'])) {
        $style->setAttribute('color', drupal_strtoupper(ltrim($sectoptions['color'], '#')));
        $hasStyle = TRUE;
    }
    if (isset($sectoptions['bold']) && $sectoptions['bold']) {
        $style->setAttribute('bold', intval((bool)$sectoptions['bold']));
        $hasStyle = TRUE;
    }
    if (isset($sectoptions['italic']) && $sectoptions['italic']) {
        $style->setAttribute('italic', intval((bool)$sectoptions['italic']));
        $hasStyle = TRUE;
    }
    if (isset($sectoptions['underline']) && $sectoptions['underline']) {
        $style->setAttribute('underline', intval((bool)$sectoptions['underline']));
        $hasStyle = TRUE;
    }
    if (!$hasStyle) {
        return FALSE;
    }
    $definition->appendChild($style);
    $apply = $document->createElement('apply');
    $apply->setAttribute('toObject', drupal_strtoupper($section));
    $apply->setAttribute('styles', $section . '_style');
    $application->appendChild($apply);
    return TRUE;
}
