<?php

/*
 * This file is part of the gumnut package.
 * (c) 2009-2010 Piers Warmers <piers@wranglers.com.au>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * BasertShopProductActions
 *
 * @package    rtShopPlugin
 * @subpackage modules
 * @author     Piers Warmers <piers@wranglers.com.au>
 */
class BasertShopProductActions extends sfActions
{
  /**
   * Executes an application defined process prior to execution of this sfAction object.
   *
   * By default, this method is empty.
   */
  public function preExecute()
  {
    sfConfig::set('app_rt_node_title', 'Shop');
    rtTemplateToolkit::setFrontendTemplateDir();
  }

  public function executeShow(sfWebRequest $request)
  {
    $this->rt_shop_product = $this->getRoute()->getObject();

    $this->forward404Unless($this->rt_shop_product);

    if(!$this->rt_shop_product->isPublished() && !$this->isAdmin())
    {
      $this->forward404('Product isn\'t published.');
    }

    $query = Doctrine::getTable('rtShopProduct')->addRelatedProductQuery($this->rt_shop_product);

    $this->related_products = $query->execute();

    rtSiteToolkit::checkSiteReference($this->rt_shop_product);

    $this->updateResponse($this->rt_shop_product);
  }

  public function executeAddToWishlist(sfWebRequest $request)
  {
    $wishlist = $this->getUser()->getAttribute('rt_shop_wish_list', array());
    $wishlist[$request->getParameter('id')] = $request->getParameter('id');
    $this->getUser()->setAttribute('rt_shop_wish_list', $wishlist);
  }

  public function executeShowWishlist(sfWebRequest $request)
  {
    $this->form = new rtShopOrderEmailForm();

    $wishlist = $this->getUser()->getAttribute('rt_shop_wish_list', array());

    if($request->hasParameter('delete'))
    {
      unset($wishlist[$request->getParameter('delete')]);
      $this->getUser()->setAttribute('rt_shop_wish_list', $wishlist);
    }

    // Send wishlist to user
    if($request->isMethod(sfRequest::POST) || $request->isMethod(sfRequest::PUT))
    {
      $this->form->bind($request->getParameter($this->form->getName()));

      if($this->form->isValid())
      {
        $vars = $this->form->getValues();

        try {
          $this->notifyUserOfWishlist($vars['email_address'],$wishlist);
        } catch (Exception $e) {

        }
        $this->getUser()->setFlash('notice', 'Thank you. Your wishlist has been sent to the selected email address.', false);
      }
      else
      {
        $this->getUser()->setFlash('default_error', true, false);
      }
    }
  }

  /**
   * Notify the user about his wishlist
   *
   * @param sfGuardUser $user
   */
  protected function notifyUserOfWishlist($email_address,$wishlist)
  {
    if(!$email_address)
    {
      return;
    }

    $vars = array('email' => $email_address);
    $vars['wishlist'] = $wishlist;

    $message_html = $this->getPartial('rtShopProduct/email_wishlist_user_html', $vars);
    $message_html = $this->getPartial('rtEmail/layout_html', array('content' => $message_html));

    $message_plain = $this->getPartial('rtShopProduct/email_wishlist_user_plain', $vars);
    $message_plain = $this->getPartial('rtEmail/layout_plain', array('content' => html_entity_decode($message_plain)));

    $message = Swift_Message::newInstance()
            ->setFrom(sfConfig::get('app_rt_shop_order_admin_email', 'from@noreply.com'))
            ->setTo($email_address)
            ->setSubject('My wishlist')
            ->setBody($message_html, 'text/html')
            ->addPart($message_plain, 'text/plain');

    $this->getMailer()->send($message);
  }

  public function executeSendToFriend(sfWebRequest $request)
  {
    $this->form = new rtShopSendToFriendForm();
    $this->form->setDefault('product_id', $request->getParameter('product_id'));

    if($request->isMethod('POST'))
    {
      $this->form->bind($request->getParameter($this->form->getName()), $request->getFiles($this->form->getName()));
      if ($this->form->isValid())
      {
        $rt_shop_product = Doctrine::getTable('rtShopProduct')->find($this->form->getValue('product_id'));

        $message = Swift_Message::newInstance()
          ->setFrom($this->form->getValue('email_address_sender'))
          ->setTo($this->form->getValue('email_address_recipient'))
          ->setSubject($rt_shop_product->getTitle())
          ->setBody($this->getPartial('rtShopProduct/send_to_friend', array('message' => $this->form->getValue('message'), 'rt_shop_product' => $rt_shop_product)))
          ->setContentType('text/html')
        ;

        $this->getMailer()->send($message);
        $this->getUser()->setFlash('notice', 'Your message has been sent');
        $this->redirect('rt_shop_product_show', $rt_shop_product);
      }
      else
      {
        $this->getUser()->setFlash('default_error', true, false);
      }
    }
  }

  private function updateResponse(rtShopProduct $page)
  {
    rtResponseToolkit::setCommonMetasFromPage($page, $this->getUser(), $this->getResponse());
  }

  private function isAdmin()
  {
    return $this->getUser()->hasCredential(sfConfig::get('app_rt_shop_product_admin_credential', 'admin_product'));
  }
}