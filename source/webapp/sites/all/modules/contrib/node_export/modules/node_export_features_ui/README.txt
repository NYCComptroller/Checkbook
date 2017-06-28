

Node Export Features UI
=======================

This module works with the Node Export Features module to provide an easy to
use method of selecting nodes to export.  It is based on this Node Export
issue: http://drupal.org/node/1626360

Currently, the feature module's UI does not support listing a large number of
nodes to select from.  Sites with large numbers of nodes caused major UI and
server performance problems.  Because of this, the node_export_feature module
currently limits the number of nodes in the Feature UI to only the first 250
nodes.

The current solution for changing what nodes to display is to write a custom
module. This module is for people who don't want to write a custom module.

When enabled, it adds a "Feature Configuration" tab on the Node Export settings
page. ( Admin->Configure->Content Authoring->Node Export )

In the Feature Configuration tab, you can select from a set of filters to
control what nodes get listed in the Feature modules UI.  The filter are:

* Number of nodes to List:

  Expand or shrink beyond the build in 250 nodes.

  NOTE: Using a large number can cause server and UI performance problems.

* Filter By Content Type:

  Only show nodes of the selected content types.

* Filter By Publishing Options:

  Filter by published, promoted, and sticky status.

* Filter by Title:

  Filter by title using an SQL 'LIKE' statement.  E.g, %Test%One%

* Filter by UUID:

  Supply a specific list of node UUIDs to show.

These filters are 'additive' so each one is an "AND" condition on the query.


Installation:

Install Node Export, then enable via the modules page (or drush or...)


Features Notes:

If you need to recreate a feature created with one or more of the filters set,
you will need to make sure that the Node Export Feature filters are the
same.
