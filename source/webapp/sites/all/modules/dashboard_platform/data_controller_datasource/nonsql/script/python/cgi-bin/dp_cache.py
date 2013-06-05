import os
import dp_settings


def getCachePath():
    return dp_settings.instance['cache']['path']


def checkPackageFolder(path):
    if (not os.path.isdir(path)):
        os.mkdir(path)

    # creating a 'hint' file for Python to initialize a package/module
    initFileName = path + '/__init__.py'
    if (not os.path.isfile(initFileName)):
        file = open(initFileName, 'w')
        file.close()
