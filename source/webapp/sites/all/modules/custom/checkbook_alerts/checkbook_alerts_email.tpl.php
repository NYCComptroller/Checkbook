Below are the results of the triggered alert(s) from checkbooknyc.com:
<?php global $conf; ?>
<?php foreach($alerts as $alert): ?>
Alert description : <?=$alert->label ?>

Alert frequency: <?=($alert->minimum_days==1?'Every Day':'Every '.$alert->minimum_days.' days') ?>

Number of new records that match alert criteria: <?=$alert->new_count ?>

Link to Checkbook NYC alert results: <?=$alert->user_url ?>

To unsubscribe to this alert click here: <?=$conf['check_book']['data_feeds']['site_url'].(substr($conf['check_book']['data_feeds']['site_url'],-1)=="/"?'':"/").'alert/unsubscribe/'.$alert->checkbook_alerts_sysid.md5($alert->checkbook_alerts_sysid.$alert->label.$alert->recipient); ?>




<?php endforeach; ?>

To unsubscribe to all alerts sent to this email address click here: <?=$conf['check_book']['data_feeds']['site_url'].'/alert/unsubscribe/'.md5($alert->recipient) ?>






Office of the Comptroller - City of New York










