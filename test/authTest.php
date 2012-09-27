<?php

require_once(dirname(__FILE__) . '/Authentication.php');

echo '<br />API Key: ' . Authentication::createApiKey('testApp') . '<br />';

// Create new entry in apiUser table: testApp / 54415ba17240f22b7feb34fe0fab08e0

test('testApp', '54415ba17240f22b7feb34fe0fab08e0'); // true
test('otherApp', '54415ba17240f22b7feb34fe0fab08e0'); // false
test('testApp', 'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx'); // false
test('otherApp', 'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx'); // false

function test($name, $key)
{
  echo '<br />Is ' . (Authentication::validate($name, $key) === false ? 'NOT' : '') .
      ' a valid combination: ' . $name . ' / ' . $key;
}

?>
