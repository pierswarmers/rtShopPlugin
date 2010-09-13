<?php

/**
 * PluginrtShopOrder form.
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 * @version    SVN: $Id: sfDoctrineFormPluginTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
abstract class PluginrtShopOrderForm extends BasertShopOrderForm
{
  public function setup()
  {
    parent::setup();

    $status = sfConfig::get('app_rt_shop_order_status_types',array('pending', 'paid', 'picking', 'dispatch', 'sent'));

    $this->widgetSchema['status'] = new sfWidgetFormSelect(array('choices' => $status));

    $this->validatorSchema['status'] = new sfValidatorChoice(array('choices' => array_keys($status), 'required' => true),array('required' =>'Please select a value'));
    $this->validatorSchema['email'] = new sfValidatorEmail(array('required' => true), array(
     'required'   => 'Please provide an email address',
     'invalid'    => 'Please provide a valid email address (me@example.com)'
    ));

    // Help text for fields
    $this->widgetSchema->setHelp('notes_user','Order notes (visible to customer)');
    $this->widgetSchema->setHelp('notes_admin','Administrator order notes (not visible to customer)');

    $this->widgetSchema->setNameFormat('rt_shop_order[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);
  }
}