<?php

/**
 * rtShopCategory actions.
 *
 * @package    symfony
 * @subpackage rtShopCategory
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class rtShopCategoryActions extends sfActions
{
  /**
   * Executes an application defined process prior to execution of this sfAction object.
   *
   * By default, this method is empty.
   */
  public function preExecute()
  {
    sfConfig::set('app_rt_node_title', 'Shop Category');
    rtTemplateToolkit::setFrontendTemplateDir();
  }
  
  public function executeIndex(sfWebRequest $request)
  {
    $this->rt_shop_category = Doctrine::getTable('rtShopCategory')->findRoot();
    $this->forward404Unless($this->rt_shop_category);
    $this->setTemplate('show');
  }

  public function executeShow(sfWebRequest $request)
  {
    $this->rt_shop_category = $this->getRoute()->getObject();

    $this->forward404Unless($this->rt_shop_category);

    if($this->rt_shop_category->getNode()->isRoot())
    {
      $this->redirect('rt_shop_category_index');
    }

    if(!$this->rt_shop_category->isPublished() && !$this->isAdmin())
    {
      $this->forward404('Page isn\'t published.');
    }

    $this->updateResponse($this->rt_shop_category);

    $pager = new sfDoctrinePager('rtShopProduct', sfConfig::get('app_rt_shop_product_per_page', 1));

    $query = Doctrine::getTable('rtShopProduct')->getQuery();
    $query->leftJoin('p.rtShopCategories c');
    $query->andWhere('c.id = ?', $this->rt_shop_category->getId());
    $pager->setQuery($query);
    $pager->setPage($request->getParameter('page', 1));
    $pager->init();


    $this->pager = $pager;
  }

  private function updateResponse(rtShopCategory $page)
  {
    rtResponseToolkit::setCommonMetasFromPage($page, $this->getUser(), $this->getResponse());
  }

  private function isAdmin()
  {
    return $this->getUser()->hasCredential(sfConfig::get('app_rt_site_admin_credential', 'admin_site'));
  }
}
