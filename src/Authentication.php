<?php

require_once(dirname(__FILE__) . '/config.php');
require_once(dirname(__FILE__) . '/Database.php');

/**
 * Helper class for authentification of client requests.
 */
class Authentication
{
  /**
   * Validate a pair of application name and API key. The method
   * will return a boolean value to indicate whether the data is
   * a valid combination or not.
   */
  public static function validate($appName, $apiKey)
  {
    // Request credentials from database
    $database = new Database();
    $credentials = $database->getAuthenticationData($appName, $apiKey);

    // Empty result means invalid name/key
    return (count($credentials) > 0);
  }

  /**
   * Create a new API key for the given application name. The
   * key is a simple MD5 hash, incorporating the app name, a
   * salt phrase and the current time.
   */
  public static function createApiKey($appName)
  {
    global $config;

    return md5($appName . $config['auth_salt'] . time());
  }
}

?>
