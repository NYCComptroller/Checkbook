package pages.help;

import java.util.regex.Matcher;
import java.util.regex.Pattern;

import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;

import utility.Driver;

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
	 
	 public static boolean isPresent(By by) {
	    return Driver.Instance.findElements(by).size() > 0;
	 }
	 
	 public static String getCurrentSelectedYear(){
		 WebElement yearSelected = Driver.Instance.findElement(By.cssSelector("#year_list_chzn > .chzn-single > span"));
		 String[] year = (yearSelected.getText()).split(" ");
		 return year[0]+year[1];
	 }

}
