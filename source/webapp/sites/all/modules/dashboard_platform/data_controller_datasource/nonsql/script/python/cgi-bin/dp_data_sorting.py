import functools

# ##############################################################################
#   Processing sort configuration
# ##############################################################################
class Sort(object):

    columnName = None
    ascending = True

    def __init__(self, sortColumnName):
        if (sortColumnName.startswith('-')):
            self.ascending = False
            self.columnName = sortColumnName[1:]
        else:
            self.columnName = sortColumnName


def parseSortColumnNames(sortColumnNames):
    if (sortColumnNames is None):
        return None

    sort = list()
    if (isinstance(sortColumnNames, list)):
        for sortColumnName in sortColumnNames:
            sort.append(Sort(sortColumnName))
    else:
        sortColumnName = sortColumnNames
        sort.append(Sort(sortColumnName))

    return sort


def containsColumnNames(sortColumnNames, columnNames):
    if (sortColumnNames is None):
        return False

    sort = parseSortColumnNames(sortColumnNames)
    for sortInstance in sort:
        if (sortInstance.columnName in columnNames):
            return True

    return False


# ##############################################################################
#   Sorting records
# ##############################################################################
def applySort(data, sortColumnNames):
    if ((data is None) or (sortColumnNames is None)):
        return False

    sort = parseSortColumnNames(sortColumnNames)

    def comparer(left, right):
        for sortInstance in sort:
            l = left[sortInstance.columnName] if (sortInstance.columnName in left) else None
            r = right[sortInstance.columnName] if (sortInstance.columnName in right) else None

            result = cmp(l, r)
            if (result != 0):
                if (not sortInstance.ascending):
                    result *= -1
                return result
        return 0
    data.sort(key = functools.cmp_to_key(comparer))

    return True
