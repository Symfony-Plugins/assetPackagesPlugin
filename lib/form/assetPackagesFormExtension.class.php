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
class assetPackagesFormExtension
{
  /**
   * Call assetPackagesPlugin methods for sfForm
   * 
   * @param  sfEvent $event
   * @return boolean returns true if the sfForm requested method is handled by assetPackagesPlugin
   */
  static public function listenToMethodNotFound(sfEvent $event)
  {
    $form = $event->getSubject();
    $requestedMethod = $event['method'];
    if (in_array($requestedMethod, get_class_methods(__CLASS__)))
    {
      $argument = $event['arguments'];
      if (is_array($argument) && isset($argument[0]))
      {
        $argument = $argument[0];
      }
      $result = self::$requestedMethod($form, $argument);
      $event->setReturnValue($result);
      return true;
    }

    return false;
  }


  /**
   * Add the package name to the form "packages" option
   * 
   * @param  sfForm $form
   * @param string|array package name
   * @return void
   */
  static public function addPackage(sfForm $form, $packageName)
  {
    $packageNames = (array) $packageName;
    $formPackages = (array) $form->getOption('packages', array());
    foreach ($packageNames as $packageName)
    {
      $packageName = trim($packageName);
      if (!in_array($packageName, $formPackages))
      {
        $formPackages[] = $packageName;
      }
    }
    $form->setOption('packages', $formPackages);
  }


  /**
   * Returns the packages name list
   * 
   * @param  sfForm $form
   * @return array $packageNameList
   */
  static public function getPackages(sfForm $form)
  {
    $packages = array();
    $formPackages = (array) $form->getOption('packages', array());
    foreach ($formPackages as $formPackage)
    {
      $formPackage = trim($formPackage);
      if ($formPackage && !in_array($formPackage, $packages))
      {
        $packages[] = $formPackage;
      }
    }
    $fieldPackages = self::getFieldPackages($form->getWidgetSchema());
    foreach ($fieldPackages as $fieldPackage)
    {
      $fieldPackage = trim($fieldPackage);
      if ($fieldPackage && !in_array($fieldPackage, $packages))
      {
        $packages[] = $fieldPackage;
      }
    }

    return $packages;
  }


  /**
   * Returns the packages from the sfWidgetFormSchema widgets
   * 
   * @param  sfWidgetFormSchema $formSchema
   * @return array $packageNameList
   */
  static public function getFieldPackages(sfWidgetFormSchema $formSchema)
  {
    $packages = array();
    $formFields = $formSchema->getFields();
    foreach ($formFields as $field)
    {
      $fieldPackages = array();
      if ($field instanceOf sfWidgetFormSchemaDecorator)
      {
        $fieldPackages = self::getFieldPackages($field->getWidget());
      }
      else if ($field->hasOption('packages'))
      {
        $fieldPackages = (array) $field->getOption('packages');
      }
      foreach ($fieldPackages as $fieldPackage)
      {
        $fieldPackage = trim($fieldPackage);
        if ($fieldPackage && !in_array($fieldPackage, $packages))
        {
          $packages[] = $fieldPackage;
        }
      }
    }

    return $packages;
  }
}