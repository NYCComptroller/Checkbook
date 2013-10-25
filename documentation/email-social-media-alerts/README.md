CheckbookNYC Email / Social Media Features
==========================================

In mid-2013, CGI, Inc contributed an email / social media alerts
feature to CheckbookNYC.  The first component of the feature allows a
user to ask Checkbook to send email to a designated address whenever
the results of a particular search change in some specified way.

The second component is similar: it allows the owner of the Checkbook
instance to set a tweet to go out whenever a search's results change.
In other words, the destination is a pre-authenticated Twitter account
from which to tweet, rather than an email address to send email to.

The slide deck CGI developed to propose the alert features actually
makes pretty good documentation, so we include it in this directory:

        Checkbook NYC Social Media Contribution 091213.pptx

We recommend looking at that slide deck first.  The rest of this
README is an explanation of the usage of the features, followed by a
description of how they are implemented and how to administrate them.

User Interface and Usage
------------------------

**Email Alerts**

After a user performs any type of search (e.g., advanced search), a
"create alert" option appears at the top of the results page, right
below the "export" button.  Clicking it brings up a popup window
requesting some basic information:

 * Email address,
 * How frequently the alert is desired (e.g., daily, weekly, etc),
 * What the alert threshold should be (e.g., when 10 more results, or 1000 more results, etc),
 * End date (default 1 year),
 * Description, i.e., a name for the alert so the user can recognize it.

No other personally identifying information (PII) is requested other
than the email address, which of course need not be a personal email
address.
          
Checkbook first sends a mailback confirmation email to the address, to
confirm the alert.  When the user clicks on the unique URL given in
the confirmation email, that activates the alert.  (The alert has no
effect until activated.)

Once the alert is activated, that user will receive an email -- no
more often than the specified alert frequency, of course -- whenever
the result count for the given search changes by at least the
threshold named by the user.

For example, if the user requested to be alerted when the result count
changes by at least 10 results, and the search had 100 results
initially, then if the next day there are 108 results and the user
requested a daily frequency, no alert will be sent for that search.
But if a few days later the results finally reach 115, now the alert
will be sent, and the new baseline will be set at 115, so that the
next alert will be sent only when the result count hits or surpasses
125.  Note the threshold applies only to *increases* in the result
count; currently, no alert would be sent if the result count *drops*
by the threshold or more (although an argument could be made that
perhaps one should be sent, since there are circumstances under which
the result count might go down).
          
If the same email address is subscribed to multiple alerts, then all
alerts that would go out to that address on a given day are batched
together in one email.  The email offers separate links to unsubscribe
from each individual alert, and at the bottom a link to unsubscribe
from all alerts.

**Twitter Alerts**

The Twitter functionality is similar to the email functionality, but
instead of an email being sent, the new result count is tweeted (based
on a provided template) from an account that is pre-authorized with
that Checkbook instance.

Unlike the email feature, the Twitter feature is intended only for use
by the owner of the Checkbook instance.  As of Oct 2013, there is no
administrative interface (web-based or otherwise) for configuring it.
Instead, one sets an email alert and then manually tweaks the relevant
table in the database to transform the alert into a Twitter alert.
See the **Developer Information** section below for more details.

Developer Information for Checkbook Email / Social Media Alerts
---------------------------------------------------------------

The code for the alerts system lives in the `checkbook_alerts` module:

        source/webapp/sites/all/modules/custom/checkbook_alerts/

Outside that module, it is integrated into Checkbook in these files:

        source/webapp/sites/all/modules/custom/widget_framework/widget_data_tables/widget_data_tables.module
        source/webapp/sites/all/themes/checkbook3/css/global.css
        source/webapp/sites/all/themes/checkbook3/js/global.js

You can see the basic diff of the feature by doing...

        $ git diff 01e7beb5ff4..bac3f0f7582

...on any branch to which the changes have been ported.

Here is the basic design:

All alerts are stored in a new `checkbook_alerts` table, whose schema
is defined in `checkbook_alerts_schema()` in

        source/webapp/sites/all/modules/custom/checkbook_alerts/checkbook_alerts.module

Every night a batch script runs, cycling through all the alerts and
re-running the query for each alert.  If the number of search results
has increased by at least the requested threshold for that alert, then
(in the email case) that alert is added to a list of alerts to be sent
for that email address, or (in the Twitter case) is immediately
tweeted.

The batch script is

        source/webapp/sites/all/modules/custom/checkbook_alerts/script/sendAlerts.php

**Setting up Tweets**

The flag that distinguishes (on a per-alert basis) between email and
Twitter alerts is the `recipient_type` column in the
`checkbook_alerts` table.  If its value is "EMAIL", then the
`recipient` value is an email address to send the alert to (possibly
as one of several alerts batched together in one email).  If the flag
is "TWITTER", then the `recipient` field is a Twitter username, and
the private Twitter keys to authenticate that username are given in an
array value in `source/webapp/sites/default/default.settings.php`,
e.g.:

        $conf['check_book']['alerts']['twitter']['TWITTER_USERNAME'] =
            array(
                'oauth_access_token' => "OAUTH_ACCESS_TOKEN",
                'oauth_access_token_secret' => "OAUTH_ACCESS_TOKEN_SECRET",
                'consumer_key' => "CONSUMER_KEY",
                'consumer_secret' => "CONSUMER_SECRET"
            );

The content of the tweet will be the value of the `description` field
for that alert, with ":count" substituted by the new result count
that caused the alert.
          
The best method to create an Twitter alert is to create an email alert
and then modify the `checkbook_alerts` table directly.
