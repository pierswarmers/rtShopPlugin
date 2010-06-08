<?php

class rtShopStockValidatorSchema extends sfValidatorSchema
{
  protected function configure($options = array(), $messages = array())
  {
    $this->addOption('attributes');

    $attributes = isset($options['attributes']) ? $options['attributes'] : false;

    if(!$attributes)
    {
      throw new sfException('Attribute list is required.');
    }

    $this->setOption('attributes', $attributes);

    foreach($attributes as $attribute)
    {
      $this->addMessage('rt_shop_variations_list_'.$attribute->getId(), 'The '.$attribute->getDisplayTitle().' is required.');
    }
  }

  protected function doClean($values)
  {
    $errorSchema = new sfValidatorErrorSchema($this);


    foreach($values as $key => $value)
    {
      $errorSchemaLocal = new sfValidatorErrorSchema($this);
      
      if ($value['quantity'] && !$value['delete'])
      {
        foreach($this->getOption('attributes') as $attribute)
        {
          $this->addMessage($attribute->getId(), 'The '.$attribute->getDisplayTitle().' is required.');
          // quantity is filled but no variation selected
          if (!$value['rt_shop_variations_list_'.$attribute->getId()])
          {
            $errorSchemaLocal->addError(new sfValidatorError($this, 'required'), 'rt_shop_variations_list_'.$attribute->getId());
          }
        }
      }

      // no caption and no filename, remove the empty values
      if (!$value['quantity'] || $value['delete'])
      {
        unset($values[$key]);
      }

      // some error for this embedded-form
      if (count($errorSchemaLocal))
      {
        $errorSchema->addError($errorSchemaLocal, (string) $key);
      }
    }

    // throws the error for the main form
    if (count($errorSchema))
    {
      throw new sfValidatorErrorSchema($this, $errorSchema);
    }

    return $values;
  }
}