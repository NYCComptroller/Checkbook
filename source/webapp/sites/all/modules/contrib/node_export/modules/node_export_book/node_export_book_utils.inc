<?php
/**
 * @file
 *   A bunch of utility functions to help with importing and exporting books.
 */

/**
 * Return the Node ID of a node with a given Menu Link ID.
 *
 * @param $mlid
 *   The MLID of a node.
 *
 * @return
 *   The NID of the node with the given MLID; xor NULL if the MLID is not
 *   pointing to a node.
 *
 * @see menu_link_load()
 */
function _node_export_book_mlid_to_nid($mlid) {
  // Load the menu link
  $menu_item = menu_link_load($mlid);

  if ($menu_item != FALSE) {
    $path = explode('/', $menu_item['link_path']);

    // Return the node ID if it's a valid node path
    if ($path[0] == 'node' and is_numeric($path[1])) {
      return intval($path[1]);
    }
  }

  // If we get here, the node wasn't valid
  return NULL;
}

/**
 * Return the Universally Unique ID of a node with a given Menu Link ID.
 *
 * @param $mlid
 *   The MLID of a node.
 *
 * @return
 *   The UUID of the node with the given MLID; xor NULL if the node has not been
 *   assigned a UUID.
 *
 * @see _node_export_book_mlid_to_nid()
 * @see _node_export_book_nid_to_uuid()
 */
function _node_export_book_mlid_to_uuid($mlid) {
  return _node_export_book_nid_to_uuid(_node_export_book_mlid_to_nid($mlid));
}

/**
 * Return the Menu Link ID of a node with a given Node ID.
 *
 * @pre
 *   The node must be in a book.
 *
 * @param $nid
 *   The NID of a node.
 *
 * @return
 *   The MLID of the node with the given NID; xor NULL if the node has no book
 *   MLID (i.e.: is not in a book).
 *
 * @see menu_get_item()
 */
function _node_export_book_nid_to_mlid($nid) {
  $node_path = 'node/' . intval($nid);

  $query = db_select('menu_links', 'ml');
  $query->addField('ml', 'mlid');
  $query->condition('link_path', $node_path);
  $result = $query->execute()->fetchAssoc();

  if ($result == FALSE) {
    return NULL;
  }

  return $result['mlid'];
}

/**
 * Return the Universally Unique ID of a node with a given Node ID.
 *
 * @param $nid
 *   The NID of a node.
 *
 * @return
 *   The UUID of the node with the given NID; xor (FALSE if the node doesn't
 *   exist OR there the node has not been assigned a UUID yet).
 *
 * @see entity_get_uuid_by_id()
 */
function _node_export_book_nid_to_uuid($nid) {
  $uuid = entity_get_uuid_by_id('node', array($nid), FALSE);
  return array_pop($uuid);
}

/**
 * Return the Node ID of a node with a given Universally-Unique ID.
 *
 * @param $uuid
 *   The UUID of a node.
 *
 * @return
 *   The NID of the node with the given UUID; xor NULL if there are no nodes
 *   with that UUID.
 *
 * @see entity_get_id_by_uuid()
 */
function _node_export_book_uuid_to_nid($uuid) {
  $nid = entity_get_id_by_uuid('node', array($uuid), FALSE);
  return array_pop($nid);
}

/**
 * Return the Menu Link ID of a node with a given Universally-Unique ID.
 *
 * @param $uuid
 *   The UUID of a node.
 *
 * @return
 *   The MLID of the node with the given UUID; xor NULL if there are no nodes
 *   with that UUID.
 *
 * @see _node_export_book_uuid_to_nid()
 * @see _node_export_book_nid_to_mlid()
 */
function _node_export_book_uuid_to_mlid($uuid) {
  return _node_export_book_nid_to_mlid(_node_export_book_uuid_to_nid($uuid));
}
