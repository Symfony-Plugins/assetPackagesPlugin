<?php

require_once dirname(__FILE__).'/../../bootstrap/unit.php';
$dispatcher = new sfEventDispatcher();

$t = new lime_test(3, new lime_output_color());


$t->comment('::addJavascript()');
$response = new sfWebResponse($dispatcher);
assetPackagesWebResponseExtension::addJavascript($response, 'foo');
assetPackagesWebResponseExtension::addJavascript(
  $response,
  array(
    'bar' => array(
      'position'  => 'first',
      'condition' => 'IE',
    ),
  )
);
$javascripts = $response->getJavascripts();
$t->ok(
  in_array('foo', array_keys($javascripts)),
  '::addJavascript() add javascript to response'
);
$javascriptNames = array_keys($javascripts);
$t->is(
  $javascriptNames[0],
  'bar',
  '::addJavascript() uses the "position" option'
);
$t->ok(
  isset($javascripts['bar']['condition']) && 'IE' === $javascripts['bar']['condition'],
  '::addJavascript() loads javascript options'
);
