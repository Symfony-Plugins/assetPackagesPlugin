<?php

class sfWebDebugPanelAssetPackages extends sfWebDebugPanel
{
  /**
   * Gets the text for the link.
   *
   * @return string The link text
   */
  public function getTitle()
  {
    return '<img src="'.$this->webDebug->getOption('image_root_path').'/config.png" alt="assetPackagesPlugin informations" /> Asset packages';
  }


  /**
   * Gets the title of the panel.
   *
   * @return string The panel title
   */
  public function getPanelTitle()
  {
    return 'assetPackages Plugin';
  }


  /**
   * Add the sfWebDebugPanelAssetPackages to the debug bar
   */
  static public function listenToLoadPanelEvent(sfEvent $event)
  {
    $event->getSubject()->setPanel('assetPackages', new sfWebDebugPanelAssetPackages($event->getSubject()));
  }


  /**
   * Gets the panel HTML content.
   *
   * @return string The panel HTML content
   */
  public function getPanelContent()
  {
    $response = sfContext::getInstance()->getResponse();
    $sfYamlDumper = new sfYamlDumper;
    $html = array();
    $packages = $response->getPackages();
    $requestedPackages = $response->getRequestedPackages();
    $html[] = '<h2>Requested packages</h2>';
    if (!count($requestedPackages))
    {
      $html[] = '<p>No requested package</p>';
    }
    else
    {
      $html[] = '<table class="sfWebDebugLogs">';
      $html[] = '<thead>';
      $html[] = '<tr>';
      $html[] = '<td></td>';
      $html[] = '<th>Require</th>';
      $html[] = '<th>Stylesheets</th>';
      $html[] = '<th>Javascripts</th>';
      $html[] = '</tr>';
      $html[] = '</thead>';
      $html[] = '<tbody>';
      foreach ($requestedPackages as $requestedPackageName)
      {
        $package = array_merge(
          array(
            'require'     => array(),
            'stylesheets' => array(),
            'javascripts' => array(),
          ),
          $packages[$requestedPackageName]
        );
        $html[] = '<tr>';
        $html[] = sprintf('<th>%s</th>', $requestedPackageName);
        $require = $package['require'];
        $requireHtml = '';
        if ($require)
        {
          $requireHtml = $require;
          if (is_array($require))
          {
            $requireHtml = implode('<br>', $require);
          }
        }
        $html[] = sprintf('<td>%s</td>', $requireHtml);
        $stylesheets = $package['stylesheets'];
        $stylesheetsHtml = '';
        if ($stylesheets)
        {
          $stylesheetsHtml = $stylesheets;
          if (is_array($stylesheets))
          {
            $stylesheetsHtml = array();
            foreach ($stylesheets as $key => $value)
            {
              $stylesheetsHtml[] = sprintf(
                '%s: %s',
                $key,
                $sfYamlDumper->dump($value)
              );
            }
            $stylesheetsHtml = implode('<br>', $stylesheetsHtml);
          }
        }
        $html[] = sprintf('<td>%s</td>', $stylesheetsHtml);
        
        
        $javascripts = $package['javascripts'];
        $javascriptsHtml = '';
        if ($javascripts)
        {
          $javascriptsHtml = $javascripts;
          if (is_array($javascripts))
          {
            $javascriptsHtml = array();
            foreach ($javascripts as $key => $value)
            {
              $javascriptsHtml[] = sprintf(
                '%s: %s',
                $key,
                $sfYamlDumper->dump($value)
              );
            }
            $javascriptsHtml = implode('<br>', $javascriptsHtml);
          }
        }
        $html[] = sprintf('<td>%s</td>', $javascriptsHtml);

        $html[] = '</tr>';
      }
      $html[] = '</tbody>';
      $html[] = '</table>';
    }
    $html[] = '<h2>Full package list</h2>';
    if (!count($packages))
    {
      $html[] = '<p>No package declaration found</p>';
    }
    else
    {
      $packagesHtml = print_r($packages, true);
      if (count($requestedPackages))
      {
        $boldPackages = array();
        foreach ($requestedPackages as $requestedPackageName)
        {
          $boldPackages[sprintf('[%s]', $requestedPackageName)] = sprintf('[<strong style="">%s</strong>]', $requestedPackageName);
        }
        $packagesHtml = strtr($packagesHtml, $boldPackages);
      }
      $packagesHtml = str_replace('    ', ' ', $packagesHtml);
      $html[] = sprintf('<pre>%s</pre>', $sfYamlDumper->dump($packages, 3));
    }

    return join("\n", $html);
  }
}