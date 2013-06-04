<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/


class MethodDocumentGenerator extends AbstractDocumentGenerator {

    public static $CSS_CLASS__METHOD_NAME = 'dpc_method_name';
    public static $CSS_CLASS__METHOD_DESC = 'dpc_method_desc';

    public static $CSS_CLASS__METHOD_PARAMETER_REQUIRED = 'dpc_method_param_required';
    public static $CSS_CLASS__METHOD_PARAMETER_OPTIONAL = 'dpc_method_param_optional';

    protected $methodName = NULL;

    public function __construct(AbstractDocumentGenerator $parent, $methodName) {
        parent::__construct($parent);
        $this->methodName = $methodName;
    }

    protected function getResourceName() {
        $resourceGenerator = $this->getParentGenerator('ResourceDocumentGenerator');

        return $resourceGenerator->resourceName;
    }

    protected function getResourceDef() {
        $endpointGenerator = $this->getParentGenerator('EndPointDocumentGenerator');

        return $endpointGenerator->endpointDef[$this->getResourceName()];
    }

    protected function findMethodDef() {
        $resourceDef = $this->getResourceDef();

        return isset($resourceDef[$this->methodName]) ? $resourceDef[$this->methodName] : NULL;
    }

    protected function findIdentifierName($def, $index) {
        $name = NULL;

        if (isset($def['args'])) {
            foreach ($def['args'] as $argumentDef) {
                if (!isset($argumentDef['source'])) {
                    continue;
                }
                if (!is_array($argumentDef['source'])) {
                    continue;
                }
                if (!isset($argumentDef['source']['path'])) {
                    continue;
                }

                if ($argumentDef['source']['path'] == $index) {
                    $name = isset($argumentDef['name']) ? $argumentDef['name'] : NULL;
                    break;
                }
            }
        }

        return $name;
    }

    protected function getIdentifierName($def, $index) {
        $name = $this->findIdentifierName($def, $index);
        if (!isset($name)) {
            throw new IllegalStateException(t('Undefined entity name'));
        }

        return $name;
    }

    protected function prepareMethodURI($def) {
        $httpMethod = $uriSuffix = NULL;
        switch ($this->methodName) {
            case 'index':
                $httpMethod = 'GET';
                break;
            case 'retrieve':
                $httpMethod = 'GET';
                $uriSuffix = '/:' . $this->getIdentifierName($def, 0);
                break;
            case 'create':
                $httpMethod = 'POST';
                $identifierName = $this->findIdentifierName($def, 0);
                if (isset($identifierName)) {
                    $uriSuffix = '/:' . $identifierName;
                }
                break;
            case 'update':
                $httpMethod = 'PUT';
                $uriSuffix = '/:' . $this->getIdentifierName($def, 0);
                break;
            case 'delete':
                $httpMethod = 'DELETE';
                $uriSuffix = '/:' . $this->getIdentifierName($def, 0);
                break;
            default:
                throw new UnsupportedOperationException();
        }

        $uri = $httpMethod . ' /api/' . $this->getResourceName();
        if (isset($uriSuffix)) {
            $uri .= $uriSuffix;
        }

        return $uri;
    }

    protected function prepareQueryString($def) {
        $queryString = NULL;

        if (isset($def['args'])) {
            foreach ($def['args'] as $argumentDef) {
                if (!isset($argumentDef['source'])) {
                    continue;
                }
                if (!is_array($argumentDef['source'])) {
                    continue;
                }
                if (!isset($argumentDef['source']['param'])) {
                    continue;
                }

                $parameterName = $argumentDef['source']['param'];
                $parameterValue = ':' . (isset($argumentDef['name']) ? $argumentDef['name'] : $parameterName);

                if (isset($queryString)) {
                    $queryString .= '&amp;';
                }

                $isRequired = isset($argumentDef['optional']) ? ($argumentDef['optional'] == FALSE) : FALSE;

                $tagAttributes = NULL;
                if (isset($argumentDef['description'])) {
                    $tagAttributes['title'] = htmlspecialchars($argumentDef['description']);
                }

                if ($isRequired) {
                    $queryString .= self::startTag('strong', self::$CSS_CLASS__METHOD_PARAMETER_REQUIRED, $tagAttributes)
                        . $parameterName
                        . self::endTag('strong');
                }
                else {
                    $queryString .= self::startTag('em', self::$CSS_CLASS__METHOD_PARAMETER_OPTIONAL, $tagAttributes)
                        . $parameterName
                        . self::endTag('em');
                }
                $queryString .= '=' . $parameterValue;
            }
        }

        return $queryString;
    }

