<?php

namespace Drupal\checkbook_log;

use Drupal\filelog\LogMessage;

/**
 * Represents a single CheckBook log event.
 */
class CheckBookLogMessage extends LogMessage {

  /**
   * Get the rendered text of the message.
   *
   * @return string
   *   The rendered text.
   */
  public function getText(): string {
    if (!isset($this->text)) {
      $this->text = $this->message;
      if (!empty($this->placeholders)) {
        $this->text = strtr($this->text, $this->placeholders);
      }
      $this->text = strip_tags($this->text);
    }
    return $this->text;
  }

}
