import json
import urllib
import urllib2
import dp_settings
import dp_qs_parameter
import dp_data_filtering


PARAMETER_NAME__DATASET = 'dn'
PARAMETER_NAME__DATASET_VERSION = 'ver'
PARAMETER_NAME__COLUMNS = 'c'
PARAMETER_NAME__FILTERS = 'p'
PARAMETER_NAME__SORT = 's'
PARAMETER_NAME__OFFSET = 'o'
PARAMETER_NAME__LIMIT = 'l'


def getServerProperties(serverName):
    if (serverName in dp_settings.datasetServers):
        return dp_settings.datasetServers[serverName]

    raise ValueError('Undefined configuration for the server: {serverName}'.format(serverName = serverName))


def queryAPI(serverName, resourceURI, parameters = None):
    server = getServerProperties(serverName)

    fullResourceURI = '/api' + resourceURI

    uri = server['scheme'] + '://' + server['host'] + fullResourceURI + '?oauth_consumer_key=' + server['security']['consumer']['key']
    if (parameters != None):
        for name, value in parameters.iteritems():
            v = str(value) if (isinstance(value, int)) else value
            uri += '&' + name + '='
            if (v is not None):
                uri += urllib.quote_plus(v)

    handler = urllib2.urlopen(uri)
    executionCode = handler.getcode()
    if (executionCode != 200):
        raise SystemError("'{resource}' returned {code}".format(resource = fullResourceURI, code = executionCode))

    if (handler.geturl() != uri):
        raise SystemError('Redirect following is not supported')

    return {'header': handler.info(), 'body': handler.read()}


def defineDatasetColumns(serverName, datasetName):
    metadataResponse = queryAPI(serverName, '/data/' + datasetName + '/metadata.json')
    dataset = json.loads(metadataResponse['body'])
    return dataset['columns'] if ('columns' in dataset) else None


def queryDataset(serverName, datasetName, columns = None, filters = None, sort = None, offset = 0, limit = None):
    queryParameters = dict()

    serializedColumns = dp_qs_parameter.serializeParameterValue(PARAMETER_NAME__COLUMNS, columns)
    if (serializedColumns is not None):
        queryParameters.update(serializedColumns)

    serializedFilters = dp_qs_parameter.serializeParameterValue(PARAMETER_NAME__FILTERS, dp_data_filtering.serializeFilters(filters))
    if (serializedFilters is not None):
        queryParameters.update(serializedFilters)

    serializedSort = dp_qs_parameter.serializeParameterValue(PARAMETER_NAME__SORT, sort)
    if (serializedSort is not None):
        queryParameters.update(serializedSort)

    if (offset != 0):
        queryParameters[PARAMETER_NAME__OFFSET] = offset

    if (limit is not None):
        queryParameters[PARAMETER_NAME__LIMIT] = limit

    queryResponse = queryAPI(serverName, '/data/' + datasetName + '/dataset', queryParameters)

    return json.loads(queryResponse['body'])


def countDatasetRecords(serverName, datasetName, filters = None):
    queryParameters = dict()

    serializedFilters = dp_qs_parameter.serializeParameterValue(PARAMETER_NAME__FILTERS, filters)
    if (serializedFilters is not None):
        queryParameters.update(serializedFilters)

    countResponse = queryAPI(serverName, '/data/' + datasetName + '/dataset/count', queryParameters)

    return json.loads(countResponse['body'])
