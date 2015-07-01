<?php
/**
 * This file is part of the Checkbook NYC financial transparency software.
 *
 * Copyright (C) 2012, 2013 New York City
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

class CheckbookAPIUtil {
    /**
     * Function is used to render a test harness for the API.
     */
    static function CallAPI() {
        global $conf;
        if($_GET['submit'] == 1) {

            $host = $conf['check_book']['data_feeds']['site_url'];
            $url = $host.'/api?wsdl';
            $xml = $_POST['txtInput'];
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            $response = curl_exec($ch);
            curl_close($ch);

            $xml = simplexml_load_string($response);
            $json = json_encode($xml);
            $array = json_decode($json,TRUE);
            $html = self::render_xml_data($array);
            echo $html;
        }
        else {
            $domain = 'spending_oge';
            $xml = file_get_contents(realpath(drupal_get_path('module', 'checkbook_api')) . "/sample/" . strtolower($domain) . ".xml");

            echo '<form action="api-test?submit=1" method="post">';
            echo 'Paste Input Here:<br>';
            echo '<textarea name="txtInput" rows="20" columns="40" style="height: 100%;">'.$xml.'</textarea>';
            echo '<br>';
            echo '<br><br>';
            echo '<input type="submit" value="Submit">';
            echo '</form> ';
        }

    }

    /**
     * Given an xml string, renders the xml to the screen in a readable structure
     *
     * @param $xml
     * @return string
     */
    static function render_xml_data($xml){

        $space = "&emsp;&emsp;";
        $indent = $space;
        $html = "<div>&lt;response&gt;"."<br/>";
        foreach($xml as $key=>$value)
        {
            if(is_array($value)) {
                $html .= $space."&lt;".$key."&gt;";
                $html .= "<br/>".self::printChildren($value,$indent.$space);
                $html .= $indent."&lt;/".$key."&gt;"."<br/>";
            }
            else {
                $html .= $indent."&lt;".$key."&gt;";
                $html .= $value;
                $html .= "&lt;/".$key."&gt;"."<br/>";
            }
        }
        $html .= "&lt;/response&gt;</div>";

        return $html;
    }

    /**
     * Recursive function to render and xml node.
     *
     * @param $child_xml
     * @param string $indent
     * @return string
     */
    static function printChildren($child_xml,$indent="") {
        $html = "";
        $space = "&emsp;&emsp;";
        foreach($child_xml as $key=>$value)
        {
            if(is_array($value)) {
                $html .= $indent;
                $html .= "&lt;".$key."&gt;";
                $html .= "<br/>".self::printChildren($value,$indent.$space);
                $html .= $indent."&lt;/".$key."&gt;"."<br/>";
            }
            else {
                $html .= $indent;
                $html .= "&lt;".$key."&gt;";
                $html .= $value;
                $html .= "&lt;/".$key."&gt;"."<br/>";
            }
        }
        return $html;
    }
} 