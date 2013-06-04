<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/


abstract class AbstractJson2PHP extends AbstractDataConverter {

    private $returnAsAssociativeArray = FALSE;
    private $cleanInput = NULL;

    public function __construct($returnAsAssociativeArray, $cleanInput = TRUE) {
        parent::__construct();
        $this->returnAsAssociativeArray = $returnAsAssociativeArray;
        $this->cleanInput = $cleanInput;
    }

    public function convert($input) {
        $cleanedInput = NULL;
        if ($this->cleanInput) {
            $stripperComment = new CommentStripper();
            $stripperWhiteCharacter = new WhiteCharacterStripper();

            $cleanedInput = $stripperWhiteCharacter->convert($stripperComment->convert($input));
        }

        return json_decode((isset($cleanedInput) ? $cleanedInput : $input), $this->returnAsAssociativeArray);
    }
}
