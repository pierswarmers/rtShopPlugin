<?php
/*
 * This file is part of the reditype package.
 * (c) 2009-2010 Piers Warmers <piers@wranglers.com.au>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * rtShopAttributeAdminActions
 *
 * @package    rtShopPlugin
 * @subpackage modules
 * @author     Piers Warmers <piers@wranglers.com.au>
 * @author     Konny Zurcher <konny@wranglers.com.au>
 */
class rtShopAttributeAdminActions extends sfActions
{
  public function executeIndex(sfWebRequest $request)
  {
    $query = Doctrine::getTable('rtShopAttribute')->getQuery();
    $query->orderBy('attribute.title ASC');

    $this->pager = new sfDoctrinePager(
      'rtShopAttribute',
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
    $this->form = new rtShopAttributeForm();
  }

  public function executeCreate(sfWebRequest $request)
  {
    $this->forward404Unless($request->isMethod(sfRequest::POST));

    $this->form = new rtShopAttributeForm();

    $this->processForm($request, $this->form);

    $this->setTemplate('new');
  }

  public function executeEdit(sfWebRequest $request)
  {
    $this->forward404Unless($rt_shop_attribute = Doctrine::getTable('rtShopAttribute')->find(array($request->getParameter('id'))), sprintf('Object rt_shop_attribute does not exist (%s).', $request->getParameter('id')));
    $this->form = new rtShopAttributeForm($rt_shop_attribute);
  }

  public function executeUpdate(sfWebRequest $request)
  {
    $this->forward404Unless($request->isMethod(sfRequest::POST) || $request->isMethod(sfRequest::PUT));
    $this->forward404Unless($rt_shop_attribute = Doctrine::getTable('rtShopAttribute')->find(array($request->getParameter('id'))), sprintf('Object rt_shop_attribute does not exist (%s).', $request->getParameter('id')));
    $this->form = new rtShopAttributeForm($rt_shop_attribute);

    $this->processForm($request, $this->form);

    $this->setTemplate('edit');
  }

  public function executeDelete(sfWebRequest $request)
  {
    $request->checkCSRFProtection();

    $this->forward404Unless($rt_shop_attribute = Doctrine::getTable('rtShopAttribute')->find(array($request->getParameter('id'))), sprintf('Object rt_shop_attribute does not exist (%s).', $request->getParameter('id')));
    $rt_shop_attribute->delete();

    $this->redirect('rtShopAttributeAdmin/index');
  }

  protected function processForm(sfWebRequest $request, sfForm $form)
  {
    $form->bind($request->getParameter($form->getName()), $request->getFiles($form->getName()));
    if ($form->isValid())
    {
      $rt_shop_attribute = $form->save();

      // Clean product cache for linked products
      foreach($rt_shop_attribute->rtShopProducts as $rt_shop_product)
      {
        rtShopProductCacheToolkit::clearCache($rt_shop_product);
      }

      $action = $request->getParameter('rt_post_save_action', 'index');

      if($action == 'edit')
      {
        $this->redirect('rtShopAttributeAdmin/edit?id='.$rt_shop_attribute->getId());
      }

      $this->redirect('rtShopAttributeAdmin/index');
    }
    $this->getUser()->setFlash('default_error', true, false);
  }
}
