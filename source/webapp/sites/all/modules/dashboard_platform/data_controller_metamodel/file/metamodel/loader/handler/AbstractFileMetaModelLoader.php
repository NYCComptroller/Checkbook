<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/




abstract class AbstractFileMetaModelLoader extends AbstractMetaModelLoader {

    private $converterJson2PHP = NULL;

    public function __construct() {
        parent::__construct();
        $this->converterJson2PHP = new Json2PHPObject();
    }

    abstract protected function getMetaModelFolderName();

    public function load(AbstractMetaModelFactory $factory, AbstractMetaModel $metamodel, array $filters = NULL, $finalAttempt) {
        $metamodelTypeFolder = $this->getMetaModelFolderName();

        $filecount = 0;
        $metamodelConfigurations = module_invoke_all('dc_metamodel');
        foreach ($metamodelConfigurations as $metamodelConfiguration) {
            $path = $metamodelConfiguration['path'] . DIRECTORY_SEPARATOR . $metamodelTypeFolder . DIRECTORY_SEPARATOR . 'metadata';

            // initial name space is not defined. It will be based on subfolder name, if any
            $namespace = NULL;

            $simplifiedPath = PathHelper::simplifyPath($path);
            LogHelper::log_info(t("Loading configuration from '@path' ...", array('@path' => $simplifiedPath)));
            if (!file_exists($path)) {
                throw new IllegalStateException(t('Folder could not be found: @path', array('@path' => $simplifiedPath)));
            }

            $filecount += $this->loadFromDirectory($metamodel, $filters, $path, $namespace);
        }
        LogHelper::log_info(t('Processed @filecount files', array('@filecount' => $filecount)));

        return self::LOAD_STATE__SUCCESSFUL;
    }

    protected function loadFromDirectory(AbstractMetaModel $metamodel, array $filters = NULL, $path, $namespace, $level = 0) {
        $filecount = 0;

        $handle = opendir($path);
        if ($handle !== FALSE) {
            $indent = str_pad('', $level * 4);
            while (($filename = readdir($handle)) !== FALSE) {
                if (is_dir($path . DIRECTORY_SEPARATOR . $filename)) {
                    if ($filename[0] != '.') {
                        $folder = DIRECTORY_SEPARATOR . $filename;

                        // once name space is defined we do not change it
                        // it will be the same for all sub-folders regardless on depth
                        $ns = isset($namespace) ? $namespace : $filename;

                        LogHelper::log_debug(t("{$indent}Scanning '@folderName' ...", array('@folderName' => $folder)));
                        $filecount += $this->loadFromDirectory($metamodel, $filters, $path . $folder, $ns, $level + 1);
                    }
                }
                elseif ($this->fileNameEndsWithJson($filename)) {
                    LogHelper::log_debug(t("{$indent}Processing '@filename' ...", array('@filename' => $filename)));

                    $this->loadFromFile($metamodel, $filters, $namespace, $path . DIRECTORY_SEPARATOR, $filename);
                    $filecount++;
                }
            }

            closedir($handle);
        }

        return $filecount;
    }

    protected function fileNameEndsWithJson($filename) {
        return strrpos($filename, '.json') === strlen($filename) - strlen('.json');
    }

    protected function loadFromFile(AbstractMetaModel $metamodel, array $filters = NULL, $namespace, $path, $filename) {
        $sourceFileName = $path . $filename;

        $contents = file_get_contents($sourceFileName);
        if ($contents === FALSE) {
            throw new IllegalStateException(t("Could not read content of '@filename' file", array('@filename' => $filename)));
        }

        $fileContent = $this->converterJson2PHP->convert($contents);
        if (!isset($fileContent)) {
            throw new IllegalStateException(t("Error in JSON structure in '@filename' file", array('@filename' => $filename)));
        }

        $modifiedDateTime = filemtime($sourceFileName);
        if ($modifiedDateTime === FALSE) {
            $modifiedDateTime = NULL;
        }

        $source = new __AbstractFileMetaModelLoader_Source();
        $source->filename = $sourceFileName;
        $source->datetime = $modifiedDateTime;
        $source->content = $fileContent;

        $this->merge($metamodel, $filters, $namespace, $source);
    }

    abstract protected function merge(AbstractMetaModel $metamodel, array $filters = NULL, $namespace, __AbstractFileMetaModelLoader_Source $source);
}

class __AbstractFileMetaModelLoader_Source extends AbstractObject {

    public $filename = NULL;
    public $datetime = NULL;
    public $content = NULL;
}
