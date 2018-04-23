
Below are the results of the triggered alert(s) from checkbooknyc.com:

<?php global $conf; 
foreach($alerts as $alert)
{
    $description = $alert->label;
    $frequency = $alert-> minimum_days  == 1 ?  "Every Day" :  "Every " . $alert->minimum_days . " days";
    $number_records = $alert->new_count;
    $results_link = $alert->user_url;
    $unsubscribe_link = $conf['check_book']['data_feeds']['site_url'].(substr($conf['check_book']['data_feeds']['site_url'],-1)=="/"?'':"/")."alert/unsubscribe/".$alert->checkbook_alerts_sysid.md5($alert->checkbook_alerts_sysid.$alert->label.$alert->recipient);
?>
Alert description: <?=$description?>

Alert frequency: <?=$frequency?>

Number of new records that match alert criteria: <?=$number_records?>


Link to Checkbook NYC alert results: <?=$results_link?>


To unsubscribe from this alert click here: <?=$unsubscribe_link?>

<?php
}
?>

To unsubscribe from all alerts sent to this email address click here:
<?=$conf['check_book']['data_feeds']['site_url'].'/alert/unsubscribe/'.md5($alert->recipient)?>.

Office of the Comptroller - City of New York

