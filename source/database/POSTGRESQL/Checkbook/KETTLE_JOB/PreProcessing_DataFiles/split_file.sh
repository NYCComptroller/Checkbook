#!/bin/sh

cd /home/gpadmin/POSTGRESQL_DB/KETTLE_JOB/PreProcessing_DataFiles/

echo $1
echo $2

dirName="/home/gpadmin/POSTGRESQL_DB/GPFDIST/datafiles/"
fileName=$dirName$2


if [ $1 == "CON" ]
then
        rm $dirName/CON_feed_*.txt -f

        grep -i '^H|CT' $fileName > $dirName/CON_feed_CT_Header.txt
        grep -i '^V|CT' $fileName > $dirName/CON_feed_CT_Vendor.txt
        grep -i '^W|CT' $fileName > $dirName/CON_feed_CT_Detail.txt
        grep -i '^A|CT' $fileName > $dirName/CON_feed_CT_Accounting_line.txt

        grep -i '^H|P' $fileName > $dirName/CON_feed_PO_Header.txt
        grep -i '^V|P' $fileName > $dirName/CON_feed_PO_Vendor.txt
        grep -i '^W|P' $fileName > $dirName/CON_feed_PO_Detail.txt
        grep -i '^A|P' $fileName > $dirName/CON_feed_PO_Accounting_line.txt

        grep -i '^H|DO' $fileName > $dirName/CON_feed_DO_Header.txt
        grep -i '^V|DO' $fileName > $dirName/CON_feed_DO_Vendor.txt
        grep -i '^A|DO' $fileName > $dirName/CON_feed_DO_Accounting_line.txt

fi

if [ $1 == "MAG" ]
then
        rm $dirName/MAG_feed_*.txt -f

        grep -i '^H|' $fileName > $dirName/MAG_feed_Header.txt
        grep -i '^V|' $fileName > $dirName/MAG_feed_Vendor.txt
        grep -i '^W|' $fileName > $dirName/MAG_feed_Detail.txt

fi

if [ $1 == "FMS" ]
then
        rm $dirName/FMS_feed_*.txt -f

        grep -i '^H|' $fileName > $dirName/FMS_feed_Header.txt
        grep -i '^V|' $fileName > $dirName/FMS_feed_Vendor.txt
        grep -i '^A|' $fileName > $dirName/FMS_feed_Accounting_line.txt


fi

