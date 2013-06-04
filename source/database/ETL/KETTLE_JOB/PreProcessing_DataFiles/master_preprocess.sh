#!/bin/sh

cd /home/gpadmin/ETL/KETTLE_JOB/PreProcessing_DataFiles/

echo $1
echo $2

rm -f goodfile.txt
rm -f badfile.txt
dirName="/home/gpadmin/ETL/GPFDIST/datafiles/"
fileName=$dirName$2
if [ $1 == "COAAgency" ] ; then	
	./removeMalformedAgencyRecords.sh $fileName
	
elif [ $1 == "COABudgetCode" ] ; then
	./removeMalformedBudgetCodeRecords.sh $fileName
	
elif [ $1 == "COADepartment" ] ; then
	./removeMalformedDepartmentRecords.sh $fileName
	
elif [ $1 == "COAExpenditureObject" ] ; then
	./removeMalformedExpenditureObjectRecords.sh $fileName
	 
elif [ $1 == "COALocation" ] ; then
	./removeMalformedLocationRecords.sh $fileName
	 
elif [ $1 == "COAObjectClass" ] ; then
	./removeMalformedObjectClassRecords.sh $fileName
	 
elif [ $1 == "COARevenueCategory" ] ; then
	./removeMalformedRevenueCategoryRecords.sh $fileName
	 
elif [ $1 == "COARevenueClass" ] ; then
	./removeMalformedRevenueClassRecords.sh $fileName
	 
elif [ $1 == "COARevenueSource" ] ; then
	./removeMalformedRevenueSourceRecords.sh $fileName
	
elif [ $1 == "CON" ] ; then
	./removeMalformedCONRecords.sh $fileName
	 
elif [ $1 == "FMS" ] ; then
	./removeMalformedFMSRecords.sh $fileName
	 
elif [ $1 == "MAG" ] ; then
	./removeMalformedMAGRecords.sh $fileName
	 
elif [ $1 == "FundingClass" ] ; then
	./removeMalformedFundingClassRecords.sh $fileName
	
elif [ $1 == "Revenue" ] ; then
	./removeMalformedRevenueRecords.sh $fileName
	 
elif [ $1 == "Budget" ] ; then
	./removeMalformedBudgetRecords.sh $fileName
	
elif [ $1 == "PMSSummary" ] ; then
	./removeMalformedPayrollSummaryRecords.sh $fileName	
	
elif [ $1 == "PMS" ] ; then
	./removeMalformedPMSRecords.sh $fileName	
	
elif [ $1 == "PendingContracts" ] ; then
	./removeMalformedPendingContracts.sh $fileName
elif [ $1 == "FMSV" ] ; then
	./removeMalformedBusinessTypeRecords.sh $fileName
	
elif [ $1 == "RevenueBudget" ] ; then
	./removeMalformedRevenueBudgetRecords.sh $fileName

fi

if ! [ -f "goodfile.txt" ]; then
        touch "goodfile.txt"
fi

mv goodfile.txt $fileName
if ! [ -f "badfile.txt" ]; then
        touch "badfile.txt"
fi


