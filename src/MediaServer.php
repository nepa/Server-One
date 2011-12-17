<?php

require_once('./config.php');
require_once('./Database.php');

/**
 * Backend media server for meta data and payload handling.
 */
class MediaServer
{
  /**
   * Handle incoming upload requests, i.e. save meta data in
   * database and store payload on disk.
   */
  public static function handleUploadRequest($latitude, $longitude, $description, $payloadType, $payload)
  {
    // Validate geo coordinates
    if (!MediaServer::validGeoCoordinates($latitude, $longitude))
    {
      return array('Error', 'Invalid or no geo coordinates provided.');
    }

    // Generate random file name
    $fileName = MediaServer::generateFileName($latitude, $longitude, $description, $payloadType);

    // Write file to disk
    if (!MediaServer::storePayload($fileName, $payload))
    {
      return array('Error', 'Could not store payload on disk.');
    }

    // Save meta data in database
    $database = new Database();
    if (!$database->saveMetadata($latitude, $longitude, $description, $fileName))
    {
      return array('Error', 'Could not write meta data to database.');
    }

    // Send response
    return array('OK', 'Media file uploaded successfully.');
  }

  /**
   * Handle incoming requests that report noise levels. Store these in
   * the database.
   */
  public static function handleReportRequest($latitude, $longitude, $noiseLevel)
  {
    // Validate geo coordinates
    if (!MediaServer::validGeoCoordinates($latitude, $longitude))
    {
      return array('Error', 'Invalid or no geo coordinates provided.');
    }

    // Store noise level in database
    $database = new Database();
    if (!$database->saveNoiseLevel($latitude, $longitude, $noiseLevel))
    {
      return array('Error', 'Could not write noise level to database.');
    }

    // Send response
    return array('OK', 'Noise level reported successfully.');
  }

  /**
   * Handle incoming requests that query for sound samples in neighbourhood.
   * File names are taken from the database.
   */
  public static function handleSamplesRequest($latitude, $longitude, $range)
  {
    // Validate geo coordinates
    if (!MediaServer::validGeoCoordinates($latitude, $longitude))
    {
      return array('Error', 'Invalid or no geo coordinates provided.');
    }

    // Return data of sound samples in neighbourhood
    $database = new Database();
    $sampleData = $database->getSamples($latitude, $longitude, $range);
    if (count($sampleData) <= 0)
    {
      return array('Info', 'No nearby sound samples found.');
    }

    // Send response
    return array('OK', 'Sound samples queried successfully.', $sampleData, count($sampleData));
  }

  /**
   * Handle incoming requests that query for noise levels in neighbourhood.
   * Sound levels are taken from the database.
   */
  public static function handleNoiseLevelsRequest($latitude, $longitude, $range)
  {
    // Validate geo coordinates
    if (!MediaServer::validGeoCoordinates($latitude, $longitude))
    {
      return array('Error', 'Invalid or no geo coordinates provided.');
    }

    // Return noise levels from neighbourhood
    $database = new Database();
    $noiseLevels = $database->getNoiseLevels($latitude, $longitude, $range);
    if (count($noiseLevels) <= 0)
    {
      return array('Info', 'No nearby sound levels found.');
    }

    // Send response
    return array('OK', 'Sound levels queried successfully.', $noiseLevels, count($noiseLevels));
  }

  /**
   * Validate geo coordinates from SOAP request.
   */
  public static function validGeoCoordinates($latitude, $longitude)
  {
    $result = false;

    if ($latitude >= -90.0 && $latitude <= 90.0 && $longitude >= -180.0 && $longitude <= 180.0)
    {
      $result = true;
    }

    return $result;
  }

  /**
   * Generate a random file name, so that we can uniquely store
   * it on disk later on.
   */
  public static function generateFileName($latitude, $longitude, $description, $payloadType)
  {
    global $config;

    $fileName = md5($latitude . $longitude . $description . time());

    switch (strtolower($payloadType))
    {
      case 'mp3':
        $fileName .= '.mp3';
        break;
      case 'ogg':
        $fileName .= '.ogg';
        break;
      // Remove any unknown file extensions for security reasons!
      default:
        $fileName .= $config['unknown_file_extension'];
        break;
    }

    return $fileName . ".s1";
  }

  /**
   * Store payload on disk.
   */
  public static function storePayload($fileName, $payload)
  {
    global $config;

    return file_put_contents($config['upload_dir'] . $fileName, base64_decode($payload));
  }
}

?>
