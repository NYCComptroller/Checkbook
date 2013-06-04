<?php



require_once dirname(__FILE__) . '/views_fusioncharts_common.inc';

class views_plugin_style_fusioncharts_multipleseries extends views_plugin_style {

    /**
     * Implementation of views_plugin_style::option_definition
     */
    public function option_definition() {

        $options = parent::option_definition();

        $formfields = array(
            'chart_type', 'chart_title', 'chart_subtitle', 'width', 'height',
            'xaxisname', 'xaxisfield', 'yaxisname', 'yaxismax', 'values', 'columns', 'tooltips',
            'series0', 'series1', 'series2', 'series3', 'series4', 'series5',
            'appearance', 'fonts', 'divlines', 'numformat', 'canvas'
        );
        foreach ($formfields as $formfield) {
            $options[$formfield] = array('default' => array(), 'translatable' => FALSE);
        }

        return $options;
    }

    /**
     * Provide a form for setting options.
     *
     * @param $form
     * @param $form_state
     */
    public function options_form(&$form, &$form_state) {

        $fieldNames = $this->display->handler->get_field_labels();
        $fieldNames = array_merge(array(null => 'Select One'), $fieldNames);
        if (module_exists('colorpicker_cck')) {
            $colorField = 'colorpicker_textfield';
        }
        else {
            $colorField = 'textfield';
        }
        $availableCharts = array(
            'Marimekko' => 'Marimekko',
            'MSArea' => 'Area',
            'MSBar2D' => 'Bar (2 dimensional)',
            'MSBar3D' => 'Bar (3 dimensional)',
            'MSColumn2D' => 'Column (2 dimensional)',
            'MSColumn3D' => 'Column (3 dimensional)',
            'MSLine' => 'Line',
            'ZoomLine' => 'Zoom Line'
        );
        $form['chart_type'] = array(
            '#type' => 'select',
            '#title' => t('Chart Type'),
            '#default_value' => isset($this->options['chart_type']) ? $this->options['chart_type'] : 'MSColumn2D',
            '#description' => t('The type of chart to be rendered.'),
            '#size' => 1,
            '#options' => $availableCharts,
            '#multiple' => FALSE
        );
        $form['chart_title'] = array(
            '#type' => 'textfield',
            '#title' => t('Title'),
            '#default_value' => $this->options['chart_title'],
            '#description' => t('The title for this chart.')
        );
        $form['chart_subtitle'] = array(
            '#type' => 'textfield',
            '#title' => t('Subtitle'),
            '#default_value' => $this->options['chart_subtitle'],
            '#description' => t('The subtitle for this chart.')
        );
        $form['width']  = array(
            '#type' => 'textfield',
            '#title' => t('Width'),
            '#default_value' => isset($this->options['width']) ? $this->options['width'] : NULL,
            '#description' => t('An explicit width in pixels for the chart.'),
            '#required' => TRUE
        );
        $form['height']  = array(
            '#type' => 'textfield',
            '#title' => t('Height'),
            '#default_value' => isset($this->options['height']) ? $this->options['height'] : NULL,
            '#description' => t('An explicit height in pixels for the chart.'),
            '#required' => TRUE
        );
        $form['xaxisname'] = array(
            '#type' => 'textfield',
            '#title' => t('X Axis caption'),
            '#default_value' => isset($this->options['xaxisname']) ? $this->options['xaxisname'] : NULL,
        );
        $form['xaxisfield'] = array(
            '#type' => 'select',
            '#title' => 'X Axis field',
            '#default_value' => isset($this->options['xaxisfield']) ? $this->options['xaxisfield'] : NULL,
            '#required' => TRUE,
            '#description' => t('View field containing values for the X axis'),
            '#size' => 1,
            '#options' => $fieldNames,
            '#multiple' => FALSE,
        );
        $form['yaxisname'] = array(
            '#type' => 'textfield',
            '#title' => t('Y Axis caption'),
            '#default_value' => isset($this->options['yaxisname']) ? $this->options['yaxisname'] : NULL,
        );

        $form['yaxismax'] = array(
            '#type' => 'textfield',
            '#title' => t('Y Axis maximum value'),
            '#default_value' => isset($this->options['yaxismax']) ? $this->options['yaxismax'] : 1,
            '#description' => t('Maximum numeric value the Y-axis may take (for column charts)'),
        );


        $form['values'] = array(
            '#type' => 'fieldset',
            '#title' => 'Column Values',
            '#tree' => TRUE,
            '#collapsible' => TRUE,
            '#collapsed' => FALSE
        );
        $form['values']['show'] = array(
            '#type' => 'checkbox',
            '#title' => t('Show values?'),
            '#description' => t('Controls whether number values are shown along with graphical elements such as columns or bars. May not apply to some chart types such as pie or donut.'),
            '#default_value' => isset($this->options['values']['show']) ? $this->options['values']['show'] : TRUE,
        );
        $form['values']['rotate'] = array(
            '#type' => 'checkbox',
            '#title' => t('Rotate values?'),
            '#description' => t('Controls whether number values are rotated 90 degrees. May not apply to some chart types such as pie or donut.'),
            '#default_value' => isset($this->options['values']['rotate']) ? $this->options['values']['rotate'] : FALSE,
        );
        $form['values']['inside'] = array(
            '#type' => 'checkbox',
            '#title' => t('Place values inside?'),
            '#description' => t('Controls whether number values are displayed inside or outside of their corresponding graphical element. May not apply to some chart types such as pie or donut.'),
            '#default_value' => isset($this->options['values']['inside']) ? $this->options['values']['inside'] : TRUE,
        );

        $form['columns'] = array(
            '#type' => 'fieldset',
            '#title' => 'Chart Area Advanced Settings',
            '#tree' => TRUE,
            '#collapsible' => TRUE,
            '#collapsed' => TRUE
        );
        $form['columns']['shadow'] = array(
            '#type' => 'checkbox',
            '#title' => t('Show column shadow?'),
            '#default_value' => isset($this->options['columns']['shadow']) ? $this->options['columns']['shadow'] : FALSE,
        );
        $form['columns']['rotate'] = array(
            '#type' => 'checkbox',
            '#title' => t('Rotate column names?'),
            '#description' => t('Controls whether column names are rotated 90 degrees. May not apply to some chart types such as pie or donut.'),
            '#default_value' => isset($this->options['columns']['rotate']) ? $this->options['columns']['rotate'] : FALSE,
        );

        $form['tooltips'] = array(
            '#type' => 'fieldset',
            '#title' => 'Tooltips',
            '#tree' => TRUE,
            '#collapsible' => TRUE,
            '#collapsed' => FALSE
        );
        $form['tooltips']['bgcolor'] = array(
            '#type' => 'textfield',
            '#title' => 'Background Color',
            '#description' => t('Color to be used as the tooltip background color, as a hex triplet.'),
            '#default_value' => isset($this->options['tooltips']['bgcolor']) ? $this->options['tooltips']['bgcolor'] : NULL,
        );
        $form['tooltips']['border'] = array(
            '#type' => 'textfield',
            '#title' => 'Border Color',
            '#description' => t('Color to be used as the tooltip border color, as a hex triplet.'),
            '#default_value' => isset($this->options['tooltips']['border']) ? $this->options['tooltips']['border'] : NULL,
        );

        for ($i = 0; $i < 6; $i++) {
            $this->generate_series($form, $i, $fieldNames);
        }

        $form['canvas'] = array(
            '#type' => 'fieldset',
            '#title' => 'Chart Canvas Settings',
            '#tree' => TRUE,
            '#collapsible' => TRUE,
            '#collapsed' => FALSE
        );
        $form['canvas']['chartcanvasbgcolor'] = array(
            '#type' => $colorField,
            '#title' => 'Canvas Background Color',
            '#description' => t('Color to be used as the Canvas Background color, as a hex triplet.'),
            '#default_value' => isset($this->options['canvas']['chartcanvasbgcolor']) ? $this->options['canvas']['chartcanvasbgcolor'] : '#DFE8EF',
        );
        $form['canvas']['chartcanvasbordercolor'] = array(
            '#type' => $colorField,
            '#title' => 'Canvas Border Color',
            '#description' => t('Color to be used as the Canvas Border color, as a hex triplet.'),
            '#default_value' => isset($this->options['canvas']['chartcanvasbordercolor']) ? $this->options['canvas']['chartcanvasbordercolor'] : '#FFFFFF',
        );
        $form['canvas']['chartcanvasborderthickness'] = array(
            '#type' => 'textfield',
            '#title' => 'Canvas Border Thickness',
            '#description' => t('Thickness of the Canvas Border, Number starting from 0.'),
            '#default_value' => isset($this->options['canvas']['chartcanvasborderthickness']) ? $this->options['canvas']['chartcanvasborderthickness'] : 0,
        );
        $form['canvas']['chartcanvasbgalpha'] = array(
            '#type' => 'textfield',
            '#title' => 'Canvas Gradient',
            '#description' => t('Transparency value for the top of the Canvas Background. Zero is completely transparent, 100 is completely opaque.'),
            '#default_value' => isset($this->options['canvas']['chartcanvasbgalpha']) ? $this->options['canvas']['chartcanvasbgalpha'] : 100,
        );
        $form['canvas']['chartcanvaspadding'] = array(
            '#type' => 'textfield',
            '#title' => 'Canvas Padding',
            '#description' => t('Padding between the chartborder and the canvas, Number starting from 0.'),
            '#default_value' => isset($this->options['canvas']['chartcanvaspadding']) ? $this->options['canvas']['chartcanvaspadding'] : 0,
        );
        $form['canvas']['canvasbasedepth'] = array(
            '#type' => 'textfield',
            '#title' => 'Canvas Base depth',
            '#description' => t('Height of canvas base, Number in pixels'),
            '#default_value' => isset($this->options['canvas']['canvasbasedepth']) ? $this->options['canvas']['canvasbasedepth'] : 1,
        );
        $form['canvas']['canvasbgdepth'] = array(
            '#type' => 'textfield',
            '#title' => 'Canvas Background depth',
            '#description' => t('Depth of Canvas Background, Number in pixels'),
            '#default_value' => isset($this->options['canvas']['canvasbgdepth']) ? $this->options['canvas']['canvasbgdepth'] : 1,
        );

        $form['appearance'] = array(
            '#type' => 'fieldset',
            '#title' => 'Chart Appearance',
            '#tree' => TRUE,
            '#collapsible' => TRUE,
            '#collapsed' => FALSE
        );
        $form['appearance']['border'] = array(
            '#type' => 'checkbox',
            '#title' => t('Show border?'),
            '#default_value' => isset($this->options['appearance']['border']) ? $this->options['appearance']['border'] : FALSE,
            '#description' => t('Draws a 1px border around the chart')
        );
        $form['appearance']['bg_top'] = array(
            '#type' => $colorField,
            '#title' => 'Background Gradient Top',
            '#description' => t('Color to be used as the top color in the background gradient, as a hex triplet.'),
            '#default_value' => isset($this->options['appearance']['bg_top']) ? $this->options['appearance']['bg_top'] : '#FFFFFF',
        );
        $form['appearance']['bg_bottom'] = array(
            '#type' => $colorField,
            '#title' => 'Background Gradient Bottom',
            '#description' => t('Color to be used as the bottom color in the background gradient, as a hex triplet.'),
            '#default_value' => isset($this->options['appearance']['bg_bottom']) ? $this->options['appearance']['bg_bottom'] : '#FFFFFF',
        );

        $form['appearance']['alpha_top'] = array(
            '#type' => 'textfield',
            '#title' => 'Background Alpha Top',
            '#description' => t('Transparency value for the top of the background. Zero is completely transparent, 100 is completely opaque.'),
            '#default_value' => isset($this->options['appearance']['alpha_top']) ? $this->options['appearance']['alpha_top'] : '0',
        );
        $form['appearance']['alpha_bottom'] = array(
            '#type' => 'textfield',
            '#title' => 'Background Alpha Bottom',
            '#description' => t('Transparency value for the bottom of the background. Zero is completely transparent, 100 is completely opaque.'),
            '#default_value' => isset($this->options['appearance']['alpha_bottom']) ? $this->options['appearance']['alpha_bottom'] : '0',
        );
        $form['appearance']['font'] = array(
            '#type' => 'textfield',
            '#title' => 'Font Family',
            '#default_value' => isset($this->options['appearance']['font']) ? $this->options['appearance']['font'] : NULL,
        );
        $form['appearance']['fontsize'] = array(
            '#type' => 'textfield',
            '#title' => 'Font Size',
            '#description' => 'Font size in screen pixels',
            '#default_value' => isset($this->options['appearance']['fontsize']) ? $this->options['appearance']['fontsize'] : NULL,
        );
        $form['appearance']['round'] = array(
            '#type' => 'checkbox',
            '#title' => t('Use round edges on columns?'),
            '#default_value' => isset($this->options['appearance']['round']) ? $this->options['appearance']['round'] : FALSE,
        );

        $form['appearance']['border_thickness'] = array(
            '#type' => 'textfield',
            '#title' => t('Plot border thickness'),
            '#description' => t('For bar and column charts, controls width of the border on bars/columns (in pixels). Use 0 to eliminate the border. Valid values are zero to 5. This field is ignored if Rounded Edges are selected.'),
            '#default_value' => isset($this->options['appearance']['border_thickness']) ? $this->options['border_thickness'] : 1
        );
        $form['appearance']['bordercolor'] = array(
            '#type' => $colorField,
            '#title' => t('Border Color for plot areas'),
            '#default_value' => isset($this->options['appearance']['bordercolor']) ? $this->options['appearance']['bordercolor'] : NULL,
            '#description' => t('Color of the border for plot areas, as a hex triplet; if left blank, defaults to black. If no border is desired, set Border Thickness to zero. This value is ignored if Rounded Edges are selected.')
        );

        $form['appearance']['plotgradient'] = array(
            '#type' => 'checkbox',
            '#title' => t('Show gradient on chart areas?'),
            '#default_value' => isset($this->options['appearance']['plotgradient']) ? $this->options['appearance']['plotgradient'] : FALSE,
            '#description' => t('If selected, this will cause each bar or column to gradate from white to its own distinct color. This value is ignored if Rounded Edges are selected.')
        );
        $form['appearance']['showalternate'] = array(
            '#type' => 'checkbox',
            '#title' => t('Shade alternate areas between division lines?'),
            '#default_value' => isset($this->options['appearance']['showalternate']) ? $this->options['appearance']['showalternate'] : TRUE
        );

        $form['appearance']['animation'] = array(
            '#type' => 'checkbox',
            '#title' => t('Use animation on load?'),
            '#default_value' => isset($this->options['appearance']['animation']) ? $this->options['appearance']['animation'] : FALSE,
        );
        $form['appearance']['use3dlighting'] = array(
            '#type' => 'checkbox',
            '#title' => t('Chart 3D Lighting?'),
            '#default_value' => isset($this->options['appearance']['use3dlighting']) ? $this->options['appearance']['use3dlighting'] : FALSE,
        );
        $form['appearance']['showlegend'] = array(
            '#type' => 'checkbox',
            '#title' => t('Show Legend?'),
            '#default_value' => isset($this->options['appearance']['showlegend']) ? $this->options['appearance']['showlegend'] : FALSE,
        );
        $form['appearance']['chartbottommargin'] = array(
            '#type' => 'textfield',
            '#title' => 'Chart Bottom Margin',
            '#description' => 'Chart Bottom Margin, start from 0.',
            '#default_value' => isset($this->options['appearance']['chartbottommargin']) ? $this->options['appearance']['chartbottommargin'] : 0,
        );
        $form['appearance']['maxcolwidth'] = array(
            '#type' => 'textfield',
            '#title' => 'Maximum column width',
            '#description' => 'Maximum allowed column width, Number in Pixels.',
            '#default_value' => isset($this->options['appearance']['maxcolwidth']) ? $this->options['appearance']['maxcolwidth'] : 40,
        );
        $form['appearance']['maxXaxisValue'] = array(
            '#type' => 'textfield',
            '#title' => t('Maximum limit of X-axis to display'),
            '#description' => t('Maximum number of Values to display on the X-axis'),
            '#default_value' => isset($this->options['appearance']['maxXaxisValue']) ? $this->options['appearance']['maxXaxisValue'] : null
        );

        $form['fonts'] = array(
            '#type' => 'fieldset',
            '#title' => 'Font Settings',
            '#tree' => TRUE,
            '#collapsible' => TRUE,
            '#collapsed' => FALSE
        );
        $form['fonts']['basefamily'] = array(
            '#type' => 'textfield',
            '#title' => 'Font Family',
            '#default_value' => isset($this->options['fonts']['basefamily']) ? $this->options['fonts']['basefamily'] : NULL,
        );
        $form['fonts']['basesize'] = array(
            '#type' => 'textfield',
            '#title' => 'Font Size',
            '#description' => 'Font size in screen pixels',
            '#default_value' => isset($this->options['fonts']['basesize']) ? $this->options['fonts']['basesize'] : NULL,
        );
        $form['fonts']['basefontcolor'] = array(
            '#type' => 'textfield',
            '#title' => 'Font Color',
            '#description' => 'Base Font Color , as a hex triplet.',
            '#default_value' => isset($this->options['fonts']['basefontcolor']) ? $this->options['fonts']['basefontcolor'] : NULL,
        );

        $fontStylable = array(
            'caption' => 'Chart Caption',
            'subcaption' => 'Chart Subcaption',
            'xaxisname' => 'X Axis Caption',
            'yaxisname' => 'Y Axis Caption',
            'datalabels' => 'X Axis Labels',
            'yaxisvalues' => 'Y Axis Labels',
            'xaxisvalues' => 'Datapoint Values',
        );
        foreach ($fontStylable as $section => $caption) {
            $form['fonts'] += views_fusioncharts_generate_font_options($section, $caption, $this->options['fonts'][$section]);
        }

        $form['divlines'] = array(
            '#type' => 'fieldset',
            '#title' => 'Horizontal Dividing Lines',
            '#tree' => TRUE,
            '#collapsible' => TRUE,
            '#collapsed' => TRUE
        );
        $form['divlines']['number'] = array(
            '#type' => 'textfield',
            '#title' => 'Number of Dividing Lines',
            '#default_value' => isset($this->options['divlines']['number']) ? $this->options['divlines']['number'] : NULL,
        );
        $form['divlines']['color'] = array(
            '#type' => 'textfield',
            '#title' => 'Background Gradient Bottom',
            '#description' => t('Color to be used for horizontal dividing lines, as a hex triplet.'),
            '#default_value' => isset($this->options['divlines']['color']) ? $this->options['divlines']['color'] : NULL,
        );
        $form['divlines']['dashed'] = array(
            '#type' => 'checkbox',
            '#title' => t('Use dashed lines?'),
            '#default_value' => isset($this->options['divlines']['dashed']) ? $this->options['divlines']['dashed'] : FALSE,
        );
        $form['divlines']['dashlen'] = array(
            '#type' => 'textfield',
            '#title' => 'Dash length in pixels',
            '#default_value' => isset($this->options['divlines']['dashlen']) ? $this->options['divlines']['dashlen'] : NULL,
        );
        $form['divlines']['dashgap'] = array(
            '#type' => 'textfield',
            '#title' => 'Dash gap in pixels',
            '#default_value' => isset($this->options['divlines']['dashgap']) ? $this->options['divlines']['dashgap'] : NULL,
        );

        $form['numformat'] = array(
            '#type' => 'fieldset',
            '#title' => 'Number Formatting',
            '#tree' => TRUE,
            '#collapsible' => TRUE,
            '#collapsed' => FALSE
        );
        $form['numformat']['commas'] = array(
            '#type' => 'checkbox',
            '#title' => t('Format numbers with commas?'),
            '#default_value' => isset($this->options['numformat']['commas']) ? $this->options['numformat']['commas'] : FALSE,
        );
        $form['numformat']['decimals'] = array(
            '#type' => 'textfield',
            '#title' => t('Decimals'),
            '#description' => t('Number of decimal places to display'),
            '#default_value' => isset($this->options['numformat']['decimals']) ? $this->options['numformat']['decimals'] : 0,
        );
        $form['numformat']['abbreviate'] = array(
            '#type' => 'checkbox',
            '#title' => t('Abbreviate large numbers?'),
            '#default_value' => isset($this->options['numformat']['abbreviate']) ? $this->options['numformat']['abbreviate'] : TRUE,
            '#description' => t('Checking this box will add K (thousands) and M (millions) to a number after truncating and rounding it.')
        );
        $form['numformat']['prefix'] = array(
            '#type' => 'textfield',
            '#title' => t('Number Prefix'),
            '#description' => t('Numeric display values are prefixed with this value.'),
            '#default_value' => isset($this->options['numformat']['prefix']) ? $this->options['numformat']['prefix'] : NULL,
        );
        $form['numformat']['suffix'] = array(
            '#type' => 'textfield',
            '#title' => t('Number Suffix'),
            '#description' => t('Numeric display values are suffixed with this value.'),
            '#default_value' => isset($this->options['numformat']['suffix']) ? $this->options['numformat']['suffix'] : NULL,
        );
    }

