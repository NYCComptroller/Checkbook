
<?php
global $conf;
$message = "<b>Below are the results of the triggered alert(s) from checkbooknyc.com: </b><br><br>".PHP_EOL;

foreach($alerts as $alert)
{
    $day = $alert-> minimum_days  == 1 ?  "Every Day" :  "Every " . $alert->minimum_days . " days";
$message .= "Alert description : " . $alert->label . "<br>".PHP_EOL;
$message .= "Alert frequency: " . $day  ."<br>". PHP_EOL;
$message .= "Number of new records that match alert criteria: " . $alert->new_count. "<br>
Link to Checkbook NYC alert results: ". $alert->user_url. "<br>
To unsubscribe to this alert click here: " . $conf['check_book']['data_feeds']['site_url'].(substr($conf['check_book']['data_feeds']['site_url'],-1)=="/"?'':"/")."alert/unsubscribe/".$alert->checkbook_alerts_sysid.md5($alert->checkbook_alerts_sysid.$alert->label.$alert->recipient).
    "<br><br><br>





";
}
$message .= "
To unsubscribe to all alerts sent to this email address click here: " . $conf['check_book']['data_feeds']['site_url'].'/alert/unsubscribe/'.md5($alert->recipient)."




<br><br><br>

Office of the Comptroller - City of New York";


print($message);








