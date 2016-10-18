<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
class FormattingUtilities{
    /**
     * return replacment patter for title ...
     * @param string $text
     * @param integer $length
     */
    static public function getTooltipMarkup($text, $length = 20, $no_of_lines = 2){
      return self::breakTextCustom(html_entity_decode($text,ENT_QUOTES), $length, $no_of_lines);
    }

    /**
     * return replacment patter for title ...
     * @param string $text
     * @param integer $length
     */
    static function breakTextCustom($text, $length = 20, $no_of_lines = 2){
        $text_array = explode(" ",$text);
        $offset = 0;
        $remaining_original = $remaining = ($length%2 == 0) ? ($length/$no_of_lines) : (round($length/$no_of_lines)-1);
        $first_line = true;
        $index= 0 ;

        if(strlen($text_array[0]) >= 2 * $remaining_original ){
            return "<span title='" . htmlentities($text,ENT_QUOTES) . "'>". substr(htmlentities($text), 0, $length -3 ) . "...</span>";
        }else{
            foreach($text_array as $key=>$value){
                if($first_line){
                    if(strlen($value) >= $remaining && $index ==0){
                        $first_line = false;
                        $second_line = true;
                        $remaining = 2* $remaining_original -  strlen($value) -1;
                        $offset = strlen($value)+1;
                    }else{
                        $remaining = $remaining - (strlen($value)+1);
                        if($remaining <= 0){
                            $first_line = false;
                            $second_line = true;
                            $remaining = $remaining_original;
                        }else{
                            $offset += strlen($value)+1;
                        }
                    }
                }
                if($second_line){
                    $prev_remaining = $remaining;
                    $remaining = $remaining - (strlen($value)+1);
                    if($remaining >= 0){
                        $offset = $offset + strlen($value)+1;
                    }else{
                        $offset = $offset+( $prev_remaining - 3  );
                        $second_line = false;
                    }
                }
                $index +=1;
            }
            if($offset < strlen($text) )
                return "<span title='" . htmlentities($text,ENT_QUOTES) . "'>". htmlentities(substr($text, 0, $offset )) . "...</span>";
            else
                return "<span title='" . htmlentities($text,ENT_QUOTES) . "'>". htmlentities(substr($text, 0, $offset )) . "</span>";
        }
    }
}

