package navigation;

import org.openqa.selenium.By;
import org.openqa.selenium.JavascriptExecutor;
import org.openqa.selenium.WebElement;
import helpers.Driver;
import java.util.List;

class PrimaryTabSelector {
	static void Select(String primaryTabClass) {
		WebElement TabContainer = Driver.Instance.findElement(By.cssSelector(".top-navigation-left > table > tbody > tr"));
		List<WebElement> tabs = TabContainer.findElements(By.tagName("td"));
		for (WebElement tab : tabs) {
			String tabclass = tab.getAttribute("class");
			//if(tabclass.equals(primaryTabClass+" active") || tabclass.equals(primaryTabClass+" first active")){
				//break;
			//}else 
				if(tabclass.contains(primaryTabClass)){
				//tab.findElement(By.className("expense-container")).click();
				WebElement tabAnchor = tab.findElement(By.className("expense-container"));
				((JavascriptExecutor) Driver.Instance).executeScript("arguments[0].scrollIntoView(true);", tabAnchor);
                tabAnchor.click();
			
	                /*if (tab.getAttribute("class").equals("active")) {
	                 *                     break;
	                } else { */     		
			
			break;
			}
		}
		
	}
	static void SelectTopRightNavigation(String primaryTabClass) {
		WebElement TabContainer = Driver.Instance.findElement(By.cssSelector(".top-navigation-right >.featured-dashboard-table >table > tbody > tr"));
	
		List<WebElement> tabs = TabContainer.findElements(By.tagName("td"));
		for (WebElement tab : tabs) {
			String tabclass = tab.getAttribute("class");
			//if(tabclass.equals(primaryTabClass+" active") || tabclass.equals(primaryTabClass+" first active")){
				//break;
			//}else 
				if(tabclass.contains(primaryTabClass)){
				//tab.findElement(By.className("expense-container")).click();
				WebElement tabAnchor = tab.findElement(By.className("expense-container"));
				((JavascriptExecutor) Driver.Instance).executeScript("arguments[0].scrollIntoView(true);", tabAnchor);
                tabAnchor.click();
			
	                /*if (tab.getAttribute("class").equals("active")) {
	                 *                     break;
	                } else { */     		
			
			break;
			}
		}
	}
}
