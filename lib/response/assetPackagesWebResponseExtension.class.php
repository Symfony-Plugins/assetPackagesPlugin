<?php

/**
 * assetPackagesWebResponseExtension class
 * 
 * Add AssetPackages methods to sfWebResponse
 * 
 * @package    assetPackagesPlugin
 * @subpackage response
 * @see        sfWebResponse
 * @author     Eric Roge <eric.roge@ui-studio.fr>
 * @version    SVN: $Id$
 */
class assetPackagesWebResponseExtension
{
  /**
   * Call assetPackagesPlugin methods for sfWebResponse
   * 
   * @param  sfEvent $event
   * @return boolean returns true if the sfWebResponse requested method is handled by assetPackagesPlugin
   */
  static public function listenToMethodNotFound(sfEvent $event)
  {
    $response = $event->getSubject();
    $requestedMethod = $event['method'];
    if (in_array($requestedMethod, get_class_methods(__CLASS__)))
    {
      $argument = $event['arguments'];
      if (is_array($argument) && isset($argument[0]))
      {
        $argument = $argument[0];
      }
      $result = self::$requestedMethod($response, $argument);
      $event->setReturnValue($result);
      return true;
    }

    return false;
  }


  /**
   * Load the asset-packages.yml packages list in sfWebResponse
   * 
   * @param  sfEvent $event
   * @return void
   */
  static public function loadPackagesList(sfEvent $event)
  {
    $context = $event->getSubject();
    $configPath = $context
                       ->getConfiguration()
                       ->getConfigCache()
                       ->checkConfig('config/asset-packages.yml')
                       ;
    $configuration = include $configPath;
    $packages = array();
    if (isset($configuration['packages']))
    {
      $packages = $configuration['packages'];
    }
    $context->getResponse()->setPackages($packages);
  }


  /**
   * Set the package list from param
   * 
   * @param  sfWebResponse $response
   * @param  array $packageList
   * @return void
   */
  static public function setPackages(sfWebResponse $response, $packageList)
  {
    $response->setSlot('_packages', $packageList);
  }


  /**
   * Returns the package name list
   * 
   * @param  sfWebResponse $response
   * @throws Exception if package list not loaded in the $response param
   * @return array $packageNameList
   */
  static public function getPackages(sfWebResponse $response)
  {
    $slots = $response->getSlots();
    foreach ($slots as $slotName => $slotValue)
    {
      if ('_packages' === $slotName)
      {
        $packages = $slotValue;
      }
    }
    if (!isset($packages))
    {
      throw new Exception(
        sprintf('The package list not loaded in the %s response',
          get_class($response)
        )
      );
    }

    return $packages;
  }


  /**
   * Add the package name to the response "_requestedPackages" slot
   * 
   * @param  sfWebResponse $response
   * @param  string        The package name to add
   * @return void
   */
  static public function addToRequestedPackages(sfWebResponse $response, $packageName)
  {
    $requestedPackages = array();
    $slots = $response->getSlots();
    if (isset($slots['_requestedPackages']))
    {
      $requestedPackages = $slots['_requestedPackages'];
    }
    if (!in_array($packageName, $requestedPackages))
    {
      $requestedPackages[] = $packageName;
    }
    $response->setSlot('_requestedPackages', $requestedPackages);
  }


  /**
   * Returns the package names of packages requested for the response
   * 
   * @param  sfWebResponse $response
   * @return array $packageNameList
   */
  static public function getRequestedPackages(sfWebResponse $response)
  {
    $slots = $response->getSlots();
    $requestedPackages = array();
    foreach ($slots as $slotName => $slotValue)
    {
      if ('_requestedPackages' === $slotName)
      {
        $requestedPackages = $slotValue;
      }
    }

    return $requestedPackages;
  }


