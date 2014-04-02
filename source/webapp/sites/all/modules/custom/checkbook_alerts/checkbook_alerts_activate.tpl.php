Please click the following link to activate your alert:
<?php global $conf; ?>

Thank you for creating an alert on checkbooknyc.com.
The following alert has been created and will be sent to your email address:
<?=$alertFrequency ?> - <?=$label ?>

To complete the process, please click on the following link:

<?=$conf['check_book']['data_feeds']['site_url'].(substr($conf['check_book']['data_feeds']['site_url'],-1)=="/"?'':"/").'alert/activate/'.$alert2['checkbook_alerts_sysid'].md5($alert2['checkbook_alerts_sysid'].$alert2['label'].$alert2['recipient']); ?>


(If link above is not active, please cut and paste full URL into your browser window.)

PLEASE DO NOT REPLY TO THIS MESSAGE!
