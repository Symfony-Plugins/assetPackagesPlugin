<?php

/**
 * assetPackagesPlugin configuration.
 * 
 * @package     assetPackagesPlugin
 * @subpackage  config
 * @author      Your name here
 * @version     SVN: $Id$
 */
class assetPackagesPluginConfiguration extends sfPluginConfiguration
{
  const VERSION = '1.0.0-DEV';

  /**
   * @see sfPluginConfiguration
   */
  public function initialize()
  {
    // In symfony good practices, plugin event listener registering should be done
    // in config/config.php
    require_once dirname(__FILE__).'/config.php';
  }
}
