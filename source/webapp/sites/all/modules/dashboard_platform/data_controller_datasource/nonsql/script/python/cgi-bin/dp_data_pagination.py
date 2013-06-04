def applyPagination(data, offset, limit):
    if (data is None):
        return False

    if (limit is None):
        if (offset == 0):
            return False
        else:
            del data[:offset]
    else:
        if (offset > 0):
            del data[:offset]
        del data[limit:]

    return True
