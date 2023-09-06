<?php
/**
 * This file is part of the Checkbook NYC financial transparency software.
 *
 * Copyright (c) 2012 – 2023 New York City
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

namespace Drupal\data_controller\MetaModel\Handler;

use Drupal\checkbook_log\LogHelper;
use Drupal\data_controller\Common\Object\Exception\IllegalStateException;
use Drupal\data_controller\Common\Pattern\AbstractObject;

abstract class AbstractMetaModel extends AbstractObject {

    public $version = NULL;
    private $assembled = FALSE;

    public function __construct() {
        parent::__construct();
        $this->version = uniqid();
    }

    public function isAssembled() {
        return $this->assembled;
    }

    public function startAssembling() {
        if (!$this->assembled) {
            throw new IllegalStateException(t('Meta Model assembling has already been started'));
        }

        $this->assembled = FALSE;
    }

    public function markAsAssembled() {
        $this->finalize();
        $this->validate();
        $this->assembled = TRUE;
    }

    protected function finalize() {}

    protected function validate() {}

    protected function checkAssemblingStarted() {
        if ($this->assembled) {
            LogHelper::log_error(t('Meta Model assembling has not been started'));
        }
    }
}
