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


class DefaultTruncateTableStatementImpl extends AbstractTruncateTableStatementImpl {

    public function generate(DataSourceHandler $handler, DatasetMetaData $dataset) {
        // NOTE: we cannot use TRUNCATE TABLE because this operation has to be part of a transaction
        // Also truncate does to work in some databases when there are FOREIGN KEYs pointing to this table
        return 'DELETE FROM ' . $dataset->source;
    }
}
