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

    $this->addOption('total', null);
    $this->addOption('code', null);

    $this->addMessage('total', 'Unknown order total.');
    $this->addMessage('code', (isset($messages['code'])) ? $messages['code'] : 'Voucher code is invalid. Please check.');
  }

  protected function doClean($value)
  {
    if(!is_null($value) && $value !== '')
    {
      if(is_numeric($this->getOption('total')) && $this->getOption('total') > 0)
      {
        $q = Doctrine_Query::create()
            ->from('rtShopPromotion p')
            ->andWhere('p.code = ?', $value)
            ->andWhere('p.type = ?', 'rtShopVoucher');
        $voucher = $q->fetchOne();

        if(!$voucher)
        {
          throw new sfValidatorError($this, 'code', array('code' => $this->getOption('code')));
        }
      }
      else
      {
        throw new sfValidatorError($this, 'total', array('total' => $this->getOption('total')));
      }
    }
    
    return $value;
  }
}