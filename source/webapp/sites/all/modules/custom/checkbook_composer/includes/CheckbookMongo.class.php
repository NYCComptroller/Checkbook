<?php

/**
 * Class CheckbookMongo
 */
class CheckbookMongo
{
  /**
   * @var array
   */
  private static $instances = [];

  /**
   * @param string $db
   * @return bool|\MongoDB\Database
   * @throws \MongoDB\Exception\InvalidArgumentException
   */
  public static function getDb($db = 'checkbooknyc')
  {
    global $conf;
    if (!extension_loaded('mongodb') || !isset($conf['checkbook_mongo_srv'])) {
      return false;
    }

    if (isset(self::$instances[$db])) {
      return self::$instances[$db];
    }

    $client = new MongoDB\Client($conf['checkbook_mongo_srv']);
    self::$instances[$db] = $client->selectDatabase($db);
    return self::$instances[$db];
  }

  /**
   * CheckbookMongo constructor.
   */
  private function __construct()
  {
  }

  /**
   *
   */
  private function __clone()
  {
  }

  /**
   *
   */
  private function __wakeup()
  {
  }
}
