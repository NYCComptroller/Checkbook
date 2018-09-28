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

        footer table {
            border: 0;
        }

        table.sample-data {
            margin: 10px;
        }
        table.sample-data, table.sample-data tr, table.sample-data td, table.sample-data th{
            background: #fefefe !important;
            font-size:smaller;
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

    <table class="status">
    <?php if($error): ?>
            <tr>
                <td>❌ <?php echo $error; ?> ❌</td>
            </tr>
    <?php else: ?>
            <tr>
                <th>File</th>
                <th>Sample</th>
            </tr>
        <?php foreach($files as $filename => $info): ?>
            <tr class="<?php echo ((empty($c) || $c=='even')?($c = 'odd'):($c = 'even')) ?>">
                <th>
                    <?php echo $filename ?>.csv
                    <small>
                        <?php echo($info['error'] ? '❌' : '✅'); ?>
                        <?php if($info['old_timestamp']):?>
                            <br /> Old file: <?php echo $info['old_timestamp'] ?>
                        <?php endif; ?>
                        <?php if($info['old_filesize']):?>
                            <br /> Old filesize(bytes): <?php echo $info['old_filesize'] ?>
                        <?php endif; ?>

                        <?php if($info['new_timestamp'] && ($info['old_timestamp'] !== $info['new_timestamp'])):?>
                            <br /> New file: <?php echo $info['new_timestamp'] ?>
                        <?php endif; ?>
                        <?php if($info['new_filesize']):?>
                            <br /> New filesize(bytes): <?php echo $info['new_filesize'] ?>
                        <?php endif; ?>
                        <br />Updated: <?php echo($info['updated'] ? '✅' : 'No'); ?>
                    </small>
                </th>
                <td>
                    <?php if($info['error']): ?>
                        ⛔ <?php echo $info['error']; ?> ⛔<br />
                    <?php endif; ?>
                    <?php if($info['warning']): ?>
                        ☢ <?php echo $info['warning']; ?> ☢<br />
                    <?php endif; ?>
                    <?php if($info['info']): ?>
                        ⚡ <?php echo $info['info']; ?> ⚡<br />
                    <?php endif; ?>
                    <?php if($info['sample']): ?>
                        <?php $headers = array_keys($info['sample'][0]); ?>
                        <table class="sample-data">
                            <tr>
                                <?php foreach ($headers as $header):?>
                                    <th>
                                        <?php echo $header ?>
                                    </th>
                                <?php endforeach;?>
                            </tr>
                            <?php foreach ($info['sample'] as $row): ?>
                                <tr>
                                    <?php foreach ($row as $cell): ?>
                                        <td>
                                            <?php echo htmlentities($cell); ?>
                                        </td>
                                    <?php endforeach; ?>
                                </tr>
                            <?php endforeach; ?>
                        </table>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    <?php endif; ?>
    </table>

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
