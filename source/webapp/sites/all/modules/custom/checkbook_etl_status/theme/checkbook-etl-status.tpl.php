<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <link href='http://fonts.googleapis.com/css?family=Roboto' rel='stylesheet' type='text/css'>

  <style type="text/css">
    body {
      font-family: Roboto;
      font-size: 12px;
    }

    main {
      margin: 20px;
    }

    table {
      border: #ccc 1px dotted;
    }

    main table {
      margin: 20px;
    }

    table.status {
      background: #ddd;
    }

    table.status th {
      text-align: right;
    }

    thead tr.header th {
      text-transform: uppercase;
      background: #3c6e95;
      color: #fff;
      text-indent:
    }

    thead tr.filename th {
      color: #bbb;
      text-align: right;
    }

    tbody tr.even {
      background: #ddd;
    }

    tbody tr.odd {
      background: #eee;
    }

    footer {
      border-top: 1px #2d5879 solid;
      text-align: center;
    }

    footer a:link, footer a:visited {
      color: #5b5b5b;
      text-decoration: none;
    }
  </style>

</head>
<body text="#5b5b5b">

<header>
  <table bgcolor="2d5879" width="100%">
    <tr align="center">
      <td width="50%"></td>
      <td width="400">
        <a href="https://checkbooknyc.com/">
          <img alt="CheckbookNYC logo" title="CheckbookNYC"
               src="https://www.checkbooknyc.com/sites/all/themes/checkbook3/images/logo.png"/><br/>
        </a>
      </td>
      <td width="50%"></td>
    </tr>
  </table>
</header>

<main>

  <table class="status" cellspacing="8">
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
    <?php endif;
  endforeach; ?>
</main>

<footer>
  <p>
    © <?php echo date('Y'); ?>, Checkbook NYC<br/>
    <small>
      <?php
      global $conf;
      if (!empty($conf['etl-status-footer'])):
        $out = '';
        $arr = [];
        foreach ($conf['etl-status-footer'] as $line) {
          foreach ($line as $text => $url):
            $arr[] = "<a target=\"_blank\" href=\"$url\">$text</a>";
          endforeach;
          $out .= join(' | ', $arr) . '<br />';
          $arr = [];
        }
        echo $out;
      endif; ?>
    </small>
  </p>
</footer>

</body>

</html>
