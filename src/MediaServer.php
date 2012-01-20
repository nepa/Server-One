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
  public static function handleUploadRequest($latitude, $longitude, $title, $timestamp, $description, $payloadType, $payload)
  {
    // Validate geo coordinates
    if (!MediaServer::validGeoCoordinates($latitude, $longitude))
    {
      return array('Error', 'Invalid or no geo coordinates provided.');
    }

    // Validate payload type
    if (!MediaServer::validatePayloadType($payloadType))
    {
      return array('Error', 'Invalid or no payload type provided.');
    }

    // Generate random file name
    $sampleID = MediaServer::generateSampleID($latitude, $longitude, $description);

    // Write file to disk
    if (!MediaServer::storePayload($sampleID, $payload))
    {
      return array('Error', 'Could not store payload on disk.');
    }

    // Save meta data in database
    $database = new Database();
    if (!$database->saveMetadata($latitude, $longitude, $title, $timestamp, $description, $sampleID, $payloadType))
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
  public static function handleReportRequest($latitude, $longitude, $zipCode, $noiseLevel)
  {
    // Validate geo coordinates
    if (!MediaServer::validGeoCoordinates($latitude, $longitude))
    {
      return array('Error', 'Invalid or no geo coordinates provided.');
    }

    // Validate zip code
    if (!MediaServer::validZipCode($zipCode, true))
    {
      return array('Error', 'Invalid zip code provided. It must not exceed ten characters.');
    }

    // Store noise level in database
    $database = new Database();
    if (!$database->saveNoiseLevel($latitude, $longitude, $zipCode, $noiseLevel))
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
   * Handle incoming requests that query for average noise level in neighbourhood.
   * Sound levels are taken from the database and average is calculated
   * across all values in range.
   */
  public static function handleAverageNoiseLevelRequest($latitude, $longitude, $range)
  {
    // Validate geo coordinates
    if (!MediaServer::validGeoCoordinates($latitude, $longitude))
    {
      return array('Error', 'Invalid or no geo coordinates provided.');
    }

    // Return average noise level from neighbourhood
    $database = new Database();
    $resultSet = $database->getNoiseLevels($latitude, $longitude, $range);
    if (count($resultSet) <= 0)
    {
      return array('Info', 'No nearby sound levels found.');
    }

    // Calculate average value and return as integer
    $sum = 0;
    foreach ($resultSet as $data)
    {
      $sum += $data['noiseLevel'];
    }
    $averageNoiseLevel = intval($sum / count($resultSet));

    // Send response
    return array('OK', 'Average sound level queried successfully.', $averageNoiseLevel);
  }

  /**
   * Handle incoming requests that query for average noise level by zip
   * code. Sound levels are taken from the database and average is
   * calculated across all values with desired postal code.
   */
  public static function handleAverageNoiseLevelByZipCodeRequest($zipCode)
  {
    // Validate zip code
    if (!MediaServer::validZipCode($zipCode, false))
    {
      return array('Error', 'Invalid or no zip code provided.');
    }

    // Return average noise level by zip code
    $database = new Database();
    $resultSet = $database->getAverageNoiseLevelByZipCode($zipCode);

    // Cast average noise level to integer
    $averageNoiseLevel = intval($resultSet[0]['averageNoiseLevel']);
    if ($averageNoiseLevel == '')
    {
      return array('Info', 'No sound levels found in postcode area.');
    }

    // Send response
    return array('OK', 'Average sound level successfully queried by zip code.', $averageNoiseLevel);
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
   * Validate zip code from SOAP request. Postal code is optional,
   * but should not exceed ten characters, if value is set.
   */
  public static function validZipCode($zipCode, $optional = false)
  {
    $result = false;

    // Zip code must never exceed ten characters
    if (strlen($zipCode) <= 10 && ($optional || !$optional && strlen($zipCode) > 0))
    {
      $result = true;
    }

    return $result;
  }

  /**
   * Validate payload type, which should either be one of "mp3",
   * "mp4" or "ogg".
   */
  public static function validatePayloadType($payloadType)
  {
    $result = false;

    if (in_array($payloadType, array('mp3', 'mp4', 'ogg')))
    {
      $result = true;
    }

    return $result;
  }

  /**
   * Generate a random sample ID, so we can store files with unique
   * names on the disk later.
   */
  public static function generateSampleID($latitude, $longitude, $title, $timestamp, $description)
  {
    return md5($latitude . $longitude . $title . $timestamp . $description . time());
  }

  /**
   * Store payload on disk.
   */
  public static function storePayload($sampleID, $payload)
  {
    global $config;

    $fileName = $config['upload_dir'] . $sampleID . $config['security_file_extension'];

    return file_put_contents($fileName, base64_decode($payload));
  }
}

?>
