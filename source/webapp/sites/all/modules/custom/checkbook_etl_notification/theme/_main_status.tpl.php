<?php
$prod_process_errors = FALSE;
if(isset($prod_stats)) {
  foreach ($prod_stats as $prod_stat) {
    if ($prod_stat['Last Run Success?'] == 'Fail') {
      if ($prod_stat['All Files Processed?'] == 'N' && $prod_stat['Shards Refreshed?'] == 'Y' && $prod_stat['Solr Refreshed?'] == 'Y') {
        $prod_process_errors = TRUE;
      }
    }
  }
}
$uat_process_errors = FALSE;
if(isset($uat_stats)) {
  foreach ($uat_stats as $uat_stat) {
    if ($uat_stat['Last Run Success?'] == 'Fail') {
      if ($uat_stat['All Files Processed?'] == 'N' && $uat_stat['Shards Refreshed?'] == 'Y' && $uat_stat['Solr Refreshed?'] == 'Y') {
        $uat_process_errors = TRUE;
      }
    }
  }
}
?>
<table cellspacing="7" class="title">
  <tbody>
    <tr><th>Production ETL Summary</th></tr>
    <?php if(!isset($prod_stats)): ?>
    <tr><td class="no-stats"><?php echo $prod_status; ?></td></tr>
    <?php endif; ?>
  </tbody>
</table>
<br>
<?php if(isset($prod_stats)): ?>
<table class="status">
  <tbody>
    <tr>
      <th>Database</th>
      <th>Last Run Date</th>
      <th>Last Run Success?</th>
      <th>Last Success Date</th>
      <th>Last File Load Date</th>
      <th>All Files Processed?</th>
      <th>Shards Refreshed?</th>
      <th>Solr Refreshed?</th>
      <?php echo ($prod_process_errors) ?  '<th>All Processes Successful?</th>' : '' ;?>
    </tr>
  <?php foreach ($prod_stats as $prod_stat): ?>
    <tr>
      <td>
        <?php echo $prod_stat['Database'] ?>
      </td>
      <td>
        <?php echo date('m-d-Y', strtotime($prod_stat['Last Run Date'])) ?>
      </td>
      <td class = "flag">
        <?php echo($prod_stat['Last Run Success?'] == 'Success' ? '✅' : '❌'); ?>
      </td>
      <td>
        <?php echo date('m-d-Y', strtotime($prod_stat['Last Success Date'])) ?>
      </td>
      <td>
        <?php echo date('m-d-Y', strtotime($prod_stat['Last File Load Date'])) ?>
      </td>
      <td class = "flag">
        <?php echo($prod_stat['All Files Processed?'] == 'N' ? '✅' : '❌'); ?>
      </td>
      <td class = "flag">
        <?php echo($prod_stat['Shards Refreshed?'] == 'Y' ? '✅' : '❌'); ?>
      </td>
      <td class = "flag">
        <?php echo($prod_stat['Solr Refreshed?'] == 'Y' ? '✅' : '❌'); ?>
      </td>
      <?php if($prod_process_errors): ?>
      <td class="flag">
        <?php echo ($prod_stat['Process Errors?']) ?  '❌' : '✅' ;?>
      </td>
      <?php endif; ?>
    </tr>
  <?php endforeach; ?>
  </tbody>
</table>
<?php endif; ?>
<br>
<br>
<table cellspacing="7" class="title">
  <tbody>
    <tr><th>UAT ETL Summary</th></tr>
    <?php if(!isset($uat_stats)): ?>
      <tr><td class="no-stats"><?php echo $uat_status; ?></td></tr>
    <?php endif; ?>
  </tbody>
</table>
<br>
<?php if(isset($prod_stats)): ?>
<table class="status">
  <tbody>
  <tr>
    <th>Database</th>
    <th>Last Run Date</th>
    <th>Last Run Success?</th>
    <th>Last Success Date</th>
    <th>Last File Load Date</th>
    <th>All Files Processed?</th>
    <th>Shards Refreshed?</th>
    <th>Solr Refreshed?</th>
    <?php echo ($uat_process_errors) ?  '<th>All Processes Successful?</th>' : '' ;?>
  </tr>
  <?php foreach ($uat_stats as $uat_stat): ?>
    <tr>
      <td>
        <?php echo $uat_stat['Database'] ?>
      </td>
      <td>
        <?php echo date('m-d-Y', strtotime($uat_stat['Last Run Date'])) ?>
      </td>
      <td class = "flag">
        <?php echo(($uat_stat['Last Run Success?'] == 'Success') ? '✅' : '❌'); ?>
      </td>
      <td>
        <?php echo date('m-d-Y', strtotime($uat_stat['Last Success Date'])) ?>
      </td>
      <td>
        <?php echo date('m-d-Y', strtotime($uat_stat['Last File Load Date'])) ?>
      </td>
      <td class = "flag">
        <?php echo($uat_stat['All Files Processed?'] == 'N' ? '✅' : '❌'); ?>
      </td>
      <td class = "flag">
        <?php echo($uat_stat['Shards Refreshed?'] == 'Y' ? '✅' : '❌'); ?>
      </td>
      <td class = "flag">
        <?php echo($uat_stat['Solr Refreshed?'] == 'Y' ? '✅' : '❌'); ?>
      </td>
      <?php if($uat_process_errors): ?>
        <td class="flag">
          <?php echo ($uat_stat['Process Errors?']) ?  '❌' : '✅' ;?>
        </td>
      <?php endif; ?>
    </tr>
  <?php endforeach; ?>
  </tbody>
</table>
<?php endif; ?>
