import os
import re
import urlparse

def adjustParameterValue(value):
    if (value is None):
        return None

    simplifiedValue = value
    if (isinstance(value, dict)):
        # checking if all keys are numeric
        indexes = dict()
        for i in value.keys():
            if (isinstance(i, int)):
                indexes[i] = i
            elif (isinstance(i, str) and i.isdigit()):
                indexes[int(i)] = i
            else:
                indexes = None
                break

        if (indexes is None):
            # processing values in existing map
            simplifiedValue = dict()
            for k, v in value.iteritems():
                simplifiedValue[k] = adjustParameterValue(v)
        else:
            # converting the map to a list
            simplifiedValue = list()
            for index in sorted(indexes):
                l = len(simplifiedValue)
                # adding missing indexes
                if (l < index):
                    for i in range(l, index - 1):
                        simplifiedValue.append(None)

                # setting value with updated index
                originalIndex = indexes[index]
                simplifiedValue.append(adjustParameterValue(value[originalIndex]))
    elif (isinstance(value, list)):
        simplifiedValue = list()
        for v in value:
            simplifiedValue.append(adjustParameterValue(v))
    elif (isinstance(value, str)):
        if (value.isdigit()):
            simplifiedValue = int(value)
        elif (value == 'true'):
            simplifiedValue = True
        elif (value == 'false'):
            simplifiedValue = False
        else:
            try:
                simplifiedValue = float(simplifiedValue)
            except ValueError:
                simplifiedValue = simplifiedValue

    return simplifiedValue


def parseParameterValue(name, required, default = None):
    qs = os.environ['QUERY_STRING']
    values = urlparse.parse_qs(qs, False, True)
    if (values is None):
        return None

    value = None
    for n, v in values.iteritems():
        used = False
        if (n == name):
            value = v[0]
            used = True
        elif (n.startswith(name + '[')):
            indexes = re.findall('\w+', n[len(name):])
            l = len(indexes)
            if (l > 0):
                # assembling hierarchical structure and assigning value to a leaf in last branch
                if (value is None):
                    value = dict()
                parentHolder = value
                for index in indexes[:l - 1]:
                    if (index not in parentHolder):
                        parentHolder[index] = dict()
                    parentHolder = parentHolder[index]
                parentHolder[indexes[l - 1]] = v[0]
                used = True
        if (used and (len(v) > 1)):
            raise ValueError('Found several values for the parameter: {parameterName}'.format(parameterName = n))

    if (value == None):
        value = default
        if (required and (value == None)):
            raise ValueError('Undefined value for the parameter: {parameterName}'.format(parameterName = name))
    else:
        value = adjustParameterValue(value)

    return value


def serializeParameterValue(name, value):
    if (value is None):
        return None

    serializedValues = dict()

    serializedName = '' if (name is None) else name
    if (isinstance(value, dict)):
        for itemKey, itemValue in value.iteritems():
            serializedItemValue = serializeParameterValue(None, itemValue)
            for k, v in serializedItemValue.iteritems():
                key = serializedName + '[' + itemKey + ']' + k
                serializedValues[key] =  v
    elif (isinstance(value, list)):
        for itemKey, itemValue in enumerate(value):
            serializedItemValue = serializeParameterValue(None, itemValue)
            for k, v in serializedItemValue.iteritems():
                key = serializedName + '[' + str(itemKey) + ']' + k
                serializedValues[key] =  v
    else:
        key = serializedName

        serializedValue = value
        if (isinstance(serializedValue, bool)):
            serializedValue = 'true' if (serializedValue) else 'false'

        serializedValues[key] = serializedValue

    return serializedValues if (len(serializedValues) > 0) else None
