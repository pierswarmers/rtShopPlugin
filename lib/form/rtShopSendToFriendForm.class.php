<?php

/**
 * PluginrtShopAttribute form.
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 * @version    SVN: $Id: sfDoctrineFormPluginTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class rtShopSendToFriendForm extends sfForm
{
  public function configure() {
    parent::configure();

    $this->setWidgets(array(
      'product_id' => new sfWidgetFormInputHidden(),
      'email_address_sender' => new sfWidgetFormInput(),
      'email_address_recipient' => new sfWidgetFormInput(),
      'message' => new sfWidgetFormTextarea()
    ));

    $this->setValidators(array(
      'product_id' => new sfValidatorInteger(array('required' => true)),
      'email_address_sender' => new sfValidatorEmail(array('max_length' => 255, 'required' => true)),
      'email_address_recipient' => new sfValidatorEmail(array('max_length' => 255, 'required' => true)),
      'message' => new sfValidatorString(array('max_length' => 255, 'required' => false))
    ));

    $this->widgetSchema['email_address_sender']->setLabel('Your Email Address');
    $this->widgetSchema['email_address_recipient']->setLabel('Friends Email Address');

    $this->widgetSchema->setNameFormat('rt_send_to_friend[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);
  }
}