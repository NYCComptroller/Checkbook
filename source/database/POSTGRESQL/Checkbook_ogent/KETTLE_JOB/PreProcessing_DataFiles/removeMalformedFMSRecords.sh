#!/bin/awk -f
{

        line = $0;
        delimiter="|";
        no_of_attributes = split(line,attributes,delimiter);
	#print "attr"attributes[1]
	#print no_of_attributes
	if((attributes[1]=="H" && no_of_attributes==22) ||
	   (attributes[1]=="V" && no_of_attributes==19) ||
	   (attributes[1]=="V" && no_of_attributes==30) ||
	   (attributes[1]=="A" && no_of_attributes==49))
		{
		       print $0 >> "goodfile.txt"
		}
        	else 
        	{
        		if(attributes[1]=="H" || attributes[1]=="V" || attributes[1]=="A" )
        		{
                		print "Line Number:" NR " " $0 >> "badfile.txt"
                	}
        	}	
}


