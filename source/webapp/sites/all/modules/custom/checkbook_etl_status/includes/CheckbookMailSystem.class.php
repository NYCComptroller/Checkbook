<?php
/**
 * Created by IntelliJ IDEA.
 * User: alexandr.perfilov
 * Date: 5/17/18
 * Time: 5:18 PM
 */

class CheckbookMailSystem extends DefaultMailSystem {
  public function format(array $message) {
    $message['body'] = implode("\n\n", $message['body']);
    $message['body'] = drupal_wrap_mail($message['body']);
    return $message;
  }
}
