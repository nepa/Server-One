<?php

require_once(dirname(__FILE__) . '/config.php');

/**
 * Database backend for data storing and information retrieval.
 */
class Database
{
  private $db = null;

  /**
   * Database constructor creates database connection.
   */
  public function __construct()
  {
    global $config;

    try
    {
      // Connect to database
      $this->db = new PDO('mysql:host=' . $config['db_hostname'] . ';dbname=' . $config['db_database'],
        $config['db_user'], $config['db_password']);
    }
    catch (PDOException $e)
    {
      die($e->getMessage());
    }
  }

  /**
   * Database desctructor deconnects fom database.
   */
  public function __destruct()
  {
    // Close connection
    $this->db = null;
  }

  /**
   * Save meta data in database.
   */
  public function saveMetadata($latitude, $longitude, $title, $timestamp, $description, $sampleID, $payloadType)
  {
    $success = false;

    // Set title and description to NULL, if string is empty
    if (empty($title))
    {
      $title = null;
    }
    if (empty($description))
    {
      $description = null;
    }

    try
    {
      // Prepare insert statement
      $preparedStatement = $this->db->prepare(
        'INSERT INTO metadata (latitude, longitude, title, timestamp, description, sampleID, fileType, reportedAt, reportedBy) ' .
        'VALUES (:latitude, :longitude, :title, :timestamp, :description, :sampleID, :fileType, :reportedAt, :reportedBy)');

      // Bind values to placeholders
      $preparedStatement->bindParam(':latitude', $latitude, PDO::PARAM_STR);
      $preparedStatement->bindParam(':longitude', $longitude, PDO::PARAM_STR);
      $preparedStatement->bindParam(':title', $title, PDO::PARAM_STR);
      $preparedStatement->bindParam(':timestamp', $timestamp, PDO::PARAM_STR);
      $preparedStatement->bindParam(':description', $description, PDO::PARAM_STR);
      $preparedStatement->bindParam(':sampleID', $sampleID, PDO::PARAM_STR);
      $preparedStatement->bindParam(':fileType', $payloadType, PDO::PARAM_STR);

      // Some logging information
      $now = date('Y-m-d H:i:s', time());
      $ip = $_SERVER['REMOTE_ADDR'];
      $preparedStatement->bindParam(':reportedAt', $now, PDO::PARAM_STR);
      $preparedStatement->bindParam(':reportedBy', $ip, PDO::PARAM_STR);

      // Execute statement
      $success = $preparedStatement->execute();
    }
    catch (PDOException $e)
    {
      die($e->getMessage());
    }

    return $success;
  }

  /**
   * Save noise level in database.
   */
  public function saveNoiseLevel($latitude, $longitude, $timestamp, $zipCode, $noiseLevel)
  {
    $success = false;

    // Set zip code to NULL, if string is empty
    if (empty($zipCode))
    {
      $zipCode = null;
    }

    try
    {
      // Prepare insert statement
      $preparedStatement = $this->db->prepare(
        'INSERT INTO noiseLevels (latitude, longitude, timestamp, zipCode, noiseLevel, reportedAt, reportedBy) ' .
        'VALUES (:latitude, :longitude, :timestamp, :zipCode, :noiseLevel, :reportedAt, :reportedBy)');

      // Bind values to placeholders
      $preparedStatement->bindParam(':latitude', $latitude, PDO::PARAM_STR);
      $preparedStatement->bindParam(':longitude', $longitude, PDO::PARAM_STR);
      $preparedStatement->bindParam(':timestamp', $timestamp, PDO::PARAM_STR);
      $preparedStatement->bindParam(':zipCode', $zipCode, PDO::PARAM_STR);
      $preparedStatement->bindParam(':noiseLevel', $noiseLevel, PDO::PARAM_INT);

      // Some logging information
      $now = date('Y-m-d H:i:s', time());
      $ip = $_SERVER['REMOTE_ADDR'];
      $preparedStatement->bindParam(':reportedAt', $now, PDO::PARAM_STR);
      $preparedStatement->bindParam(':reportedBy', $ip, PDO::PARAM_STR);

      // Execute statement
      $success = $preparedStatement->execute();
    }
    catch (PDOException $e)
    {
      die($e->getMessage());
    }

    return $success;
  }

  /**
   * Query database for nearby sound samples.
   */
  public function getSamples($latitude, $longitude, $range)
  {
    $result = array();

    try
    {
      // Calculate corner points
      $cornerPoints = $this->calculateArea($latitude, $longitude, $range);

      // Prepare select statement
      $preparedStatement = $this->db->prepare(
        'SELECT latitude, longitude, title, timestamp, description, sampleID FROM metadata WHERE ' .
        '(latitude BETWEEN :lat_north AND :lat_south) AND ' .
        '(longitude BETWEEN :long_west AND :long_east)');

      // Bind values to placeholders
      $preparedStatement->bindParam(':lat_north', $cornerPoints['lat_north'], PDO::PARAM_STR);
      $preparedStatement->bindParam(':lat_south', $cornerPoints['lat_south'], PDO::PARAM_STR);
      $preparedStatement->bindParam(':long_west', $cornerPoints['long_west'], PDO::PARAM_STR);
      $preparedStatement->bindParam(':long_east', $cornerPoints['long_east'], PDO::PARAM_STR);

      // Execute statement
      $preparedStatement->execute();

      // Fetch result set and eventually return it
      $result = $preparedStatement->fetchAll(PDO::FETCH_ASSOC);
    }
    catch (PDOException $e)
    {
      die($e->getMessage());
    }

    return $result;
  }

