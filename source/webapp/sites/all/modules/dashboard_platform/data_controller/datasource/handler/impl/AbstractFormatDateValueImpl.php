<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/


abstract class AbstractFormatDateValueImpl extends AbstractObject {

    protected $adjustedMasks = NULL;

    abstract protected function adjustMask($mask);

    protected function prepareMask($mask) {
        if (isset($this->formattedMasks[$mask])) {
            return $this->formattedMasks[$mask];
        }

        $adjustedMask = $this->adjustMask($mask);

        $this->formattedMasks[$mask] = $adjustedMask;

        return $adjustedMask;
    }

    public function format(DataSourceHandler $handler, $formattedValue, $mask) {
        $adjustedMask = $this->prepareMask($mask);

        return $this->formatImpl($handler, $formattedValue, $adjustedMask);
    }

    abstract protected function formatImpl(DataSourceHandler $handler, $formattedValue, $adjustedMask);
}
