<?php

/*
 * This file is part of the rtShopPlugin package.
 *
 * (c) 2006-2008 digital Wranglers <steercms@wranglers.com.au>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * rtShopVoucherManager
 *
 * @package    rtShopPlugin
 * @subpackage manager
 * @author     Piers Warmers <piers@wranglers.com.au>
 * @author     Konny Zurcher <konny@wranglers.com.au>
 */
class rtShopVoucherManager
{
  private $_session_token = 'rt_shop_frontend_gift_voucher';
  private $_user;
  private $_rt_shop_voucher;

  /**
   * Constructor function
   *
   * @param array $options
   */
  public function __construct($options = array())
  {
    // Set the user
    $this->_user = sfContext::getInstance()->getUser();
  }

  /**
   * Return array with voucher data
   *
   * @return array
   */
  public function getSessionVoucherArray()
  {
    return unserialize($this->getSessionVoucher());
  }

  /**
   * Return array with voucher data
   *
   * @return void
   */
  public function setSessionVoucherArray(array $voucher)
  {
    // Clean message text, remove html tags
    $voucher['message'] = strip_tags(html_entity_decode($voucher['message']));

    // Add gift voucher title
    $voucher['title'] = 'Gift Voucher';

    // Write date to session
    $this->_user->setAttribute($this->_session_token, serialize($voucher));
  }

  /**
   * Saves a voucher to the database using an rtShopPromotionVoucher
   *
   * @param  string $comment Voucher comment
   * @param  string $title
   * @return void
   */
  public function persistSessionVoucher($comment = '', $title = null)
  {
    if(!$this->hasSessionVoucher())
    {
      return false;
    }

    $this->_rt_shop_voucher = $this->mergeData(new rtShopVoucher(), $comment, $title);
    $this->_rt_shop_voucher->save();

    return $this->_rt_shop_voucher;
  }

  /**
   * @return rtShopVoucher
   */
  public function getPersistedVoucher()
  {
    return $this->_rt_shop_voucher;
  }

  /**
   * Add data to rtShopVoucher object
   *
   * @param  rtShopVoucher $rt_shop_voucher
   * @param  string $comment Voucher comment
   * @param  string $title
   * @return rtShopVoucher
   */
  private function mergeData(rtShopVoucher $rt_shop_voucher, $comment = '', $title = null)
  {  
    $voucher = $this->getSessionVoucherArray();

    $title = is_null($title) ? 'Gift Voucher' : $title;

    $rt_shop_voucher->setTitle($title);
    $rt_shop_voucher->setDateTo(date('Y-m-d H:i:s',strtotime(sprintf("+%s days",sfConfig::get('app_rt_shop_gift_voucher_valid_for',365)))));
    $rt_shop_voucher->setReductionType('dollarOff');
    $rt_shop_voucher->setReductionValue($voucher['reduction_value']);
    $rt_shop_voucher->setMode('Single');
    $rt_shop_voucher->setCount(1);
    $rt_shop_voucher->setStackable(false);
    $rt_shop_voucher->setComment($this->getSessionVoucherAsString().chr(13).chr(13).$comment);
    
    return $rt_shop_voucher;
  }

  /**
   * Return voucher data as formatted string
   *
   * @return string
   */
  public function getSessionVoucherAsString()
  {
    $voucher = $this->getSessionVoucherArray();

    $first_name    = $voucher['first_name'];
    $last_name     = $voucher['last_name'];
    $email_address = $voucher['email_address'];
    $message       = $voucher['message'];

    $string = <<<EOF
Created for:
$first_name $last_name <$email_address>

Message:
$message
EOF;

    return $string;
  }

  /**
   * @return rtShopVoucher
   */
  protected function getSessionVoucher()
  {
    return $this->hasSessionVoucher() ? $this->_user->getAttribute($this->_session_token) : null;
  }

  /**
   * Return is vouche data is available
   * 
   * @return boolean True if voucher data available
   */
  public function hasSessionVoucher()
  {
    return $this->_user->hasAttribute($this->_session_token);
  }

  /**
   * Reset voucher data in session
   */
  public function resetSessionVoucher()
  {
    $this->_user->getAttributeHolder()->remove($this->_session_token);
  }

  /**
   * Convenience method for generic notification method.
   * 
   * @param rtShopVoucher $rt_shop_order
   */
  public function notifyRecipientFromRtShopOrder(rtShopOrder $rt_shop_order)
  {
    $this->notifyRecipient($rt_shop_order->getBillingAddress()->getFirstName(), $rt_shop_order->getBillingAddress()->getLastName(), $rt_shop_order->getEmailAddress());
  }

  /**
   * Notify recipient about gift voucher
   *
   * @param string $sender_fname Sender first name
   * @param string $sender_lname Sender last name
   * @param string $sender_email Sender email address
   * @return void
   */
  public function notifyRecipient($sender_fname, $sender_lname, $sender_email)
  {
    sfContext::getInstance()->getConfiguration()->loadHelpers(array('Number','Partial'));
   
    $session_voucher_array = $this->getSessionVoucherArray();

    // If rtShopVoucher or session data does not exist, exit
    if(get_class($this->_rt_shop_voucher) !== 'rtShopVoucher' && !is_array($session_voucher_array))
    {
      return;
    }

    // Variables
    $vars = array();
    $vars['voucher_session_array'] = $session_voucher_array;    // Voucher session data
    $vars['rt_shop_voucher']       = $this->_rt_shop_voucher;   // rtShopVoucher
    $vars['sender_fname']          = $sender_fname;
    $vars['sender_lname']          = $sender_lname;
    $vars['sender_email']          = $sender_email;

    // Templates
    $message_html  = get_partial('rtShopVoucher/email_gift_voucher_html', $vars);
    $message_html  = get_partial('rtEmail/layout_html', array('content' => $message_html));

    $message_plain = get_partial('rtShopVoucher/email_gift_voucher_plain', $vars);
    $message_plain = get_partial('rtEmail/layout_plain', array('content' => html_entity_decode($message_plain)));

    $message = Swift_Message::newInstance()
            ->setFrom($sender_email)
            ->setTo($session_voucher_array['email_address'])
            ->setSubject(sprintf('A %s gift voucher for you from %s %s',format_currency($session_voucher_array['reduction_value'], sfConfig::get('app_rt_currency', 'AUD')),$sender_fname,$sender_lname))
            ->setBody($message_html, 'text/html')
            ->addPart($message_plain, 'text/plain');

    sfContext::getInstance()->getMailer()->send($message);
  }

  /**
   * Create voucher, notify recipient
   *
   * @param rtShopOrder $order
   * @return boolean
   */
  public function createWithOrder(rtShopOrder $order)
  {
    $voucher = $this->persistSessionVoucher('Created with order: #' . $order->getReference(), 'Gift voucher created by: ' . $order->getBillingAddress()->getFirstName() . ' ' . $order->getBillingAddress()->getLastName());

    if($voucher)
    {
      $this->notifyRecipientFromRtShopOrder($order);
      //$this->resetSessionVoucher();
      return $voucher;
    }
    
    return false;
  }
}