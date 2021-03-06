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
  <br/>
  <br/>
  <br/>
</main>

<footer>
  <?php include("_footer.tpl.php"); ?>
</footer>

</body>

</html>
