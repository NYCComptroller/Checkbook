#!/bin/bash

# runs on ETL server after all files were imported

DATE=`date +%Y%m%d`

aws s3 cp $ETL_FISA_PATH/$DATE.log s3://$ETL_FISA_S3_BUCKET/$ETL_FISA_S3_PREFIX
