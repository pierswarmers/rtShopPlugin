<?php

/**
 * rtShopCategoryAdmin actions.
 *
 * @package    symfony
 * @subpackage rtShopCategoryAdmin
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class rtShopCategoryAdminActions extends sfActions
{
  public function executeIndex(sfWebRequest $request)
  {
    $query = Doctrine::getTable('rtShopCategory')->getQuery();
    $query->orderBy('page.root_id ASC, page.lft ASC');
    $this->rt_shop_categorys = $query->execute();
  }

  public function getrtShopCategory(sfWebRequest $request)
  {
    $this->forward404Unless($rt_shop_category = Doctrine::getTable('rtShopCategory')->find(array($request->getParameter('id'))), sprintf('Object rt_shop_category does not exist (%s).', $request->getParameter('id')));
    return $rt_shop_category;
  }
  
  public function executeTree(sfWebRequest $request)
  {
  }

  public function executeShow(sfWebRequest $request)
  {
    rtSiteToolkit::siteRedirect($this->getrtShopCategory($request));
  }
  
  public function executeNew(sfWebRequest $request)
  {
    $this->getUser()->setFlash('notice', 'Please select the tree to create the page in.');
    $this->redirect('rtShopCategoryAdmin/tree');
    $this->form = new rtShopCategoryForm();
  }

  public function executeCreate(sfWebRequest $request)
  {
    $this->forward404Unless($request->isMethod(sfRequest::POST));

    $this->form = new rtShopCategoryForm();

    $this->processForm($request, $this->form);

    $this->setTemplate('new');
  }

  public function executeEdit(sfWebRequest $request)
  {
    $this->forward404Unless($rt_shop_category = Doctrine::getTable('rtShopCategory')->find(array($request->getParameter('id'))), sprintf('Object rt_shop_category does not exist (%s).', $request->getParameter('id')));
    $this->form = new rtShopCategoryForm($rt_shop_category);
  }

  public function executeUpdate(sfWebRequest $request)
  {
    $this->forward404Unless($request->isMethod(sfRequest::POST) || $request->isMethod(sfRequest::PUT));
    $this->forward404Unless($rt_shop_category = Doctrine::getTable('rtShopCategory')->find(array($request->getParameter('id'))), sprintf('Object rt_shop_category does not exist (%s).', $request->getParameter('id')));
    $this->form = new rtShopCategoryForm($rt_shop_category);

    $this->processForm($request, $this->form);

    $this->setTemplate('edit');
  }

  public function executeDelete(sfWebRequest $request)
  {
    $request->checkCSRFProtection();

    $this->forward404Unless($rt_shop_category = Doctrine::getTable('rtShopCategory')->find(array($request->getParameter('id'))), sprintf('Object rt_shop_category does not exist (%s).', $request->getParameter('id')));

    $this->clearCache($rt_shop_category);
    $rt_shop_category->delete();

    $this->redirect('rtShopCategoryAdmin/index');
  }

  public function executeVersions(sfWebRequest $request)
  {
    $this->rt_shop_category = $this->getrtShopCategory($request);
    $this->rt_shop_category_versions = Doctrine::getTable('rtShopCategoryVersion')->findById($this->rt_shop_category->getId());
  }

  public function executeCompare(sfWebRequest $request)
  {
    $this->rt_shop_category = $this->getrtShopCategory($request);
    $this->current_version = $this->rt_shop_category->version;

    if(!$request->hasParameter('version1') || !$request->hasParameter('version2'))
    {
      $this->getUser()->setFlash('error', 'Please select two versions to compare.', false);
      $this->redirect('rtShopCategoryAdmin/versions?id='.$this->rt_shop_category->getId());
    }

    $this->version_1 = $request->getParameter('version1');
    $this->version_2 = $request->getParameter('version2');
    $this->versions = array();

    $this->versions[1] = array(
      'title' => $this->rt_shop_category->revert($this->version_1)->title,
      'content' => $this->rt_shop_category->revert($this->version_1)->content,
      'description' => $this->rt_shop_category->revert($this->version_1)->description,
      'updated_at' => $this->rt_shop_category->revert($this->version_1)->updated_at
    );
    $this->versions[2] = array(
      'title' => $this->rt_shop_category->revert($this->version_2)->title,
      'content' => $this->rt_shop_category->revert($this->version_2)->content,
      'description' => $this->rt_shop_category->revert($this->version_1)->description,
      'updated_at' => $this->rt_shop_category->revert($this->version_1)->updated_at
    );
  }

  public function executeRevert(sfWebRequest $request)
  {
    $this->rt_shop_category = $this->getrtShopCategory($request);
    $this->rt_shop_category->revert($request->getParameter('revert_to'));
    $this->rt_shop_category->save();
    $this->getUser()->setFlash('notice', 'Reverted to version ' . $request->getParameter('revert_to'), false);
    $this->clearCache($this->rt_shop_category);
    $this->redirect('rtShopCategoryAdmin/edit?id='.$this->rt_shop_category->getId());
  }
  
  protected function processForm(sfWebRequest $request, sfForm $form)
  {
    $form->bind($request->getParameter($form->getName()), $request->getFiles($form->getName()));
    if ($form->isValid())
    {
      $rt_shop_category = $form->save();
      $this->clearCache($rt_shop_category);

      $action = $request->getParameter('rt_post_save_action', 'index');

      if($action == 'edit')
      {
        $this->redirect('rtShopCategoryAdmin/edit?id='.$rt_shop_category->getId());
      }elseif($action == 'show')
      {
        $this->redirect('rt_shop_category_show',$rt_shop_category);
      }

      $this->redirect('rtShopCategoryAdmin/index');
    }
    $this->getUser()->setFlash('default_error', true, false);
  }

  private function clearCache($rt_shop_category = null)
  {
    rtShopCategoryCacheToolkit::clearCache($rt_shop_category);
  }
}
