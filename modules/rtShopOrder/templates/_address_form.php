<?php use_helper('I18N', 'rtForm') ?>
<?php use_stylesheets_for_form($form) ?>
<?php use_javascripts_for_form($form) ?>

<?php  echo $form->renderHiddenFields() ?>

<ul class="rt-form-schema">
<?php echo $form ?>
</ul>
