// $Id: uniform.js,v 1.4 2010/11/02 20:51:53 realityloop Exp $
/**
* @file
* JavaScript related to Uniform.
*/

Drupal.behaviors.uniform = {
  attach: function(context, settings) {
    if (settings.uniform != undefined) {
      if (settings.uniform['selectors']) {
        if (settings.uniform['not']) {
          jQuery(settings.uniform['selectors'].join(':not(.uniform-processed), '), context).not(settings.uniform['not']).addClass('uniform-processed').uniform();
        }
        else {
          jQuery(settings.uniform['selectors'].join(':not(.uniform-processed), '), context).addClass('uniform-processed').uniform();
        }
      }
    }
  }
}
