import dp_api
import dp_qs_parameter
import dp_dataset_script
import dp_data_filtering


PARAMETER_NAME__CALLBACK_SERVER_NAME = 'callback'
PARAMETER_NAME__OPERATION = 'exec'

OPERATION_NAME__DATASET_COLUMN_DEFINITION = 'defineDatasetColumns'
OPERATION_NAME__DATASET_QUERY = 'queryDataset'
OPERATION_NAME__DATASET_RECORD_COUNT = 'countDatasetRecords'


def execute():
    result = None

    # ----- parsing query parameters
    callbackServerName = dp_qs_parameter.parseParameterValue(PARAMETER_NAME__CALLBACK_SERVER_NAME, True)
    operation = dp_qs_parameter.parseParameterValue(PARAMETER_NAME__OPERATION, True)
    datasetName = dp_qs_parameter.parseParameterValue(dp_api.PARAMETER_NAME__DATASET, True)
    datasetVersion = dp_dataset_script.parseVersion(dp_qs_parameter.parseParameterValue(dp_api.PARAMETER_NAME__DATASET_VERSION, False))
    columns = dp_qs_parameter.parseParameterValue(dp_api.PARAMETER_NAME__COLUMNS, False)
    filters = dp_data_filtering.parseFilters(dp_qs_parameter.parseParameterValue(dp_api.PARAMETER_NAME__FILTERS, False))
    sort = dp_qs_parameter.parseParameterValue(dp_api.PARAMETER_NAME__SORT, False)
    offset = dp_qs_parameter.parseParameterValue(dp_api.PARAMETER_NAME__OFFSET, False, 0)
    limit = dp_qs_parameter.parseParameterValue(dp_api.PARAMETER_NAME__LIMIT, False)

    # ----- preparing dataset script module
    module = dp_dataset_script.accessScriptModule(callbackServerName, datasetName, datasetVersion)

    # ----- retrieving dataset data
    func = getattr(module, operation, None)
    if (operation == OPERATION_NAME__DATASET_COLUMN_DEFINITION):
        result = None if (func is None) else func(callbackServerName, datasetName)
    elif (operation == OPERATION_NAME__DATASET_QUERY):
        if (func is None):
            raise NameError('{functionName} function was not defined'.format(functionName = operation))
        result = func(callbackServerName, datasetName, columns, filters, sort, offset, limit)
    elif (operation == OPERATION_NAME__DATASET_RECORD_COUNT):
        count = None if (func is None) else func(callbackServerName, datasetName, filters)
        if (count is None):
            funcQuery = getattr(module, OPERATION_NAME__DATASET_QUERY, None)
            if (funcQuery is None):
                if (func is None):
                    message = OPERATION_NAME__DATASET_QUERY + ' and ' + OPERATION_NAME__DATASET_RECORD_COUNT + ' functions were'
                else:
                    message = OPERATION_NAME__DATASET_QUERY + ' function was'
                message += ' not defined. Record count cannot be obtained'
                raise NameError(message)
            else:
                data = funcQuery(callbackServerName, datasetName, dp_data_filtering.getFilterColumnNames(filters), filters, None, 0, None)
                count = 0 if (data is None) else len(data)
        result = count
    else:
        raise NameError("'{operation}' is not supported yet".format(operation = operation))

    return result
