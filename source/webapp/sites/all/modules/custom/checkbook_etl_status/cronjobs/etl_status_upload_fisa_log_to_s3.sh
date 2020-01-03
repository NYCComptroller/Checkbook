#!/bin/bash
# runs on ETL server after all files were imported
# ex.,
# ETL_FISA_PATH=/mnt/www/checkbook-files/etl/files_log
# ETL_FISA_S3_BUCKET=checkbook-etl-logs
# ETL_FISA_ARCHIVE_PREFIX=/data/xfer/fisa-pms/archive
# ETL_FISA_ARCHIVE_POSTFIX=FMS3/FMS32AREI/PRDFMS/ENCRYPTED
# ETL_FISA_SOURCE_PATH=/vol2share/NYC/FEEDS/SOURCE_DIR
#
# CRONTAB:
# ETL upload FISA log to S3
# 15 21 * * * /bin/sh /home/gpadmin/scripts/etl_status_upload_fisa_log_to_s3.sh

DATE=$(date +%Y%m%d)

source /etc/profile.d/etl.sh

find $ETL_FISA_SOURCE_PATH -user fisa-pms -type f -mtime -0.25  -exec wc -lc {} + > $ETL_FISA_PATH/$DATE.log
find $ETL_FISA_ARCHIVE_PREFIX/$DATE/$ETL_FISA_ARCHIVE_POSTFIX -user fisa-pms -type f -exec wc -c {} + > $ETL_FISA_PATH/$DATE.pgp.log

unset PYTHONPATH
unset PYTHONHOME

/bin/aws s3 sync $ETL_FISA_PATH/ s3://$ETL_FISA_S3_BUCKET/FISA-PGP/ --exclude=* --include=*.pgp.log
sleep 5m
/bin/aws s3 sync $ETL_FISA_PATH/ s3://$ETL_FISA_S3_BUCKET/FISA/ --exclude=* --include=*.log --exclude=*.pgp.log
