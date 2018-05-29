package navigation;

import java.util.List;

import org.openqa.selenium.By;
import org.openqa.selenium.JavascriptExecutor;
import org.openqa.selenium.WebElement;

import helpers.Driver;

public class SecondarySubVendorTabSelector {
	public static void Select(String secondaryTabName) {
	
        WebElement secTabContainer = Driver.Instance.findElement(By.cssSelector(".nyc_subvendors_totals_links tr"));
        List<WebElement> tabs = secTabContainer.findElements(By.tagName("td"));
        for (WebElement tab : tabs) {
            WebElement tabAnchor = tab.findElement(By.tagName("a"));
            String tabValue = tabAnchor.getText();
            String tabName;
            String subStr = tabValue.substring(tabValue.indexOf("\n")+1, tabValue.lastIndexOf("\n"));
            if(subStr.contains("\n"))
            	tabName = tabValue.substring(tabValue.indexOf("\n")+1, tabValue.lastIndexOf("\n")).replace("\n", " ");
            else
            	tabName = tabValue.substring(0, tabValue.lastIndexOf("\n")).replace("\n", " ");
            if (tabName.equals(secondaryTabName)) {
                /*if (tab.getAttribute("class").equals("active")) {
                    break;
                } else { */
            	((JavascriptExecutor) Driver.Instance).executeScript("arguments[0].scrollIntoView(true);", tabAnchor);
                    tabAnchor.click();
                    //WebElement tabAnchor = tabAnchor.findElement(By.partialLinkText("Details"));
                
                    break;
                //}

            }
        }
    }
	 static boolean isAt(String secondaryTabName) {
	        WebElement secTabContainer = Driver.Instance.findElement(By.cssSelector(".nyc_subvendors_totals_links tr"));
	        List<WebElement> tabs = secTabContainer.findElements(By.tagName("td"));
	        Boolean isAt = false;
	        for (WebElement tab : tabs) {
	            WebElement tabAnchor = tab.findElement(By.tagName("a"));
	            String tabValue = tabAnchor.getText();
	            String tabName;
	            String subStr = tabValue.substring(tabValue.indexOf("\n")+1, tabValue.lastIndexOf("\n"));
	            if(subStr.contains("\n"))
	            	tabName = tabValue.substring(tabValue.indexOf("\n")+1, tabValue.lastIndexOf("\n")).replace("\n", " ");
	            else
	            	tabName = tabValue.substring(0, tabValue.lastIndexOf("\n")).replace("\n", " ");
	            if (tabName.equals(secondaryTabName)) {
	                if (tab.getAttribute("class").equals("active")) {
	                	isAt = true;
	                	break;
	                } else { 
	                	isAt = false;
	                	break;
	                }
	            }
	        }
			return isAt;
	       
	    }

}
