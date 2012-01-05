<?php
/*
 * This file is part of the reditype package.
 * (c) 2006-2008 digital Wranglers <steercms@wranglers.com.au>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * rtShopDeletePendingOrdersTask
 *
 * @package    rtShopPlugin
 * @author     Piers Warmers <piers@wranglers.com.au>
 * @author     Konny Zurcher <konny@wranglers.com.au>
 */

class rtShopDeletePendingOrdersTask extends sfDoctrineBaseTask
{
  private $_debug_verbose = true;
  private $_removed_orders = 0;
  private $_default_days = 2;

  /**
   * Configure
   *
   */
  protected function configure()
  {
    $this->addArguments(array(
      new sfCommandArgument('remove-orders-older-than', sfCommandArgument::OPTIONAL, 'Days to date before which all pending orders will be deleted'),
    ));

    $this->addOptions(array(
      new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name', 'frontend'),
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
      new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'doctrine'),
    ));

    $this->namespace        = 'rt';
    $this->name             = 'shop-delete-pending-orders';
    $this->briefDescription = 'Remove orders with status PENDING older than specified days';
  }

  /**
   * Execute function
   *
   * @param array $arguments Arguments
   * @param array $options  Options
   */
  protected function execute($arguments = array(), $options = array())
  {
    // Arguments
    if ($arguments['remove-orders-older-than'] == '') {
      $days = $this->_default_days;
    } else {
      $days = $arguments['remove-orders-older-than'];
    }

    // Validate the remove-orders-older-than name
    if (!preg_match('/^[0-9]*$/', $days))
    {
      throw new sfCommandException(sprintf('The remove-orders-older-than value "%s" is invalid (use integer).', $days));
    }

    // Database actions
    $databaseManager = new sfDatabaseManager($this->configuration);
    $q = Doctrine::getTable('rtShopOrder')->getPendingOlderThanQuery($days);
    $orders = $q->execute();

    if ($this->_debug_verbose) {
      $this->_removed_orders = count($orders);
      $this->log('-----------------------------------------------');
      $this->log('--- Remove PENDING orders older than '.$days.' days ---');
      $this->log('-----------------------------------------------');
      $this->logSection('remove-orders-older-than', sprintf('Orders older than %s days: [%s]',$days,count($orders)));

      if (count($orders) > 0)
      {
        foreach ($orders as $order) {
          $this->logSection('remove-orders-older-than', sprintf('Created_at: [%s] // Reference: [%s]',$order->getCreatedAt(),$order->getReference()));
          $order->delete();
        }
        $this->logSection('remove-orders-older-than', sprintf('Total removed pending orders: [%s]',$this->_removed_orders));
      } else {
        $this->logSection('remove-orders-older-than', 'No orders to delete');
      }
    } else {
      $orders->delete();
    }
  }
}