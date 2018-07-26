<html>
<head>
  <link href='http://fonts.googleapis.com/css?family=Roboto' rel='stylesheet' type='text/css'>
</head>
<body>
<div class="zone-wrapper">
  <img src="https://www.checkbooknyc.com/sites/all/themes/checkbook3/images/logo.png"/>
</div>

<table class="status">
  <tbody>
  <?php foreach ([$prod_status, $uat_status] as $json): ?>
    <tr>
      <th><?php echo $json['source'] ?> ETL</th>
      <td title="<?php echo $json['hint'] ?>">
        <?php echo($json['success'] ? '✅' : '❌'); ?>
      </td>
      <td>
        <?php echo $json['hint'] ?>
      </td>
    </tr>
  <?php endforeach; ?>
  </tbody>
</table>

<?php
foreach ([$prod_status, $uat_status] as $json):
  if ($json['invalid_records_timestamp'] && $json['invalid_records']): ?>

    <table class="file">
      <thead>
      <tr class="filename">
        <th colspan="<?php echo sizeof($json['invalid_records'][0]) ?>">
          <?php echo $json['source'] ?> ETL `invalid_records_details.csv`
          (<?php echo date("Y-m-d g:iA", $json['invalid_records_timestamp']); ?>)
        </th>
      </tr>
      <?php $first = true;
      foreach ($json['invalid_records'] as $row):
      $td = $first ? 'th' : 'td'; ?>
      <tr class="header">
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
  <?php endif;
endforeach; ?>

<!--<div class="zone-wrapper zone-wrapper-btm"/>-->


<style type="text/css">
  body {
    font-family: Roboto;
  }

  table {
    margin: 25px 0;
    color: #5b5b5b;
    font-size: 12px;
  }
  tbody {
    border: #ccc 1px solid;
  }

  table, th, td {
    border-collapse: collapse;
    padding: 5px;
  }

  table.status th {
    text-align:right;
  }

  tbody tr:nth-of-type(even) {
    background: rgba(230, 230, 230, 0.5);
  }

  tbody tr:nth-of-type(odd) {
    background: rgba(240, 240, 240, 0.5);
  }

  thead tr.filename th {
    background: #fff;
    /*background: rgba(45, 88, 121, 0.8);*/
    color: #777;
    text-align: right;
    /*padding:15px;*/
  }

  thead tr.header th {
    text-transform: uppercase;
    background: rgb(60, 110, 149);
    color: #fff;
    padding: 15px;
    /*border: #fff solid 1px;*/
  }
  thead tr.header {
    border: rgb(60, 110, 149) 1px solid;
  }

  .zone-wrapper {
    background: #2d5879;
    height: 48px;
    margin-bottom: 10px;
    width: 100%;
  }

  .zone-wrapper img {
    float: right;
  }

  .zone-wrapper-btm {
    margin-top: 20px;
  }
</style>
</body>

</html>
