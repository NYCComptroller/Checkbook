<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/




class PHP2Json extends AbstractDataConverter {

    public function convert($input) {
        return json_encode($input);
    }
}
