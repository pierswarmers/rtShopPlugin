<?php

/*
 * This file is part of the reditype package.
 *
 * (c) 2009-2010 digital Wranglers <info@wranglers.com.au>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * BasertShopVoucherAdminComponents
 *
 * @package    rtShopPlugin
 * @subpackage modules
 * @author     Piers Warmers <piers@wranglers.com.au>
 * @author     Konny Zurcher <konny@wranglers.com.au>
 */
class BasertShopVoucherAdminComponents extends sfComponents
{
  /**
   * Create redeem voucher link, shown in text input field
   *
   * @example: http://example.com/order/voucher/redeem?code=123456&redirect=/category/3/shirts
   *
   * @param sfWebRequest $request
   */
  public function executeRedeemLink(sfWebRequest $request)
  {
    $this->rt_shop_voucher = Doctrine::getTable('rtShopVoucher')->findOneById($request->getParameter('id'));
    if(!$this->rt_shop_voucher)
    {
      $action->setLayout(false);
    }
  }
}