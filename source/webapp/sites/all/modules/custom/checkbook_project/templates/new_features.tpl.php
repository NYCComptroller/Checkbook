<h3>NEW FEATURES</h3>
<?php
foreach($new_features as $new_feature=>$value){
    $release_date = date("d/m/Y",strtotime($value->field_release_date['und'][0]['value']));
    print '<b>' . $release_date . ': '.$value->title .'</b>';
    print $value->body['und'][0]['safe_value'];
}
