<?php

/**
 * Test client for media file upload.
 */

try
{
  $file = file_get_contents('./test.mp3');

  $client = new SoapClient('http://localhost/s1/s1.php?wsdl');
  $response = $client->uploadSample('51.586923', '7.656859', 'Some recorded audio.', 'mp3', base64_encode($file));

  echo 'Response: "Status ' . $response['Statuscode'] . ': ' . $response['Message'] . '"';
}
catch (SoapFault $e)
{
  echo 'Error: ' . $e->faultstring;
}

?>
