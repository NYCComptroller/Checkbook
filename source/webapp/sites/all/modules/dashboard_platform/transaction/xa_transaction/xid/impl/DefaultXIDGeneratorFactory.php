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
