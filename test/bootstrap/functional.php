<?php

if (!isset($app))
{
  $app = 'frontend';
}
if (!isset($_SERVER['SYMFONY']))
{
  $_SERVER['SYMFONY'] = dirname(__FILE__).'/../../../../lib/vendor/symfony/lib';
}

require_once $_SERVER['SYMFONY'].'/autoload/sfCoreAutoload.class.php';
sfCoreAutoload::register();

function assetPackagesPlugin_cleanup()
{
  sfToolkit::clearDirectory(dirname(__FILE__).'/../fixtures/project/cache');
  sfToolkit::clearDirectory(dirname(__FILE__).'/../fixtures/project/log');
}
assetPackagesPlugin_cleanup();
register_shutdown_function('assetPackagesPlugin_cleanup');

require_once dirname(__FILE__).'/../fixtures/project/config/ProjectConfiguration.class.php';
$configuration = ProjectConfiguration::getApplicationConfiguration($app, 'test', isset($debug) ? $debug : true);
sfContext::createInstance($configuration);

/**
 * Create a sfBrowser instante and add plugin listeners
 */
function createBrowserWithAssetPackagesPluginEvents()
{
  $b = new sfBrowser;
  $b->addListener('context.load_factories', array('assetPackagesWebResponseExtension', 'loadPackagesList'));
  $b->addListener('response.method_not_found', array('assetPackagesWebResponseExtension', 'listenToMethodNotFound'));
  $b->addListener('form.method_not_found', array('assetPackagesFormExtension', 'listenToMethodNotFound'));
  $b->addListener('debug.web.load_panels', array('sfWebDebugPanelAssetPackages', 'listenToLoadPanelEvent'));

  return $b;
}