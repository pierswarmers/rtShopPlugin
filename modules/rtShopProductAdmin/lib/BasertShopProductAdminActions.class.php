<?php
/*
 * This file is part of the reditype package.
 * (c) 2009-2010 Piers Warmers <piers@wranglers.com.au>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * BasertShopProductAdminActions
 *
 * @package    rtShopPlugin
 * @subpackage modules
 * @author     Piers Warmers <piers@wranglers.com.au>
 * @author     Konny Zurcher <konny@wranglers.com.au>
 */
class BasertShopProductAdminActions extends sfActions
{
  /**
   * Create stock report
   * 
   * Formats are: web, csv, xml and json
   *
   * @param sfWebRequest $request
   */
  public function executeStockReport(sfWebRequest $request)
  {
    $q = Doctrine_Query::create()->from('rtShopStock s');
    $q->select('p.title,
                p.sku,
                s.sku,
                s.quantity,
                s.id,
                s.product_id,
                s.price_retail,
                s.price_promotion,
                s.price_wholesale,
                s.length,
                s.width,
                s.height,
                s.weight,                
                sv.stock_id,
                v.title')
      ->leftJoin('s.rtShopProduct p')
      ->andWhere('p.id = s.product_id')
      ->leftJoin('s.rtShopStockToVariation sv')
      ->leftJoin('sv.rtShopVariation v')
      ->andWhere('sv.stock_id = s.id')
      ->orderBy('p.sku, s.sku, v.attribute_id');
    $stocks = $q->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

    // Create custom array
    $this->key_order = array('s_id', 's_sku', 's_quantity', 's_price_retail', 's_price_promotion', 's_price_wholesale', 's_length', 's_width', 's_height', 's_weight', 'p_id', 'p_sku', 'p_title', 'v_variations');
    $this->stocks = array();
    $i=0;
    foreach($stocks as $stock)
    {
      $this->stocks[$i]['s_id'] = $stock['id'];
      $this->stocks[$i]['s_sku'] = $stock['sku'];
      $this->stocks[$i]['s_quantity'] = $stock['quantity'];
      $this->stocks[$i]['s_price_retail'] = $stock['price_retail'];
      $this->stocks[$i]['s_price_promotion'] = $stock['price_promotion'];
      $this->stocks[$i]['s_price_wholesale'] = $stock['price_wholesale'];
      $this->stocks[$i]['s_length'] = $stock['length'];
      $this->stocks[$i]['s_width'] = $stock['width'];
      $this->stocks[$i]['s_height'] = $stock['height'];
      $this->stocks[$i]['s_weight'] = $stock['weight'];
      $this->stocks[$i]['p_id'] = $stock['rtShopProduct']['id'];
      $this->stocks[$i]['p_sku'] = $stock['rtShopProduct']['sku'];
      $this->stocks[$i]['p_title'] = preg_replace('/[\$,]/', '', $stock['rtShopProduct']['title']);
      $variations = '';
      foreach($stock['rtShopStockToVariation'] as $variation)
      {
        $variations .= $variation['rtShopVariation']['title'].' / ';
      }
      $this->stocks[$i]['v_variations'] = substr($variations, 0, -3);
      $i++;
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
        $response->setHttpHeader('Content-Disposition','inline; filename="stock_report.csv"');
      }
      else
      {
        $response->setHttpHeader('Content-Disposition','attachment; filename="stock_report.csv"');
      }
      
      $this->setLayout(false);
    }

    // Pager
    $this->pager = new sfDoctrinePager(
      'rtShopStock',
      $this->getReportCountPerPage($request)
    );
    $this->pager->setQuery($q);
    $this->pager->setPage($request->getParameter('page', 1));
    $this->pager->init();
  }

  private function getReportCountPerPage(sfWebRequest $request)
  {
    $count = sfConfig::get('app_rt_admin_report_pagination_limit', 200);
    if($request->hasParameter('show_more'))
    {
      $count = sfConfig::get('app_rt_admin_pagination_per_page_multiple', 2) * $count;
    }

    return $count;
  }

