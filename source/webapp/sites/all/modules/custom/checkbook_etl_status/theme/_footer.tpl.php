<table width="100%">
  <tr align="center">
    <td>
      © <?php echo date('Y'); ?>, Checkbook NYC<br/>
      <?php if(isset($dev_mode) && $dev_mode):?>
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
      <?php endif; ?>
    </td>
  </tr>
</table>
