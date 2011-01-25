<?php

require_once dirname(__FILE__).'/../../bootstrap/unit.php';
$dispatcher = new sfEventDispatcher();

$t = new lime_test(2, new lime_output_color());


$t->comment('::setPackages()');
$response = new sfWebResponse($dispatcher);
$packagesList = array('foo');
assetPackagesWebResponseExtension::setPackages($response, $packagesList);
$slots = $response->getSlots();
$t->ok(
  $found = isset($slots['_packages']),
  '::setPackages() add a "_packages" slot to response'
);
if (!$found)
{
  $t->skip('Cannot test registred packages', 1);
}
else
{
  $t->is_deeply(
    $slots['_packages'],
    $packagesList,
    '::setPackages() register the package array param'
  );
}
