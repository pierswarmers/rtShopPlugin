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
 * BasertShopVoucherActions
 *
 * @package    rtShopPlugin
 * @subpackage modules
 * @author     Piers Warmers <piers@wranglers.com.au>
 * @author     Konny Zurcher <konny@wranglers.com.au>
 */
class BasertShopVoucherActions extends sfActions
{
  private $_rt_shop_voucher_manager;

  public function preExecute()
  {
    sfConfig::set('app_rt_node_title', 'Gift Voucher');
    rtTemplateToolkit::setFrontendTemplateDir();
  }

  public function executeNew(sfWebRequest $request)
  {
    if($this->getVoucherManager()->hasSessionVoucher())
    {
      $this->getUser()->setFlash('error', 'Only one gift voucher allowed.');
      $this->redirect('rt_shop_voucher_edit');
    }

    $this->form = new rtShopVoucherPublicForm();
  }

  public function executeCreate(sfWebRequest $request)
  {
    $this->forward404Unless($request->isMethod(sfRequest::POST));

    $this->form = new rtShopVoucherPublicForm();

    $this->processForm($request, $this->form);

    $this->setTemplate('new');
  }

  public function executeEdit(sfWebRequest $request)
  {
    $this->getReferer($request);
    
    $this->forward404Unless(is_array($this->getVoucherManager()->getSessionVoucherArray()), 'Array for rt_shop_voucher does not exist');

    $this->form = new rtShopVoucherPublicForm($this->getVoucherManager()->getSessionVoucherArray());
  }

  public function executeUpdate(sfWebRequest $request)
  {
    $this->forward404Unless($request->isMethod(sfRequest::POST) || $request->isMethod(sfRequest::PUT));
    $this->forward404Unless(is_array($this->getVoucherManager()->getSessionVoucherArray()), 'Array for rt_shop_voucher does not exist');

    $this->form = new rtShopVoucherPublicForm($this->getVoucherManager()->getSessionVoucherArray());

    $this->processForm($request, $this->form);

    $this->setTemplate('edit');
  }

  /**
   * Remove voucher data from session
   *
   * @param sfWebRequest $request
   */
  public function executeDelete(sfWebRequest $request)
  {
    if($this->getVoucherManager()->hasSessionVoucher())
    {
      $this->getVoucherManager()->resetSessionVoucher();
      $this->getUser()->setFlash('notice', 'Voucher has been removed.');
    }

    // Redirect to referer page
    $refer = $this->getReferer($request);
    
    if($refer)
    {
      $this->cleanReferer();
      $this->redirect($refer);
    }

    // No referer, go to new voucher
    $this->redirect('@homepage');
  }

  /**
   * Return referer from request or session
   * 
   * @param sfWebRequest $request
   * @return string
   */
  protected function getReferer(sfWebRequest $request)
  {
    $referer = false;
    
    if($request->hasParameter('rt-voucher-referer'))
    {
      $referer = $request->getParameter('rt-voucher-referer');
      $this->getUser()->setAttribute('rt-voucher-referer', $referer);
    }
    elseif($this->getUser()->hasAttribute('rt-voucher-referer'))
    {
      $referer = $this->getUser()->getAttribute('rt-voucher-referer');
    }

    return $referer;
  }

  /**
   * Remove referer from session
   */
  protected function cleanReferer()
  {
    $this->getUser()->getAttributeHolder()->remove('rt-voucher-referer');
  }

  /**
   * Process general forms
   *
   * @param sfWebRequest $request  Request object
   * @param sfForm       $form     Form object
   */
  protected function processForm(sfWebRequest $request, sfForm $form)
  {
    $form->bind($request->getParameter($form->getName()), $request->getFiles($form->getName()));
    if($form->isValid())
    {
      $form_values = $form->getValues();
      
      // Reset session variable
      $this->getVoucherManager()->resetSessionVoucher();

      // Add data to session
      $this->getVoucherManager()->setSessionVoucherArray($form_values);
      $this->getUser()->setFlash('notice', 'Voucher has been '.($request->getParameter('action') == 'update' ? 'updated' : 'created'));

      // Redirect to referer page
      $refer = $this->getReferer($request);

      if($refer)
      {
        $this->cleanReferer();
        $this->redirect($refer);
      }

      // No referer, go to edit voucher
      $this->redirect('rt_shop_voucher_edit');
    }
    $this->getUser()->setFlash('default_error', true, false);
  }

  /**
   * @return rtShopVoucherManager
   */
  public function getVoucherManager()
  {
    if(is_null($this->_rt_shop_voucher_manager))
    {
      $this->_rt_shop_voucher_manager = new rtShopVoucherManager();
    }

    return $this->_rt_shop_voucher_manager;
  }
}