package pages;

import java.util.List;

import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;

import utility.Driver;

public class HomePage {

	public static void GoTo(String url) {
		Driver.GoTo(url);
	}

	public static void SelectYear(String year) {
		String yearRequired = YearType.getCurrentYear(year);
		WebElement yearSelected = Driver.Instance.findElement(By.cssSelector("#year_list_chzn > .chzn-single > span"));	
		 if(!(yearSelected.getText()).equals(yearRequired)){
			 WebElement dropdownContainer = Driver.Instance.findElement(By.cssSelector("#year_list_chzn > .chzn-single"));
			 dropdownContainer.click();
			 WebElement dropdown = Driver.Instance.findElement(By.cssSelector("#year_list_chzn > .chzn-drop > .chzn-results")); 
			 List<WebElement> options = dropdown.findElements(By.tagName("li"));
			 WebElement selectedYear = null;
			 for (WebElement option : options) {
				 String optionYear = option.getText();
				if(optionYear.equals(yearRequired)){
					selectedYear = options.get(options.indexOf(option));
					break;
				}
			}
			selectedYear.click();			 
		 }
		
	}

}
