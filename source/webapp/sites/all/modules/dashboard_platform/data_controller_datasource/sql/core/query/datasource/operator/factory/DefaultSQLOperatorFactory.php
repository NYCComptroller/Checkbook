<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/




class DefaultSQLOperatorFactory extends SQLOperatorFactory {

    protected function getHandlerClassName($operatorClassName) {
        $sqlOperatorClassName = 'SQL_' . $operatorClassName;

        if (!class_exists($sqlOperatorClassName)) {
            throw new IllegalArgumentException(t(
                'Unsupported SQL-driven extension for the operator: @className',
                array('@className' => $operatorClassName)));
        }

        return $sqlOperatorClassName;
    }

    public function getHandler(DataSourceQueryHandler $datasourceQueryHandler, OperatorHandler $operatorHandler) {
        $operatorClassName = get_class($operatorHandler);

        $sqlOperatorClassName = $this->getHandlerClassName($operatorClassName);

        return new $sqlOperatorClassName($datasourceQueryHandler, $operatorHandler);
    }
}
