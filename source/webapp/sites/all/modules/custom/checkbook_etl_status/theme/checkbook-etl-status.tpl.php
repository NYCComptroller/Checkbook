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

    table {
      border: #ccc 1px dotted;
      margin-left: auto;
      margin-right: auto;
    }

    table.status {
      font-size: larger;
      background: #ddd;
    }

    table.status th {
      text-align: right;
    }

    thead tr.header th, tbody th.env {
      text-transform: uppercase;
      background: #3c6e95;
      color: #fff;
      text-indent:
    }

    table.dbconnections tr.header th, table.dbconnections th.env {
      background: #8e9eac;
    }

    thead tr.filename th {
      color: darkred;
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

    a:link, footer a:visited {
      color: #5b5b5b;
      text-decoration: none;
    }

    footer table{
      border: 0;
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
  <br/>
  <br/>

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

  <br/>
  <br/>

  <?php
  foreach ([$prod_status, $uat_status] as $json):
    if ($json['audit_status_timestamp'] && $json['audit_status']): ?>
        <table class="file" cellpadding="5">
            <?php if(['OK'] == $json['audit_status']):?>
            <tbody>
            <tr class="odd">
                <td>
                    <strong>PROD-UAT Match</strong> ✅
                    <?php echo $json['audit_status_time_diff']; ?>
                </td>
            </tr>
            </tbody>
            <?php else: ?>
            <thead>
            <tr class="filename">
                <th colspan="<?php echo sizeof($json['audit_status'][0]) ?>">
                    <?php echo $json['source'] ?> ETL `audit_status.txt`
                    (<?php echo date("Y-m-d g:iA", $json['audit_status_timestamp']); ?>)
                </th>
            </tr>
            </thead>
            <tbody>
            <tr class="odd">
                <td>
                    <strong>PROD-UAT Match</strong> ❌ <br />
                </td>
            </tr>
            <tr class="even">
                <td>
                    <?php echo json_encode($json['audit_status']); ?>
                </td>
            </tr>
            </tbody>
            <?php endif; ?>
        </table>
        <br/>
        <br/>
    <?php
    endif;
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
      <br/>
      <br/>
    <?php endif;
  endforeach; ?>
  <br/>
  <br/>
    <?php if(!empty($solr_health_status)):
        $i = 0;
    ?>
    <table class="dbconnections" cellpadding="3">
        <thead>
        <tr>
            <th colspan="2">
                SOLR HEALTH STATUS
            </th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($solr_health_status as $solrServer => $cores): ?>
            <?php foreach ($cores as $core => $health): ?>
                <tr class="<?php echo($i++ % 2 ? 'even' : 'odd'); ?>">
                    <?php if($i % 2): ?>
                        <th class="env" rowspan="2"><?php echo $solrServer; ?></th>
                    <?php endif; ?>
                    <th><?php echo "<a target='_blank' href='{$health['url']}'>{$core}</a>"; ?></th>
                    <td>
                        <?php echo ('OK' == $health['status'] ? '✅' : ('❌ </br >'.$health['status'])); ?>
                </tr>
            <?php endforeach; ?>
        <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>
  <br/>
  <br/>
  <br/>
  <br/>
  <?php if (!empty($connections)):
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
</main>

<footer>
  <table width="100%">
    <tr align="center">
      <td>
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
      </td>
    </tr>
  </table>
</footer>

</body>

</html>
