
<style type="text/css">
  table, th, td{
    border:1px #ccc solid;
    border-collapse: collapse;
    padding: 5px;
  }
  .zone-wrapper{
    background: url('https://www.checkbooknyc.com/sites/all/themes/checkbook3/images/header-bg.png') repeat-x scroll 0 0 transparent;
    height: 48px;
    margin-bottom: 10px;
  }
  .zone-wrapper img{
    float: right;
  }
  .zone-wrapper-btm {
    margin-top: 20px;
  }
</style>

<div class="zone-wrapper">
  <img src="https://www.checkbooknyc.com/sites/all/themes/checkbook3/images/logo.png" />
</div>

<table>
  <tr>
    <td>UAT ETL STATUS:</td>
    <td><?php echo $uat_status; ?></td>
  </tr>
  <tr>
    <td>PROD ETL STATUS:</td>
    <td><?php echo $prod_status; ?></td>
  </tr>
</table>

<?php echo $comment; ?>

<?php if ($invalid_records_timestamp && $invalid_records): ?>

<br />
<p>
  <strong>PROD ETL `invalid_records_details.csv` found:</strong><br />
  (Last modified: <?php echo date("Y-m-d g:iA", $invalid_records_timestamp); ?>)
</p>


<table>
  <?php $first = true;
  foreach($invalid_records as $row):
    $td = $first ? 'th' : 'td'; ?>
  <tr>
    <?php foreach ($row as $item){ echo "<$td>".htmlentities($item)."</$td>"; }?>
  </tr>
  <?php $first = false;
  endforeach; ?>
</table>
<?php endif; ?>

<div class="zone-wrapper zone-wrapper-btm">
</div>
