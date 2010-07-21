<?php
/*
 * This file is part of the reditype package.
 * (c) 2009-2010 Piers Warmers <piers@wranglers.com.au>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * BasertShopVoucherAdminActions
 *
 * @package    rtShopPlugin
 * @subpackage modules
 * @author     Piers Warmers <piers@wranglers.com.au>
 * @author     Konny Zurcher <konny@wranglers.com.au>
 */
class BasertShopVoucherAdminActions extends sfActions
{
  public function preExecute()
  {
    rtTemplateToolkit::setTemplateForMode('backend');
    sfConfig::set('app_rt_node_title', 'Vouchers');
  }
  
  public function executeIndex(sfWebRequest $request)
  {
    $query = Doctrine::getTable('rtShopVoucher')->getQuery();
    $query->orderBy('p.created_at DESC');

    $this->pager = new sfDoctrinePager(
      'rtShopVoucher',
      sfConfig::get('app_rt_admin_pagination_limit', 50)
    );

    $this->pager->setQuery($query);
    $this->pager->setPage($request->getParameter('page', 1));
    $this->pager->init();
  }

  public function executeNew(sfWebRequest $request)
  {
    $this->form = new rtShopVoucherForm();
  }

  public function executeCreate(sfWebRequest $request)
  {
    $this->forward404Unless($request->isMethod(sfRequest::POST));

    $this->form = new rtShopVoucherForm();

    $this->processForm($request, $this->form);

    $this->setTemplate('new');
  }

  public function executeEdit(sfWebRequest $request)
  {
    $this->forward404Unless($rt_shop_voucher = Doctrine::getTable('rtShopVoucher')->find(array($request->getParameter('id'))), sprintf('Object rt_shop_voucher does not exist (%s).', $request->getParameter('id')));
    $this->form = new rtShopVoucherForm($rt_shop_voucher);
  }

  public function executeUpdate(sfWebRequest $request)
  {
    $this->forward404Unless($request->isMethod(sfRequest::POST) || $request->isMethod(sfRequest::PUT));
    $this->forward404Unless($rt_shop_voucher = Doctrine::getTable('rtShopVoucher')->find(array($request->getParameter('id'))), sprintf('Object rt_shop_voucher does not exist (%s).', $request->getParameter('id')));
    $this->form = new rtShopVoucherForm($rt_shop_voucher);

    $this->processForm($request, $this->form);

    $this->setTemplate('edit');
  }

  public function executeDelete(sfWebRequest $request)
  {
    $request->checkCSRFProtection();

    $this->forward404Unless($rt_shop_voucher = Doctrine::getTable('rtShopVoucher')->find(array($request->getParameter('id'))), sprintf('Object rt_shop_voucher does not exist (%s).', $request->getParameter('id')));
    $rt_shop_voucher->delete();

    $this->redirect('rtShopVoucherAdmin/index');
  }

  /**
   * Batch create vouchers
   *
   * @param sfWebRequest $request  Request object
   */
  public function executeBatchCreate(sfWebRequest $request)
  {
    $this->form = new rtShopVoucherBatchForm();

    if($request->isMethod('post'))
    {
      $this->processBatchForm($request, $this->form);
      
      $batch_data = $request->getParameter('rt_shop_voucher_batch');
      if ($this->form->isValid())
      {
        $form_values = $this->form->getValues();

        $reference = rtShopVoucherToolkit::generateBatch($this->form->getValues(),$form_values['batchsize']);

        if($reference)
        {
          $this->getUser()->setFlash('notice',$form_values['batchsize'].' vouchers created - ref. #'.$reference);
          $this->redirect('rtShopVoucherAdmin/index');
        }
        else
        {
          $this->getUser()->setFlash('error','An error occured while creating batch vouchers', false);
        }
      } 
      $this->getUser()->setFlash('default_error', true, false);
    }
  }

  /**
   * Delete batch vouchers
   *
   * @param sfWebRequest $request
   */
  public function executeBatchDelete(sfWebRequest $request)
  {
    if(!$request->hasParameter('id'))
    {
      $this->redirect('rtShopVoucherAdmin/index');
    }

    $q = Doctrine_Query::create()
          ->delete('rtShopVoucher v')
          ->andWhere('v.batch_reference = ?', $request->getParameter('id'))
          ->execute();

    $this->getUser()->setFlash('notice','Batch vouchers with reference '.$request->getParameter('id').' were deleted.');
    $this->redirect('rtShopVoucherAdmin/index');
  }

  /**
   * Delete batch vouchers
   *
   * @param sfWebRequest $request
   */
  public function executeBatchShow(sfWebRequest $request)
  {
    $this->batch = Doctrine::getTable('rtShopVoucher')->findInfoOnBatchReference($request->getParameter('id'));
  }

  /**
   * Create CSV file based on batch reference
   *
   * @param sfWebRequest $request
   * @return void
   */
  public function executeBatchDownload(sfWebRequest $request)
  {
    if(!$request->hasParameter('id'))
    {
      $this->redirect('rtShopVoucherAdmin/index');
    }

    $q = Doctrine_Query::create()
            ->select('v.code')
            ->from('rtShopVoucher v')
            ->addWhere('v.batch_reference = ?', $request->getParameter('id'));
    $vouchers = $q->fetchArray();

    if(!$vouchers)
    {
      $this->getUser()->setFlash('error','No vouchers with this reference found.');
      $this->redirect('rtShopVoucherAdmin/index');
    }

    $list = array();
    foreach($vouchers as $key => $value)
    {
      $list[] = $value['code'];
    }

    $this->list = $list;

    $reference = $request->getParameter('id');
    sfConfig::set('sf_web_debug', false);
    $response = $this->getResponse();
    $response->setHttpHeader('Last-Modified', date('r'));
    //$response->setContentType("text/csv");
    $response->setContentType("application/octet-stream");
    $response->setHttpHeader('Content-Length', strlen(implode("\r\n", $list)));
    $response->setHttpHeader('Cache-Control','no-store, no-cache');
    if (strstr($_SERVER['HTTP_USER_AGENT'], "MSIE"))
    {
      $response->setHttpHeader('Content-Disposition','inline; filename="'.$reference.'.csv"');
    }
    else
    {
      $response->setHttpHeader('Content-Disposition','attachment; filename="'.$reference.'.csv"');
    }
    $response->setContent(implode("\r\n", $list));

    $this->setLayout(false);
    return sfView::NONE;
  }

  /**
   * Process batch create form
   *
   * @param sfWebRequest $request  Request object
   * @param sfForm       $form     Form object
   */
  protected function processBatchForm(sfWebRequest $request, sfForm $form)
  {
    $form->bind($request->getParameter($form->getName()), $request->getFiles($form->getName()));
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
    if ($form->isValid())
    {
      $rt_shop_voucher = $form->save();

      $action = $request->getParameter('rt_post_save_action', 'index');

      if($action == 'edit')
      {
        $this->redirect('rtShopVoucherAdmin/edit?id='.$rt_shop_voucher->getId());
      }

      $this->redirect('rtShopVoucherAdmin/index');
    }
    $this->getUser()->setFlash('default_error', true, false);
  }
}