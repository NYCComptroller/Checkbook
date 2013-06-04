<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/




class CommentStripper extends AbstractDataConverter {

    public function convert($input) {
        $output = $input;

        if (isset($output)) {
            // removing lines marked with //
            $output = preg_replace('#\/\/.*#', ' ', $output);
            // removing /* ... */
            $output = preg_replace('#\/\*(.|[\r\n])*?\*\/#', ' ', $output);
            $output = trim($output);
            if (strlen($output) === 0) {
                $output = NULL;
            }
        }

        return $output;
    }
}
