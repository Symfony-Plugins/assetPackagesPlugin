<?php

require_once dirname(__FILE__).'/../../bootstrap/unit.php';

$t = new lime_test(11, new lime_output_color());


$t->comment('::addPackage()');
$response = new sfWebResponse(new sfEventDispatcher);
$packages = array(
  'foo' => array(
    'stylesheets' => 'bar',
    'javascripts' => 'baz',
  ),
);
assetPackagesWebResponseExtension::setPackages($response, $packages);
try
{
  assetPackagesWebResponseExtension::addPackage($response, 'jhqgsjdsgq');
  $t->fail('::addPackage() throws an exception when package is unknown');
}
catch (Exception $e)
{
  $t->pass('::addPackage() throws an exception when package is unknown');
}
assetPackagesWebResponseExtension::addPackage($response, 'foo');
$stylesheets = $response->getStylesheets();
$javascripts = $response->getJavascripts();
$t->ok(
  in_array('bar', array_keys($stylesheets))
  && in_array('baz', array_keys($javascripts)),
  '::addPackage() add package stylesheets and javascripts to response'
);
$t->ok(
  in_array('foo', assetPackagesWebResponseExtension::getRequestedPackages($response)),
  '::addPackage() add the package to the response RequestedPackages list'
);
$response = new sfWebResponse(new sfEventDispatcher);
$packages = array(
  'foo' => array(
    'stylesheets' => array(
      'style1' => array(),
      'style2' => array(
        'position' => 'first',
        'media'    => 'print'
      ),
    ),
    'javascripts' => array(
      'script1' => array(),
      'script2' => array(
        'position'  => 'first',
        'condition' => 'IE',
      ),
    ),
  ),
);
assetPackagesWebResponseExtension::setPackages($response, $packages);
assetPackagesWebResponseExtension::addPackage($response, 'foo');
$stylesheets = $response->getStylesheets();
$t->ok(
  in_array('style1', array_keys($stylesheets))
  && in_array('style2', array_keys($stylesheets)),
  '::addPackage() is able to add several stylesheets from a package'
);
$javascripts = $response->getJavascripts();
$t->ok(
  in_array('script1', array_keys($javascripts))
  && in_array('script2', array_keys($javascripts)),
  '::addPackage() is able to add several javascripts from a package'
);

$stylesheetNames = array_keys($stylesheets);
$javascriptNames = array_keys($javascripts);
$t->is_deeply(
  array($stylesheetNames[0], $javascriptNames[0]),
  array('style2', 'script2'),
  '::addPackage() uses the "position" option for stylesheets and javascripts'
);
$t->ok(
  isset($stylesheets['style2']['media']) && 'print' === $stylesheets['style2']['media']
  && isset($javascripts['script2']['condition']) && 'IE' === $javascripts['script2']['condition'],
  '::addPackage() loads stylesheets and javascripts with their options'
);


$t->comment('::addPackage() - Require');
$response = new sfWebResponse(new sfEventDispatcher);
$packages = array(
  'foo' => array(
    'require'     => 'foo',
    'stylesheets' => 'stylesheetfoo',
    'javascripts' => 'javascriptfoo',
  ),
);
assetPackagesWebResponseExtension::setPackages($response, $packages);
try
{
  assetPackagesWebResponseExtension::addPackage($response, 'foo');
  $t->fail('->addPackage() throws an exception a package requires itself');
}
catch (Exception $e)
{
  $t->pass('->addPackage() throws an exception a package requires itself');
}
$response = new sfWebResponse(new sfEventDispatcher);
$packages = array(
  'foo' => array(
    'require'     => 'bar',
    'stylesheets' => 'stylesheetfoo',
    'javascripts' => 'javascriptfoo',
  ),
  'bar' => array(
    'stylesheets' => 'stylesheetbar',
    'javascripts' => 'javascriptbar',
  )
);
assetPackagesWebResponseExtension::setPackages($response, $packages);
assetPackagesWebResponseExtension::addPackage($response, 'foo');
$t->ok(
  in_array('bar', assetPackagesWebResponseExtension::getRequestedPackages($response)),
  '::addPackage() call the required package automatically'
);
$stylesheets = $response->getStylesheets();
$javascripts = $response->getJavascripts();
$stylesheetNames = array_keys($stylesheets);
$javascriptNames = array_keys($javascripts);
$t->is_deeply(
  array($stylesheetNames[0], $javascriptNames[0]),
  array('stylesheetbar', 'javascriptbar'),
  '::addPackage() loads required package before the requested one'
);
$response = new sfWebResponse(new sfEventDispatcher);
$packages = array(
  'foo' => array(
    'require'     => array('bar', 'baz'),
    'stylesheets' => 'stylesheetfoo',
    'javascripts' => 'javascriptfoo',
  ),
  'bar' => array(
    'stylesheets' => 'stylesheetbar',
    'javascripts' => 'javascriptbar',
  ),
  'baz' => array(
    'stylesheets' => 'stylesheetbaz',
    'javascripts' => 'javascriptbaz',
  ),
);
assetPackagesWebResponseExtension::setPackages($response, $packages);
assetPackagesWebResponseExtension::addPackage($response, 'foo');
$t->ok(
  in_array('bar', assetPackagesWebResponseExtension::getRequestedPackages($response))
  && in_array('baz', assetPackagesWebResponseExtension::getRequestedPackages($response)),
  '::addPackage() the "require" option accepts an array of package names'
);