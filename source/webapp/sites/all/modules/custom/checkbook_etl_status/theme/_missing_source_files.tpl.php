<table class="file" cellpadding="5" title="file_data_statistics.csv (<?php
echo date("Y-m-d g:iA", $json['match_status_timestamp']); ?>)">
  <thead>
  <tr class="filename">
    <th colspan="3">
      <?php echo $json['source'] ?> ETL :: Missing data source files
    </th>
  </tr>
  <tr>
    <th>
      Data source name
    </th>
    <th>
    </th>
    <th>
      Last successful run
    </th>
  </tr>
  </thead>
  <tbody>
  <?php
  $key = 0;
  foreach ($json['match_status'] as $source => $source_status):
    $class = $key++ % 2 ? 'odd' : 'even';
    ?>
    <tr class="<?php echo $class ?>">
      <td><?php echo htmlentities($source) ?></td>
      <td>❌</td>
      <td><?php echo $source_status; ?></td>
    </tr>
  <?php
  endforeach; ?>
  </tbody>
</table>
