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

    // Is dispatch user only show picking status orders
    if($this->getUser()->hasCredential(sfConfig::get('app_rt_shop_order_dispatch_credential', 'admin_shop_order_dispatch')))
    {
      $query->andWhereIn('o.status', array(rtShopOrder::STATUS_PICKING,rtShopOrder::STATUS_SENDING,rtShopOrder::STATUS_SENT));
      $query->orderBy('o.status ASC, o.created_at DESC');
    }

    $this->pager = new sfDoctrinePager(
      'rtShopOrder',
      $this->getCountPerPage($request)
    );

    $this->pager->setQuery($query);
    $this->pager->setPage($request->getParameter('page', 1));
    $this->pager->init();

    // Summary stats
    $this->stats = $this->stats();

    // Summary graph
    $this->orders_by_day = $this->getGraphOrderByDaySummary();
  }

  /**
   * Return array with revenue totals for the last 30 days
   *
   * @param integer $days
   * @return array
   */
  private function getGraphOrderByDaySummary($days = 30)
  {
    $query = Doctrine::getTable('rtShopOrder')->getQuery();
    $query->select('concat(month(o.created_at), "-", day(o.created_at)) as date, sum(o.total_charge)')
          ->andWhere('o.status != ?', rtShopOrder::STATUS_PENDING)
          ->andWhere('date(o.created_at) >= ?', date('Y-m-d H:i:s',strtotime(sprintf("-%s days",$days))))
          ->groupBy('DAY(o.created_at)');

    $raw_data = $query->execute(array(), Doctrine_Core::HYDRATE_SCALAR);

    $data = array();
    foreach($raw_data as $item)
    {
      $data[$item['o_date']] = $item['o_sum'];
    }

    $totals = array();
    for($i=0; $i<$days; $i++)
    {
      $date_check = date('n-j',strtotime(sprintf("-%s days",$i)));

      if(key_exists($date_check, $data))
      {
        $totals[$i] = (float) $data[$date_check];
      }
      else
      {
        $totals[$i] = 0;
      }
      
    }
    return $totals;
  }

  /**
   * Return summary display data
   *
   * @return array
   */
  private function stats()
  {
    $is_dispatch = $this->getUser()->hasCredential(sfConfig::get('app_rt_shop_order_dispatch_credential', 'admin_shop_order_dispatch'));

    // Dates
    $first_next_month = date('Y-m-d H:i:s',mktime(00,00,00,(date("n")+1 <= 12) ? date("n")+1 : 1 ,1,(date("n")+1 <= 12) ? date("Y") : date("Y")+1));
    $first_this_month = date('Y-m-d H:i:s',mktime(00,00,00,date("n"),1,date("Y")));
    $first_last_month = date('Y-m-d H:i:s',mktime(00,00,00,(date("n") != 1) ? date("n")-1 : 12,1,(date("n") != 1) ? date("Y") : date("Y")-1));

    // SQL queries
    $con = Doctrine_Manager::getInstance()->getCurrentConnection();

    $stats = array();

    if($is_dispatch && !$this->getUser()->isSuperAdmin())
    {
      $result_picking_total       = $con->fetchAssoc("select count(payment_charge) as count from rt_shop_order where status = '".rtShopOrder::STATUS_PICKING."'");

      $stats['picking']            = $result_picking_total[0] != '' ? $result_picking_total[0] : 0;

      return $stats;
    }

    $result_revenue_total         = $con->fetchAssoc("select sum(payment_charge) as revenue, count(payment_charge) as count from rt_shop_order where status <> 'pending'");
    $result_revenue_today         = $con->fetchAssoc("select sum(payment_charge) as revenue, count(payment_charge) as count from rt_shop_order where date(created_at) = date(NOW()) and status <> 'pending'");
    $result_revenue_yesterday     = $con->fetchAssoc("select sum(payment_charge) as revenue, count(payment_charge) as count from rt_shop_order where date(created_at) = date_sub(curdate(),interval 1 day) and status <> 'pending'");
    $result_revenue_month_current = $con->fetchAssoc("select sum(payment_charge) as revenue, count(payment_charge) as count from rt_shop_order where status <> 'pending' and created_at > '".$first_this_month."' and created_at < '".$first_next_month."'");
    $result_revenue_month_last    = $con->fetchAssoc("select sum(payment_charge) as revenue, count(payment_charge) as count from rt_shop_order where status <> 'pending' and created_at > '".$first_last_month."' and created_at < '".$first_this_month."'");

    $stats['total']            = $result_revenue_total[0] != '' ? $result_revenue_total[0] : 0.0;
    $stats['total']['revenue'] = $stats['total']['revenue'] != null ? $stats['total']['revenue'] : 0;

    $stats['today']            = $result_revenue_today[0];
    $stats['today']['revenue'] = $stats['today']['revenue'] != null ? $stats['today']['revenue'] : 0;

    $stats['yesterday']            = $result_revenue_yesterday[0];
    $stats['yesterday']['revenue'] = $stats['yesterday']['revenue'] != null ? $stats['yesterday']['revenue'] : 0;

    $stats['month_current']    = $result_revenue_month_current[0];
    $stats['month_current']['revenue'] = $stats['month_current']['revenue'] != null ? $stats['month_current']['revenue'] : 0;

    $stats['month_last']       = $result_revenue_month_last[0];
    $stats['month_last']['revenue'] = $stats['month_last']['revenue'] != null ? $stats['month_last']['revenue'] : 0;
    
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
    $rt_shop_order = $this->getRtShopOrderObjectById($request);
    $this->checkOrderStatusForDispatch($rt_shop_order->getStatus());
    $this->rt_shop_order = $rt_shop_order;
  }

  public function executeEdit(sfWebRequest $request)
  {
    // temporary redirect
    $this->redirect('rtShopOrderAdmin/show?id='.$request->getParameter('id'));

    $rt_shop_order = $this->getRtShopOrderObjectById($request);
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
    $this->form = new rtShopOrderReportDateForm();
    $fields = 'o.reference,o.status,o.id,o.is_wholesale,o.email_address,o.user_id,o.shipping_charge,o.tax_charge,o.tax_component,o.tax_mode,o.tax_rate,o.promotion_reduction,o.promotion_id,o.voucher_reduction,o.voucher_id,o.voucher_code,o.items_charge,o.total_charge,o.payment_transaction_id,o.payment_type,o.payment_charge,o.created_at,o.updated_at';
    if($this->getRequest()->getParameter('sf_format') != 'csv')
    {
      $fields .= ',o.promotion_data,o.voucher_data,o.products_data,o.payment_data';
    }
    $fieldnames = preg_replace('/[\$.]/', '_', $fields);
    $this->key_order = explode(',', $fieldnames);
    $q = Doctrine_Query::create()->from('rtShopOrder o');
    $q->select($fields);
    $q->andWhere('o.status != ?', rtShopOrder::STATUS_PENDING);

    if($request->isMethod(sfRequest::POST) || $request->isMethod(sfRequest::PUT))
    {
      $order_report = $request->getParameter('rt_shop_order_report');
      if($order_report['date_from']['year'] != '' && $order_report['date_from']['month'] != '' && $order_report['date_from']['day'] != '')
      {
        $q->andWhere('o.created_at >= ?', sprintf('%s-%s-%s 00:00:00',$order_report['date_from']['year'],$order_report['date_from']['month'],$order_report['date_from']['day']));
      }
      if($order_report['date_to']['year'] != '' && $order_report['date_to']['month'] != '' && $order_report['date_to']['day'] != '')
      {
        $q->andWhere('o.created_at <= ?', sprintf('%s-%s-%s 00:00:00',$order_report['date_to']['year'],$order_report['date_to']['month'],$order_report['date_to']['day']));
      }
    }
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

  public function executeUpdate(sfWebRequest $request)
  {
    $this->forward404Unless($request->isMethod(sfRequest::POST) || $request->isMethod(sfRequest::PUT));

    $rt_shop_order = $this->getRtShopOrderObjectById($request);
    
    $this->rt_shop_order = $rt_shop_order;
    $this->form = new rtShopOrderForm($rt_shop_order);

    $this->processForm($request, $this->form);

    $this->setTemplate('edit');
  }

  public function executeDelete(sfWebRequest $request)
  {
    $request->checkCSRFProtection();

    $rt_shop_order = $this->getRtShopOrderObjectById($request);
    
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
    $rt_shop_order = $this->getRtShopOrderObjectById($request);

    $this->checkOrderStatusForDispatch($request->getParameter('rt-shop-order-status'));

    if($rt_shop_order->getStatus() != $request->getParameter('rt-shop-order-status'))
    {      
      $rt_shop_order->setStatus($request->getParameter('rt-shop-order-status'));
      $rt_shop_order->save();

      switch ($rt_shop_order->getStatus())
      {
        case rtShopOrder::STATUS_PICKING:
          $this->notifyStatusChangeToPicking($rt_shop_order);
          break;
        case rtShopOrder::STATUS_SENT:
          $this->notifyStatusChangeToSent($rt_shop_order);
          break;
        default:
          break;
      }
      $this->getUser()->setFlash('notice', 'Order status has been changed to '.$request->getParameter('rt-shop-order-status'));
    }

    $this->redirect('rtShopOrderAdmin/show?id='.$request->getParameter('id'));
  }

  /**
   * Status update to "Picking", send email to random dispatch user to notify of
   * confirmation that order is ready to send.
   *
   * @param rtShopOrder $rt_shop_order
   */
  protected function notifyStatusChangeToPicking($rt_shop_order)
  {
    $dispatch_users = Doctrine::getTable('rtGuardUser')->getUsersArrayByPermission('admin_shop_order_dispatch');

    shuffle($dispatch_users);

    if(count($dispatch_users) == 0)
    {
      return;
    }

    $vars = array('dispatch_user' => $dispatch_users[0]);
    $vars['rt_shop_order'] = $rt_shop_order;
    $vars['user'] = $rt_shop_order->getBillingAddress() ? $rt_shop_order->getBillingAddress()->getData() : '';

    $message_html = $this->getPartial('rtShopOrderAdmin/email_status_dispatch_html', $vars);
    $message_html = $this->getPartial('rtEmail/layout_html', array('content' => $message_html));

    $message_plain = $this->getPartial('rtShopOrderAdmin/email_status_dispatch_plain', $vars);
    $message_plain = $this->getPartial('rtEmail/layout_plain', array('content' => html_entity_decode($message_plain)));

    $message = Swift_Message::newInstance()
               ->setFrom(sfConfig::get('app_rt_shop_order_admin_email', 'from@noreply.com'))
               ->setTo($dispatch_users[0]['u_email_address'])
               ->setSubject('Order #'.$rt_shop_order->getReference().' ready for picking')
               ->setBody($message_html, 'text/html')
               ->addPart($message_plain, 'text/plain');

    $this->getMailer()->send($message);
  }

  /**
   * Status update to "Sent", send email to order recipient to notify them of parcel pickup.
   *
   * @param rtShopOrder $rt_shop_order
   */
  protected function notifyStatusChangeToSent($rt_shop_order)
  {
    // Stub method, nothing yet!
  }

  /**
   * Check that the provided order status is available for a dispatch user.
   *
   * @param string $status
   * @return boolean
   */
  protected function checkOrderStatusForDispatch($status)
  {
    $cred  = sfConfig::get('app_rt_shop_order_dispatch_credential', 'admin_shop_order_dispatch');
    
    $allowed_dispatch_status  = array(rtShopOrder::STATUS_PICKING, rtShopOrder::STATUS_SENDING, rtShopOrder::STATUS_SENT);

    if($this->getUser()->hasCredential($cred) && !in_array($status, $allowed_dispatch_status))
    {
      return false;
    }
    return true;
  }

  /**
   * Helper method to retrieve order objects via either a passed id, or alternatively
   * an ID request parameter.
   *
   * @param sfWebRequest $request
   * @param mixed        $id
   */
  protected function getRtShopOrderObjectById($request, $id = null)
  {
    if(is_null($id))
    {
      $id = $request->getParameter('id');
    }

    $rt_shop_order = Doctrine::getTable('rtShopOrder')->find($id);

    $this->forward404Unless($rt_shop_order, sprintf('Object rt_shop_order does not exist (%s).', $id));

    return $rt_shop_order;
  }

  /**
   * Process a submitted order form
   * 
   * @param sfWebRequest $request
   * @param sfForm $form
   */
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