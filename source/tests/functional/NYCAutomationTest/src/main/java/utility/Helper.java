package utility;

import java.util.regex.Matcher;
import java.util.regex.Pattern;

import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;

public class Helper {

	public static int GetTotalEntries(String textString, int index) {	
		String[] text = textString.split(" ");
		String fifth = text[index];
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
	 
	 public static float billionStringToFloat(String num){
		 Pattern pattern1 = Pattern.compile("\\$(.*?)B");
		 Matcher matcher1 = pattern1.matcher(num);
		 Pattern pattern2 = Pattern.compile("\\$(.*?)M");
		 Matcher matcher2 = pattern2.matcher(num);
		 Pattern pattern3 = Pattern.compile("\\$(.*?)K");
		 Matcher matcher3 = pattern3.matcher(num);
		 if (matcher1.find()) {
			 return Float.parseFloat(matcher1.group(1));	   
		 }else if(matcher2.find()){
			 return Float.parseFloat(matcher2.group(1));
		 }else if(matcher3.find()){
			 return Float.parseFloat(matcher3.group(1));
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
