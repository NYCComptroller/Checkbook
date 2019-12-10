<?php if ($solr_stats = $uat_status['solr_stats'] ?? false): $i = 0; ?>
  <table class="dbconnections">
    <thead>
    <tr>
      <th colspan="3">
        SOLR STATS
      </th>
    </tr>
    <tr class="header">
      <th>collection</th>
      <th colspan="2">stats</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($solr_stats as $collection_stats): $lines=[]; ?>
      <tr class="<?php echo($i++ % 2 ? 'even' : 'odd'); ?>">
        <th rowspan="<?php echo sizeof($collection_stats['stats'])?>"><strong><?php echo $collection_stats['collection']; ?></strong></th>
      <?php foreach ($collection_stats['stats'] as $domain=>$number):
        $number = number_format($number);
        $lines[] = "<td><strong>{$domain}:</strong></td><td>{$number}</td>";
      endforeach;
      echo join('</tr></tr>',$lines);
      ?>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
  <br/>
  <br/>
<?php endif; ?>


<?php if (!empty($connections) && isset($dev_mode) && $dev_mode): $i = 0; ?>
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
