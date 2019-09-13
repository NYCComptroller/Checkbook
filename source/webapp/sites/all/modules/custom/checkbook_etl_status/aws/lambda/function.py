#import json
import urllib.parse
import boto3
import os
import pymongo

print('Loading function')

s3 = boto3.client('s3')


def lambda_handler(event, context):
  #print("Received event: " + json.dumps(event, indent=2))

  # Get the object from the event and show its content type
  bucket = event['Records'][0]['s3']['bucket']['name']
  key = urllib.parse.unquote_plus(event['Records'][0]['s3']['object']['key'], encoding='utf-8')
  try:
    response = s3.get_object(Bucket=bucket, Key=key)
    content = response['Body'].read().decode("utf-8").splitlines()

    lines = [line.split() for line in content]
    total = lines.pop()
    _, filename = key.split('/')
    date, _ = filename.split('.')
    record = {
      "_id": date,
      "date": date,
      "file": filename,
      "totalLines": total[0],
      "totalBytes": total[1],
      "lines": lines
    }

    connect = {
      'user': os.environ.get('MONGO_USER'),
      'pass': os.environ.get('MONGO_PASS'),
      'cluster': os.environ.get('MONGO_CLUSTER')
    }

    connect_string = "mongodb+srv://{user}:{pass}@{cluster}?retryWrites=true&w=majority".format(**connect)

    client = pymongo.MongoClient(connect_string)
    db = client.checkbooknyc
    collection = db.etlstatuslogs
    collection.replace_one(filter={"_id":date}, replacement=record, upsert=True)

    return response['ContentType']
  except Exception as e:
    print(e)
    print('Error getting object {} from bucket {}. Make sure they exist and your bucket is in the same region as this function.'.format(key, bucket))
    raise e
