#!/bin/bash
# runs on ETL server after all files were imported

source ~/.bashrc

DATE=`date +%Y%m%d`

find $ETL_FISA_SOURCE_PATH -user fisa-pms -type f -mtime -0.25  -exec wc -lc {} + > $ETL_FISA_PATH/$DATE.log
find $ETL_FISA_ARCHIVE_PREFIX/$DATE/$ETL_FISA_ARCHIVE_POSTFIX -user fisa-pms -type f -exec wc -c {} + > $ETL_FISA_PATH/$DATE.pgp.log
