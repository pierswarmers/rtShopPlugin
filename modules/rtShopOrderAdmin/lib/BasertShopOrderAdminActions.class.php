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

    $query->select('DATE_FORMAT(o.created_at,"%m-%d") as date, sum(o.total_charge), count(o.id)')
          ->andWhere('o.status != ?', rtShopOrder::STATUS_PENDING)
          ->andWhere('date(o.created_at) <= ?', date('Y-m-d H:i:s'))
          ->andWhere('date(o.created_at) >= ?', date('Y-m-d H:i:s',strtotime(sprintf("-%s days",$days))))
          ->groupBy('date');

    $raw_data = $query->execute(array(), Doctrine_Core::HYDRATE_SCALAR);

    // Rewrite array
    $data = array();
    foreach($raw_data as $item)
    {
      $data[$item['o_date']] = $item['o_sum'];
    }

    // Fill up array
    $totals = array();
    for($i=0; $i<$days; $i++)
    {
      $date_check = date('m-d',strtotime(sprintf("-%s days",$i)));

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

  protected function getCountPerPage(sfWebRequest $request)
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
    // Disable creation of orders in admin
    $this->getUser()->setFlash('notice','Order creation in admin has beend disabled',true);
    $this->redirect('rtShopOrderAdmin/index');

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
    // Temporary redirect
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
    // Date switch
    $has_from_date = false;
    $has_to_date   = false;

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
      if($order_report['date_from']['year'] !== '' && $order_report['date_from']['month'] !== '' && $order_report['date_from']['day'] !== '')
      {
        $has_from_date = true;
        $q->andWhere('o.created_at >= ?', sprintf('%s-%s-%s 00:00:00',$order_report['date_from']['year'],$order_report['date_from']['month'],$order_report['date_from']['day']));
      }
      if($order_report['date_to']['year'] !== '' && $order_report['date_to']['month'] !== '' && $order_report['date_to']['day'] !== '')
      {
        $has_to_date = true;
        $q->andWhere('o.created_at <= ?', sprintf('%s-%s-%s 23:59:59',$order_report['date_to']['year'],$order_report['date_to']['month'],$order_report['date_to']['day']));
      }
    }
    $q->orderBy('o.created_at');
    $this->orders = $q->execute(array(), Doctrine_Core::HYDRATE_SCALAR);

    // Set order report headers for export files
    if(in_array($this->getRequest()->getParameter('sf_format'), array('csv','xml','json')))
    {
      $report_filename = $this->getReportFilename($order_report, $has_from_date, $has_to_date);
      $this->setReportHeader($this->getRequest()->getParameter('sf_format'), $report_filename);
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
   * Return report filename with date
   *
   * @example order_report_from_[year|month|day]_to_[year|month|day].[csv/xml/json]
   * @param   Boolean $has_from_date
   * @param   Boolean $has_to_date
   * @return  string
   */
  protected function getReportFilename($report_array, $has_from_date = false,$has_to_date = false)
  {
    // Texts and dates
    $filename         = 'order_report';
    $from_text        = '_from_';
    $to_text          = '_to_';
    $from_date        = $report_array['date_from'];
    $to_date          = $report_array['date_to'];

    // Date string => [year|month|day]
    $from_date_string = sprintf('%s%s%s',$from_date['year'],
                                         strlen($from_date['month']) > 1 ? $from_date['month'] : '0'.$from_date['month'],
                                         strlen($from_date['day']) > 1 ? $from_date['day'] : '0'.$from_date['day']);
    $to_date_string   = sprintf('%s%s%s',$to_date['year'],
                                         strlen($to_date['month']) > 1 ? $to_date['month'] : '0'.$to_date['month'],
                                         strlen($to_date['day']) > 1 ? $to_date['day'] : '0'.$to_date['day']);

    if($has_from_date)
    {
      $filename .= $from_text.$from_date_string;
    }

    if($has_to_date)
    {
      $filename .= $to_text.$to_date_string;
    }

    return $filename;
  }

  /**
   * Set headers for csv, xml and json exports
   *
   * @param String $sf_format
   */
  protected function setReportHeader($sf_format, $filename = 'order_report')
  {
    $response = $this->getResponse();
    // Format switch
    switch ($sf_format) {
      case 'csv':
        $response->setHttpHeader('Last-Modified', date('r'));
        $response->setContentType("application/octet-stream");
        $response->setHttpHeader('Cache-Control','no-store, no-cache');
        $response->setHttpHeader('Content-Disposition','attachment; filename="'.$filename.'.csv"');
        break;
      case 'xml':
        $response->setHttpHeader('Content-Disposition','attachment; filename="'.$filename.'.xml"');
        break;
      case 'json':
        $response->setHttpHeader('Content-Disposition','attachment; filename="'.$filename.'.json"');
        break;
    }
  }

  /**
   * API: Return XML or JSON stream of orders other than pending
   *
   * @param  sfWebRequest $request
   * @return Mixed
   */
  public function executeDownloadReport(sfWebRequest $request)
  {
    $response = $this->getResponse();

    // 403 - Access denied
    if(!rtApiToolkit::grantApiAccess($request->getParameter('auth')))
    {
      $response->setHeaderOnly(true);
      $response->setStatusCode(403);
      return sfView::NONE;
    }

    // Get orders
    $q = Doctrine_Query::create()->from('rtShopOrder o');

    // With from date
    if($request->hasParameter('date_from') && $request->getParameter('date_from') !== '')
    {
      $q->andWhere('o.created_at >= ?', urldecode($request->getParameter('date_from')));
    }

    // With to date
    if($request->hasParameter('date_to') && $request->getParameter('date_to') !== '')
    {
      $q->andWhere('o.created_at <= ?', urldecode($request->getParameter('date_to')));
    }

    // Only orders with status...
    if($request->hasParameter('status') && $request->getParameter('status') !== '')
    {
      $q->andWhere('o.status = ?', urldecode($request->getParameter('status')));
    }

    // Pending orders always excluded
    $q->andWhere('o.status != ?', rtShopOrder::STATUS_PENDING);

    $q->orderBy('o.created_at');
    $this->orders = $q->execute(array(), Doctrine_Core::HYDRATE_SCALAR);

    if(in_array($this->getRequest()->getParameter('sf_format'), array('xml','json')))
    {
      $this->setLayout(false);
    }
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
    $this->getDispatcher($request)->notify(new sfEvent($this, 'doctrine.admin.delete_object', array('object' => $rt_shop_order)));
    $this->redirect('rtShopOrderAdmin/index');
  }

  /**
   * Return array with daily order sums and counts for sales charts
   *
   * @param  Integer $months
   * @return Array
   */
  private function getGraphOrderSummaryRange($months = 6)
  {
    $query = Doctrine::getTable('rtShopOrder')->getQuery();

    $query->select('DATE_FORMAT(o.created_at,"%Y%m%d") as date, sum(o.total_charge), count(o.id)')
          ->andWhere('o.status != ?', rtShopOrder::STATUS_PENDING)
          ->andWhere('date(o.created_at) <= ?', date('Y-m-d H:i:s'))
          ->andWhere('date(o.created_at) >= ?', date('Y-m-d H:i:s',strtotime(sprintf("-%s months",$months))))
          ->groupBy('date');

    $raw_data = $query->execute(array(), Doctrine_Core::HYDRATE_SCALAR);

    // Build empty days array
    $last_six_months = array();
    $day_today = date('d');
    for ($i=$months; $i>=0; $i--)
    {
      // Days of current month
      $days_in_month = date('t', mktime(0,0,0,(date('m')-$i),28,date('Y')));

      // Month and year of current iteration
      $month = date('m', mktime(0,0,0,(date('m')-$i),$days_in_month,date('Y')));
      $year  = date('Y', mktime(0,0,0,(date('m')-$i),$days_in_month,date('Y')));

      if($i == $months)
      {
        // Fill up array of last month
        $days = $days_in_month - $day_today;
        for ($j = $day_today; $j <= $days_in_month; $j++) {
          $day = (strlen($j) == 1) ? '0'.$j: $j;
          $last_six_months[$year.$month.$day] = array('o_sum' => 0, 'o_count' => 0);
        }
      }
      elseif($i < $months && $i > 0)
      {
        // Fill up array of months in between
        for ($k = 1; $k <= $days_in_month; $k++) {
          $day = (strlen($k) == 1) ? '0'.$k: $k;
          $last_six_months[$year.$month.$day] = array('o_sum' => 0, 'o_count' => 0);
        }
      }
      else
      {
        // Fill up array of current month
        for ($l = 1; $l <= $day_today; $l++) {
          $day = (strlen($l) == 1) ? '0'.$l: $l;
          $last_six_months[$year.$month.$day] = array('o_sum' => 0, 'o_count' => 0);
        }
      }
    }

    // Add revenue data to zero values array
    foreach($raw_data as $key => $value)
    {
      $last_six_months[$value['o_date']]['o_sum']   = $value['o_sum'];
      $last_six_months[$value['o_date']]['o_count'] = $value['o_count'];
    }

    return $last_six_months;
  }

  /**
   * Show sales graphs page
   *
   * @param sfWebRequest $request
   */
  public function executeGraph(sfWebRequest $request)
  {
    // 6 months range (total income per day)
    $summary_data = $this->getGraphOrderSummaryRange();

    $counts_list = '';
    $sums_list   = '';
    $i           = 0;
    foreach($summary_data as $key => $value)
    {
      $separator = ($i > 0) ? ',' : '';
      $sums_list .= $separator.$value['o_sum'];
      $counts_list .= $separator.$value['o_count'];
      $i++;
    }

    $this->days_in_months            = implode(",", range(1,count($summary_data),1));
    $this->revenue_per_day_in_months = $sums_list;

    // 12 months range (total income/average order value per month)
    $total_data   = $this->getGraphOrderTotalRange();
    $average_data = $this->getGraphOrderAverageRange();

    $sums_total_month_list = '';
    $j = 0;
    foreach($total_data as $key => $value)
    {
      $separator = ($j > 0) ? ',' : '';
      $sums_total_month_list .= $separator.$value;
      $j++;
    }

    $average_order_list = '';
    $k = 0;
    foreach($average_data as $key => $value)
    {
      $separator = ($k > 0) ? ',' : '';
      $average_order_list .= $separator.$value;
      $k++;
    }

    $this->total_income_in_month  = $sums_total_month_list;
    $this->average_order_in_month = $average_order_list;
  }

  /**
   * Return array with total per months values for the specified months range
   * 
   * @param integer $months
   * @return arrau
   */
  public function getGraphOrderTotalRange($months = 12)
  {
    // Build empty months array
    $range_months = array();
    for($i=$months; $i>=1; $i--)
    {
      $days_in_month = date('t', mktime(0,0,0,(date('m')-$i),28,date('Y')));
      $month = date('m', mktime(0,0,0,(date('m')-$i),$days_in_month,date('Y')));
      $year  = date('Y', mktime(0,0,0,(date('m')-$i),$days_in_month,date('Y')));
      $range_months[$year.$month] = 0;
    }

    // Add data to emtpy month array where applicable
    foreach($this->getGraphOrderRangeData($months) as $key => $value)
    {
      $range_months[$value['o_date']] = $value['o_sum'];
    }

    return $range_months;
  }

  /**
   * Return array with average order value per months values for the specified months range
   *
   * @param integer $months
   * @return arrau
   */
  public function getGraphOrderAverageRange($months = 12)
  {
    // Build empty months array
    $range_months = array();
    for($i=$months; $i>=1; $i--)
    {
      $days_in_month = date('t', mktime(0,0,0,(date('m')-$i),28,date('Y')));
      $month = date('m', mktime(0,0,0,(date('m')-$i),$days_in_month,date('Y')));
      $year  = date('Y', mktime(0,0,0,(date('m')-$i),$days_in_month,date('Y')));
      $range_months[$year.$month] = 0;
    }

    // Add data to emtpy month array where applicable
    foreach($this->getGraphOrderRangeData($months) as $key => $value)
    {
      $range_months[$value['o_date']] = round($value['o_sum'] / $value['o_count'],2);
    }

    return $range_months;
  }

  /**
   * Return rtShopOrder date for specified months range
   * Range is from 1st this month to 1st of month range specified
   *
   * @see getGraphOrderAverageRange, getGraphOrderTotalRange
   * @param integer $months
   * @return array
   */
  protected function getGraphOrderRangeData($months)
  {
    $start_range = date('Y-m-d H:i:s', mktime(0,0,0,date('m'),1,date('Y')));
    $end_range   = date('Y-m-d H:i:s', mktime(0,0,0,(date('m')-$months),1,date('Y')));

    $query = Doctrine::getTable('rtShopOrder')->getQuery();
    $query->select('DATE_FORMAT(o.created_at,"%Y%m") as date, sum(o.total_charge), count(o.id)')
          ->andWhere('o.status != ?', rtShopOrder::STATUS_PENDING)
          ->andWhere('date(o.created_at) < ?', $start_range)
          ->andWhere('date(o.created_at) >= ?', $end_range)
          ->groupBy('date');

    return $query->execute(array(), Doctrine_Core::HYDRATE_SCALAR);
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
    // Retrieve active users with dispatch permission
    $dispatch_users = Doctrine::getTable('rtGuardUser')
                      ->getUsersArrayByPermissionQuery('admin_shop_order_dispatch')
                      ->execute(array(), Doctrine_Core::HYDRATE_SCALAR);

    // Stop if no dispatch user available
    if(count($dispatch_users) == 0)
    {
      return;
    }

    // Randomize selection
    shuffle($dispatch_users);
    $dispatch_user = $dispatch_users[0];

    // Send mail
    $vars = array('dispatch_user' => $dispatch_user);
    $vars['rt_shop_order'] = $rt_shop_order;
    $vars['user'] = $rt_shop_order->getBillingAddress() ? $rt_shop_order->getBillingAddress()->getData() : '';

    $message_html = $this->getPartial('rtShopOrderAdmin/email_status_dispatch_html', $vars);
    $message_html = $this->getPartial('rtEmail/layout_html', array('content' => $message_html));

    $message_plain = $this->getPartial('rtShopOrderAdmin/email_status_dispatch_plain', $vars);
    $message_plain = $this->getPartial('rtEmail/layout_plain', array('content' => html_entity_decode($message_plain)));

    $message = Swift_Message::newInstance()
               ->setFrom(sfConfig::get('app_rt_shop_order_admin_email', 'from@noreply.com'))
               ->setTo($dispatch_user['u_email_address'])
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
    $cred = sfConfig::get('app_rt_shop_order_dispatch_credential', 'admin_shop_order_dispatch');
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
      $this->getDispatcher($request)->notify(new sfEvent($this, 'doctrine.admin.save_object', array('object' => $rt_shop_order)));
      $this->redirect('rtShopOrderAdmin/edit?id='.$rt_shop_order->getId());
    }
  }

  /**
   * @return sfEventDispatcher
   */
  protected function getDispatcher(sfWebRequest $request)
  {
    return ProjectConfiguration::getActive()->getEventDispatcher(array('request' => $request));
  }
}