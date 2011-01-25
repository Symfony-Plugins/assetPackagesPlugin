<?php

require_once dirname(__FILE__).'/../../bootstrap/unit.php';

$t = new lime_test(3, new lime_output_color());


$t->comment('->listenToMethodNotFound()');
$event = new sfEvent(new stdClass, 'foo-event', array('method' => 'unknownMethod'));
$t->ok(
  !assetPackagesWebResponseExtension::listenToMethodNotFound($event),
  '::listenToMethodNotFound() returns false if the requested method doesn’t belong to assetPackagesWebResponseExtension'
);
$t->todo('::listenToMethodNotFound() returns true if the requested method belongs to assetPackagesWebResponseExtension');
$t->todo('::listenToMethodNotFound() calls the requested method');
