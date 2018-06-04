<?php

$aliases['checkbook-os-qa'] = array(
  'uri' => 'qa.checkbook.nyc.reisys.com',
  'root' => '/var/www/html',
  'db-url' => 'mysqli://drupal:qZk3r9S@localhost/checkbook_drupal',
  'remote-host' => 'qa.checkbook.nyc.reisys.com',
  'remote-user' => 'hudson',
  'path-aliases' => array(
    '%files' => 'sites/default/files',
    '%dump' => '/var/tmp/drupal.sql', // Arbitrary location for temp files
   ),
);


$aliases['checkbook-os-qa2'] = array(
    'uri' => 'qa.checkbook3.nyc.reisys.com',
    'root' => '/var/www/html',
    'db-url' => 'mysqli://drupal:qZk3r9S@localhost/checkbook_drupal',
    'remote-host' => 'qa.checkbook3.nyc.reisys.com',
    'remote-user' => 'hudson',
    'path-aliases' => array(
        '%files' => 'sites/default/files',
        '%dump' => '/var/tmp/drupal.sql', // Arbitrary location for temp files
    ),
);

$aliases['checkbook-os-uat'] = array(
  'uri' => 'uat.checkbook.nyc.reisys.com',
  'root' => '/var/www/html',
  'db-url' => 'mysqli://drupal:qZk3r9S@localhost/checkbook_drupal',
  'remote-host' => 'uat.checkbook.nyc.reisys.com',
  'remote-user' => 'hudson',
  'path-aliases' => array(
    '%files' => 'sites/default/files',
    '%dump' => '/var/tmp/drupal.sql', // Arbitrary location for temp files
   ),
);

$aliases['checkbook-os-uat2'] = array(
    'uri' => 'uat.checkbook3.nyc.reisys.com',
    'root' => '/var/www/html',
    'db-url' => 'mysqli://drupal:qZk3r9S@localhost/checkbook_drupal',
    'remote-host' => 'uat.checkbook3.nyc.reisys.com',
    'remote-user' => 'hudson',
    'path-aliases' => array(
        '%files' => 'sites/default/files',
        '%dump' => '/var/tmp/drupal.sql', // Arbitrary location for temp files
    ),
);
