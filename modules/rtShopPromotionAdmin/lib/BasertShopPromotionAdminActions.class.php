<?php
/*
 * This file is part of the reditype package.
 * (c) 2009-2010 Piers Warmers <piers@wranglers.com.au>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * BasertShopPromotionAdminActions
 *
 * @package    rtShopPlugin
 * @subpackage modules
 * @author     Piers Warmers <piers@wranglers.com.au>
 * @author     Konny Zurcher <konny@wranglers.com.au>
 */
class BasertShopPromotionAdminActions extends sfActions
{
  public function preExecute()
  {
    rtTemplateToolkit::setTemplateForMode('backend');
    sfConfig::set('app_rt_node_title', 'Promotions');
  }

  public function executeIndex(sfWebRequest $request)
  {
    $query = Doctrine::getTable('rtShopPromotion')->getQuery();
    $query->andWhere('p.type != "rtShopVoucher"');
    $query->orderBy('p.created_at DESC');

    $this->pager = new sfDoctrinePager(
      'rtShopPromotion',
      $this->getCountPerPage($request)
    );

    $this->pager->setQuery($query);
    $this->pager->setPage($request->getParameter('page', 1));
    $this->pager->init();

    $this->stats = $this->stats();
  }

  private function stats()
  {
    // Date
    $date_now = date("Y-m-d H:i:s");

    // SQL queries
    $con = Doctrine_Manager::getInstance()->getCurrentConnection();

    $result_promotions_total             = $con->fetchAssoc("select count(id) as count from rt_shop_promotion where type != 'rtShopVoucher'");
    $result_promotions_active            = $con->fetchAssoc("select count(id) as count from rt_shop_promotion where type != 'rtShopVoucher' and (date_from <= '".$date_now."' OR date_from IS NULL) and (date_to > '".$date_now."' OR date_to IS NULL)");
    $result_promotions_total_cart        = $con->fetchAssoc("select count(id) as count from rt_shop_promotion where type = 'rtShopPromotionCart'");
    $result_promotions_total_product     = $con->fetchAssoc("select count(id) as count from rt_shop_promotion where type = 'rtShopPromotionProduct'");

    // Create array
    $stats = array();
    $stats['total']          = $result_promotions_total[0] != '' ? $result_promotions_total[0] : 0;
    $stats['total_active']   = $result_promotions_active[0] != '' ? $result_promotions_active[0] : 0;
    $stats['total_cart']     = $result_promotions_total_cart[0] != '' ? $result_promotions_total_cart[0] : 0;
    $stats['total_product']  = $result_promotions_total_product[0] != '' ? $result_promotions_total_product[0] : 0;

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
    $this->form = new rtShopPromotionForm();
  }

  public function executeCreate(sfWebRequest $request)
  {
    $this->forward404Unless($request->isMethod(sfRequest::POST));

    $this->form = new rtShopPromotionForm();

    $this->processForm($request, $this->form);

    $this->setTemplate('new');
  }

  public function executeEdit(sfWebRequest $request)
  {
    $this->forward404Unless($rt_shop_promotion = Doctrine::getTable('rtShopPromotion')->find(array($request->getParameter('id'))), sprintf('Object rt_shop_promotion does not exist (%s).', $request->getParameter('id')));
    $this->form = new rtShopPromotionForm($rt_shop_promotion);
  }

  public function executeUpdate(sfWebRequest $request)
  {
    $this->forward404Unless($request->isMethod(sfRequest::POST) || $request->isMethod(sfRequest::PUT));
    $this->forward404Unless($rt_shop_promotion = Doctrine::getTable('rtShopPromotion')->find(array($request->getParameter('id'))), sprintf('Object rt_shop_promotion does not exist (%s).', $request->getParameter('id')));
    $this->form = new rtShopPromotionForm($rt_shop_promotion);

    $this->processForm($request, $this->form);

    $this->setTemplate('edit');
  }

  public function executeDelete(sfWebRequest $request)
  {
    $request->checkCSRFProtection();

    $this->forward404Unless($rt_shop_promotion = Doctrine::getTable('rtShopPromotion')->find(array($request->getParameter('id'))), sprintf('Object rt_shop_promotion does not exist (%s).', $request->getParameter('id')));
    $rt_shop_promotion->delete();

    $this->redirect('rtShopPromotionAdmin/index');
  }

  protected function processForm(sfWebRequest $request, sfForm $form)
  {
    $form->bind($request->getParameter($form->getName()), $request->getFiles($form->getName()));
    if ($form->isValid())
    {
      $rt_shop_promotion = $form->save();

      $action = $request->getParameter('rt_post_save_action', 'index');

      if($action == 'edit')
      {
        $this->redirect('rtShopPromotionAdmin/edit?id='.$rt_shop_promotion->getId());
      }

      $this->redirect('rtShopPromotionAdmin/index');
    }
    $this->getUser()->setFlash('default_error', true, false);
  }
}