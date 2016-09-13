<?php
foreach($new_features as $new_feature=>$value){
    if($value->field_display['und'][0]['value']){
        $release_date = date("d/m/Y",strtotime($value->field_release_date['und'][0]['value']));
        print '<div><strong>' . $release_date . ': '.$value->title .'</strong></div>';
        print $value->body['und'][0]['safe_value'];
    }
}
