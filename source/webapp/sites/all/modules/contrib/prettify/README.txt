
Google Code Prettify for Drupal 7 

-- SUMMARY --

Simple and lightweight syntax highlighting of source code snippets using Google
Code Prettify JavaScript library for Drupal.

Google Code Prettify supports all C-like (Java, PHP, C#, etc), Bash-like, and
XML-like languages without need to specify the language and has customizable
styles via CSS. Widely used with good cross-browser support.

-- REQUIREMENTS --

* Google Code Prettify library
  http://code.google.com/p/google-code-prettify

-- INSTALLATION --

* Download the latest Google Code Prettify JavaScript library from:
  http://code.google.com/p/google-code-prettify/

  Extract the content and place the js files and css inside of the following
  directory: sites/all/libraries/prettify
  
  Alternately, you can download the latest bleeding-edge development version
  using the following command from sites/all/libraries/ directory:
  
  svn export http://google-code-prettify.googlecode.com/svn/trunk/ prettify
  
  Finally, you should have something like this:
       
  sites/all/libraries/prettify/prettify.js, or
  sites/all/libraries/prettify/src/prettify.js

* Enable module as usual.

-- USAGE AND CONFIGURATION --

* Out of the box, code prettify comes configured to automatically perform syntax
  highlighting of source code snippets in <pre>...</pre> or <code>...</code>
  tags of your Drupal site.
  
  Automatic syntax highlighting mode is pretty simple, but powerful at the same
  time. Several settings can be configured at:
  Administration >> Configuration >> User interface >> Code prettify

* In addition, code prettify module also provides a filter to allow users can
  post code verbatim (without having to worry about manually escaping < and >
  characters).
  
  Prettify filter can be enabled and configured at:
  Administration >> Configuration >> Content authoring >> Text formats
  
-- TIPS & TRICKS --

* If you use a WYSIWYG editor is recommended use the automatic syntax
  highlighting mode.
  
* You don't need to specify the language of source code snippets since prettify
  will guess, but you can specify a language by specifying the language
  extension with the class:
  http://google-code-prettify.googlecode.com/svn/trunk/README.html

* This module includes several themes to customize the colors and styles of
  source code snippets. See the theme gallery (prettify/gallery) for examples.
  You can create your own custom CSS styles, too. 

-- DEVELOPERS --

Code prettify module provides a simple API for use by other modules and themes.

The server-side API looks as follows:

* prettify_add_library()

  Adds the prettify javascript and stylesheets to the current page. You
  should use this when you wish to use the client-side API to be pretty printed.

* theme('prettify', array('text' => $code))

  Returns the HTML of source code snippets will automatically be pretty printed. 

The client-side API looks as follows:

* Drupal.prettify.prettifyBlock(element)

  Use this method to syntax highlighting of the source code snippets. For example:

  $('pre code').each(function(i, e) {
    Drupal.prettify.prettifyBlock(e)}
  );

-- CREDITS --

Author and maintainer:
* Sergio Martín Morillas (smartinm) - http://drupal.org/user/191570

This module includes several CSS styles publicly available which are used as
themes for code prettify. Go to configure administration page for more info.
