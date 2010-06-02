<?php

/**
 * PluginrtShopProduct form.
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 * @version    SVN: $Id: sfDoctrineFormPluginTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
abstract class PluginrtShopProductForm extends BasertShopProductForm
{
  public function setup()
  {
    parent::setup();

    unset($this['comment_status']);

    $this->widgetSchema->setHelp('is_featured', 'Make this page available to search engine robots');
  }
}
