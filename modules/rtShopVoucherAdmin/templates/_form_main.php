
  <div class="rt-admin-toggle-panel">
    <h2><?php echo __('Advanced Configuration') ?></h2>
    <table class="rt-admin-toggle-panel-content">
      <tbody>
      <?php echo render_form_row($form['stackable']); ?>
      <?php echo render_form_row($form['date_from']); ?>
      <?php echo render_form_row($form['date_to']); ?>
      <?php //echo render_form_row($form['quantity_from']); ?>
      <?php //echo render_form_row($form['quantity_to']); ?>
      <?php echo render_form_row($form['total_from']); ?>
      <?php echo render_form_row($form['total_to']); ?>
      </tbody>
    </table>
  </div>

  <div class="rt-admin-toggle-panel">
    <h2><?php echo __('Usage Settings') ?></h2>
    <table class="rt-admin-toggle-panel-content">
      <tbody>
      <?php echo render_form_row($form['mode']); ?>
      <?php echo render_form_row($form['count']); ?>
      </tbody>
    </table>
  </div>

  <div class="rt-admin-toggle-panel">
    <h2><?php echo __('Adminstration Notes') ?></h2>
    <table class="rt-admin-toggle-panel-content">
      <tbody>
      <?php echo render_form_row($form['comment']); ?>
      </tbody>
    </table>
  </div>