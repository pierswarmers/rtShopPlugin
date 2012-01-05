<?php
/*
 * This file is part of the rtShopPlugin package.
 * (c) 2006-2008 digital Wranglers <steercms@wranglers.com.au>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * rtShopVoucherValidator
 *
 * @package    rtShopPlugin
 * @subpackage validators
 * @author     Piers Warmers <piers@wranglers.com.au>
 * @author     Konny Zurcher <konny@wranglers.com.au>
 */
class rtShopVoucherValidator extends sfValidatorBase
{
  protected function configure($options = array(), $messages = array())
  {
    parent::configure($options, $messages);

    $this->addOption('rt_shop_order', null);
    $this->addMessage('code', (isset($messages['code'])) ? $messages['code'] : 'Voucher code is invalid. Please check.');
  }

  protected function doClean($values)
  {
    $rt_shop_order = $this->getOption('rt_shop_order');
    $total = $rt_shop_order->getGrandTotalPrice();
    if(!is_null($values) && $values !== '')
    {
      if($total > 0)
      {
        $q = Doctrine_Query::create()
            ->from('rtShopPromotion p')
            ->andWhere('p.code = ?', $values)
            ->andWhere('p.type = ?', 'rtShopVoucher');
        
        $voucher = $q->fetchOne();

        if(!$voucher)
        {
          throw new sfValidatorError($this, 'code', array('code' => $values['code']));
        }
      }
    }
    
    return $values;
  }
}