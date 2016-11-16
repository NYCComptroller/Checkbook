package navigation;

import java.util.List;

import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;

import utility.Driver;

public class SecondaryTabSelector {

	public static void Select(String secondaryTabName) {
		WebElement payrollContainer = Driver.Instance.findElement(By.cssSelector(".nyc_totals_links tr"));
		List<WebElement> tabs = payrollContainer.findElements(By.tagName("td"));
		for (WebElement tab : tabs) {
			WebElement tabAnchor = tab.findElement(By.tagName("a"));
			String tabValue = tabAnchor.getText();
			String tabName = tabValue.substring(0,tabValue.lastIndexOf("\n")).replace("\n", " ");
			if(tabName.equals(secondaryTabName)){
				if(tab.getAttribute("class").equals("active")){
					break;
				}else{
					tabAnchor.click();
					break;
				}
				
			}	
		}
		
		
	}

}
