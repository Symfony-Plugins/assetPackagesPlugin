<?php

require_once dirname(__FILE__).'/../../bootstrap/unit.php';
$dispatcher = new sfEventDispatcher();

$t = new lime_test(3, new lime_output_color());


$t->comment('::addStylesheet()');
$response = new sfWebResponse($dispatcher);
assetPackagesWebResponseExtension::addStylesheet($response, 'foo');
assetPackagesWebResponseExtension::addStylesheet(
  $response,
  array(
    'bar' => array(
      'position' => 'first',
      'media'    => 'print',
    ),
  )
);
$stylesheets = $response->getStylesheets();
$t->ok(
  in_array('foo', array_keys($stylesheets)),
  '::addStylesheet() add stylesheet to response'
);
$stylesheetNames = array_keys($stylesheets);
$t->is(
  $stylesheetNames[0],
  'bar',
  '::addStylesheet() uses the "position" option'
);
$t->ok(
  isset($stylesheets['bar']['media']) && 'print' === $stylesheets['bar']['media'],
  '::addStylesheet() loads stylesheet options'
);
