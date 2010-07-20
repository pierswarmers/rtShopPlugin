<?php
/*
 * This file is part of the reditype package.
 * (c) 2009-2010 Piers Warmers <piers@wranglers.com.au>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * rtShopOrderEmailFormclass
 *
 * @package    rtShopPlugin
 * @subpackage forms
 * @author     Piers Warmers <piers@wranglers.com.au>
 * @author     Konny Zurcher <konny@wranglers.com.au>
 */
class rtShopOrderEmailForm extends BasertShopOrderForm
{
  public function setup()
  {
    parent::setup();

    $this->enableCSRFProtection();

    $this->widgetSchema->setFormFormatterName('table');

    $this->useFields(array('id','email_address'));
//
//    $billing_address = new rtAddress;
//    $billing_address->setType('billing');
//    $billing_address->setModel('rtShopOrder');
//
//    $shipping_address = new rtAddress;
//    $shipping_address->setType('shipping');
//    $shipping_address->setModel('rtShopOrder');
//
//    if(!$this->isNew())
//    {
//      $tmp_address_1 = Doctrine::getTable('rtAddress')->getAddressForObjectAndType($this->getObject(), 'shipping');
//      if($tmp_address_1)
//      {
//        $shipping_address = $tmp_address_1;
//      }
//      $tmp_address_2 = Doctrine::getTable('rtAddress')->getAddressForObjectAndType($this->getObject(), 'billing');
//      if($tmp_address_2)
//      {
//        $billing_address = $tmp_address_2;
//      }
//      $billing_address->setModelId($this->object->getId());
//      $shipping_address->setModelId($this->object->getId());
//    }
//
//    $this->embedForm('billing_address', new rtAddressForm($billing_address, array('object' => $this->object, 'is_optional' => false, 'use_names' => true)));
//    $this->embedForm('shipping_address', new rtAddressForm($shipping_address, array('object' => $this->object, 'is_optional' => true, 'use_names' => true)));
//
//    $order_id = sfContext::getInstance()->getUser()->getAttribute('rt_shop_frontend_order_id');
//    $order = Doctrine::getTable('rtShopOrder')->find($order_id);

    $this->widgetSchema->setLabel('email_address',"Email Address");
    $this->widgetSchema['email_address'] = new sfWidgetFormInput(array(), array('class'=>'text'));
    $this->setValidator('email_address', new sfValidatorEmail(array('max_length' => 255, 'required' => true)));

    $this->widgetSchema->setNameFormat('rt_shop_order[%s]');
    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);
  }


  public function saveEmbeddedForms($con = null, $forms = null)
  {
//    if (null === $forms)
//    {
//      $forms = $this->embeddedForms;
//
//      foreach(array('shipping_address') as $name)
//      {
//        $address = $this->getValue($name);
//
//        if (!isset($address['address_1']))
//        {
//          unset($forms[$name]);
//        }
//      }
//    }

    return parent::saveEmbeddedForms($con, $forms);
  }
//
//  public function getModelName()
//  {
//    return 'rtShopOrder';
//  }
}