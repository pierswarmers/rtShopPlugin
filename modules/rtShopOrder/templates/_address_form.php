<?php use_helper('I18N', 'rtForm') ?>
<?php use_stylesheets_for_form($form) ?>
<?php use_javascripts_for_form($form) ?>

<?php  echo $form->renderHiddenFields() ?>
<ul class="rt-form-schema">
  <li class="rt-form-row"><?php echo $form['first_name']->renderLabel() ?><div class="rt-form-field"><?php echo $form['first_name'] ?></div></li>
  <li class="rt-form-row"><?php echo $form['last_name']->renderLabel() ?><div class="rt-form-field"><?php echo $form['last_name'] ?></div></li>
  <li class="rt-form-row"><?php echo $form['address_1']->renderLabel() ?><div class="rt-form-field"><?php echo $form['address_1'] ?></div></li>
  <li class="rt-form-row"><?php echo $form['address_2']->renderLabel() ?><div class="rt-form-field"><?php echo $form['address_2'] ?></div></li>
  <li class="rt-form-row"><?php echo $form['town']->renderLabel() ?><div class="rt-form-field"><?php echo $form['town'] ?></div></li>
  <li class="rt-form-row"><?php echo $form['country']->renderLabel() ?><div class="rt-form-field"><?php echo $form['country'] ?></div></li>
  <li class="rt-form-row"><?php echo $form['state']->renderLabel() ?><div class="rt-form-field"><?php echo $form['state'] ?></div></li>
  <li class="rt-form-row"><?php echo $form['postcode']->renderLabel() ?><div class="rt-form-field"><?php echo $form['postcode'] ?></div></li>
  <li class="rt-form-row"><?php echo $form['phone']->renderLabel() ?><div class="rt-form-field"><?php echo $form['phone'] ?></div></li>
  <li class="rt-form-row"><?php echo $form['instructions']->renderLabel() ?><div class="rt-form-field"><?php echo $form['instructions'] ?></div></li>
</ul>

<script type="text/javascript">
  $(function() {
    $('#<?php echo $form->getName()?>_country').change(function() {

      var holder =  $('#<?php echo $form->getName()?>_state').parent();

      holder.html('<span class="loading">Loading states...</span>');
      $('#<?php echo $form->getName()?>_state').remove();
      $.ajax({
        type: "POST",
        url: '<?php echo url_for('rtAdmin/stateInput') ?>',
        data: ({
          country : $(this).find('option:selected').attr('value'),
          id      : '<?php echo $form->getName()?>_state',
          name    : '<?php echo $form->getName()?>[state]'
        }),
        dataType: "html",
        success: function(data) {
          holder.html(data);
        }
      });
    });
  });
</script>