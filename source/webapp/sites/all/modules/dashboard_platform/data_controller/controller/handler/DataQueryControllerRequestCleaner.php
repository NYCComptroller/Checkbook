<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/


class DataQueryControllerRequestCleaner extends AbstractObject {

    protected function adjustDatasetName($datasetName) {
        return StringHelper::trim($datasetName);
    }

    protected function adjustColumns($columns) {
        return ArrayElementTrimmer::trimList($columns);
    }

    protected function adjustOrderBy($orderBy) {
        return ArrayElementTrimmer::trimList($orderBy);
    }

    protected function adjustCompositeParameter($compositeParameter) {
        $adjustedCompositeParameter = NULL;

        $parameterIndex = 0;
        foreach ($compositeParameter as $key => $value) {
            $adjustedKey = is_string($key) ? StringHelper::trim($key) : $key;

            $adjustedValues = NULL;
            if ($value instanceof OperatorHandler) {
                // we do not need to change anything for the value
                $adjustedValues[] = $value;
            }
            else {
                // what if the value is a list of operators
                $operatorFound = FALSE;
                if (is_array($value)) {
                    foreach ($value as $v) {
                        if ($v instanceof OperatorHandler) {
                            $operatorFound = TRUE;
                            break;
                        }
                    }
                }

                // we found at least one operator in the list
                if ($operatorFound) {
                    foreach ($value as $k => $v) {
                        $adjustedValue = ($v instanceof OperatorHandler)
                            ? $v
                            : OperatorFactory::getInstance()->initiateHandler(EqualOperatorHandler::$OPERATOR__NAME, array($v));
                        $adjustedValue->weight = $parameterIndex++;

                        $adjustedValues[$k] = $adjustedValue;
                    }
                }
                else {
                    $adjustedValue = OperatorFactory::getInstance()->initiateHandler(EqualOperatorHandler::$OPERATOR__NAME, array($value));
                    $adjustedValue->weight = $parameterIndex++;

                    $adjustedValues[] = $adjustedValue;
                }
            }

            $adjustedCompositeParameter[$adjustedKey] = $adjustedValues;
        }

        return $adjustedCompositeParameter;
    }

    protected function adjustParameters($parameters) {
        $adjustedParameters = NULL;

        if (isset($parameters)) {
            if (is_array($parameters) && ArrayHelper::isIndexedArray($parameters)) {
                foreach ($parameters as $key => $compositeParameter) {
                    $adjustedParameters[$key] = $this->adjustCompositeParameter($compositeParameter);
                }
            }
            else {
                $adjustedParameters = $this->adjustCompositeParameter($parameters);
            }
        }

        return $adjustedParameters;
    }

    protected function cleanRequest($request) {
        if ($request instanceof DataQueryControllerRequestTree) {
            foreach ($request->branches as $branch) {
                $this->cleanRequest($branch);
            }
        }
        elseif ($request instanceof AbstractDataQueryControllerRequest) {
            $request->datasetName = $this->adjustDatasetName($request->datasetName);
            $request->columns = $this->adjustColumns($request->columns);
            $request->parameters = $this->adjustParameters($request->parameters);
            $request->orderBy = $this->adjustOrderBy($request->orderBy);
        }
    }

    protected function reformatRequest($request) {
        return $request;
    }

    public function adjustRequest($request) {
        $this->cleanRequest($request);

        return $this->reformatRequest($request);
    }
}
