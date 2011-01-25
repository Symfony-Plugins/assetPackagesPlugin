<?php

require_once dirname(__FILE__).'/../../bootstrap/unit.php';

$t = new lime_test(2, new lime_output_color());


$t->comment('::addToRequestedPackages()');
$response = new sfWebResponse(new sfEventDispatcher);
assetPackagesWebResponseExtension::addToRequestedPackages($response, 'foo');
$slots = $response->getSlots();
$t->ok(
  $found = isset($slots['_requestedPackages']),
  '::addToRequestedPackages() add a "_requestedPackages" to response'
);
if (!$found)
{
  $t->skip('Cannot test registred "_requestedPackages" slot', 1);
}
else
{
  $t->ok(
    in_array('foo', $slots['_requestedPackages']),
    '::addToRequestedPackages() add the package name to the "_requestedPackages" slot array'
  );
}
