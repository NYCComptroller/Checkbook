<?php foreach ([$prod_status, $uat_status] as $json):
        if ($json['audit_status_timestamp']): ?>
            <table class="file" cellpadding="5">
                <?php if (!empty($json['audit_status']) && (['OK'] == $json['audit_status'])): ?>
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
                        <th>
                            <?php echo $json['source'] ?> ETL `audit_status.txt`
                            (<?php echo date("Y-m-d g:iA", $json['audit_status_timestamp']); ?>)
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr class="odd">
                        <td>
                            <strong>PROD-UAT Match</strong> ❌ <br/>
                        </td>
                    </tr>
                    <tr class="even">
                        <td>
                            <?php
                                if(empty($json['audit_status'])):
                                    echo '*file is empty*';
                                else:
                                    echo json_encode($json['audit_status']);
                                endif;
                            ?>
                        </td>
                    </tr>
                    </tbody>
                <?php endif; ?>
            </table>
            <br/>
            <br/>
        <?php
        endif;
        if ($json['invalid_records_timestamp'] && $json['invalid_records']):
          include('_invalid_records.tpl.php');
        endif;?>
        <?php if ($json['match_status_timestamp'] && $json['match_status']):
          include('_missing_source_files.tpl.php');  ?>
        <?php elseif('PROD' == $json['source']):?>
  <br/>
  <br/>
            <table class="file" cellpadding="5">
                <thead>
                <tr class="filename">
                    <th>
                        PROD ETL :: Missing data source files
                    </th>
                </tr>
                </thead>
                <tbody>
                <tr class="odd">
                    <td align="center">✅ No missing data found ✅</td>
                </tr>
                </tbody>
                <tbody>
            </table>
        <?php endif;
    endforeach; ?>
