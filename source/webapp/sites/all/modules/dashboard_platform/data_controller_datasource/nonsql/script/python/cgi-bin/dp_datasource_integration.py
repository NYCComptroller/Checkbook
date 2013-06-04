#!/usr/bin/python
import cgitb
import json
import dp_datasource_operation

cgitb.enable()

print 'Content-Type: text/plain'
print ''

result = dp_datasource_operation.execute()
if (result is not None):
    print json.dumps(result)
