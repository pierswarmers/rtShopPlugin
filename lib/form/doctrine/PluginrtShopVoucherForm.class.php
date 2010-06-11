<?php

/**
 * PluginrtShopVoucher form.
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 * @version    SVN: $Id: sfDoctrineFormPluginTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
abstract class PluginrtShopVoucherForm extends BasertShopVoucherForm
{
  public function setup()
  {
    parent::setup();

    unset(
      $this['type']
    );

    $this->setWidget('mode', new sfWidgetFormChoice(array('choices' => array('Single' => 'Single user voucher', 'Group' => 'Group / multi-user voucher'))));

    $this->widgetSchema->setHelp('count', 'How many times can this voucher be used. The "count" will automtically reduce per usage.');

  }
}
