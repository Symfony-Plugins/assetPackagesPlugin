<?php


// If you modify this listener list, you shall update createBrowserWithAssetPackagesPluginEvents()
// in /assetPackagesPlugin/test/bootstrap/functional.php
$this->dispatcher->connect('context.load_factories', array('assetPackagesWebResponseExtension', 'loadPackagesList'));
$this->dispatcher->connect('response.method_not_found', array('assetPackagesWebResponseExtension', 'listenToMethodNotFound'));
$this->dispatcher->connect('form.method_not_found', array('assetPackagesFormExtension', 'listenToMethodNotFound'));
if (sfConfig::get('sf_web_debug'))
{
  $this->dispatcher->connect('debug.web.load_panels', array('sfWebDebugPanelAssetPackages', 'listenToLoadPanelEvent'));
}
