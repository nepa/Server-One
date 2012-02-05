<?php

require_once('./config.php');
require_once('./Database.php');

/**
 * Download manager for client-side sample fetching.
 */

class DownloadManager
{
  /**
   * Fetch a sound sample from disk and send it to requesting client.
   */
  public function fetchSample($sampleID)
  {
    global $config;

    $fileName = '';
    $fileType = '';
    $sampleName = '';

    // Sample ID must be provided
    if ($sampleID == '')
    {
      die('Please provide a sample ID.');
    }

    // Payload type lookup in database
    $database = new Database();
    $resultSet = $database->getPayloadType($sampleID);
    if (count($resultSet) <= 0)
    {
      die('No sample found for ID \'' . $sampleID . '\'.');
    }
    else
    {
      // Build file name
      if (!empty($resultSet[0]['fileType']))
      {
        $fileType = $resultSet[0]['fileType'];
      }
      $fileName = $sampleID . $config['security_file_extension'];
      $sampleName = 'AudioSample-' . $sampleID . '.' . $resultSet[0]['fileType'];

      // Prevent directory traversal attacks
      $fileName = str_replace('..', '', $fileName);

      // Check if file exists on disk
      if (file_exists($config['upload_dir'] . $fileName))
      {
        // Set MIME type and file name
        header('Content-Type: ' . $this->getContentType($fileType));
        header('Content-Disposition: attachment; filename="' . $sampleName . '"');

        // Send file to client
        readfile($config['upload_dir'] . $fileName);

        // Exit script
        exit();
      }
    }
  }

  /**
   * Private helper method to get the content type for a
   * file extension (e.g. map mp3 files to audio/mpeg).
   */
  private function getContentType($fileType)
  {
    $contentType = 'audio/';

    switch (strtolower($fileType))
    {
      case 'mp3':
        $contentType .= 'mpeg';
        break;
      case 'm4a':
        $contentType .= 'm4a';
        break;
      case 'ogg':
        $contentType .= 'ogg';
        break;
      default:
        die('Unknown file extension. Cannot determine content type.');
        break;
    }

    return $contentType;
  }
}

// Get sample ID from HTTP GET
$sampleID = '';
if (!empty($_GET['sid']))
{
  $sampleID = $_GET['sid'];
}

// Download file
$downloadManager = new DownloadManager();
$downloadManager->fetchSample($sampleID);

?>
