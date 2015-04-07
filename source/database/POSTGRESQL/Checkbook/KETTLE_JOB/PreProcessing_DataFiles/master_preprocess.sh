#!/bin/sh

cd /home/gpadmin/POSTGRESQL/Checkbook/KETTLE_JOB/PreProcessing_DataFiles/

echo $1
echo $2

rm -f goodfile.txt
rm -f badfile.txt
dirName="/home/gpadmin/POSTGRESQL/Checkbook/GPFDIST/datafiles/"
fileName=$dirName$2
tempFile="temp_file_utf8.txt"
tempFileName=$dirName$tempFile
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

elif [ $1 == "SubVendor" ] ; then
        ./removeMalformedSubConBusTypeRecords.sh $fileName

elif [ $1 == "SubConStatus" ] ; then
        ./removeMalformedSubConStatusRecords.sh $fileName

elif [ $1 == "SubContract" ] ; then
	iconv -f ISO88592 -t UTF8 < $fileName > $tempFileName
	rm -rf $fileName
	mv $tempFileName $fileName 
	rm -rf $tempFileName
        ./removeMalformedSubContractRecords.sh $fileName

elif [ $1 == "SubSpending" ] ; then
	iconv -f ISO88592 -t UTF8 < $fileName > $tempFileName
        rm -rf $fileName
        mv $tempFileName $fileName
	rm -rf $tempFileName
        ./removeMalformedSubSpendingRecords.sh $fileName

fi

if ! [ -f "goodfile.txt" ]; then
        touch "goodfile.txt"
fi

mv goodfile.txt $fileName
if ! [ -f "badfile.txt" ]; then
        touch "badfile.txt"
fi


chmod 745 $fileName 
