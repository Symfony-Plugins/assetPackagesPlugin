<?php

/**
 * Adds a package to the response object.
 *
 * @see    assetPackagesWebResponseExtension::addPackage()
 * @param  string $packageName
 * @return void
 */
function use_package($packageName)
{
  sfContext::getInstance()->getResponse()->addPackage($packageName);
}

/**
 * Adds packages from the supplied form to the response object.
 *
 * @param sfForm $form
 * @return void
 */
function use_packages_for_form(sfForm $form)
{
  foreach ($form->getPackages() as $packageName)
  {
    use_package($packageName);
  }
}