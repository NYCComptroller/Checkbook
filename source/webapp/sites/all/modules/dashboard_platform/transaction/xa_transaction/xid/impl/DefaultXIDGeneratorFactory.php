<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/


class DefaultXIDGeneratorFactory extends XIDGeneratorFactory {

    private static $generator = NULL;

    public function getGenerator() {
        if (!isset(self::$generator)) {
            $generatorConfigurations = module_invoke_all('xid_generator');
            $count = count($generatorConfigurations);
            if ($count == 0) {
                throw new IllegalStateException(t('No XID generators were registered'));
            }
            elseif ($count == 1) {
                reset($generatorConfigurations);
                $generatorConfiguration = current($generatorConfigurations);
                $classname = $generatorConfiguration['classname'];

                self::$generator = new $classname();
            }
            else {
                throw new IllegalStateException(t('Only one XID generator is supported at a time'));
            }
        }

        return self::$generator;
    }
}
