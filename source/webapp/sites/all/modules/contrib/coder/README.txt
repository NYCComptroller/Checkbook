
Developer Module that assists with code review and version upgrade that supports
a plug-in extensible hook system so contributed modules can define additional
review standards.

Built-in support for:
 - Drupal Coding Standards - http://drupal.org/node/318
 - Handle text in a secure fashion - http://drupal.org/node/28984

Coder Sniffer
-------------

See the README.txt file in the coder_sniffer directory.


Installation
------------

Copy coder.module to your module directory and then enable on the admin
modules page.  Enable the modules that admin/config/development/coder/settings
works on, then view the coder results page at coder.


Automated Testing (PHPUnit)
---------------------------

Coder Sniffer comes with a PHPUnit test suite to make sure the sniffs work correctly.
Use Composer to install the dependencies:

  composer install

Then execute the tests:

  ./vendor/bin/phpunit


Author
------
Doug Green
douggreen@douggreenconsulting.com
