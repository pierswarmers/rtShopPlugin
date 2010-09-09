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
      $this['type'],
      $this['batch_reference'],
      $this['created_at'],
      $this['updated_at']
    );

    $this->setWidget('code', new sfWidgetFormInputText());
    $this->setWidget('mode', new sfWidgetFormChoice(array('choices' => array('Single' => 'Single user voucher', 'Group' => 'Group / multi-user voucher'))));

    $this->validatorSchema['code'] = new sfValidatorAnd(
      array(
        new sfValidatorString(array('min_length' => 1,'max_length' => 12, 'required' => true),array('required' => 'Please provide a valid voucher code.')),
        new rtShopVoucherCodeValidator(array('required' => true),array('required' => 'Please provide a valid voucher code.'))
      )
    );

    $this->validatorSchema->setPostValidator(
      new sfValidatorDoctrineUnique(array('model'=>'rtShopVoucher', 'column'=> array('code')),array('invalid' => 'Voucher code already used.'))
    );

    $this->widgetSchema->setLabel('code',"Voucher Code");

    $this->widgetSchema->setHelp('count', 'How many times can this voucher be used. The "count" will automtically reduce per usage.');

  }
}
