<?php
/*
 * This file is part of the reditype package.
 * (c) 2006-2008 digital Wranglers <steercms@wranglers.com.au>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * rtShopDeleteExpiredVouchersTask
 *
 * @package    rtShopPlugin
 * @author     Piers Warmers <piers@wranglers.com.au>
 * @author     Konny Zurcher <konny@wranglers.com.au>
 */

class rtShopDeleteExpiredVouchersTask extends sfDoctrineBaseTask
{
  private $_debug_verbose = true;
  private $_removed_vouchers = 0;

  /**
   * Configure
   *
   */
  protected function configure()
  {
    $this->addOptions(array(
      new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name', 'frontend'),
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
      new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'doctrine'),
    ));

    $this->namespace        = 'rt';
    $this->name             = 'shop-delete-expired-vouchers';
    $this->briefDescription = 'Remove expired vouchers based on date';
  }

  /**
   * Execute function
   *
   * @param array $arguments Arguments
   * @param array $options  Options
   */
  protected function execute($arguments = array(), $options = array())
  {
    // Database actions
    $databaseManager = new sfDatabaseManager($this->configuration);
    $q = Doctrine::getTable('rtShopVoucher')->getExpiredRescrictionQuery();
    $vouchers = $q->execute();

    $this->_removed_vouchers = count($vouchers);
    if ($this->_debug_verbose) {
      $this->log('---------------------------------------');
      $this->log('--- Remove expired vouchers by date ---');
      $this->log('---------------------------------------');

      if (count($vouchers) > 0)
      {
        foreach ($vouchers as $voucher) {
          $this->logSection('shop-delete-expired-vouchers', sprintf('Date_to: [%s] // Code: [%s]',$voucher->getDateTo(),$voucher->getCode()));
          $voucher->delete();
        }
      } else {
        $this->logSection('shop-delete-expired-vouchers', 'No expired vouchers to delete');
      }
    } else {
      $vouchers->delete();
    }
    $this->logSection('shop-delete-expired-vouchers', sprintf('Total removed expired vouchers: [%s]',$this->_removed_vouchers));
  }
}