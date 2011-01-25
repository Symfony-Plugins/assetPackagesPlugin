<?php

if (!isset($_SERVER['SYMFONY']))
{
  $_SERVER['SYMFONY'] = dirname(__FILE__).'/../../../../lib/vendor/symfony/lib';
}

require_once $_SERVER['SYMFONY'].'/autoload/sfCoreAutoload.class.php';
sfCoreAutoload::register();

$configuration = new sfProjectConfiguration(dirname(__FILE__).'/../fixtures/project');
require_once $configuration->getSymfonyLibDir().'/vendor/lime/lime.php';

function assetPackagesPlugin_autoload_again($class)
{
  $autoload = sfSimpleAutoload::getInstance();
  $autoload->reload();
  return $autoload->autoload($class);
}
spl_autoload_register('assetPackagesPlugin_autoload_again');

if (file_exists($config = dirname(__FILE__).'/../../config/assetPackagesPluginConfiguration.class.php'))
{
  require_once $config;
  $plugin_configuration = new assetPackagesPluginConfiguration($configuration, dirname(__FILE__).'/../..', 'assetPackagesPlugin');
}
else
{
  $plugin_configuration = new sfPluginConfigurationGeneric($configuration, dirname(__FILE__).'/../..', 'assetPackagesPlugin');
}
