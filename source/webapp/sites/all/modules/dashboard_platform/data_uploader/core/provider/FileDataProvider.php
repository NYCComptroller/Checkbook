<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/


class FileDataProvider extends AbstractDataProvider {

    private $filename = NULL;
    private $handle = FALSE;

    public function __construct($filename) {
        parent::__construct();
        $this->filename = $filename;
    }

    public function openResource() {
        LogHelper::log_notice(t('Parsing @filename ...', array('@filename' => $this->filename)));

        $result = parent::openResource();

        if ($result) {
            ini_set('auto_detect_line_endings', TRUE);
            $this->handle = fopen($this->filename, 'r');
            $result = $this->handle !== FALSE;
        }

        return $result;
    }

    protected function readLineFromResource() {
        $this->checkHandle();

        $this->incrementLineNumber();

        $line = fgets($this->handle);
        if ($line !== FALSE) {
            // removing line separators
            $line = trim($line, "\r\n");
        }

        return $line;
    }

    public function closeResource() {
        $this->checkHandle();

        fclose($this->handle);

        parent::closeResource();
    }

    protected function checkHandle() {
        if ($this->handle === FALSE) {
            throw new IllegalStateException(t("File '@fileName' has not been opened", array('@fileName' => $this->filename)));
        }
    }
}
