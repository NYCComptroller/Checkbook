<?php
/**
* This file is part of the Checkbook NYC financial transparency software.
* 
* Copyright (C) 2012, 2013 New York City
* 
* This program is free software: you can redistribute it and/or modify
* it under the terms of the GNU Affero General Public License as
* published by the Free Software Foundation, either version 3 of the
* License, or (at your option) any later version.
* 
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU Affero General Public License for more details.
* 
* You should have received a copy of the GNU Affero General Public License
* along with this program.  If not, see <http://www.gnu.org/licenses/>.
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
