<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/




class WhiteCharacterStripper extends AbstractDataConverter {

    public function convert($input) {
        $output = $input;

        if (isset($output)) {
            $output = trim(preg_replace('/(\s){2,}/', ' ', str_replace(array("\r\n", "\n"), array(' ', ' '), $output)));
            if (strlen($output) === 0) {
                $output = NULL;
            }
        }

        return $output;
    }
}