  public function executeIndex(sfWebRequest $request)
  {
    $query = Doctrine::getTable('rtShopProduct')->getQuery();
    $query->orderBy('page.created_at DESC');

    $this->pager = new sfDoctrinePager(
      'rtShopProduct',
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

    // SQL queries
    $con = Doctrine_Manager::getInstance()->getCurrentConnection();

    $result_products_total               = $con->fetchAssoc("select count(id) as count from rt_shop_product");
    $result_products_total_published     = $con->fetchAssoc("select count(id) as count from rt_shop_product where published = 1 and (published_from <= '".$date_now."' OR published_from IS NULL) and (published_to > '".$date_now."' OR published_to IS NULL)");

    // Create array
    $stats = array();
    $stats['total']                = $result_products_total[0] != '' ? $result_products_total[0] : 0;
    $stats['total_published']      = $result_products_total_published[0] != '' ? $result_products_total_published[0] : 0;

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

  public function getrtShopProduct(sfWebRequest $request)
  {
    $this->forward404Unless($rt_shop_product = Doctrine::getTable('rtShopProduct')->find(array($request->getParameter('id'))), sprintf('Object rt_shop_product does not exist (%s).', $request->getParameter('id')));
    return $rt_shop_product;
  }

  public function executeShow(sfWebRequest $request)
  {
    rtSiteToolkit::siteRedirect($this->getrtShopProduct($request));
  }

  public function executeNew(sfWebRequest $request)
  {
    $this->form = new rtShopProductForm();
  }

  public function executeCreate(sfWebRequest $request)
  {
    $this->forward404Unless($request->isMethod(sfRequest::POST));

    $this->form = new rtShopProductForm();

    $this->processForm($request, $this->form);

    $this->setTemplate('new');
  }

  public function executeEdit(sfWebRequest $request)
  {
    $this->forward404Unless($rt_shop_product = Doctrine::getTable('rtShopProduct')->find(array($request->getParameter('id'))), sprintf('Object rt_shop_product does not exist (%s).', $request->getParameter('id')));
    $this->form = new rtShopProductForm($rt_shop_product);
  }

  public function executeUpdate(sfWebRequest $request)
  {
    $this->forward404Unless($request->isMethod(sfRequest::POST) || $request->isMethod(sfRequest::PUT));
    $this->forward404Unless($rt_shop_product = Doctrine::getTable('rtShopProduct')->find(array($request->getParameter('id'))), sprintf('Object rt_shop_product does not exist (%s).', $request->getParameter('id')));
    $this->form = new rtShopProductForm($rt_shop_product);

    $this->processForm($request, $this->form);

    $this->setTemplate('edit');
  }

  public function executeDelete(sfWebRequest $request)
  {
    $request->checkCSRFProtection();

    $this->forward404Unless($rt_shop_product = Doctrine::getTable('rtShopProduct')->find(array($request->getParameter('id'))), sprintf('Object rt_shop_product does not exist (%s).', $request->getParameter('id')));
    $rt_shop_product->delete();

    $this->redirect('rtShopProductAdmin/index');
  }

  public function executeStock(sfWebRequest $request)
  {
    $this->forward404Unless($rt_shop_product = Doctrine::getTable('rtShopProduct')->find(array($request->getParameter('id'))), sprintf('Object rt_shop_product does not exist (%s).', $request->getParameter('id')));

    $count = 1;
    
    if($request->isMethod('POST'))
    {
      $vals = $request->getParameter('rt_shop_product');
      $count = $vals['newRows'];
    }

    $this->form = new rtShopStockCollectionForm($rt_shop_product, array('newRows' => $count));

    if($request->isMethod('POST'))
    {
      $this->form->bind($request->getParameter($this->form->getName()), $request->getFiles($this->form->getName()));
      if ($this->form->isValid())
      {
        $rt_shop_product = $this->form->save();
        $this->clearCache($rt_shop_product);

        $action = $request->getParameter('rt_post_save_action', 'index');

        if($action == 'edit')
        {
          $this->redirect('rtShopProductAdmin/stock?id='.$rt_shop_product->getId());
        }

        $this->redirect('rtShopProductAdmin/edit?id='.$rt_shop_product->getId());
      }
    }

    $this->rt_shop_product = $rt_shop_product;
  }

  public function executeStockRow(sfWebRequest $request)
  {
    $this->forward404Unless($rt_shop_product = Doctrine::getTable('rtShopProduct')->find(array($request->getParameter('id'))), sprintf('Object rt_shop_product does not exist (%s).', $request->getParameter('id')));
    $this->rt_shop_product = $rt_shop_product;
    $newRows = $request->getParameter('count');

    $this->attributes = $this->attributes = Doctrine::getTable('rtShopAttribute')->findByProductId($rt_shop_product->getId());;
    
    $rt_shop_stock  = new rtShopStock();
    $rt_shop_stock->setProductId($rt_shop_product->getId());

    $stock_form = new rtShopStockForm($rt_shop_stock, array('name_format' => 'rt_shop_product[newStocks]['.$newRows.'][%s]'));

    $this->setLayout(false);
    sfConfig::set('sf_debug', false);

    $this->stock_form = $stock_form;
  }

  public function executeVersions(sfWebRequest $request)
  {
    $this->rt_shop_product = $this->getrtShopProduct($request);
    $this->rt_shop_product_versions = Doctrine::getTable('rtShopProductVersion')->findById($this->rt_shop_product->getId());
  }

  public function executeCompare(sfWebRequest $request)
  {
    $this->rt_shop_product = $this->getrtShopProduct($request);
    $this->current_version = $this->rt_shop_product->version;

    if(!$request->hasParameter('version1') || !$request->hasParameter('version2'))
    {
      $this->getUser()->setFlash('error', 'Please select two versions to compare.', false);
      $this->redirect('rtShopProductAdmin/versions?id='.$this->rt_shop_product->getId());
    }

    $this->version_1 = $request->getParameter('version1');
    $this->version_2 = $request->getParameter('version2');
    $this->versions = array();

    $this->versions[1] = array(
      'title' => $this->rt_shop_product->revert($this->version_1)->title,
      'content' => $this->rt_shop_product->revert($this->version_1)->content,
      'description' => $this->rt_shop_product->revert($this->version_1)->description,
      'updated_at' => $this->rt_shop_product->revert($this->version_1)->updated_at
    );
    $this->versions[2] = array(
      'title' => $this->rt_shop_product->revert($this->version_2)->title,
      'content' => $this->rt_shop_product->revert($this->version_2)->content,
      'description' => $this->rt_shop_product->revert($this->version_1)->description,
      'updated_at' => $this->rt_shop_product->revert($this->version_1)->updated_at
    );
  }

  public function executeRevert(sfWebRequest $request)
  {
    $this->rt_shop_product = $this->getrtShopProduct($request);
    $this->rt_shop_product->revert($request->getParameter('revert_to'));
    $this->rt_shop_product->save();
    $this->getUser()->setFlash('notice', 'Reverted to version ' . $request->getParameter('revert_to'), false);
    $this->clearCache($this->rt_shop_product);
    $this->redirect('rtShopProductAdmin/edit?id='.$this->rt_shop_product->getId());
  }

  public function executeToggle(sfWebRequest $request)
  {
    $rt_shop_product = Doctrine_Core::getTable('rtShopProduct')->find(array($request->getParameter('id')));
    if(!$rt_shop_product)
    {
      $this->status = 'error';
      return sfView::SUCCESS;
    }

    $rt_shop_product->setPublished(!$rt_shop_product->getPublished());
    $this->status = $rt_shop_product->getPublished() ? 'activated' : 'deactivated';
    $rt_shop_product->save();
    $this->clearCache($rt_shop_product);
  }

  /**
   * Return products as JSON reponse for product selections
   *
   * @param sfWebRequest $request
   * @return JSON
   */
  public function executeProductSelect(sfWebRequest $request)
  {
    $this->getResponse()->setContentType('application/json');

    $query = Doctrine::getTable('rtIndex')->getBaseSearchQuery($request->getParameter('q'), $this->getUser()->getCulture());
    $query->andWhere('i.model = ?', 'rtShopProduct');
    $products_raw = $query->execute();

    $products = array();
    foreach ($products_raw as $product)
    {
      //$products[$product->getId()] = $product->getTitle();
      $products[$product->getModelId()] = (string) $product;
    }
    
    return $this->renderText(json_encode($products));
  }

  protected function processForm(sfWebRequest $request, sfForm $form)
  {
    $form->bind($request->getParameter($form->getName()), $request->getFiles($form->getName()));
    if ($form->isValid())
    {
      $rt_shop_product = $form->save();
      $this->clearCache($rt_shop_product);

      $action = $request->getParameter('rt_post_save_action', 'index');

      if($action == 'edit')
      {
        $this->redirect('rtShopProductAdmin/edit?id='.$rt_shop_product->getId());
      }elseif($action == 'show')
      {
        $this->redirect('rt_shop_product_show',$rt_shop_product);
      }

      $this->redirect('rtShopProductAdmin/index');
    }

    $this->getUser()->setFlash('default_error', true, false);
  }

  private function clearCache($rt_shop_product = null)
  {
    rtShopProductCacheToolkit::clearCache($rt_shop_product);
  }

}
