#!/bin/sh
echo $1
echo $2

gpssh  -h mdw1 -e "/home/gpadmin/athiagarajan/NYC/master_preprocess.sh $1 $2"
