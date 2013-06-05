<?php
 


require_once dirname(__FILE__) . '/views_fusioncharts_common.inc';

class views_plugin_style_fusioncharts_singleseries extends views_plugin_style {

    /**
     * Implementation of views_plugin_style::option_definition
     */
    public function option_definition() {

        $options = parent::option_definition();

        $formfields = array(
            'chart_type', 'chart_title', 'chart_subtitle', 'width', 'height',
            'xaxisname', 'xaxisfield', 'yaxisname', 'yaxisfield', 'yaxismax', 'yaxismin', 'yaxisvaluesstep', 'values',
            'columns', 'tooltips', 'appearance', 'fonts', 'divlines', 'numformat', 'canvas', 'anchor'
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
        $fieldNames = array_merge(array(NULL => 'Select One'), $fieldNames);
        if (module_exists('colorpicker_cck')) {
            $colorField = 'colorpicker_textfield';
        }
        else {
            $colorField = 'textfield';
        }

        $availableCharts = array(
            // Single-series charts
            'Area2D' => 'Area',
            'Bar2D' => 'Bar',
            'Column2D' => 'Column (2 dimensional)',
            'Column3D' => 'Column (3 dimensional)',
            'Doughnut2D' => 'Donut (2 dimensional)',
            'Doughnut3D' => 'Donut (3 dimensional)',
            'Line' => 'Line',
            'Pareto2D' => 'Pareto (2 dimensional)',
            'Pareto3D' => 'Pareto (3 dimensional)',
            'Pie2D' => 'Pie (2 dimensional)',
            'Pie3D' => 'Pie (3 dimensional)'
            /*'FCF_Area2D' => 'Area',
            'FCF_Bar2D' => 'Bar',
            'FCF_Column2D' => 'Column (2 dimensional)',
            'FCF_Column3D' => 'Column (3 dimensional)',
            'FCF_Doughnut2D' => 'Donut (2 dimensional)',
            'Doughnut3D' => 'Donut (3 dimensional)',
            'FCF_Line' => 'Line',
            'Pareto2D' => 'Pareto (2 dimensional)',
            'Pareto3D' => 'Pareto (3 dimensional)',
            'FCF_Pie2D' => 'Pie (2 dimensional)',
            'FCF_Pie3D' => 'Pie (3 dimensional)'*/
        );

        $form['chart_type'] = array(
            '#type' => 'select',
            '#title' => t('Chart Type'),
            '#default_value' => isset($this->options['chart_type']) ? $this->options['chart_type'] : 'Column2D',
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
        $form['yaxisfield'] = array(
            '#type' => 'select',
            '#title' => 'Y Axis field',
            '#default_value' => isset($this->options['yaxisfield']) ? $this->options['yaxisfield'] : NULL,
            '#required' => TRUE,
            '#description' => t('View field containing values for the Y axis'),
            '#size' => 1,
            '#options' => $fieldNames,
            '#multiple' => FALSE,
        );

        $form['yaxismax'] = array(
            '#type' => 'textfield',
            '#title' => 'Y Axis maximum value',
            '#default_value' => isset($this->options['yaxismax']) ? $this->options['yaxismax'] : NULL,
            '#description' => t('Maximum numeric value the Y-axis may take (for column charts)'),
        );

        $form['yaxismin'] = array(
            '#type' => 'textfield',
            '#title' => 'Y Axis minimum value',
            '#default_value' => isset($this->options['yaxismin']) ? $this->options['yaxismin'] : NULL,
            '#description' => t('Minimum numeric value the Y-axis may take (for column charts/it works for Line Charts too.)'),
        );
        $form['yaxisvaluesstep'] = array(
            '#type' => 'textfield',
            '#title' => 'Y Axis Values Step',
            '#default_value' => isset($this->options['yaxisvaluesstep']) ? $this->options['yaxisvaluesstep'] : NULL,
            '#description' => t('The values that should be displayed in the y-axis, to avoid overlapping of y-axis values.'),
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
            '#title' => t('Rotate column Values?'),
            '#description' => t('Controls whether column values are rotated 90 degrees. May not apply to some chart types such as pie or donut.'),
            '#default_value' => isset($this->options['columns']['rotate']) ? $this->options['columns']['rotate'] : FALSE,
        );

        $form['columns']['linkfield'] = array(
            '#type' => 'select',
            '#title' => 'Column URL Field',
            '#default_value' => isset($this->options['columns']['linkfield']) ? $this->options['columns']['linkfield'] : NULL,
            '#description' => t('View field containing URL to which the user will be redirected when clicking the corresponding area of the chart.'),
            '#size' => 1,
            '#options' => $fieldNames,
            '#multiple' => FALSE
        );
        $form['columns']['colorfield'] = array(
            '#type' => 'select',
            '#title' => 'Column Color Field',
            '#default_value' => isset($this->options['columns']['colorfield']) ? $this->options['columns']['colorfield'] : NULL,
            '#description' => t('View field containing the color for the corresponding chart area.'),
            '#size' => 1,
            '#options' => $fieldNames,
            '#multiple' => FALSE
        );

        $form['tooltips'] = array(
            '#type' => 'fieldset',
            '#title' => 'Tooltips',
            '#tree' => TRUE,
            '#collapsible' => TRUE,
            '#collapsed' => FALSE
        );
        $form['tooltips']['field'] = array(
            '#type' => 'select',
            '#title' => 'Tooltip field',
            '#default_value' => isset($this->options['tooltips']['field']) ? $this->options['tooltips']['field'] : NULL,
            '#description' => t('View field containing values for tooltips'),
            '#size' => 1,
            '#options' => $fieldNames,
            '#multiple' => FALSE,
        );
        $form['tooltips']['bgcolor'] = array(
            '#type' => $colorField,
            '#title' => 'Background Color',
            '#description' => t('Color to be used as the background color in tooltips, as a hex triplet.'),
            '#default_value' => isset($this->options['tooltips']['bgcolor']) ? $this->options['tooltips']['bgcolor'] : NULL,
        );
        $form['tooltips']['border'] = array(
            '#type' => $colorField,
            '#title' => 'Border Color',
            '#description' => t('Color to be used as the border color in tooltips, as a hex triplet.'),
            '#default_value' => isset($this->options['tooltips']['border']) ? $this->options['tooltips']['border'] : NULL,
        );

        $form['anchor'] = array(
            '#type' => 'fieldset',
            '#title' => 'Line Chart Anchor Settings',
            '#tree' => TRUE,
            '#collapsible' => TRUE,
            '#collapsed' => FALSE
        );
    /*    $form['anchor']['anchorsides'] = array(
            '#type' => 'textfield',
            '#title' => 'Anchor Sides',
            '#description' => t('This attribute sets the number of sides the anchor will have,It should be Numeric Value greater than 3.'),
            '#default_value' => isset($this->options['anchor']['anchorsides']) ? $this->options['anchor']['anchorsides'] : NULL,
        );

        $form['anchor']['anchorradius'] = array(
            '#type' => 'textfield',
            '#title' => 'Anchor Radius',
            '#description' => t('This attribute sets the radius of the Anchor, Numeric Value.'),
            '#default_value' => isset($this->options['anchor']['anchorradius']) ? $this->options['anchor']['anchorradius'] : NULL,
        );*/
        $form['anchor']['anchorsides'] = array(
            '#type' => 'select',
            '#title' => 'Anchor Sides',
            '#default_value' => isset($this->options['anchor']['anchorsides']) ? $this->options['anchor']['anchorsides'] : NULL,
            '#required' => FALSE,
            '#description' => t('This attribute sets the number of sides the anchor will have.'),
            '#size' => 1,
            '#options' => $fieldNames,
            '#multiple' => FALSE,
        );

        $form['anchor']['anchorradius'] = array(
            '#type' => 'select',
            '#title' => 'Anchor Radius',
            '#default_value' => isset($this->options['anchor']['anchorradius']) ? $this->options['anchor']['anchorradius'] : NULL,
            '#required' => FALSE,
            '#description' => t('This attribute sets the radius of the Anchor, Numeric Value.'),
            '#size' => 1,
            '#options' => $fieldNames,
            '#multiple' => FALSE,
        );
        $form['anchor']['anchorlink'] = array(
            '#type' => 'select',
            '#title' => 'Anchor Link',
            '#default_value' => isset($this->options['anchor']['anchorlink']) ? $this->options['anchor']['anchorlink'] : NULL,
            '#required' => FALSE,
            '#description' => t('This attribute sets the link of the Anchor.'),
            '#size' => 1,
            '#options' => $fieldNames,
            '#multiple' => FALSE,
        );
        $form['anchor']['anchorbgcolor'] = array(
            '#type' => $colorField,
            '#title' => 'Anchor Background Color',
            '#description' => t('Color to be used as the background color of the anchor, as a hex triplet.'),
            '#default_value' => isset($this->options['anchor']['anchorbgcolor']) ? $this->options['anchor']['anchorbgcolor'] : '#5599DC',
        );
        $form['anchor']['anchorbordercolor'] = array(
            '#type' => $colorField,
            '#title' => 'Anchor Border Color',
            '#description' => t('Color to be used as the border color of the anchor, as a hex triplet.'),
            '#default_value' => isset($this->options['anchor']['anchorbordercolor']) ? $this->options['anchor']['anchorbordercolor'] : '#000000',
        );

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
            '#default_value' => isset($this->options['canvas']['chartcanvaspadding']) ? $this->options['canvas']['chartcanvaspadding'] : 100,
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
        $form['appearance']['chartbordercolor'] = array(
            '#type' => $colorField,
            '#title' => 'Chart Border Color',
            '#description' => t('Color to be used as the Chart Border Color, as a hex triplet.'),
            '#default_value' => isset($this->options['appearance']['chartbordercolor']) ? $this->options['appearance']['chartbordercolor'] : '#F2F2F2',
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

        $form['appearance']['round'] = array(
            '#type' => 'checkbox',
            '#title' => t('Use round edges on chart areas?'),
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
        $form['appearance']['linecolor'] = array(
            '#type' => $colorField,
            '#title' => t('Chart Line Color'),
            '#default_value' => isset($this->options['appearance']['linecolor']) ? $this->options['appearance']['linecolor'] : NULL,
            '#description' => t('Color of the Line Drawned for the data values, as a hex triplet.')
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
        $form['appearance']['rotatelabels'] = array(
            '#type' => 'checkbox',
            '#title' => t('Rotate the Labels?'),
            '#default_value' => isset($this->options['appearance']['rotatelabels']) ? $this->options['appearance']['rotatelabels'] : FALSE,
            '#description' => t('Rotate the labels. It will help while  displaying the x axis labels without making it crowded.')
        );
        $form['appearance']['rotatenames'] = array(
            '#type' => 'checkbox',
            '#title' => t('Rotate the Names?'),
            '#default_value' => isset($this->options['appearance']['rotatenames']) ? $this->options['appearance']['rotatenames'] : FALSE,
            '#description' => t('Rotate the Names. It will help while  displaying the x axis labels without making it crowded.')
        );
        $form['appearance']['showshadow'] = array(
            '#type' => 'checkbox',
            '#title' => t('Show Shadow.'),
            '#default_value' => isset($this->options['appearance']['showshadow']) ? $this->options['appearance']['showshadow'] : TRUE,
            '#description' => t('This attribute helps you set whether the shadow would be shown or not.')
        );
        $form['appearance']['hidefusionchartmenu'] = array(
            '#type' => 'checkbox',
            '#title' => t('Show Fusion Chart label?'),
            '#default_value' => isset($this->options['appearance']['hidefusionchartmenu']) ? $this->options['appearance']['hidefusionchartmenu'] : TRUE,
            '#description' => t('If Unchecked-Hide the Fusion Chart label on right click.')
        );
        $form['appearance']['maxXaxisValue'] = array(
            '#type' => 'textfield',
            '#title' => t('Maximum limit of X-axis to display'),
            '#description' => t('Maximum number of Values to display on the X-axis'),
            '#default_value' => isset($this->options['appearance']['maxXaxisValue']) ? $this->options['appearance']['maxXaxisValue'] : NULL
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
            '#collapsed' => (bool)count($this->options['divlines'])
        );
        $form['divlines']['number'] = array(
            '#type' => 'textfield',
            '#title' => 'Number of Dividing Lines',
            '#default_value' => isset($this->options['divlines']['number']) ? $this->options['divlines']['number'] : NULL,
        );
        $form['divlines']['color'] = array(
            '#type' => $colorField,
            '#title' => 'Background Gradient Bottom',
            '#description' => t('Color to be used for horizontal dividing lines, as a hex triplet.'),
            '#default_value' => isset($this->options['divlines']['color']) ? $this->options['divlines']['color'] : NULL,
        );
        $form['divlines']['dashed'] = array(
            '#type' => 'checkbox',
            '#title' => t('Use dashed lines?'),
            '#default_value' => isset($this->options['divlines']['dashed']) ? $this->options['divlines']['dashed'] : NULL,
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
        $func = views_theme_functions('views_views_fusioncharts_singleseries_style', $this->view, $this->display);
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
}
