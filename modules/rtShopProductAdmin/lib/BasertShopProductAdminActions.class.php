<?php

/**
 * rtShopProductAdmin actions.
 *
 * @package    symfony
 * @subpackage rtShopProductAdmin
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class BasertShopProductAdminActions extends sfActions
{
  public function executeIndex(sfWebRequest $request)
  {
    $this->rt_shop_products = Doctrine::getTable('rtShopProduct')
      ->createQuery('a')
      ->execute();
  }

  public function getrtShopProduct(sfWebRequest $request)
  {
    $this->forward404Unless($rt_shop_product = Doctrine::getTable('rtShopProduct')->find(array($request->getParameter('id'))), sprintf('Object rt_shop_product does not exist (%s).', $request->getParameter('id')));
    return $rt_shop_product;
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
      $this->redirect('rtShopProduct/versions?id='.$this->rt_shop_product->getId());
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

  protected function processForm(sfWebRequest $request, sfForm $form)
  {
    $form->bind($request->getParameter($form->getName()), $request->getFiles($form->getName()));
    if ($form->isValid())
    {
      $rt_shop_product = $form->save();
      $this->clearCache($rt_shop_product);

      $this->redirect('rtShopProductAdmin/index');
    }
  }

  private function clearCache($rt_shop_product = null)
  {
    $cache = $this->getContext()->getViewCacheManager();

    if ($cache)
    {
      $cache->remove('rtShopProduct/index'); // index page
      $cache->remove('rtShopProduct/index?page=*'); // index with page
      $cache->remove('rtShopProduct/feed?format=*'); // feed
      $cache->remove('@sf_cache_partial?module=rtShopProduct&action=_latest&sf_cache_key=*');

      if($rt_shop_product)
      {
        $cache->remove(sprintf('rtShopProduct/show?id=%s&slug=%s', $rt_shop_product->getId(), $rt_shop_product->getSlug())); // show page
        $cache->remove('@sf_cache_partial?module=rtShopProduct&action=_shop_product&sf_cache_key='.$rt_shop_product->getId()); // show page partial.
      }
    }
  }
}
