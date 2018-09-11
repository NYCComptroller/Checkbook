# cron:
# 11 9 * * * apache /usr/bin/python /var/www/html/sites/all/modules/custom/checkbook_api_ref_files/converter/csv2xlsx.py /net/mdw1/data/datafeeds/*/refdata/
#
# shell:
# su -s /bin/sh apache -c "python /var/www/html/sites/all/modules/custom/checkbook_api_ref_files/converter/csv2xlsx.py /net/mdw1/data/datafeeds/*/refdata/"
import os
import sys
import glob
import csv
from xlsxwriter.workbook import Workbook

reload(sys)
sys.setdefaultencoding('utf8')

for path in sys.argv[1:]:
  if os.path.exists(path):
    for csvfile in glob.glob(os.path.join(path, '*.csv')):
      print "Converting "+csvfile
      workbook = Workbook(csvfile[:-4] + '.xlsx', {'constant_memory': True})

      worksheet = workbook.add_worksheet()
      with open(csvfile, 'rt') as f:
        reader = csv.reader(f)
        for r, row in enumerate(reader):
          for c, col in enumerate(row):
            worksheet.write(r, c, col)
      workbook.close()
