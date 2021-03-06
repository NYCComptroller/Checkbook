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

include_once CUSTOM_MODULES_DIR . '/checkbook_project/customclasses/util/CheckbookDateUtil.php';

use PHPUnit\Framework\TestCase;

/**
 * Class CheckbookDateUtilTest
 */
class CheckbookDateUtilTest extends TestCase
{
    /**
     * Tests year2yearId function
     */
    public function testYear2Id()
    {
        $this->assertEquals(120, CheckbookDateUtil::year2yearId(2019));
    }

    /**
     * Tests yearId2Year function
     */
    public function testYearId2year()
    {
        $this->assertEquals(2020, CheckbookDateUtil::yearId2Year(121));
    }
}


