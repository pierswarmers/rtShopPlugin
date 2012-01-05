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
    $this->addMessage('expiry', 'Please provide both month and year values.');
  }

  protected function doClean($values)
  {
    $errorSchema = new sfValidatorErrorSchema($this);

    // Both month and year must be set.
    if($values['cc_expire']['month'] === '' || $values['cc_expire']['year'] === '')
    {
      $errorSchema->addError(new sfValidatorError($this, 'expiry'), 'cc_expire');
    }

    if (count($errorSchema))
    {
      throw new sfValidatorErrorSchema($this, $errorSchema);
    }

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