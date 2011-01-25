<?php

require_once dirname(__FILE__).'/../../bootstrap/unit.php';

$t = new lime_test(4, new lime_output_color());


$t->comment('::getPackages()');
$form = new sfForm;
$form->setWidgets(array(
  'firstname' => new sfWidgetFormInputText(),
  'lastname'  => new sfWidgetFormInputText(),
));
$form->setOption('packages', array('formPackage1', 'formPackage2'));
$form->getWidget('firstname')->addOption('packages', 'foo');
$form->getWidget('lastname')->addOption('packages', array('bar', 'baz'));

$packagesList = assetPackagesFormExtension::getPackages($form);
$t->comment('For a simple form');
$t->ok(
  in_array('formPackage1', $packagesList)
  && in_array('formPackage2', $packagesList),
  '::getPackages() returns packages from form option'
);
$t->ok(
  in_array('foo', $packagesList)
  && in_array('bar', $packagesList)
  && in_array('baz', $packagesList),
  '::getPackages() returns the form widget packages'
);

$t->comment('For an embedded form');
$userForm = new sfForm;
$userForm->setWidgets(array(
  'firstname' => new sfWidgetFormInputText(),
  'lastname'  => new sfWidgetFormInputText(),
));
$userForm->getWidget('firstname')->addOption('packages', 'foo');
$phoneForm = new sfForm;
$phoneForm->setWidgets(array(
  'phonetype' => new sfWidgetFormInputText(),
  'phone'     => new sfWidgetFormInputText(),
));
$phoneForm->getWidget('phonetype')->addOption('packages', 'bar');
$userForm->embedForm('phone', $phoneForm);
$packagesList = assetPackagesFormExtension::getPackages($userForm);
$t->ok(
  in_array('bar', $packagesList),
  '::getPackages() returns packages from embedded forms widgets'
);
$superForm = new sfForm;
$superForm->embedForm('user', $userForm);
$packagesList = assetPackagesFormExtension::getPackages($userForm);
$t->ok(
  in_array('bar', $packagesList),
  '::getPackages() dig into all embedded forms'
);