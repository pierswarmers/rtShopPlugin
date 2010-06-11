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
    $this->rt_shop_promotions = Doctrine::getTable('rtShopPromotion')
      ->createQuery('p')
      // Include all promotions appart from vouchures.
      ->andWhere('p.type != "rtShopVoucher"')
      ->execute();
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
  }
}