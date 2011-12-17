<?php

/**
 * Server for webservice request handling.
 */

error_reporting(E_ALL); // No error reporting in live environment

require_once('./MediaService.php');

try
{
  $server = new SoapServer('./s1.wsdl', array('encoding' => 'UTF-8'));
  $server->setClass('MediaService');
  $server->handle();
}
catch (SoapFault $e)
{
  die($e->faultstring);
}

?>
