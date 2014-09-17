#!/usr/bin/gawk4 -f
{

        line = $0;
        fieldpat = "([^,]*)|(\"[^\"]+\")";
        no_of_attributes = patsplit(line,attributes,fieldpat);
        if(no_of_attributes==13){
                print $0 >> "goodfile.txt"
        } else
        {
                print "Line Number: " NR " Attributes: " no_of_attributes " " $0 >> "badfile.txt"
        }
}
