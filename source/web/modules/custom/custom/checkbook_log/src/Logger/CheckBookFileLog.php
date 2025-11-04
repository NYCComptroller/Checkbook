<?php

namespace Drupal\checkbook_log\Logger;

use Drupal\Component\Datetime\TimeInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Logger\LogMessageParserInterface;
use Drupal\Core\State\StateInterface;
use Drupal\filelog\LogFileManagerInterface;
use Drupal\filelog\Logger\FileLog;
use Drupal\Core\Render\BubbleableMetadata;
use Drupal\checkbook_log\CheckBookLogMessage;
use Drupal\Component\Render\PlainTextOutput;

/**
 * CheckBook decorator for File-based logger.
 */
class CheckBookFileLog extends FileLog {

  /**
   * FileLog constructor.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $configFactory
   *   The config.factory service.
   * @param \Drupal\Core\State\StateInterface $state
   *   The state service.
   * @param \Drupal\Core\Utility\Token $token
   *   The token service.
   * @param \Drupal\Component\Datetime\TimeInterface $time
   *   The datetime.time service.
   * @param \Drupal\Core\Logger\LogMessageParserInterface $parser
   *   The logger.log_message_parser service.
   * @param \Drupal\filelog\LogFileManagerInterface $fileManager
   *   The filelog.file_manager service.
   */
  public function __construct(ConfigFactoryInterface $configFactory,
                              StateInterface $state,
                              TimeInterface $time,
                              LogMessageParserInterface $parser,
                              LogFileManagerInterface $fileManager) {
    $this->config = $configFactory->get('filelog.settings');
    $this->state = $state;
    $this->time = $time;
    $this->parser = $parser;
    $this->fileManager = $fileManager;
  }

  /**
   * Renders a message to a string.
   *
   * @param mixed $level
   *   Severity level of the log message.
   * @param string $message
   *   Content of the log message.
   * @param array $context
   *   Context of the log message.
   *
   * @return string
   *   The formatted message.
   */
  protected function render(mixed $level, string $message, array $context = []): string {
    // Populate the message placeholders.
    $variables = $this->parser->parseMessagePlaceholders($message, $context);
    // Pass in bubbleable metadata that are just discarded later to prevent a
    // LogicException due to too early rendering. The metadata of the string
    // is not needed as it is not used for cacheable output but for writing to a
    // logfile.
    $bubbleable_metadata_to_discard = new BubbleableMetadata();
    $log = new CheckBookLogMessage($level, $message, $variables, $context);
    $this->token = \Drupal::service('token');
    $entry = $this->token->replace(
      $this->config->get('format'),
      ['log' => $log],
      [],
      $bubbleable_metadata_to_discard
    );
    return PlainTextOutput::renderFromHtml($entry);
  }

}
