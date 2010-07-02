<?php
/*
 * This file is part of the rtShopPlugin package.
 * (c) 2006-2008 digital Wranglers <steercms@wranglers.com.au>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * rtShopCreditCardValidator
 *
 * @package    rtShopPlugin
 * @subpackage validators
 * @author     Piers Warmers <piers@wranglers.com.au>
 * @author     Konny Zurcher <konny@wranglers.com.au>
 */
class rtShopCreditCardValidator extends sfValidatorBase
{
  protected function configure($options = array(), $messages = array())
  {
    $this->setMessage('invalid', 'Invalid credit card number.');
    $this->addMessage('invalid_ccv', 'Invalid card validation code.');
    $this->addMessage('expired', 'Credit card expired.');
  }

  protected function doClean($values)
  {
    $errorSchema = new sfValidatorErrorSchema($this);

//    $cleaned_number = preg_replace('/\D/', '', $values['cc_number']);
//    $number_length = strlen($cleaned_number);
//
//    // Check credit card number
//    if (!is_null($values['cc_number']) && (!is_numeric($cleaned_number) || !($number_length >= 13 && $number_length <= 16) || !($this->checkLuhn($cleaned_number))))
//    {
//      $errorSchema->addError(new sfValidatorError($this, $this->getMessage('invalid')), 'cc_number');
//    }
//
//    //Card validation code => MasterCard, Visa, Diners Club, Discover, JCB = 3
//    //                        American Express  = 4
//    if (!is_null($values['cc_verification']) && !is_numeric($values['cc_verification']))
//    {
//      $errorSchema->addError(new sfValidatorError($this, $this->getMessage('invalid_ccv')), 'cc_verification');
//    }
//
//    // Expiry date
//    if($values['cc_expire']['month'] !== '' && $values['cc_expire']['year'] !== '')
//    {
//      $date_now = date("Y-m-d H:i:s", mktime(0,0,0,date("m"),date("d"),date("Y")));
//      $date_expire = date("Y-m-d H:i:s", mktime(0, 0, 0, $values['cc_expire']['month'], cal_days_in_month(CAL_GREGORIAN, $values['cc_expire']['month'], $values['cc_expire']['year']), $values['cc_expire']['year']));
//      if ($date_expire <= $date_now)
//      {
//        $errorSchema->addError(new sfValidatorError($this, $this->getMessage('expired')), 'cc_expire');
//      }
//    }
//    else
//    {
//      $errorSchema->addError(new sfValidatorError($this, 'required'), 'cc_expire');
//    }
//
//    if (count($errorSchema))
//    {
//      throw new sfValidatorErrorSchema($this, $errorSchema);
//    }

    return $values;
  }

  /**
   * Apply Luhn check
   *
   * @param string $number Cleaned credit card number
   * @return boolean True if check is successful
   */
  private function checkLuhn($number)
  {
    $number_length = strlen($number);
    $parity=$number_length % 2;

    $total=0;
    for ($i=0; $i<$number_length; $i++) {
      $digit=$number[$i];
      if ($i % 2 == $parity) {
        $digit*=2;
        if ($digit > 9) {
          $digit-=9;
        }
      }
      $total+=$digit;
    }

    return ($total % 10 == 0) ? true : false;
  }
}