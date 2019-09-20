# import json
import urllib.parse
import boto3
import os
import pymongo
import re
from typing import List, Dict

print('Loading function')


def lambda_handler(event, context):
  # print("Received event: " + json.dumps(event, indent=2))

  # Get the object from the event and show its content type
  bucket = event['Records'][0]['s3']['bucket']['name']
  key = urllib.parse.unquote_plus(event['Records'][0]['s3']['object']['key'], encoding='utf-8')
  try:
    s3 = boto3.client('s3')
    response = s3.get_object(Bucket=bucket, Key=key)
    content = response['Body'].read().decode("utf-8").splitlines()

    folder, filename = key.split('/')
    if 'FISA' == folder:
      log_fisa_contracts(filename, content)
    if 'FISA-GPG' == folder:
      log_fisa_gpg_contracts(filename, content)

    return response['ContentType']
  except Exception as e:
    print(e)
    print(
      'Error getting object {} from bucket {}. Make sure they exist and your bucket is in the same region as this function.'.format(
        key, bucket))
    raise e


def checkbook_mongo_db():
  connect = {
    'user': os.environ.get('MONGO_USER'),
    'pass': os.environ.get('MONGO_PASS'),
    'cluster': os.environ.get('MONGO_CLUSTER')
  }
  connect_string = "mongodb+srv://{user}:{pass}@{cluster}?retryWrites=true&w=majority".format(**connect)

  client = pymongo.MongoClient(connect_string)
  db = client.checkbooknyc
  return db


def log_fisa_contracts(filename, content):
  lines = [line.split() for line in content]
  total = lines.pop()
  date, _ = filename.split('.')
  contract_lines = filter_contracts(lines)
  record = {
    "_id": date,
    "date": date,
    "file": filename,
    "totalLines": total[0],
    "totalBytes": total[1],
    "lines": lines,
    "contract_lines": contract_lines,
    "missing": find_missing(date, contract_lines)
  }

  checkbook_mongo_db().etlstatuslogs.replace_one(filter={"_id": date}, replacement=record, upsert=True)
  pass


def find_missing(date, contract_lines):
  missing = checkbook_mongo_db().etlstatusgpglogs.find_one({"_id": date})
  missing = list(missing.get('contract_lines').keys())
  for line in contract_lines:
    if line in missing:
      missing.remove(line)
  missing = [i.replace('.txt', '.pgp') for i in missing]
  return missing


# find_missing('20190919', [])


def log_fisa_gpg_contracts(filename, content):
  lines = [line.split() for line in content]
  total = lines.pop()
  (date, _, _) = filename.split('.')
  record = {
    "_id": date,
    "date": date,
    "file": filename,
    "totalBytes": total[0],
    "lines": lines,
    "contract_lines": filter_gpg(lines)
  }

  checkbook_mongo_db().etlstatusgpglogs.replace_one(filter={"_id": date}, replacement=record, upsert=True)
  pass


def filter_contracts(lines: List[str]) -> Dict:
  rules = checkbook_mongo_db().configs.find_one({"_id": "fisa_contract_regex"})
  rules = rules.get('rules')
  filtered = {}
  for line in lines:
    (num_lines, num_bytes, filename) = line
    filename = os.path.basename(filename)
    for rule in rules:
      if re.match(rule['actual_pattern'], filename):
        filtered[filename] = {
          'lines': num_lines,
          'bytes': num_bytes,
          'filename': filename
        }
        break
  return filtered


def filter_gpg(lines: List[str]) -> Dict:
  rules = checkbook_mongo_db().configs.find_one({"_id": "fisa_contract_regex"})
  rules = rules.get('rules')
  filtered = {}
  for line in lines:
    (num_bytes, filename) = line
    filename = os.path.basename(filename).replace('.pgp', '.txt')
    for rule in rules:
      if re.match(rule['actual_pattern'], filename):
        filtered[filename] = {
          'bytes': num_bytes,
          'filename': filename
        }
        break
  return filtered


def test():
  sample = [
    ["40900", "6062535", "/filepath/AIE2_DLY_PCO_MA_20190912004414.txt"],
    ["3028", "248396", "/filepath/AIE3_DLY_BUD_92L3_20190905004322.txt"],
    ["2925", "243332", "/filepath/AIE3_DLY_BUD_92L3_20190910011110.txt"],
    ["2925", "243332", "/filepath/AIEG_DLY_SCNTRC_BTY_20190906012248.txt"],
    ["447", "26428", "/filepath/AIE4_DLY_BUD_93L1_20190907004519.txt"]
  ]
  print(filter_contracts(sample))


# test()


def test_gpg():
  sample = [
    ["40900", "/filepath/AIE2_DLY_PCO_MA_20190912004414.pgp"],
    ["3028", "/filepath/AIE3_DLY_BUD_92L3_20190905004322.pgp"],
    ["2925", "/filepath/AIE3_DLY_BUD_92L3_20190910011110.pgp"],
    ["2925", "/filepath/AIEG_DLY_SCNTRC_BTY_20190906012248.pgp"],
    ["447", "/filepath/AIE2_DLY_PCO_MA_20190913004554.pgp"]
  ]
  print(filter_gpg(sample))

# test_gpg()
