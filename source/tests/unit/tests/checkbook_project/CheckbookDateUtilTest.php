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
     *
     */
    public function testCurrentCalendarYear()
    {
        $this->assertEquals(date('Y'), CheckbookDateUtil::getCurrentCalendarYear());
    }

    /**
     *
     */
    public function testCurrentCalendarYearId()
    {
        $this->assertEquals(date('Y') - 1899, CheckbookDateUtil::getCurrentCalendarYearId());
    }

    /**
     *
     */
    public function testCurrentFiscalYear()
    {
        $year = date('Y');
        if (date('m') > 6) {
            $year++;
        }
        $this->assertEquals($year, CheckbookDateUtil::getCurrentFiscalYear());
    }

    /**
     *
     */
    public function testCurrentFiscalYearId()
    {
        $year = date('Y');
        if (date('m') > 6) {
            $year++;
        }
        $this->assertEquals($year - 1899, CheckbookDateUtil::getCurrentFiscalYearId());
    }

    /**
     *
     */
    public function testYear2Id()
    {
        $this->assertEquals(120, CheckbookDateUtil::year2yearId(2019));
    }

    /**
     *
     */
    public function testYearId2year()
    {
        $this->assertEquals(2020, CheckbookDateUtil::yearId2Year(121));
    }
}


