<?php

require_once(dirname(__FILE__) . '/config.php');

/**
 * Logging facility for easier system monitoring.
 */
class Logger
{
  /** Constants for service events */
  const SAMPLE_UPLOAD            = 'UPLOAD';
  const NOISE_REPORT             = 'REPORT';
  const SAMPLES_REQUEST          = 'SAMPLE';
  const NOISE_REQUEST            = 'NREQUE';
  const NOISE_AVG_REQUEST        = 'AVGREQ';
  const NOISE_AVG_BY_ZIP_REQUEST = 'AVGZIP';

  /**
   * Hook for message logging. Messages will be written to
   * log file and they will be sent to admin email address.
   */
  public static function log($message, $action, $level = 'II')
  {
    global $config;

    // Some logging information
    $now = date('Y-m-d H:i:s', time());
    $ip = $_SERVER['REMOTE_ADDR'];

    // Data string
    $data = '(' . $level . ') ' . $action . ', ' . $now . ' [IP ' . $ip . ']: ' . $message . "\n";

    // Write to log file
    file_put_contents($config['logfile'], $data, FILE_APPEND | LOCK_EX);

    // Send notification email
    switch ($action)
    {
      case Logger::SAMPLE_UPLOAD:
      case Logger::NOISE_REPORT:
        Logger::email($message, $now, $ip, $action, $level);
        break;
    }
  }

  /**
   * Helper method to send emails through PHP.
   */
  public static function email($message, $now, $ip, $action, $level)
  {
    global $config;

    $success = false;

    // Build subject line
    $subject = '[MMS] (' . $level . ') Webservice notification: ' . $action;

    // Create message text
    $data = $message . "\n\n" .
            'From IP ' . $ip . "\n\n" .
            'Received at ' . $now . "\n\n" .
            'This notification was sent by Server One.';

    // Set headers
    $headers = 'From: ' . $config['from_email'] . "\r\n" .
               'Reply-To: ' . $config['from_email'] . "\r\n" .
               'X-Mailer: Server One';

    // Send email, if all addresses are set in config
    if (!empty($config['from_email']) && !empty($config['to_email']))
    {
      $success = mail($config['to_email'], $subject, $data, $headers);
    }

    return $success;
  }
}

?>
