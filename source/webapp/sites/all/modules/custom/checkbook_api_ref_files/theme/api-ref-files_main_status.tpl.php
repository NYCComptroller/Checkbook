<table cellspacing="7" class="title">
  <tbody>
    <tr><th>API Ref files Summary</th></tr>
  </tbody>
</table>
<br>
<table class="status">
  <tbody>
    <tr>
      <th>File name</th>
      <th>Success?</th>
      <th>Old file</th>
      <th>Old filesize(bytes)</th>
      <th>New file</th>
      <th>New filesize(bytes)</th>
      <th>All Files Processed?</th>
      <th>Updated?</th>
      <th>Sample</th>
    </tr>
    <?php foreach ($files as $filename => $info): ?>
      <tr class="<?php echo ((empty($c) || $c=='even')?($c = 'odd'):($c = 'even')) ?>">
        <td>
          <?php echo $filename ?>
        </td>
        <td class = "flag">
          <?php echo($info['error'] ? '❌' : '✅'); ?>
        </td>
        <td>
          <?php if($info['old_timestamp']):?>
            <?php echo $info['old_timestamp'] ?>
          <?php endif; ?>
        </td>
        <td>
          <?php if($info['old_filesize']):?>
            <?php echo $info['old_filesize'] ?>
          <?php endif; ?>
        </td>
        <td>
          <?php if($info['new_timestamp'] && ($info['old_timestamp'] !== $info['new_timestamp'])):?>
            <?php echo $info['new_timestamp'] ?>
          <?php endif; ?>
        </td>
        <td>
          <?php if($info['new_filesize']):?>
            <?php echo $info['new_filesize'] ?>
          <?php endif; ?>
        </td>
        <td class="flag">
          <?php echo($info['updated'] ? '✅' : 'No'); ?>
        </td>
        <td>
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
  </tbody>
</table>