TODAY=$(date)
SCRIPT_DIR=/home/gpadmin/POSTGRESQL/Checkbook_ogent/CREATE_NEW_DATABASE
export SCRIPT_DIR
EXEC_TIME=`date +%m%d%Y:%T`
export EXEC_TIME
DB_NAME=checkbook_ogent_postgresql
export DB_NAME
echo "--------------------CREATE TABLES STRUCTURES---------------------"

psql -d $DB_NAME -f $SCRIPT_DIR/NYCCheckbookDDL.sql
psql -d $DB_NAME -f $SCRIPT_DIR/NYCCheckbookETL_DDL.sql
psql -d $DB_NAME -f $SCRIPT_DIR/NYCCheckbookDDL_EDC.sql
psql -d $DB_NAME -f $SCRIPT_DIR/NYCCheckbookETL_DDL_EDC.sql

echo "--------------------Reference tables-----------------------------"

psql -d $DB_NAME -f $SCRIPT_DIR/ScriptsForReferenceTables.sql

echo "--------------------Creating Procedures-----------------------------"
psql -d $DB_NAME -f $SCRIPT_DIR/COAScripts.sql 
psql -d $DB_NAME -f $SCRIPT_DIR/FMSVScripts.sql
psql -d $DB_NAME -f $SCRIPT_DIR/MAGScripts.sql 
psql -d $DB_NAME -f $SCRIPT_DIR/CONScripts.sql 
psql -d $DB_NAME -f $SCRIPT_DIR/CON-DOScripts.sql
psql -d $DB_NAME -f $SCRIPT_DIR/CON-POScripts.sql 
psql -d $DB_NAME -f $SCRIPT_DIR/FMSScripts.sql 
psql -d $DB_NAME -f $SCRIPT_DIR/Scripts.sql 
psql -d $DB_NAME -f $SCRIPT_DIR/RevenueBudgetScripts.sql 
psql -d $DB_NAME -f $SCRIPT_DIR/RevenueScripts.sql 
psql -d $DB_NAME -f $SCRIPT_DIR/PMSScripts.sql 
psql -d $DB_NAME -f $SCRIPT_DIR/EDCContracts.sql
psql -d $DB_NAME -f $SCRIPT_DIR/TDCContracts.sql
psql -d $DB_NAME -f $SCRIPT_DIR/RestoreOGETransactionsData.sql
psql -d $DB_NAME -f $SCRIPT_DIR/ModifyFMSDataForOGE.sql

psql -d $DB_NAME -f $SCRIPT_DIR/PendingContracts.sql  
echo "--pendingscripts "
psql -d $DB_NAME -f $SCRIPT_DIR/VendorScripts.sql 
psql -d $DB_NAME -f $SCRIPT_DIR/ExternalTableScripts.sql

