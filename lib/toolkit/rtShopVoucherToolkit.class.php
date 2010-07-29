<?php
/*
 * This file is part of the rtShopPlugin package.
 * (c) 2006-2008 digital Wranglers <steercms@wranglers.com.au>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * rtShopVoucherToolkit
 *
 * @package    rtShopPlugin
 * @subpackage rtShopPluginTools
 * @author     Piers Warmers <piers@wranglers.com.au>
 * @author     Konny Zurcher <konny@wranglers.com.au>
 */

class rtShopVoucherToolkit
{
  /**
   * Generate voucher code of the form: A0BF67CEF6764
   *
   * @param Integer $length String length (default = 12)
   * @return String $code Voucher code
   */
  public static function generateVoucherCode($length = 8)
  {
    $code = md5(mt_rand().microtime());
    $code = substr($code, 0, $length);
    $code = strtoupper($code);
    $code = 'V'.substr($code, 0, -1);

    return $code;
  }

  /**
   * Return formatedd voucher code: A0BF-67CE-F676
   *
   * @param String $code Voucher code
   * @return String Formated vocuher code
   */
  public static function formatVoucherCode($code)
  {
    if (strlen($code) != 12) {
      return false;
    }
    $code = sprintf('%s-%s-%s',substr($code, 0, 4),substr($code, 4, 4),substr($code, 8, 4));
    return $code;
  }

  /**
   * Get applicable voucher
   *
   * @param String $voucher Code
   * @param String $date Order date
   * @return rtOrderVoucher
   */
  public static function getApplicable($voucher, $total, $date = null)
  {
    if(is_string($voucher))
    {
      $voucher = Doctrine::getTable('rtShopVoucher')->findValid($voucher, $total, $date);
    }
    
    if (count($voucher) > 0) {
      return Doctrine::getTable('rtShopVoucher')->find($voucher[0]['id']);
    }

    return false;
  }

  /**
   * Apply voucher to order total
   *
   * @param string|rtOrderVoucher $voucher
   * @param float  $total
   * @param rtOrderVoucher $voucher
   * @param string $date
   * @return float
   */
  public static function applyVoucher($voucher, $total, $date = null)
  {
    if(is_string($voucher))
    {
      $voucher = self::getApplicable($voucher, $total, $date);
    }

    if($voucher) {
      $reduction_type = $voucher->getReductionType();
      $reduction_value = $voucher->getReductionValue();
      switch ($reduction_type)
      {
        case 'percentageOff':
          $percentage = $reduction_value/100;
          $total = $total - ($total * $percentage);
          break;
        case 'dollarOff':
          $total = $total - $reduction_value;
          break;
      }
    }

    return $total;
  }

  /**
   * Create batches of vouchers
   *
   * @param Array $batch Batch data from form request
   */
  public static function generateBatch($voucher, $batchsize, $verbose = true)
  {
    if(!is_array($voucher))
    {
      throw new Exception('Batch create has to be supplied with an array supplied by rtBatchVoucherForm');
    }

    $table = Doctrine::getTable('rtShopPromotion');
    $sql_batch_limit = 1000;

    if($voucher['batch_reference'] == '')
    {
      $voucher['batch_reference'] = self::generateVoucherCode(8);
    }

    $voucher['type'] = 'rtShopVoucher';
    
    $values = array();
    $columns = array('date_from','date_to','reduction_type','reduction_value','title','type','batch_reference','count','mode','total_from','total_to','created_at','updated_at','code');

    // Add date_from, date_to fields only if set
    if($voucher['date_from'] == '' || $voucher['date_from'] == NULL)
    {
      unset($columns[0]);
    }
    if($voucher['date_to'] == '' || $voucher['date_to'] == NULL)
    {
      unset($columns[1]);
    }

    // Add total_from, total_to fields only if set
    if($voucher['total_from'] == '' || $voucher['total_from'] == NULL)
    {
      unset($columns[9]);
    }
    if($voucher['total_to'] == '' || $voucher['total_to'] == NULL)
    {
      unset($columns[10]);
    }

    foreach($columns as $key => $colname)
    {
      if(!array_key_exists($colname, $voucher) && !array_key_exists($colname, array('created_at' => null, 'updated_at' => null)))
      {
        if($verbose)
        {
          sfContext::getInstance()->getLogger()->err('{rtShopBatchVoucher} Input fieldname: '.$colname.' missing in ::generateBatch().');
        }
        return false;
      }
    }
    $rows = array($voucher['date_from'],$voucher['date_to'],$voucher['reduction_type'],$voucher['reduction_value'],$voucher['title'],$voucher['type'],$voucher['batch_reference'],$voucher['count'],$voucher['mode'],$voucher['total_from'],$voucher['total_to'],date('Y-m-d H:i:s'),date('Y-m-d H:i:s'));

    // Add date_from, date_to fields only if set
    if($voucher['date_from'] == '' || $voucher['date_from'] == NULL)
    {
      unset($rows[0]);
    }
    if($voucher['date_to'] == '' || $voucher['date_to'] == NULL)
    {
      unset($rows[1]);
    }

    // Add total_from, total_to fields only if set
    if($voucher['total_from'] == '' || $voucher['total_from'] == NULL)
    {
      unset($rows[9]);
    }
    if($voucher['total_to'] == '' || $voucher['total_to'] == NULL)
    {
      unset($rows[10]);
    }

    // How many main loops
    // (e.g 25 / 10 = 2.5)
    $divider = $batchsize/$sql_batch_limit;
    // The leftover from main loops
    // (e.g if $divider = 2.5 => 3 loops, but 3rd loop with less iterations)
    $leftover = ($divider-floor($divider))*$sql_batch_limit;
    
    // Doctrine connection
    $conn = Doctrine_Manager::connection();
    $dbh = $conn->getDbh();

    // Split loop into batches to avoid database memory problems
    for ($i = 1; $i <= ceil($divider); $i++) {
      $max = ($i < ceil($divider) || fmod($batchsize, $sql_batch_limit) == 0) ? $sql_batch_limit : $leftover;
      for($j = 1; $j <= $max; $j++)
      {
        $rows[13] = self::generateVoucherCode();
        $values[] = sprintf('("%s")', implode('", "', $rows));
      }
      if(isset($values))
      {
        $dbh->exec(sprintf('INSERT INTO %s (%s) VALUES %s', $table->getTableName(), implode(', ', $columns), implode(',', $values)));
      }
      unset($values);
    }

    if($verbose)
    {
      sfContext::getInstance()->getLogger()->notice('{rtShopBatchVoucher} '.$batchsize.' vouchers with reference: '.$voucher['batch_reference'].' were successfully created. Details: '.serialize($voucher));
    }
    return $voucher['batch_reference'];
  }

  /**
   * Generate CSV file from supplied batch voucher data
   *
   * @param Array $reference Array in which values are strings with comma separated comlumns
   */
  public static function generateCsvFile($reference)
  {
    $q = Doctrine_Query::create()
            ->select('v.code')
            ->from('rtShopVoucher v')
            ->addWhere('v.batch_reference = ?', $reference);
    $vouchers = $q->fetchArray();

    $list = array();
    foreach($vouchers as $key => $value)
    {
      $list[] = $value['code'];
    }

    $location = sys_get_temp_dir().DIRECTORY_SEPARATOR;
    $file = $reference.'.csv';
    $filepath = $location.$file;

    $fp = fopen($filepath, 'w');
    foreach ($list as $line) {
      fputcsv($fp, split(',', $line));
    }
    fclose($fp);

    if(!is_file($filepath))
    {
      return false;
    }

    return true;
  }
}