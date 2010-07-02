<?php

/**
 * PluginrtShopVariation form.
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 * @version    SVN: $Id: sfDoctrineFormPluginTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
abstract class PluginrtShopVariationForm extends BasertShopVariationForm
{
  public function setup()
  {
    parent::setup();

    $this->useFields(array('position', 'title', 'image'));

    $this->setWidget('position', new sfWidgetFormInputText(array(), array('style' => 'width:50px')));

    $file_exists = false;
    $path = '';
    
    if(!$this->isNew())
    {
      $this->setWidget('delete', new sfWidgetFormInputCheckbox());
      $this->validatorSchema['delete'] = new sfValidatorBoolean();
      $this->setWidget('image_delete', new sfWidgetFormInputCheckbox());
      $this->validatorSchema['image_delete'] = new sfValidatorBoolean();

      $file_location =sfConfig::get('sf_upload_dir') . '/variations/'.$this->getObject()->image;

      if(is_file($file_location))
      {
        $file_exists = true;
        $path = rtAssetToolkit::getThumbnailPath(sfConfig::get('sf_upload_dir') . '/variations/'.$this->getObject()->image, array('maxWidth' => 30, 'maxHeight' => 30));
      }
    }

    $template = '%input%';

    if($file_exists)
    {
      $template = '<div style="float:left; margin-right:10px;">%file%</div> %input%<br />%delete% %delete_label%';
    }

    $this->setWidget('image', new sfWidgetFormInputFileEditable(array(
      'file_src'    => $path,
      'edit_mode'   => !$this->isNew(),
      'is_image'    => true,
      'with_delete' => $this->getObject()->image ? true : false,
      'template'    => $template
    )));
    $this->setValidator('image', new sfValidatorFile(array(
      'mime_types' => 'web_images',
      'required' => false,
      'path' => sfConfig::get('sf_upload_dir').'/variations',
    ),
    array('mime_types' => 'Wrong type of file... should be a jpg, gif or png.')));

    $this->validatorSchema['position']->setOption('required', false);

    $pattern = '/^[a-zA-Z0-9 (),.:\"\/\'&#@\-\|!?+=*_]{1,}$/';
    $this->setValidator('title', new sfValidatorRegex(array('pattern' => $pattern, 'required' => false)));

    $this->validatorSchema['title']->setOption('required', false);
  }
}
