<?php
if(count($new_features) > 0)
    foreach($new_features as $new_feature=>$value){
        if($value->field_display['und'][0]['value']){
            $release_date = date("m/d/Y",strtotime($value->field_release_date['und'][0]['value']));
            print "<div class='new-features-title'>" . $release_date . ": ".$value->title ."</div>";
            print "<div class='new-features-description'>".$value->body['und'][0]['safe_value'] . "</div>";
        }
}else{
    print "<div id='no-records' class='clearfix'><span>There are no new features available now.</span></div>";
}
    
