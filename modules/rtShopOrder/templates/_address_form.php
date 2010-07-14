<?php use_helper('I18N', 'rtForm') ?>
<?php use_stylesheets_for_form($form) ?>
<?php use_javascripts_for_form($form) ?>

<?php  echo $form->renderHiddenFields() ?>
<table class="rt-admin-toggle-panel-content">
  <tbody>
    <?php echo render_form_row($form['first_name']); ?>
    <?php echo render_form_row($form['last_name']); ?>
    <?php echo render_form_row($form['address_1']); ?>
    <?php echo render_form_row($form['address_2']); ?>
    <?php echo render_form_row($form['town']); ?>
    <?php echo render_form_row($form['country']); ?>
    <?php echo render_form_row($form['state']); ?>
    <?php echo render_form_row($form['postcode']); ?>
    <?php echo render_form_row($form['phone']); ?>
    <?php echo render_form_row($form['instructions']); ?>
  </tbody>
</table>
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