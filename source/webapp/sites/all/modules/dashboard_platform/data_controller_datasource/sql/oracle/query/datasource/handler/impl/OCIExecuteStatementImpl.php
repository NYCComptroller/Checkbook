<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/


class OCIExecuteStatementImpl extends AbstractExecuteStatementImpl {

    public function executeIndividualStatement(DataSourceHandler $handler, $connection, $sql) {
        $statement = OCIImplHelper::oci_parse($connection, $sql);
        try {
            OCIImplHelper::oci_execute($connection, $statement, OCI_NO_AUTO_COMMIT);
            $affectedRecordCount = OCIImplHelper::oci_num_rows($connection, $statement);
        }
        catch (Exception $e) {
            OCIImplHelper::oci_free_statement($connection, $statement);

            throw $e;
        }
        OCIImplHelper::oci_free_statement($connection, $statement);

        return $affectedRecordCount;
    }
}
