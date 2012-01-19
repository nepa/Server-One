<?php

require_once('./MediaServer.php');

ini_set('soap.wsdl_cache_enabled', '0');

/**
 * Webservice that handles media files.
 */
class MediaService
{
  /**
   * Method for file upload.
   */
  public function uploadSample($latitude, $longitude, $title, $timestamp, $description, $payloadType, $payload)
  {
    return MediaServer::handleUploadRequest($latitude, $longitude, $title, $timestamp, $description, $payloadType, $payload);
  }

  /**
   * Method for sound level report.
   */
   public function reportNoiseLevel($latitude, $longitude, $zipCode, $noiseLevel)
   {
     return MediaServer::handleReportRequest($latitude, $longitude, $zipCode, $noiseLevel);
   }

   /**
    * Method to query for sound samples in neighbourhood.
    */
    public function getSamples($latitude, $longitude, $range)
    {
      return MediaServer::handleSamplesRequest($latitude, $longitude, $range);
    }

    /**
     * Method to query for noise levels in neighbourhood.
     */
    public function getNoiseLevels($latitude, $longitude, $range)
    {
      return MediaServer::handleNoiseLevelsRequest($latitude, $longitude, $range);
    }

    /**
     * Method to query for the average noise level in neighbourhood.
     */
    public function getAverageNoiseLevel($latitude, $longitude, $range)
    {
      return MediaServer::handleAverageNoiseLevelRequest($latitude, $longitude, $range);
    }

    /**
     * Method to query for the average noise level by zip code.
     */
    public function getAverageNoiseLevelByZipCode($zipCode)
    {
      return MediaServer::handleAverageNoiseLevelByZipCodeRequest($zipCode);
    }
}

?>
