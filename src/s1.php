<?php

/**
 * Server for webservice request handling.
 */

error_reporting(0); // No error reporting in live environment

require_once(dirname(__FILE__) . '/MediaService.php');

try
{
  $server = new SoapServer(dirname(__FILE__) . '/s1.wsdl', array('encoding' => 'ISO-8859-1'));
  $server->setClass('MediaService');
  $server->handle();
}
catch (SoapFault $e)
{
  die($e->faultstring);
}

?>
