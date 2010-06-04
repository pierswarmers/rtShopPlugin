<?php

class rtShopVariationValidatorSchema extends sfValidatorSchema
{
  protected function configure($options = array(), $messages = array())
  {
    $this->addMessage('title', 'The title is required.');
  }

  protected function doClean($values)
  {
    $errorSchema = new sfValidatorErrorSchema($this);

    foreach($values as $key => $value)
    {
      $errorSchemaLocal = new sfValidatorErrorSchema($this);

      // no caption and no filename, remove the empty values
      if (!$value['title'])
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