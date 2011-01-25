<?php

require_once dirname(__FILE__).'/../../bootstrap/unit.php';
require_once $_SERVER['SYMFONY'].'/../test/unit/sfContextMock.class.php';
require_once dirname(__FILE__).'/../../../lib/helper/AssetPackagesHelper.php';

class myRequest
{
}

class myController
{
}

$context = sfContext::getInstance(array(
  'request'    => 'myRequest',
  'response'   => 'sfWebResponse',
  'controller' => 'myController',
));
$context->getEventDispatcher()->connect('response.method_not_found', array('assetPackagesWebResponseExtension', 'listenToMethodNotFound'));

$t = new lime_test(3, new lime_output_color());


$t->comment('use_package()');
$response = $context->getResponse();
$packages = array(
  'foo' => array(
    'stylesheets' => 'bar',
    'javascripts' => 'baz',
  ),
);
assetPackagesWebResponseExtension::setPackages($response, $packages);
use_package('foo');
$t->ok(
  in_array('foo', assetPackagesWebResponseExtension::getRequestedPackages($response)),
  'use_package() add the package to the response'
);
$stylesheets = $response->getStylesheets();
$javascripts = $response->getJavascripts();
$t->ok(
  in_array('bar', array_keys($stylesheets))
  && in_array('baz', array_keys($javascripts)),
  'use_package() add package stylesheets and javascripts to response'
);


$t->comment('->use_packages_for_form()');
class FormMock56G extends sfForm
{
  function getPackages()
  {
    return array('foo');
  }
}
$form = new FormMock56G;
$response = $context->getResponse();
$packages = array(
  'foo' => array(
    'stylesheets' => 'bar',
    'javascripts' => 'baz',
  ),
);
assetPackagesWebResponseExtension::setPackages($response, $packages);
use_packages_for_form($form);
$t->ok(
  in_array('foo', assetPackagesWebResponseExtension::getRequestedPackages($response)),
  'use_packages_for_form() add the form packages to the response'
);