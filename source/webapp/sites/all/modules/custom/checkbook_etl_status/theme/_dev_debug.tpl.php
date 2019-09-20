<?php if (!empty($connections) && isset($dev_mode) && $dev_mode):
  $i = 0;
  ?>
  <table class="dbconnections" cellpadding="3">
    <thead>
    <tr>
      <th colspan="<?php echo(sizeof($connection_keys) + 1); ?>">
        DB CONNECTIONS
      </th>
    </tr>
    <tr class="header">
      <th></th>
      <?php foreach ($connection_keys as $key): ?>
        <th><?php echo $key; ?></th>
      <?php endforeach; ?>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($connections as $env => $v): ?>
      <tr class="<?php echo($i++ % 2 ? 'even' : 'odd'); ?>">
        <th class="env"><?php echo $env; ?></th>
        <?php foreach ($connection_keys as $key): ?>
          <th><?php echo(!empty($v[$key]) ? str_replace('|', '<br />', $v[$key]) : '-'); ?></th>
        <?php endforeach; ?>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
  <br/>
  <br/>
<?php endif; ?>
