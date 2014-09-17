#!/bin/awk -f
{

        line = $0;
        delimiter="|";
        no_of_attributes = split(line,attributes,delimiter);
        if(attributes[2]=="CT1" || attributes[2]=="CTA1" || attributes[2]=="CTA2")
	{
		#print no_of_attributes
        	#print "attr" attributes[1]
		if((attributes[1]=="H" && no_of_attributes==113) ||
		   (attributes[1]=="W" && no_of_attributes==69) ||
		   (attributes[1]=="V" && no_of_attributes==40) ||
		   (attributes[1]=="A" && no_of_attributes==71))
		{
		       print $0 >> "goodfile.txt"
		}
        	else if (attributes[1]=="H" || attributes[1]=="W" || attributes[1]=="V" || attributes[1]=="A") 
        	{
                	print "Line Number:" NR " " $0 >> "badfile.txt"
        	}	
	}
        if(attributes[2]=="POC" || attributes[2]=="PCC1" || attributes[2]=="POD")
	{
		#print no_of_attributes
        	#print "attr" attributes[1]
		if((attributes[1]=="H" && no_of_attributes==95) ||
		   (attributes[1]=="W" && no_of_attributes==35) ||
		   (attributes[1]=="V" && no_of_attributes==38) ||
		   (attributes[1]=="A" && no_of_attributes==71))
		{
        	        print $0 >> "goodfile.txt"
		}
        	else if (attributes[1]=="H" || attributes[1]=="W" || attributes[1]=="V" || attributes[1]=="A") 
        	{
                	print "Line Number:" NR " " $0 >> "badfile.txt"
        	}	
	}
        if(attributes[2]=="DO1"	)
	{
		#print no_of_attributes
        	#print "attr" attributes[1]
		if((attributes[1]=="H" && no_of_attributes==90) ||
		   (attributes[1]=="V" && no_of_attributes==37) ||
		   (attributes[1]=="A" && no_of_attributes==69))
		{
        	        print $0 >> "goodfile.txt"
		}
        	else if (attributes[1]=="H" || attributes[1]=="V" || attributes[1]=="A") 
        	{
                	print "Line Number:" NR " " $0 >> "badfile.txt"
        	}	
	}

}

