<?php

/**
 * rtShopOrderAdmin actions.
 *
 * @package    reditype
 * @subpackage rtShopOrderAdmin
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class rtShopOrderAdminActions extends sfActions
{
  public function executeIndex(sfWebRequest $request)
  {
    $this->rt_shop_orders = Doctrine::getTable('rtShopOrder')
      ->createQuery('a')
      ->execute();
  }

  public function executeNew(sfWebRequest $request)
  {
    $this->form = new rtShopOrderForm();
  }

  public function executeCreate(sfWebRequest $request)
  {
    $this->forward404Unless($request->isMethod(sfRequest::POST));

    $this->form = new rtShopOrderForm();

    $this->processForm($request, $this->form);

    $this->setTemplate('new');
  }

  public function executeEdit(sfWebRequest $request)
  {
    $this->forward404Unless($rt_shop_order = Doctrine::getTable('rtShopOrder')->find(array($request->getParameter('id'))), sprintf('Object rt_shop_order does not exist (%s).', $request->getParameter('id')));
    $this->rt_shop_order = $rt_shop_order;
    $this->form = new rtShopOrderForm($rt_shop_order);
  }

  public function executeUpdate(sfWebRequest $request)
  {
    $this->forward404Unless($request->isMethod(sfRequest::POST) || $request->isMethod(sfRequest::PUT));
    $this->forward404Unless($rt_shop_order = Doctrine::getTable('rtShopOrder')->find(array($request->getParameter('id'))), sprintf('Object rt_shop_order does not exist (%s).', $request->getParameter('id')));
    $this->rt_shop_order = $rt_shop_order;
    $this->form = new rtShopOrderForm($rt_shop_order);

    $this->processForm($request, $this->form);

    $this->setTemplate('edit');
  }

  public function executeDelete(sfWebRequest $request)
  {
    $request->checkCSRFProtection();

    $this->forward404Unless($rt_shop_order = Doctrine::getTable('rtShopOrder')->find(array($request->getParameter('id'))), sprintf('Object rt_shop_order does not exist (%s).', $request->getParameter('id')));
    $rt_shop_order->delete();

    $this->redirect('rtShopOrderAdmin/index');
  }

  protected function processForm(sfWebRequest $request, sfForm $form)
  {
    $form->bind($request->getParameter($form->getName()), $request->getFiles($form->getName()));
    if ($form->isValid())
    {
      $rt_shop_order = $form->save();

      $this->redirect('rtShopOrderAdmin/edit?id='.$rt_shop_order->getId());
    }
  }
}
