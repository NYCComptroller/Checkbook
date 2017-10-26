Testing
==================

This file discusses the various test suites used in Checkbook.  We
distinguish between server-side and client-side (browser-based) testing.


Server-side
------------

TBD


Client-side
---------------

Checkbook uses [Selenium](http://www.seleniumhq.org/) for browser-based
testing (that is, UI testing).  For the most part these tests are
written in Java.  See `source/tests/functional/NYCAutomationTest` for
these Java-based Selenium tests.

### Writing a new test

There are a few layers of test infrastructure.  To add an entirely new
suite of tests:

1.  Create a new test suite in
`source/tests/functional/NYCAutomationTest/src/test/java/suites/`.

2. Include that new suite in `pom.xml` like this:

   <include>**/suites/__YOUR_SUITE_NAME__.java</include>

This should be in the same `<includes>` block as the other suites
(currently `FunctionalTest.java`).


To add a new class of tests to an existing suite:

1. Add a new class to the suite subdirectory
(e.g. `source/tests/functional/NYCAutomationTest/src/test/java/smoke/`).

2. Add the class to the list of classes in the suite file
(e.g. `source/tests/functional/NYCAutomationTest/src/test/java/suites/FunctionalTest.java`),
under `@SuiteClasses`.

Add a new test to an existing class:

1. Write a new method in a class file.

### Maven

To use these tests:

    $ cd source/tests/functional/NYCAutomationTest

    # Using whatever editor you prefer, edit the conf file to use your
    server and auth credentials.  Make sure not to commit your secrets
    to a public repository:
    
    $ emacs src/test/resources/conf.properties
    
      # Edit the file to use your URL and database connection
      # information (and the current fiscal year, for now).  This should
      # match the information in settings.php for the PostgreSQL
      # `checkbook` database.  Set your operating system to Windows,
      # Linux, or Mac.
      
    $ mvn test
      # If the webdriver is set up, this will open a browser window and
      # perform some tests.



