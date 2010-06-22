<?php

/**
 * rtShopCategory actions.
 *
 * @package    symfony
 * @subpackage rtShopCategory
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class BasertShopCategoryActions extends sfActions
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

  /**
   * Show the root level category.
   *
   * @param sfWebRequest $request
   */
  public function executeIndex(sfWebRequest $request)
  {
    $this->setTemplate('show');
    $this->runShow(Doctrine::getTable('rtShopCategory')->findRoot(), $request);
  }

  /**
   * Show a category, retrieved from the route.
   *
   * @param sfWebRequest $request
   */
  public function executeShow(sfWebRequest $request)
  {
    $rt_shop_category = $this->getRoute()->getObject();
    $this->forward404Unless($rt_shop_category);
    $this->redirectIf($rt_shop_category->getNode()->isRoot(), 'rt_shop_category_index');
    $this->runShow($rt_shop_category, $request);
  }

  /**
   * Run the common show logic which is shared between executeIndex() and executeShow().
   *
   * @param rtShopCategory $rt_shop_category
   * @param sfWebRequest $request
   */
  public function runShow(rtShopCategory $rt_shop_category, sfWebRequest $request)
  {
    $this->rt_shop_category = $rt_shop_category;
    $this->forward404Unless($this->rt_shop_category);
    $this->forward404If(!$this->rt_shop_category->isPublished() && !$this->isAdmin(),'Category isn\'t published.');

    $this->updateResponse($this->rt_shop_category);

    $query = Doctrine::getTable('rtShopProduct')->addPublishedQuery()
             ->leftJoin('page.rtShopCategories c')
             ->andWhere('c.id = ?', $this->rt_shop_category->getId());

    $this->pager = new sfDoctrinePager('rtShopProduct', sfConfig::get('app_rt_shop_product_per_page', 10));
    $this->pager->setQuery($query);
    $this->pager->setPage($request->getParameter('page', 1));
    $this->pager->init();
  }

  /**
   * Set the header information.
   *
   * @param rtShopCategory $page
   */
  private function updateResponse(rtShopCategory $page)
  {
    rtResponseToolkit::setCommonMetasFromPage($page, $this->getUser(), $this->getResponse());
  }

  /**
   * Is the current user an admin user.
   *
   * @return boolean
   */
  private function isAdmin()
  {
    return $this->getUser()->hasCredential(sfConfig::get('app_rt_site_admin_credential', 'admin_site'));
  }
}