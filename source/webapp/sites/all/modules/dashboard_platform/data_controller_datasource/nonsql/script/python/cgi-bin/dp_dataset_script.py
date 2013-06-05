import os
import sys
import importlib
import dp_api
import dp_cache


def getScriptCachePath():
    return dp_cache.getCachePath() + '/script'

# registering dataset script location with Python
sys.path.append(getScriptCachePath())


def convertDatasetName2ModuleName(datasetName):
    moduleName = datasetName.replace(':', '_')
    return moduleName


def parseVersion(version):
    adjustedVersion = None
    if (version is None):
        adjustedVersion = None
    elif (isinstance(version, int)):
        adjustedVersion = str(version)
    elif (adjustedVersion(version, str)):
        adjustedVersion = version
    else:
        raise ValueError('Unsupported format of version: {classname}'.format(classname = version.__class__.__name__))

    return adjustedVersion


def prepareScriptCacheFileName(serverName, datasetName, version):
    moduleName = convertDatasetName2ModuleName(datasetName)

    filename = getScriptCachePath() + '/' + serverName + '/' + moduleName
    if (version is not None):
        filename += '_' + version
    filename += '.py'

    return filename


def isScriptInCache(serverName, datasetName, version):
    filename = prepareScriptCacheFileName(serverName, datasetName, version)

    return os.path.isfile(filename)


def requestScriptBody(serverName, datasetName):
    scriptResponse = dp_api.queryAPI(serverName, '/dataset/' + datasetName + '/script.txt')
    script = scriptResponse['body']
    version = scriptResponse['header'].get('ETag')

    return {'script': script, 'version': version}


def cacheScript(serverName, datasetName, version, script):
    scriptPath = getScriptCachePath()
    if (not os.path.isdir(scriptPath)):
        os.mkdir(scriptPath)

    packagePath = scriptPath + '/' + serverName
    dp_cache.checkPackageFolder(packagePath)

    filename = prepareScriptCacheFileName(serverName, datasetName, version)

    tempFileName = filename + '.temp'
    file = open(tempFileName, 'w')
    try:
        file.write(script)
    finally:
        file.close()

    if (os.path.isfile(filename)):
        os.remove(filename)
    os.rename(tempFileName, filename)


def importScriptModule(serverName, datasetName, version):
    packageName = serverName
    moduleName = convertDatasetName2ModuleName(datasetName)
    if (version is not None):
        moduleName += '_' + version

    package = importlib.import_module(packageName)
    return importlib.import_module(packageName + '.' + moduleName)


def accessScriptModule(serverName, datasetName, version):
    found = isScriptInCache(serverName, datasetName, version)
    if (not found):
        scriptProperties = requestScriptBody(serverName, datasetName)
        script = scriptProperties['script']
        version = scriptProperties['version']

        cacheScript(serverName, datasetName, version, script)

    return importScriptModule(serverName, datasetName, version)