    protected function prepareMessageBodyParameters($def) {
        $parameters = NULL;

        if (isset($def['args'])) {
            foreach ($def['args'] as $argumentDef) {
                if (!isset($argumentDef['source'])) {
                    continue;
                }
                $isArgumentDataRelated = FALSE;
                if (is_array($argumentDef['source'])) {
                    if ((isset($argumentDef['source'][0]) && ($argumentDef['source'][0] == 'data'))
                            || isset($argumentDef['source']['data'])) {
                        $isArgumentDataRelated = TRUE;
                    }
                }
                elseif ($argumentDef['source'] == 'data') {
                    $isArgumentDataRelated = TRUE;
                }
                if (!$isArgumentDataRelated) {
                    continue;
                }

                $parameterName = $argumentDef['name'];
                $parameterType = $argumentDef['type'];

                $isRequired = isset($argumentDef['optional']) ? ($argumentDef['optional'] == FALSE) : FALSE;

                $tagAttributes = NULL;
                if (isset($argumentDef['description'])) {
                    $tagAttributes['title'] = htmlspecialchars($argumentDef['description']);
                }

                $parameter = NULL;
                if ($isRequired) {
                    $parameter = self::startTag('strong', self::$CSS_CLASS__METHOD_PARAMETER_REQUIRED, $tagAttributes)
                        . $parameterName
                        . self::endTag('strong');
                }
                else {
                    $parameter = self::startTag('em', self::$CSS_CLASS__METHOD_PARAMETER_OPTIONAL, $tagAttributes)
                        . $parameterName
                        . self::endTag('em');
                }
                $parameters[] = "$parameter ($parameterType)";
            }
        }

        return $parameters;
    }

    protected function generateNestedResource($name, $def, $useIdentifier, &$buffer) {
        $methodURI = $this->prepareMethodURI($def);

        $uri = $methodURI;
        if (isset($name)) {
            $uri .= '/' . $name;
        }

        if ($useIdentifier) {
            $identifierName = $this->findIdentifierName($def, 2);
            if (isset($identifierName)) {
                $uri .= '/:' . $identifierName;
            }
        }

        $buffer .= self::startTag('tr');

        $buffer .= self::startTag('td', self::$CSS_CLASS__METHOD_NAME);
        $queryString = $this->prepareQueryString($def);
        $buffer .= $uri . (isset($queryString) ? "?$queryString" : '');
        $buffer .= self::endTag('td');

        $buffer .= self::startTag('td', self::$CSS_CLASS__METHOD_NAME);
        $bodyParameters = $this->prepareMessageBodyParameters($def);
        if (isset($bodyParameters)) {
            foreach ($bodyParameters as $parameter) {
                $buffer .= self::startTag('div') . $parameter . self::endTag('div');
            }
        }
        $buffer .= self::endTag('td');

        $buffer .= self::startTag('td', self::$CSS_CLASS__METHOD_DESC);
        $buffer .= isset($def['help']) ? $def['help'] : '';
        $buffer .= self::endTag('td');

        $buffer .= self::endTag('tr');
    }

    protected function startGeneration(&$buffer) {
        $methodDef = $this->findMethodDef();

        if (isset($methodDef)) {
            $this->generateNestedResource(NULL, $methodDef, FALSE, $buffer);
        }

        $resourceDef = $this->getResourceDef();
        switch ($this->methodName) {
            case 'create':
                // adding support for actions
                if (isset($resourceDef['actions'])) {
                    foreach ($resourceDef['actions'] as $actionName => $actionDef) {
                        $this->generateNestedResource($actionName, $actionDef, FALSE, $buffer);
                    }
                }
                // adding support for targeted actions
                if (isset($resourceDef['targeted actions'])) {
                    foreach ($resourceDef['targeted actions'] as $actionName => $actionDef) {
                        $this->generateNestedResource($actionName, $actionDef, FALSE, $buffer);
                    }
                }
                break;
            case 'retrieve':
                // adding support for relationship
                if (isset($resourceDef['relationships'])) {
                    foreach ($resourceDef['relationships'] as $relationshipName => $relationshipDef) {
                        $this->generateNestedResource($relationshipName, $relationshipDef, TRUE, $buffer);
                    }
                }
                break;
        }
    }
}
