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
