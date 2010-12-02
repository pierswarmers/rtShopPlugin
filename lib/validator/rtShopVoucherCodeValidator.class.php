<?php
/*
 * This file is part of the rtShopPlugin package.
 * (c) 2006-2008 digital Wranglers <steercms@wranglers.com.au>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * rtShopVoucherCodeValidator
 *
 * @package    rtShopPlugin
 * @subpackage validators
 * @author     Piers Warmers <piers@wranglers.com.au>
 * @author     Konny Zurcher <konny@wranglers.com.au>
 */
class rtShopVoucherCodeValidator extends sfValidatorBase
{
  protected function configure($options = array(), $messages = array())
  {
    parent::configure($options, $messages);
  }

  protected function doClean($value)
  {
    if(!is_null($value) && $value !== '')
    {
      // Replace space with hypen
      //$clean = ereg_replace("[[:space:]]", "-", $value);   // deprecated in php 5.3
      $clean = preg_replace('/\s+/', '-', $value);
      // Removed special characters: Allowed are a-z, A-Z, 0-9,_ (under-dash),- (hypen)
      $clean = preg_replace('/[^\w\d_-]/si', '', $clean);
      // Uppercase string
      $clean = strtoupper($clean);
    }
    else
    {
      throw new sfValidatorError($this, 'code', array('code' => $values['code']));
    }

    return $clean;
  }
}