    /**
     * Implementation of view_style_plugin::theme_functions(). Returns an array of theme functions to use
     * for the current style plugin
     * @return array
     */
    public function theme_functions() {
        $func = views_theme_functions('views_views_fusioncharts_multipleseries_style', $this->view, $this->display);
        //kpr($func); die();
        return $func;
    }

    /**
     * Implementation of views_style_plugin::additional_theme_functions(). Returns empty array.
     * @return array
     */
    public function additional_theme_functions() {
        return array();
    }

    private function generate_series(&$form, $index, &$fieldNames) {
        $series = array();
        $seriesconfig = array(
            '#type' => 'fieldset',
            '#title' => 'Data Series ' . ($index + 1),
            '#tree' => TRUE,
            '#collapsible' => TRUE
        );
        $series['label'] = array(
            '#type' => 'textfield',
            '#title' => 'Series Label',
            '#description' => 'If this is left blank, this series will not be rendered.',
            '#default_value' => isset($this->options['series' . $index]['label']) ? $this->options['series' . $index]['label'] : NULL
        );
        $series['yaxisfield'] = array(
            '#type' => 'select',
            '#title' => 'Y Axis field',
            '#default_value' => isset($this->options['series' . $index]['yaxisfield']) ? $this->options['series' . $index]['yaxisfield'] : NULL,
            '#description' => t('View field containing values for the Y axis for this series. If this is left blank, this series will not be rendered.'),
            '#size' => 1,
            '#options' => $fieldNames,
            '#multiple' => FALSE
        );
        /*$series['color'] = array(
            '#type' => 'textfield',
            '#title' => 'Color',
            '#description' => t('Color to be used for this series, as a hex triplet. If left blank, FusionCharts will select a color.'),
            '#default_value' => isset($this->options['series' . $index]['color']) ? $this->options['series' . $index]['color'] : NULL
        );*/
        $series['color'] = array(
            '#type' => 'select',
            '#title' => 'Color',
            '#description' => t('Color to be used for this series, as a hex triplet. If left blank, FusionCharts will select a color.'),
            '#default_value' => isset($this->options['series' . $index]['color']) ? $this->options['series' . $index]['color'] : NULL,
            '#size' => 1,
            '#options' => $fieldNames,
            '#multiple' => FALSE
        );
        $series['linkfield'] = array(
            '#type' => 'select',
            '#title' => 'Link field',
            '#default_value' => isset($this->options['series' . $index]['linkfield']) ? $this->options['series' . $index]['linkfield'] : NULL,
            '#description' => t('View field containing link URL for this series.'),
            '#size' => 1,
            '#options' => $fieldNames,
            '#multiple' => FALSE
        );
        $series['tooltipfield'] = array(
            '#type' => 'select',
            '#title' => 'Tooltip field',
            '#default_value' => isset($this->options['series' . $index]['tooltipfield']) ? $this->options['series' . $index]['tooltipfield'] : NULL,
            '#description' => t('View field containing tooltip values for this series.'),
            '#size' => 1,
            '#options' => $fieldNames,
            '#multiple' => FALSE
        );
        if ($index < 2) {
            $seriesconfig['#collapsed'] = FALSE;
        }
        else {
            $seriesconfig['#collapsed'] = TRUE;
            foreach ($series as $key => $val) {
                if (isset($this->options['series' . $index][$key]) &&  drupal_strlen($this->options['series' . $index][$key]) > 0) {
                    $seriesconfig['#collapsed'] = FALSE;
                    break;
                }
            }
        }
        $form['series' . $index] = array_merge($seriesconfig, $series);
    }

}
