<?php

class defaultActions extends sfActions
{
  public function executeIndex(sfWebRequest $request)
  {
  }


  public function executeForm(sfWebRequest $request)
  {
    $this->form = new myUserForm;
  }
}
