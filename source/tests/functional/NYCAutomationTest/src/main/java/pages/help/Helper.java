package pages.help;

import java.util.regex.Matcher;
import java.util.regex.Pattern;

public class Helper {

	public static int GetTotalEntries(String textString, int index) {	
		String fifth = textString.split(" ")[index];
		return stringToInt(fifth);
	}
	
	 public static int stringToInt(String num) {
	    return Integer.parseInt(num.replace(",", ""));
	}
	 
	 public static Number billionStringToNumber(String num){
		 Pattern pattern = Pattern.compile("\\$(.*?)B");
		 Matcher matcher = pattern.matcher(num);
		 if (matcher.find()) {
			 return Double.parseDouble(matcher.group(1));	   
			}
		 else return 0;
	 }

}