  /**
   * Add package to sfWebResponse
   * 
   * @param  sfWebResponse $response
   * @param  array packages
   * @throws Exception if package is uknown
   * @return void
   */
  static public function addPackage(sfResponse $response, $arguments)
  {
    $packages = self::getPackages($response);
    $arguments = (array) $arguments;
    foreach($arguments as $requestedPackageName)
    {
      if (!is_string($requestedPackageName))
      {
        throw new sfException(
          sprintf(
            '%s->addPackage() accepts only an array of package names',
            get_class($response)
          )
        );
      }
      $requestedPackageName = trim($requestedPackageName);
      $requestedPackages = self::getRequestedPackages($response);
      // has the package already been requested ?
      if (in_array($requestedPackageName, $requestedPackages))
      {
        continue;
      }
      if (!isset($packages[$requestedPackageName]))
      {
        throw new sfException(
          sprintf('The requested %s package is unknown', $requestedPackageName)
        );
      }
      $package = array_merge(
        array(
          'require'     => array(),
          'stylesheets' => array(),
          'javascripts' => array(),
        ),
        $packages[$requestedPackageName]
      );

      $requiredPackages = (array) $package['require'];
      foreach ($requiredPackages as $requiredPackage)
      {
        if (!is_string($requiredPackage))
        {
          throw new Exception(
            sprintf(
              'The package "require" option accepts only an array of package names',
              get_class($response)
            )
          );
        }
        $requiredPackage = trim($requiredPackage);
        if ($requestedPackageName === $requiredPackage)
        {
          throw new sfException(
            sprintf(
              'The %s package cannot require itself',
              $requestedPackageName
            )
          );
        }
        self::addPackage($response, $requiredPackage);
        $requestedPackages = self::getRequestedPackages($response);
      }

      $stylesheets = $package['stylesheets'];
      if (is_array($stylesheets))
      {
        // An array_map could be more elegant here
        foreach ($stylesheets as $name => $stylesheetConfig)
        {
          self::addStylesheet($response, array($name => $stylesheetConfig));
        }
      }
      else
      {
        self::addStylesheet($response, $stylesheets);
      }

      $javascripts = $package['javascripts'];
      if (is_array($javascripts))
      {
        // An array_map could be more elegant here
        foreach ($javascripts as $name => $javascriptConfig)
        {
          self::addJavascript($response, array($name => $javascriptConfig));
        }
      }
      else
      {
        self::addJavascript($response, $javascripts);
      }
    }
    self::addToRequestedPackages($response, $requestedPackageName);
  }


  /**
   * Add stylesheet to the response
   *
   * @param sfWebResponse $response The response used for stylesheet addition
   * @param string|array stylesheet file name or config array
   * @return void
   */
  static public function addStylesheet(sfWebResponse $response, $stylesheet)
  {
    $position = '';
    $key = $stylesheet;
    $options = array();
    if (is_array($stylesheet))
    {
      $key = key($stylesheet);
      if (is_string($stylesheet[$key]))
      {
        $key = $stylesheet[$key];
      }
      else if (is_array($stylesheet[$key]))
      {
        $options = $stylesheet[$key];
        if (isset($options['position']))
        {
          $position = $options['position'];
          unset($options['position']);
        }
      }
    }
    $response->addStylesheet($key, $position, $options);
  }


  /**
   * Add javascript to the response
   *
   * @param sfWebResponse $response The response used for javascript addition
   * @param string|array javascript file name or config array
   * @return void
   */
  static public function addJavascript(sfWebResponse $response, $javascript)
  {
    $position = '';
    $key = $javascript;
    $options = array();
    if (is_array($javascript))
    {
      $key = key($javascript);
      if (is_string($javascript[$key]))
      {
        $key = $javascript[$key];
      }
      else if (is_array($javascript[$key]))
      {
        $options = $javascript[$key];
        if (isset($options['position']))
        {
          $position = $options['position'];
          unset($options['position']);
        }
      }
    }
    $response->addJavascript($key, $position, $options);
  }
}