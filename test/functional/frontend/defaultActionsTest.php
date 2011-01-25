<?php

include dirname(__FILE__).'/../../bootstrap/functional.php';

$b = new sfTestFunctional(createBrowserWithAssetPackagesPluginEvents(), $t = new lime_test(5));

$b->
  info('Test default page')->
  get('/')//->with('response')->debug()
  ;

$b->info('The default page calls the foo and the baz packages');

$response = $b->getResponse();
$stylesheets = $response->getStylesheets();
$javascripts = $response->getJavascripts();

$t->ok(
  in_array('stylebaz', array_keys($stylesheets))
  && in_array('scriptbaz', array_keys($javascripts)),
  '::addPackage() add package stylesheets and javascripts to response'
);
$b->info('The foo package require the bar package');
$t->ok(
  in_array('stylebar', array_keys($stylesheets))
  && in_array('scriptbar', array_keys($javascripts)),
  '::addPackage() supports autoloading of required packages'
);


$b->
  info('Test form packages')->
  get('/test-form')
  ;
$response = $b->getResponse();
$stylesheets = $response->getStylesheets();
$javascripts = $response->getJavascripts();
$t->ok(
  in_array('myuser', array_keys($stylesheets))
  && in_array('myuser', array_keys($javascripts)),
  '::getPackages() calls form packages'
);
$t->ok(
  in_array('stylefirstname', array_keys($stylesheets))
  && in_array('scriptfirstname', array_keys($javascripts)),
  '::getPackages() calls form widget packages'
);
$t->ok(
  in_array('phone', array_keys($stylesheets))
  && in_array('phone', array_keys($javascripts)),
  '::getPackages() calls widget packages from embedded forms'
);
