<table cellspacing="7" class="title">
  <tbody>
    <tr><th>Production ETL Summary</th></tr>
  </tbody>
</table>
<br>
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
        <?php echo($prod_stat['All Files Processed?'] == 'Y' ? '✅' : '❌'); ?>
      </td>
      <td class = "flag">
        <?php echo($prod_stat['Shards Refreshed?'] == 'Y' ? '✅' : '❌'); ?>
      </td>
      <td class = "flag">
        <?php echo($prod_stat['Solr Refreshed?'] == 'Y' ? '✅' : '❌'); ?>
      </td>
    </tr>
  <?php endforeach; ?>
  </tbody>
</table>
<br>
<br>
<table cellspacing="7" class="title">
  <tbody>
    <tr><th>UAT ETL Summary</th></tr>
  </tbody>
</table>
<br>
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
        <?php echo($uat_stat['All Files Processed?'] == 'Y' ? '✅' : '❌'); ?>
      </td>
      <td class = "flag">
        <?php echo($uat_stat['Shards Refreshed?'] == 'Y' ? '✅' : '❌'); ?>
      </td>
      <td class = "flag">
        <?php echo($uat_stat['Solr Refreshed?'] == 'Y' ? '✅' : '❌'); ?>
      </td>
    </tr>
  <?php endforeach; ?>
  </tbody>
</table>

