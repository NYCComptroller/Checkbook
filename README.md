Checkbook NYC
=============

Checkbook NYC is an open source financial transparency web application.

Checkbook provides transparent access to a city's or other jurisdiction's
financial information, through web-based dashboard views that show activity
in categories such as Revenue, Budget, Spending, Contracts, and
Payroll.  It also offers that information programmatically via APIs.

Checkbook operates on a read-only, filtered copy of a city's financial
data.  Confidential information like municipal employee's names and
addresses are not even in the Checkbook database.  Data is loaded via
an extract-transform-load (ETL) process; a typical frequency for
running the ETL import is a few times per week.

The New York City Office of the Comptroller runs a production instance
of Checkbook NYC at http://checkbooknyc.com/.

Checkbook NYC is licensed under the GNU Affero General Public License,
version 3.0.  See the file LICENSE.md for details.

Installing Checkbook
--------------------

Checkbook runs in a standard LAMP-stack environment: Apache HTTPD and
Apache Solr, MySQL and PostgreSQL.  Checkbook is built on top of
Drupal, but you do not need to install Drupal first, as Checkbook's
own source code includes the appropriate version of Drupal.

See the file INSTALL.md for details.

Developing Checkbook
--------------------

The source code to Checkbook NYC is available here:

      https://github.com/NYCComptroller/Checkbook

You can use the usual ways to interact with the project there (submit
pull requests, file tickets in the issue tracker, etc), and we invite
you to join us in the Checkbook NYC technical discussion forum:

      https://groups.google.com/d/forum/checkbooknyc
