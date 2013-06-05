import re

class AbstractOperator(object):

    parameterValues = None

    def __init__(self, *unnamedParameterValues, **namedParameterValues):
        parameterValues = dict()

        supportedParameters = self.getSupportedParameterNames()
        if (supportedParameters is not None):
            for index, name in enumerate(supportedParameters):
                if (name in namedParameterValues):
                    parameterValues[name] = namedParameterValues[name]
                elif (index < len(unnamedParameterValues)):
                    parameterValues[name] = unnamedParameterValues[index]

        self.parameterValues = parameterValues if (len(parameterValues) > 0) else None

    def getSupportedParameterNames(self):
        return None

    def getParameterValue(self, parameterName, composite = False, required = False):
        operatorValues = None
        if ((self.parameterValues is not None) and (parameterName in self.parameterValues)):
            operatorValues = self.parameterValues[parameterName]

        if (operatorValues is None):
            if (required):
                if (self.parameterValues is None):
                    raise ValueError(
                        "Undefined '{parameterName}' parameter for '{operatorName}' operator".format(
                            operatorName = self.__class__.__name__, parameterName = parameterName))
                else:
                    raise ValueError(
                        "Undefined '{parameterName}' parameter for '{operatorName}' operator. Provided parameters: [{availableParameterNames}]".format(
                            operatorName = self.__class__.__name__, parameterName = parameterName, availableParameterNames = ','.join(self.parameterValues.keys())))
        elif (not composite and isinstance(operatorValues, list)):
            raise ValueError(
                "Composite parameter value is not allowed for '{operatorName}' operator: {parameterName}".format(
                    operatorName = self.__class__.__name__, parameterName = parameterName))

        return operatorValues

    def getParameters(self):
        return self.parameterValues

    def check(self, value):
        return False


class EmptyOperator(AbstractOperator):

    name = 'empty'

    def check(self, value):
        return value is None


class NotEmptyOperator(AbstractOperator):

    name = 'empty.not'

    def check(self, value):
        return value is not None


class AbstractValueBasedOperator(AbstractOperator):

    values = None

    def __init__(self, *unnamedParameterValues, **namedParameterValues):
        super(AbstractValueBasedOperator, self).__init__(*unnamedParameterValues, **namedParameterValues)
        self.values = self.getParameterValue('value', True, False)

    def getSupportedParameterNames(self):
        return ['value']


class EqualOperator(AbstractValueBasedOperator):

    name = 'equal'

    def check(self, value):
        if (isinstance(self.values, list)):
            result = False
            for v in self.values:
                if (value == v):
                    result = True
                    break
        else:
            result = value == self.values

        return result


class NotEqualOperator(EqualOperator):

    name = 'equal.not'

    def check(self, value):
        return not super(NotEqualOperator, self).check(value)


class LessThanOperator(AbstractValueBasedOperator):

    name = 'less.than'

    def check(self, value):
        return (value is not None) and (value < self.values)


class LessOrEqualOperator(AbstractValueBasedOperator):

    name = 'less.or.equal'

    def check(self, value):
        return (value is not None) and (value <= self.values)


class GreaterThanOperator(AbstractValueBasedOperator):

    name = 'greater.than'

    def check(self, value):
        return (value is not None) and (value > self.values)


class GreaterOrEqualOperator(AbstractValueBasedOperator):

    name = 'greater.or.equal'

    def check(self, value):
        return (value is not None) and (value >= self.values)


class RangeOperator(AbstractOperator):

    name = 'range'

    fromValue = None
    toValue = None

    def __init__(self, *unnamedParameterValues, **namedParameterValues):
        super(RangeOperator, self).__init__(*unnamedParameterValues, **namedParameterValues)
        self.fromValue = self.getParameterValue('from', False, False)
        self.toValue = self.getParameterValue('to', False, False)

    def getSupportedParameterNames(self):
        return ['from', 'to']

    def check(self, value):
        result = True

        if (self.fromValue is not None):
            if (value < self.fromValue):
                result = False

        if (self.toValue is not None):
            if (value > self.toValue):
                result = False

        if ((self.fromValue is None) and (self.toValue is None) and (value is not None)):
            result = False

        return result


class NotInRangeOperator(RangeOperator):

    name = 'range.not'

    def check(self, value):
        return not super(NotInRangeOperator, self).check(value)


class WildcardOperator(AbstractOperator):

    name = 'wildcard'

    program = None

    def __init__(self, *unnamedParameterValues, **namedParameterValues):
        super(WildcardOperator, self).__init__(*unnamedParameterValues, **namedParameterValues)

        wildcard = self.getParameterValue('wildcard', False, True)

        # preparing regular expression
        pattern = ''
        mapping = None
        for c in wildcard:
            if (c == '?'):
                mapping = '.'
            elif (c == '*'):
                mapping = '.*'
            else:
                mapping = '\\' + hex(ord(c))[1:]
            pattern += mapping
        # matching end of string
        if (mapping != '*'):
            pattern += '$'

        self.program = re.compile(pattern, re.IGNORECASE | re.DOTALL)

    def getSupportedParameterNames(self):
        return ['wildcard']

    def check(self, value):
        return False if (value is None) else (self.program.match(value) is not None)


class NotWildcardOperator(WildcardOperator):

    name = 'wildcard.not'

    def check(self, value):
        return not super(NotWildcardOperator, self).check(value)



operatorClasses = dict()

def registerOperatorClass(operatorClass):
    operatorClasses[operatorClass.name] = operatorClass


def initiateOperator(operatorName, parameterValues):
    if (operatorName not in operatorClasses):
        raise NameError('Unsupported operator: {operatorName}'.format(operatorName = operatorName))

    classRef = operatorClasses[operatorName]

    return classRef(**parameterValues)


registerOperatorClass(EmptyOperator)
registerOperatorClass(NotEmptyOperator)
registerOperatorClass(EqualOperator)
registerOperatorClass(NotEqualOperator)
registerOperatorClass(LessThanOperator)
registerOperatorClass(LessOrEqualOperator)
registerOperatorClass(GreaterThanOperator)
registerOperatorClass(GreaterOrEqualOperator)
registerOperatorClass(RangeOperator)
registerOperatorClass(NotInRangeOperator)
registerOperatorClass(WildcardOperator)
registerOperatorClass(NotWildcardOperator)
