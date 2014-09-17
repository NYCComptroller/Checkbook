cd /home/gpadmin/POSTGRESQL/Checkbook_ogent/KETTLE_JOB/DUMP_AND_RESTORE
rm -rf errors_analysys.txt
grep -Ric "ERROR" *.err > errors_analysys.txt
output=$(grep -ic ".err:0" errors_analysys.txt)
if [[ $output = 8 ]]; then
    echo "Success"
 else
    echo "Fail"
 fi
