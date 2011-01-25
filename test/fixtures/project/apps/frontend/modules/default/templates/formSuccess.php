<?php use_stylesheets_for_form($form) ?>
<?php use_javascripts_for_form($form) ?>
<?php use_helper('AssetPackages') ?>
<?php use_packages_for_form($form) ?>

<form action="#" method="post">
  <table>
    <?php echo $form ?>
  </table>
  <p><input type="submit" value="Submit" /></p>
</form>