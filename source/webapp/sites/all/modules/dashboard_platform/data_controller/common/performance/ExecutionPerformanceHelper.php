<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/

 


class ExecutionPerformanceHelper {

    public static function formatExecutionTime($timeStart) {
        $SCALE_INTERVAL = 10;

        $timeEnd = 1000 * (microtime(TRUE) - $timeStart);
        $output = "$timeEnd ms";

        if ($timeEnd > $SCALE_INTERVAL) {
            $timeScale = $SCALE_INTERVAL;
            while ($timeEnd > ($newTimeScale = ($timeScale * $SCALE_INTERVAL))) {
                $timeScale = $newTimeScale;
            }

            $output .= " (>$timeScale ms)";
        }

        return $output;
    }
}
