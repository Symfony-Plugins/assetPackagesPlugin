<?php

require_once dirname(__FILE__).'/../../bootstrap/unit.php';
$dispatcher = new sfEventDispatcher();

$t = new lime_test(2, new lime_output_color());


$t->comment('::setPackages()');
$response = new sfWebResponse($dispatcher);
try
{
  assetPackagesWebResponseExtension::getPackages($response);
  $t->fail('::getPackages() throws an exception when response has no packages');
}
catch (Exception $e)
{
  $t->pass('::getPackages() throws an exception when response has no packages');
}
$packagesList = array('foo');
assetPackagesWebResponseExtension::setPackages($response, $packagesList);
$packages = assetPackagesWebResponseExtension::getPackages($response);
$t->is_deeply(
  $packagesList,
  $packages,
  '::getPackages() returns the reponse packages'
);