<?php

require_once dirname(__FILE__).'/../../bootstrap/unit.php';

$t = new lime_test(1, new lime_output_color());


$t->comment('::getFieldPackages()');
$widgetFormSchema = new sfWidgetFormSchema(array(
  'firstname' => new sfWidgetFormInputText(),
  'lastname'  => new sfWidgetFormInputText(),
));
$widgetFormSchema['firstname']->addOption('packages', 'foo');
$packagesList = assetPackagesFormExtension::getFieldPackages($widgetFormSchema);
$t->ok(
  in_array('foo', $packagesList),
  '::getFieldPackages() returns the sfWidgetFormSchema widget packages'
);