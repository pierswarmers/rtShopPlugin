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
      $this->getCountPerPage($request)
    );

    $this->pager->setQuery($query);
    $this->pager->setPage($request->getParameter('page', 1));
    $this->pager->init();

    $this->stats = $this->stats();
  }

  private function stats()
  {
    // Dates
    $date_now         = date("Y-m-d H:i:s");
    $first_next_month = date('Y-m-d H:i:s',mktime(00,00,00,(date("n")+1 <= 12) ? date("n")+1 : 1 ,1,(date("n")+1 <= 12) ? date("Y") : date("Y")+1));
    $first_this_month = date('Y-m-d H:i:s',mktime(00,00,00,date("n"),1,date("Y")));
    $first_last_month = date('Y-m-d H:i:s',mktime(00,00,00,(date("n") != 1) ? date("n")-1 : 12,1,(date("n") != 1) ? date("Y") : date("Y")-1));

    // SQL queries
    $con = Doctrine_Manager::getInstance()->getCurrentConnection();

    $result_vouchers_total               = $con->fetchAssoc("select count(id) as count from rt_shop_promotion where type = 'rtShopVoucher'");
    $result_vouchers_total_active        = $con->fetchAssoc("select count(id) as count from rt_shop_promotion where type = 'rtShopVoucher' and count > 0 and (date_from <= '".$date_now."' OR date_from IS NULL) and (date_to > '".$date_now."' OR date_to IS NULL)");
//    $result_users_total_active        = $con->fetchAssoc("select count(id) as count from sf_guard_user where is_active = 1");
//    $result_users_total_admin         = $con->fetchAssoc("select count(id) as count from sf_guard_user where is_super_admin = 1");
//    $result_users_total_unused        = $con->fetchAssoc("select count(id) as count from sf_guard_user where last_login Is Null");
//    $result_users_added_current_month = $con->fetchAssoc("select count(id) as count from sf_guard_user where created_at > '".$first_this_month."' and created_at < '".$first_next_month."'");

    // Create array
    $stats = array();
    $stats['total']         = $result_vouchers_total[0] != '' ? $result_vouchers_total[0] : 0;

    $stats['total_active']  = $result_vouchers_total_active[0] != '' ? $result_vouchers_total_active[0] : 0;
//
//    $stats['total_admin']   = $result_users_total_admin[0] != '' ? $result_users_total_admin[0] : 0;
//
//    $stats['total_unused']  = $result_users_total_unused[0] != '' ? $result_users_total_unused[0] : 0;
//
//    $stats['month_current'] = $result_users_added_current_month[0] != '' ? $result_users_added_current_month[0] : 0;

    return $stats;
  }

  private function getCountPerPage(sfWebRequest $request)
  {
    $count = sfConfig::get('app_rt_admin_pagination_limit', 50);
    if($request->hasParameter('show_more'))
    {
      $count = sfConfig::get('app_rt_admin_pagination_per_page_multiple', 2) * $count;
    }

    return $count;
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
   * Create batch reports as CSV, XML and JSON formats
   * 
   * @param sfWebRequest $request 
   */
  public function executeBatchReport(sfWebRequest $request)
  {
    if(!$request->hasParameter('id'))
    {
      $this->redirect('rtShopVoucherAdmin/index');
    }

    $fields = 'v.code,v.title,v.batch_reference,v.reduction_type,v.reduction_value,v.count,v.mode,v.total_from,v.total_to,v.date_from,v.date_to,v.created_at,v.updated_at';
    $fieldnames = preg_replace('/[\$.]/', '_', $fields);
    $this->key_order = explode(',', $fieldnames);
    $q = Doctrine_Query::create()
            ->select($fields)
            ->from('rtShopVoucher v')
            ->addWhere('v.batch_reference = ?', $request->getParameter('id'))
            ->orderBy('v.count ASC');
    $vouchers = $q->execute(array(), Doctrine_Core::HYDRATE_SCALAR);

    $this->vouchers = array();
    $i=0;
    foreach($vouchers as $voucher)
    {
      foreach($this->key_order as $key => $value)
      {
        if($value === 'v_title')
        {
          $this->vouchers[$i][$value] = preg_replace('/[^a-zA-Z0-9_ - . $]/s', '', $voucher[$value]);
        }
        else
        {
          $this->vouchers[$i][$value] = $voucher[$value];
        }
      }
      $i++;
    }

    if(!$this->vouchers)
    {
      $this->getUser()->setFlash('error','No vouchers with this reference found.');
      $this->redirect('rtShopVoucherAdmin/index');
    }

    // CSV header
    if($this->getRequest()->getParameter('sf_format') === 'csv')
    {
      $response = $this->getResponse();
      $response->setHttpHeader('Last-Modified', date('r'));
      $response->setContentType("application/octet-stream");
      $response->setHttpHeader('Cache-Control','no-store, no-cache');
      if (strstr($_SERVER['HTTP_USER_AGENT'], "MSIE"))
      {
        $response->setHttpHeader('Content-Disposition','inline; filename="batch_report.csv"');
      }
      else
      {
        $response->setHttpHeader('Content-Disposition','attachment; filename="batch_report.csv"');
      }

      $this->setLayout(false);
    }
  }

  /**
   * Create CSV file based on batch reference
   *
   * @param sfWebRequest $request
   * @return void
   */
//  public function executeBatchDownload(sfWebRequest $request)
//  {
//    if(!$request->hasParameter('id'))
//    {
//      $this->redirect('rtShopVoucherAdmin/index');
//    }
//
//    $q = Doctrine_Query::create()
//            ->select('v.code')
//            ->from('rtShopVoucher v')
//            ->addWhere('v.batch_reference = ?', $request->getParameter('id'));
//    $vouchers = $q->fetchArray();
//
//    if(!$vouchers)
//    {
//      $this->getUser()->setFlash('error','No vouchers with this reference found.');
//      $this->redirect('rtShopVoucherAdmin/index');
//    }
//
//    $list = array();
//    foreach($vouchers as $key => $value)
//    {
//      $list[] = $value['code'];
//    }
//
//    $this->list = $list;
//
//    $reference = $request->getParameter('id');
//    sfConfig::set('sf_web_debug', false);
//    $response = $this->getResponse();
//    $response->setHttpHeader('Last-Modified', date('r'));
//    //$response->setContentType("text/csv");
//    $response->setContentType("application/octet-stream");
//    $response->setHttpHeader('Content-Length', strlen(implode("\r\n", $list)));
//    $response->setHttpHeader('Cache-Control','no-store, no-cache');
//    if (strstr($_SERVER['HTTP_USER_AGENT'], "MSIE"))
//    {
//      $response->setHttpHeader('Content-Disposition','inline; filename="'.$reference.'.csv"');
//    }
//    else
//    {
//      $response->setHttpHeader('Content-Disposition','attachment; filename="'.$reference.'.csv"');
//    }
//    $response->setContent(implode("\r\n", $list));
//
//    $this->setLayout(false);
//    return sfView::NONE;
//  }

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