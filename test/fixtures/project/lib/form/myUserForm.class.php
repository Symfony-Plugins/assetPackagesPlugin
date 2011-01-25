<?php

class myUserForm extends BaseForm
{
  public function configure()
  {
    $this->addPackage('form-myuser');

    $this->setWidgets(array(
      'firstname' => new sfWidgetFormInputText(),
      'lastname'  => new sfWidgetFormInputText(),
    ));
    $this->widgetSchema['firstname']->addOption('packages', 'widget-firstname');

    $this->embedForm('phone', new myPhoneForm);
  }
}
