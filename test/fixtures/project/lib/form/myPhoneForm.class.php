<?php

class myPhoneForm extends BaseForm
{
  public function configure()
  {
    $this->setWidgets(array(
      'phonetype' => new sfWidgetFormInputText(),
      'phone'     => new sfWidgetFormInputText(),
    ));
    $this->widgetSchema['phonetype']->addOption('packages', 'widget-phonetype');
  }
}
