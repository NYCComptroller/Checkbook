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
