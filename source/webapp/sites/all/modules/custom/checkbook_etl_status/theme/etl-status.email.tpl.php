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
    }

    td.bytesize, tr.bytesize {
      text-align: right;
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

    tr.today td {
      color: darkgreen;
    }

    footer {
      border-top: 1px #2d5879 solid;
      text-align: center;
    }

    a:link, footer a:visited {
      color: #5b5b5b;
      text-decoration: none;
    }

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
  <?php include("_main_status.tpl.php"); ?>
  <br/>
  <br/>
  <?php include("_prod_uat_match.tpl.php"); ?>
  <br/>
  <br/>
  <?php include("_fisa_files.tpl.php"); ?>
  <br/>
  <br/>
  <br/>

  <?php include("_dev_debug.tpl.php"); ?>
</main>

<footer>
  <?php include("_footer.tpl.php"); ?>
</footer>

</body>

</html>
