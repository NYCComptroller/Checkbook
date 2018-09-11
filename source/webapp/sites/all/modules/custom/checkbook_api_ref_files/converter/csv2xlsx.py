import os
import sys
import glob
import csv
from xlsxwriter.workbook import Workbook

reload(sys)
sys.setdefaultencoding('utf8')

fn = sys.argv[1]
if os.path.exists(fn):
  for csvfile in glob.glob(os.path.join(fn, '*.csv')):
    print "Converting "+csvfile
    workbook = Workbook(csvfile[:-4] + '.xlsx', {'constant_memory': True})

    worksheet = workbook.add_worksheet()
    with open(csvfile, 'rt') as f:
      reader = csv.reader(f)
      for r, row in enumerate(reader):
        for c, col in enumerate(row):
          worksheet.write(r, c, col)
    workbook.close()
