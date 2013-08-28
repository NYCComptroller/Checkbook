Please click the following link to activate your alert:
<?php global $conf; ?>

Thank you for creating an alert on checkbooknyc.com.
The following alert has been created and will be sent to your email address:
<?=$alertFrequency ?> - <?=$label ?>

To complete the process, please click on the following link:

<?=$conf['check_book']['data_feeds']['site_url'].'alert/activate/'.$alert->checkbook_alerts_sysid.md5($alert->checkbook_alerts_sysid.$alert->label.$alert->recipient),array('absolute'=>true); ?>


(If link above is not active, please cut and paste full URL into your browser window.)

PLEASE DO NOT REPLY TO THIS MESSAGE!
