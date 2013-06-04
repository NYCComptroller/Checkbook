<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/




abstract class AbstractValueComparator extends AbstractComparator {

    protected function compareSingleValue($a, $b, $isOrderAscending) {
        $result = 0;

        if (isset($a)) {
            if (isset($b)) {
                if (is_numeric($a) && is_numeric($b)) {
                    $delta = $a - $b;
                    $result = ($delta > 0)
                        ? 1
                        : (($delta < 0) ? -1 : 0);
                }
                else {
                    $result = strcasecmp($a, $b);
                }
            }
            else {
                $result = 1;
            }
        }
        elseif (isset($b)) {
            $result = -1;
        }

        if (($result != 0) && !$isOrderAscending) {
            $result *= -1;
        }

        return $result;
    }
}
