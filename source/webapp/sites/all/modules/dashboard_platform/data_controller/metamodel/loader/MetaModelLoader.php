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




interface MetaModelLoader {

    function getName();

    function prepare(AbstractMetaModelFactory $factory, AbstractMetaModel $metamodel);

    /*
     * A loader can return self::LOAD_STATE__POSTPONED if it wants to wait until other loaders complete loading meta model.
     * The state can be returned as long as it is necessary and ($finalAttempt == TRUE) indicates last possible attempt to load meta model
     */
    function load(AbstractMetaModelFactory $factory, AbstractMetaModel $metamodel, array $filters = NULL, $finalAttempt);

    function finalize(AbstractMetaModelFactory $factory, AbstractMetaModel $metamodel);
}
