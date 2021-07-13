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

    table.title{
      font-size: 13px;
      font-weight: bolder;
      border: none;
      color:#3c5e7c;
    }

    .no-stats{
      font-size: 12px;
      color:darkred;
    }
    
    table.status tr:nth-child(even){background-color: #f2f2f2;}

    table.status tr:hover {background-color: #ddd;}

    table.status th {
      text-align: right;
      background: #3c6e95;
      color: #fff;
      padding: 4px;
    }

    /* thead tr.header th, tbody th.env {
            text-transform: uppercase;
            background: #3c6e95;
            color: #fff;
            text-indent:
    } */

    /* table.dbconnections tr.header th, table.dbconnections th.env {
            background: #8e9eac;
    } */

    /* thead tr.filename th {
            color: darkred;
            text-align: right;
        }

        tbody tr.even {
            background: #ddd;
        }

        tbody tr.odd {
            background: #eee;
        } */


    table.status td{
      padding-left: 2px;
      text-align: center;
    }

    footer {
      border-top: 1px #2d5879 solid;
      text-align: center;
    }

    a:link, footer a:visited {
      color: #5b5b5b;
      text-decoration: none;
    }

    /* table.sample-data {
            margin: 10px;
        }
        table.sample-data, table.sample-data tr, table.sample-data td, table.sample-data th{
            background: #fefefe !important;
            font-size:smaller;
        } */

    footer table {
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
  <?php include("api-ref-files_main_status.tpl.php"); ?>
  <br/>
  <br/>
  <br/>
  <br/>
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
