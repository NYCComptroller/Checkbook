<?php
/**
 * This file is part of the Checkbook NYC financial transparency software.
 *
 * Copyright (C) 2019 New York City
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


include_once CUSTOM_MODULES_DIR . '/../dashboard_platform/data_controller/common/pattern/AbstractObject.php';
include_once CUSTOM_MODULES_DIR . '/../dashboard_platform/data_controller/common/object/converter/DataConverter.php';
include_once CUSTOM_MODULES_DIR . '/../dashboard_platform/data_controller/common/object/converter/handler/AbstractDataConverter.php';
include_once CUSTOM_MODULES_DIR . '/../dashboard_platform/data_controller/common/object/converter/handler/CommentStripper.php';
include_once CUSTOM_MODULES_DIR . '/../dashboard_platform/data_controller/common/object/converter/handler/AbstractJson2PHP.php';
include_once CUSTOM_MODULES_DIR . '/../dashboard_platform/data_controller/common/object/converter/handler/Json2PHP.php';
include_once CUSTOM_MODULES_DIR . '/../dashboard_platform/data_controller/common/object/converter/handler/WhiteCharacterStripper.php';




use PHPUnit\Framework\TestCase;

/**
 * Class JsonUtilTest
 */
class JsonUtilTest extends TestCase
{

    /**
     *
     */
    public function test_modules_custom_json()
    {
        $this->markTestIncomplete('todo');
        return;

        $dirItr    = new RecursiveDirectoryIterator(dirname(__DIR__, 4)
            .'/webapp/sites/all/modules/custom');
        $itr       = new RecursiveIteratorIterator($dirItr);

        foreach ($itr as $file) {
            if ($file->isDir() || ('json' !== $file->getExtension())){
                continue;
            }

//            echo $file->getPathname() . PHP_EOL;

            $path = $file->getPathname();
            $dirs = explode(DIRECTORY_SEPARATOR, dirname($path));
            $displayPath = join('/',array_slice($dirs, -3,3));
            $displayPath .= '/'.basename($path);

            $content = file_get_contents($file->getPathname());
            $valid = $this->json_is_valid($content);
            if (!$valid) {
                $converter = new Json2PHPObject();
                $obj = $converter->convert($content);
                if (!$obj) {
                    echo 'Invalid JSON: '.$displayPath;
                    echo PHP_EOL;
                    var_dump($obj);
                    echo PHP_EOL;
                } else {
                    echo 'Valid JSON: '.$displayPath;
                    echo PHP_EOL;
                }
            }
//            $this->assertTrue($valid, 'Invalid JSON: '.$displayPath);
        }

    }

    //JSON Validator function
    function json_is_valid($data=NULL) {
        if (!empty($data)) {
            @json_decode($data);
            return (json_last_error() === JSON_ERROR_NONE);
        }
        return false;
    }
}

class JsonFilterIterator extends RecursiveFilterIterator {

    public static $FILTERS = [
        'json',
    ];

    public function accept() {
        echo $this->current()->getFilename().PHP_EOL;
        return in_array(
            $this->current()->getExtension(),
            self::$FILTERS,
            true
        );
    }

}
