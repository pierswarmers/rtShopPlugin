<?php
/*
 * This file is part of the reditype package.
 * (c) 2009-2010 Piers Warmers <piers@wranglers.com.au>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * BasertShopOrderAdminActions
 *
 * @package    rtShopPlugin
 * @subpackage modules
 * @author     Piers Warmers <piers@wranglers.com.au>
 * @author     Konny Zurcher <konny@wranglers.com.au>
 */
class BasertShopOrderAdminActions extends sfActions
{
  public function executeIndex(sfWebRequest $request)
  {
    $query = Doctrine::getTable('rtShopOrder')->getQuery();
    $query->andWhere('o.status != ?', rtShopOrder::STATUS_PENDING);
    $query->orderBy('o.created_at DESC');

    $this->pager = new sfDoctrinePager(
      'rtShopOrder',
      $this->getCountPerPage($request)
    );

    $this->pager->setQuery($query);
    $this->pager->setPage($request->getParameter('page', 1));
    $this->pager->init();
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
    $this->form = new rtShopOrderForm();
  }

  public function executeCreate(sfWebRequest $request)
  {
    $this->forward404Unless($request->isMethod(sfRequest::POST));

    $this->form = new rtShopOrderForm();

    $this->processForm($request, $this->form);

    $this->setTemplate('new');
  }

  public function executeShow(sfWebRequest $request)
  {
    $this->forward404Unless($rt_shop_order = Doctrine::getTable('rtShopOrder')->find(array($request->getParameter('id'))), sprintf('Object rt_shop_order does not exist (%s).', $request->getParameter('id')));
    $this->rt_shop_order = $rt_shop_order;
  }

  public function executeEdit(sfWebRequest $request)
  {
    // temporary redirect
    $this->redirect('rtShopOrderAdmin/show?id='.$request->getParameter('id'));

    $this->forward404Unless($rt_shop_order = Doctrine::getTable('rtShopOrder')->find(array($request->getParameter('id'))), sprintf('Object rt_shop_order does not exist (%s).', $request->getParameter('id')));
    $this->rt_shop_order = $rt_shop_order;
    $this->form = new rtShopOrderForm($rt_shop_order);
  }

  /**
   * Create order report as XML format
   *
   * @param sfWebRequest $request
   */
  public function executeOrderReport(sfWebRequest $request)
  {
    $fields = 'o.reference,o.status,o.id,o.is_wholesale,o.email_address,o.user_id,o.shipping_charge,o.tax_charge,o.tax_component,o.tax_mode,o.tax_rate,o.promotion_reduction,o.promotion_id,o.voucher_reduction,o.voucher_id,o.voucher_code,o.items_charge,o.total_charge,o.payment_transaction_id,o.payment_type,o.payment_charge,o.created_at,o.updated_at';
    if($this->getRequest()->getParameter('sf_format') != 'csv')
    {
      $fields .= ',o.promotion_data,o.voucher_data,o.products_data,o.payment_data';
    }
    $fieldnames = preg_replace('/[\$.]/', '_', $fields);
    $this->key_order = explode(',', $fieldnames);
    $q = Doctrine_Query::create()->from('rtShopOrder o');
    $q->select($fields);
    if($request->hasParameter('from') && $request->hasParameter('to'))
    {
      $q->andWhere('o.created_at >= ?', $request->getParameter('from'));
      $q->andWhere('o.created_at <= ?', $request->getParameter('to'));
    }
    $q->andWhere('o.status = ?', rtShopOrder::STATUS_PAID);
    $q->orderBy('o.created_at');
    $this->orders = $q->execute(array(), Doctrine_Core::HYDRATE_SCALAR);

    // CSV header
    if($this->getRequest()->getParameter('sf_format') === 'csv')
    {
      $response = $this->getResponse();
      $response->setHttpHeader('Last-Modified', date('r'));
      $response->setContentType("application/octet-stream");
      $response->setHttpHeader('Cache-Control','no-store, no-cache');
      if (strstr($_SERVER['HTTP_USER_AGENT'], "MSIE"))
      {
        $response->setHttpHeader('Content-Disposition','inline; filename="order_report.csv"');
      }
      else
      {
        $response->setHttpHeader('Content-Disposition','attachment; filename="order_report.csv"');
      }

      $this->setLayout(false);
    }

    // Pager
    $this->pager = new sfDoctrinePager(
      'rtShopOrder',
      $this->getCountPerPage($request)
    );
    $this->pager->setQuery($q);
    $this->pager->setPage($request->getParameter('page', 1));
    $this->pager->init();
  }