  /**
   * Query database for nearby sound levels.
   */
  public function getNoiseLevels($latitude, $longitude, $range)
  {
    $result = array();

    try
    {
      // Calculate corner points
      $cornerPoints = $this->calculateArea($latitude, $longitude, $range);

      // Prepare select statement
      $preparedStatement = $this->db->prepare(
        'SELECT latitude, longitude, timestamp, zipCode, noiseLevel FROM noiseLevels WHERE ' .
        '(latitude BETWEEN :lat_north AND :lat_south) AND ' .
        '(longitude BETWEEN :long_west AND :long_east)');

      // Bind values to placeholders
      $preparedStatement->bindParam(':lat_north', $cornerPoints['lat_north'], PDO::PARAM_STR);
      $preparedStatement->bindParam(':lat_south', $cornerPoints['lat_south'], PDO::PARAM_STR);
      $preparedStatement->bindParam(':long_west', $cornerPoints['long_west'], PDO::PARAM_STR);
      $preparedStatement->bindParam(':long_east', $cornerPoints['long_east'], PDO::PARAM_STR);

      // Execute statement
      $preparedStatement->execute();

      // Fetch result set and eventually return it
      $result = $preparedStatement->fetchAll(PDO::FETCH_ASSOC);
    }
    catch (PDOException $e)
    {
      die($e->getMessage());
    }

    return $result;
  }

  /**
   * Query database for average sound level by zip code.
   */
  public function getAverageNoiseLevelByZipCode($zipCode)
  {
    $result = array();

    try
    {
      // Prepare select statement
      $preparedStatement = $this->db->prepare(
        'SELECT AVG(noiseLevel) AS averageNoiseLevel FROM noiseLevels WHERE zipCode = :zipCode');

      // Bind value to placeholder
      $preparedStatement->bindParam(':zipCode', $zipCode, PDO::PARAM_STR);

      // Execute statement
      $preparedStatement->execute();

      // Fetch result set and eventually return it
      $result = $preparedStatement->fetchAll(PDO::FETCH_ASSOC);
    }
    catch (PDOException $e)
    {
      die($e->getMessage());
    }

    return $result;
  }

  /**
   * Private helper method to calculate the corner points of
   * the area that surrounds the current geo location.
   */
  private function calculateArea($latitude, $longitude, $range)
  {
    $result = array();

    // latitude/longitude are given in decimal degree
    // range is given in km
    //
    // 0.01 degree is equal to 1.11 km, so
    // 1 km is about 0.009 degree
    $rangeInDegree = $range * 0.009;

    // Calculate northern and southern margin of area
    $result['lat_north'] = $latitude - $rangeInDegree;
    $result['lat_south'] = $latitude + $rangeInDegree;
    if ($result['lat_north'] > $result['lat_south'])
    {
      $temp = $result['lat_north'];
      $result['lat_north'] = $result['lat_south'];
      $result['lat_south'] = $temp;
    }

    // Calculate western and eastern margin of area
    $result['long_west'] = $longitude - $rangeInDegree;
    $result['long_east'] = $longitude + $rangeInDegree;
    if ($result['long_west'] > $result['long_east'])
    {
      $temp = $result['long_east'];
      $result['long_east'] = $result['long_west'];
      $result['long_west'] = $temp;
    }

    return $result;
  }

  /**
   * Get payload type of a sound sample.
   */
  public function getPayloadType($sampleID)
  {
    $result = array();

    try
    {
      // Prepare select statement
      $preparedStatement = $this->db->prepare(
        'SELECT fileType FROM metadata WHERE sampleID = :sampleID');

      // Bind value to placeholder
      $preparedStatement->bindParam(':sampleID', $sampleID, PDO::PARAM_STR);

      // Execute statement
      $preparedStatement->execute();

      // Fetch result set and eventually return it
      $result = $preparedStatement->fetchAll(PDO::FETCH_ASSOC);
    }
    catch (PDOException $e)
    {
      die($e->getMessage());
    }

    return $result;
  }

  /**
   * Query database for authentification data.
   */
  public function getAuthenticationData($appName, $apiKey)
  {
    $result = array();

    try
    {
      // Prepare select statement
      $preparedStatement = $this->db->prepare(
        'SELECT appName, apiKey FROM apiUsers WHERE appName = :appName AND apiKey = :apiKey');

      // Bind value to placeholder
      $preparedStatement->bindParam(':appName', $appName, PDO::PARAM_STR);
      $preparedStatement->bindParam(':apiKey', $apiKey, PDO::PARAM_STR);

      // Execute statement
      $preparedStatement->execute();

      // Fetch result set and eventually return it
      $result = $preparedStatement->fetchAll(PDO::FETCH_ASSOC);
    }
    catch (PDOException $e)
    {
      die($e->getMessage());
    }

    return $result;
  }
}

?>
