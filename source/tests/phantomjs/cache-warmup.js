/*
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

// How to: run with "phantomjs cache-warmup.js"

const URLS = [
  'dev-checkbook-nyc.reisys.com',
  'dev2-checkbook-nyc.reisys.com',
  'qa-checkbook-nyc.reisys.com',
  'qa2-checkbook-nyc.reisys.com'
];

// phantomjs page object and helper flag
var page = require('webpage').create(),
  loadInProgress = false,
  pageIndex = 0;

page.viewportSize = {
  width: 1024
};

// page handlers
page.onLoadStarted = function () {
  loadInProgress = true;
  console.log(URLS[pageIndex] + ' load started');
};

page.onLoadFinished = function () {
  loadInProgress = false;
  page.render("scr_" + URLS[pageIndex] + ".png");
  console.log(URLS[pageIndex] + ' load finished');
  pageIndex++;
};

// try to load or process a new page every 250ms
setInterval(function () {
  if (!loadInProgress && pageIndex < URLS.length) {
    console.log("capturing " + URLS[pageIndex]);
    page.open('https://' + URLS[pageIndex] + '/');
  }
  if (pageIndex == URLS.length) {
    console.log("image render complete!");
    phantom.exit();
  }
}, 10000);
