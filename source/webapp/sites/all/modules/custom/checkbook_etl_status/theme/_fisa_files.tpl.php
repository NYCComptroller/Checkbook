<?php if (!empty($fisa_files)): $i = 0;
  $oldPrefix = ''; ?>

  <table class="file" cellpadding="5">
    <thead>
    <tr class="filename">
      <th colspan="3">Missing Decrypted PGP FISA contracts files:</th>
    </tr>
    <?php if (isset($fisa_files['missing']) && sizeof($fisa_files['missing'])): ?>
    <tr class="header">
      <th>Filename</th>
    </tr>
    <?php endif; ?>
    </thead>
    <tbody>
    <tr class="odd">
      <td align="center">✅ No missing files detected ✅</td>
    </tr>
    <?php if (isset($fisa_files['missing']) && sizeof($fisa_files['missing'])): ?>
      <?php foreach ($fisa_files['missing'] as $file): ?>
        <tr class="<?php echo($i++ % 2 ? 'even' : 'odd'); ?>">
          <td>❌ <?= $file ?> ❌</td>
        </tr>
      <?php endforeach; ?>
    <?php endif; ?>
    </tbody>
  </table>
  <br/>
  <br/>

  <table class="file" cellpadding="5">
    <thead>
    <tr class="filename">
      <th colspan="3">FISA contracts files (received by <?= $fisa_files['date'] ?> 10pm EST):</th>
    </tr>
    <tr class="header">
      <th>Bytesize</th>
      <th class="bytesize">Number of lines</th>
      <th>Filename</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($fisa_files['contract_lines'] as $line):
      list($prefix,) = explode('_' . date('Y'), $line['filename']);
      $strong = (strpos($line['filename'], $fisa_files['date'])) ? 'today' : '';
      if ($prefix !== $oldPrefix) {
        $oldPrefix = $prefix;
        $i++;
      }
      ?>
      <tr class="<?php echo($i % 2 ? 'even' : 'odd'); ?> <?= $strong ?>">
        <td><?= $line['bytes'] ?></td>
        <td class="bytesize"><?= $line['lines'] ?></td>
        <td><?= $line['filename'] ?></td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
<?php else: ?>
  <table class="file" cellpadding="5">
    <thead>
    <tr class="filename" align="center">
      <th> FISA files:</th>
    </tr>
    </thead>
    <tbody>
    <tr class="odd">
      <td> ❌ NO DATA ❌</td>
    </tr>
    </tbody>
  </table>
<?php endif; ?>
