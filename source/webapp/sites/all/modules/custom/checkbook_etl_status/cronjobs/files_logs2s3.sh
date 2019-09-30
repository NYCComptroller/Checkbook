#!/bin/bash

# runs on ETL server after all files were imported

source ~/.bashrc

DATE=`date +%Y%m%d`

aws s3 cp $ETL_FISA_PATH/$DATE.pgp.log s3://$ETL_FISA_S3_BUCKET/FISA-PGP/
sleep 5m
aws s3 cp $ETL_FISA_PATH/$DATE.log s3://$ETL_FISA_S3_BUCKET/FISA/