  /**
   * Create order report schema file (XSD)
   *
   * @param sfWebRequest $request
   */
  public function executeOrderXsd(sfWebRequest $request)
  {

  }

  public function executeUpdate(sfWebRequest $request)
  {
    $this->forward404Unless($request->isMethod(sfRequest::POST) || $request->isMethod(sfRequest::PUT));
    $this->forward404Unless($rt_shop_order = Doctrine::getTable('rtShopOrder')->find(array($request->getParameter('id'))), sprintf('Object rt_shop_order does not exist (%s).', $request->getParameter('id')));
    $this->rt_shop_order = $rt_shop_order;
    $this->form = new rtShopOrderForm($rt_shop_order);

    $this->processForm($request, $this->form);

    $this->setTemplate('edit');
  }

  public function executeDelete(sfWebRequest $request)
  {
    $request->checkCSRFProtection();

    $this->forward404Unless($rt_shop_order = Doctrine::getTable('rtShopOrder')->find(array($request->getParameter('id'))), sprintf('Object rt_shop_order does not exist (%s).', $request->getParameter('id')));
    $rt_shop_order->delete();

    $this->redirect('rtShopOrderAdmin/index');
  }

  private function getGraphOrderSummary($month)
  {
    $query = Doctrine::getTable('rtShopOrder')->getQuery();

    $query->select('day(o.created_at), month(o.created_at), count(o.id), sum(o.total_charge)')
          ->andWhere('o.status != ?', rtShopOrder::STATUS_PENDING)
          ->andWhere('MONTH(o.created_at) = ?', $month)
          ->groupBy('DAY(o.created_at)');

    $raw_data = $query->execute(array(), Doctrine_Core::HYDRATE_SCALAR);

    $data = array();

    // form better keys
    foreach($raw_data as $item)
    {
      $data[$item['o_month'].'-'.$item['o_day']] = $item;
    }

    $raw_data = $data;
    $data = array();
    
    for($i=1; $i<=31; $i++)
    {
      if(date('n') == $month && date('j') < $i)
      {
        continue;
      }
      $key = $month.'-'.$i;

      if(!isset($raw_data[$key]))
      {
        $data[$key]['o_count'] = 0;
        $data[$key]['o_sum'] = 0;
        $data[$key]['o_day'] = $i;
        $data[$key]['o_month'] = $month;
      }
      else
      {
        $data[$key] = $raw_data[$key];
      }
    }

    return $data;
  }

  public function executeGraph(sfWebRequest $request)
  {
    $span_back_in_months = 3;

    $orders_by_month = array();

    $orders_by_month[] = $this->getGraphOrderSummary(date('n'));
    $orders_by_month[] = $this->getGraphOrderSummary(date('n') - 1);
    $orders_by_month[] = $this->getGraphOrderSummary(date('n') - 2);
    $orders_by_month[] = $this->getGraphOrderSummary(date('n') - 3);

    $this->orders_by_month = $orders_by_month;
  }

  /**
   * Update order status
   *
   * @param sfWebRequest $request
   */
  public function executeStatusUpdate(sfWebRequest $request)
  {
    $this->forward404Unless($rt_shop_order = Doctrine::getTable('rtShopOrder')->find(array($request->getParameter('id'))), sprintf('Object rt_shop_order does not exist (%s).', $request->getParameter('id')));

    if($rt_shop_order->getStatus() != $request->getParameter('rt-shop-order-status'))
    {
      $rt_shop_order->setStatus($request->getParameter('rt-shop-order-status'));
      $rt_shop_order->save();
      $this->getUser()->setFlash('notice', 'Order status has been changed to '.$request->getParameter('rt-shop-order-status'));
    }

    $this->redirect('/rtShopOrderAdmin/show?id='.$request->getParameter('id'));
  }

  protected function processForm(sfWebRequest $request, sfForm $form)
  {
    $form->bind($request->getParameter($form->getName()), $request->getFiles($form->getName()));
    if ($form->isValid())
    {
      $rt_shop_order = $form->save();

      $this->redirect('rtShopOrderAdmin/edit?id='.$rt_shop_order->getId());
    }
  }
}