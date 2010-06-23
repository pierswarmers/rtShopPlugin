<?php

class rtShopFilter extends sfFilter
{
  /**
   * Executes the filter chain, returning the response content with an additional admin toolbar.
   * 
   * @param sfFilterChain $filterChain
   */
  public function execute(sfFilterChain $filterChain)
  {
    $filterChain->execute();

    $context = sfContext::getInstance();

    $user = $context->getUser();

    $ph = $context->getRequest()->getParameterHolder();

    if($ph->get('module') === 'rtShopCategory' && $ph->get('action') === 'show')
    {
      $user->setAttribute('rt_shop_category_id', $ph->get('id'));
    }
  }
}