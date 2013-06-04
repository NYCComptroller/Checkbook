<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/


/**
 * Script to send notification emails
 */

_drush_bootstrap_drupal_full();

	try{
        $completedJobRequests = QueueUtil::getPendingEmailsInfo();
        LogHelper::log_debug( $completedJobRequests ); 
    }catch(Exception $claimException){
        LogHelper::log_debug("$logId: Error while fetching job from queue: ". $claimException);
        return;
    }

    foreach($completedJobRequests as $request){
		LogHelper::log_info( $request ); 
		try{ 
			global $conf;
        	$dir = variable_get('file_public_path','sites/default/files')
                .'/'.$conf['check_book']['data_feeds']['output_file_dir'];

        	$file = $dir . '/' . $request['filename'];
 			$params= array("download_url"=>$file
	 		  ,"download_url_compressed"=>$file.'.zip'
			  ,"expiration_date"=>date('d-M-Y',$request['end_time'] + 3600 * 24 * 7 ) 
			  ,"contact_email"=>$request['contact_email']
			  ,"tracking_num"=>$request['token']
			  );		  
			LogHelper::log_debug( $params ); 
  			$response = drupal_mail('checkbook_datafeeds', "download_notification", $request['contact_email'], null, $params);
  			LogHelper::log_debug( $response );
  			if($response['result'] )
  				QueueUtil::updateJobRequestEmailStatus($request['rid']);
		}catch(Exception $claimException){
        	LogHelper::log_debug("Error while Sending Email Notification: ". $claimException . $params );
        return;
    	}  	
    }
