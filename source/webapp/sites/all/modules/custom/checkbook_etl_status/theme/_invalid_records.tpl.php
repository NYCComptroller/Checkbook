<?php if ($json['invalid_records_timestamp'] && $json['invalid_records']): ?>
<table class="file" cellpadding="5">
  <thead>
  <tr class="filename">
    <th colspan="<?php echo sizeof($json['invalid_records'][0]) ?>">
      <?php echo $json['source'] ?> ETL `invalid_records_details.csv`
      (<?php echo date("Y-m-d g:iA", $json['invalid_records_timestamp']); ?>)
    </th>
  </tr>
  <?php $first = true;
  foreach ($json['invalid_records'] as $key => $row):
  $class = $key ? ($key % 2 ? 'odd' : 'even') : 'header';
  $td = $first ? 'th' : 'td'; ?>
  <tr class="<?php echo $class ?>">
    <?php foreach ($row as $item) {
      echo "<$td>" . htmlentities($item) . "</$td>";
    } ?>
  </tr>
  <?php if ($first): ?>
  </thead>
  <tbody>
  <?php endif; ?>
  <?php $first = false;
  endforeach; ?>
  </tbody>
</table>
<?php endif; ?>
