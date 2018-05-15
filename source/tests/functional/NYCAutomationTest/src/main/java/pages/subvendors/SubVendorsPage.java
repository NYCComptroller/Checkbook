package pages.subvendors;

import pages.home.HomePage;
import helpers.Driver;
import helpers.Helper;
import navigation.SubVendorCategory;
import navigation.TopNavigation;
import navigation.SubVendorCategory.SubVendorCategoryOption;

import java.util.ArrayList;
import java.util.List;
import java.util.concurrent.TimeUnit;

import org.openqa.selenium.By;
import org.openqa.selenium.JavascriptExecutor;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.ui.ExpectedConditions;
import org.openqa.selenium.support.ui.WebDriverWait;

public class SubVendorsPage {	
   
    	public enum WidgetOption{
    		Top5Checks, TopChecks,Top5SubVendors,TopSubVendors , Top5PrimeVendors, TopPrimeVendors,Top5Agencies,TopAgencies,
    		 Top5SubContracts,TopSubContracts
    	}
    	
    	public static boolean IsAt() {
    		WebElement spendingCont = Driver.Instance.findElement(By.cssSelector(".top-navigation-left > table > tbody > tr .spending"));
        	Boolean spendingSelected = (spendingCont.getAttribute("class")).contains("active");	
        	WebElement contractsCont = Driver.Instance.findElement(By.cssSelector(".top-navigation-left > table > tbody > tr .contracts"));
        	Boolean contractsSelected = (contractsCont.getAttribute("class")).contains("active");	
           // WebElement mwbeCont = Driver.Instance.findElement(By.cssSelector(".top-navigation-right .a[class='mwbe subvendors']"));
        	// WebElement mwbeCont = Driver.Instance.findElement(By.cssSelector(".top-navigation-right .input[class='mwbe subvendors']"));
          //  WebElement mwbeCont = Driver.Instance.findElement(By.cssSelector(".top-navigation-right > table > tbody > tr > td.mwbe.subvendors"));
        	    WebElement mwbeCont = Driver.Instance.findElement(By.cssSelector(".top-navigation-right .mwbe"));
            Boolean mwbeSelected = (mwbeCont.getAttribute("class")).contains("active");	   
            return (spendingSelected || contractsSelected) && mwbeSelected;
    	}
    	
    	public static void GoTo(String domain, SubVendorCategoryOption category) {
    		switch(domain) {
    		case "Spending":
    				TopNavigation.Spending.Select();
        		SubVendorCategory.select(category);
        		break;
    		case "Contracts":
    			TopNavigation.Contracts.Select();
    			SubVendorCategory.select(category);	
    			break;
    			
    		}
    	
    		
    	}
    

    	public static String GetSubVendorSpendingAmount() {
    		WebElement mwbeAmt = Driver.Instance.findElement(By.cssSelector(".top-navigation-right .mwbe.subvendors .top-navigation-amount"));
    		return mwbeAmt.getText().substring((mwbeAmt.getText().indexOf("$")));
    	}
    	public static String GetSpendingSubVendorAmount() {
            WebElement spendingAmt = Driver.Instance.findElement(By.cssSelector(".top-navigation-right .mwbe.subvendors .top-navigation-amount"));
            return spendingAmt.getText().substring((spendingAmt.getText().indexOf("$")));
           }
    		
		 public static String GetBottomNavSpendingAmount() {
	            WebElement spendingAmt = Driver.Instance.findElement(By.cssSelector(".nyc_totals_links .active > .positioning > a .dollars"));
	            return spendingAmt.getText().substring((spendingAmt.getText().indexOf("$")));
	        }
   
        
        ///Widgets counts
    	
    	public static Integer GetTop5WidgetTotalCount(WidgetOption option) {
    		switch (option) {
    		case Top5Checks:	
    			return HomePage.GetWidgetTotalNumber("Top 5 Checks");
    		case TopChecks:
    			return HomePage.GetWidgetTotalNumber("Top Checks");
    		case Top5SubContracts:
    			return HomePage.GetWidgetTotalNumber("Top 5 Sub Contracts");
    		case TopSubContracts:
    			return HomePage.GetWidgetTotalNumber("Top Sub Contracts");
    		case Top5PrimeVendors:
    			return HomePage.GetWidgetTotalNumber("Top 5 Prime Vendors");
    		case TopPrimeVendors:
    			return HomePage.GetWidgetTotalNumber("Top Prime Vendors");
    	    case Top5Agencies:
    			return HomePage.GetWidgetTotalNumber("Top 5 Agencies");
    		case TopAgencies:
    			return HomePage.GetWidgetTotalNumber("Top Agencies");
    		default:
    			return null;
    		}
    	}
    	
    	public static void GoToTop5DetailsPage(WidgetOption option) {
    		WebElement detailsContainer = null;
    		switch (option) {
    			case Top5Checks:	
    				if(!HomePage.IsAtTop5DetailsPage("Top 5 checks"))
    					detailsContainer = HomePage.GetWidgetDetailsContainer("Top 5 Checks");
    				break;
    			case TopChecks:
    				if(!HomePage.IsAtTop5DetailsPage("Top Checks"))
    					detailsContainer = HomePage.GetWidgetDetailsContainer("Top 5 Checks");
    				break;
    			case Top5SubContracts:
    				if(!HomePage.IsAtTop5DetailsPage("Top 5 Sub Contracts"))
    					detailsContainer = HomePage.GetWidgetDetailsContainer("Top 5 Contracts");
    				break;
    			case TopSubContracts:
    				if(!HomePage.IsAtTop5DetailsPage("Top Sub Contracts"))
    					detailsContainer = HomePage.GetWidgetDetailsContainer("Top Contracts");
    				break;
    		
    			case Top5PrimeVendors:
    				if(!HomePage.IsAtTop5DetailsPage("Top 5 Prime Vendors"))
    					detailsContainer = HomePage.GetWidgetDetailsContainer("Top 5 Prime Vendors");
    				break;
    			case TopPrimeVendors:
    				if(!HomePage.IsAtTop5DetailsPage("Top Prime Vendors"))
    					detailsContainer = HomePage.GetWidgetDetailsContainer("Top Prime Vendors");
    				break;
    
    			case Top5Agencies:
    				if(!HomePage.IsAtTop5DetailsPage("Top 5 Agencies"))
    					detailsContainer = HomePage.GetWidgetDetailsContainer("Top 5 Agencies");
    				break;
    			case TopAgencies:
    				if(!HomePage.IsAtTop5DetailsPage("Top Agencies"))
    					detailsContainer = HomePage.GetWidgetDetailsContainer("Top Agencies");
    				break;
    			default:
    				break;
    		}
    		WebElement detailsAnchor = detailsContainer.findElement(By.partialLinkText("Details"));
    		((JavascriptExecutor) Driver.Instance).executeScript("arguments[0].scrollIntoView(true);", detailsAnchor);
    		detailsAnchor.click();	
    		Driver.Instance.manage().timeouts().implicitlyWait(30, TimeUnit.SECONDS);
    	}
    	
      
    
    
 /// visualization  titles   
    public static ArrayList<String> VisualizationTitles() {
		
		return HomePage.VisualizationTitles();
	}
  /// /// widget titles 
	public static ArrayList<String> WidgetTitles() {
		ArrayList<String> titles = new ArrayList<String>();
		List<WebElement> titleContainers = Driver.Instance.findElements(By.className("tableHeader"));
		for (WebElement titleContainer : titleContainers) {
			WebElement titleHeaderContainer = titleContainer.findElement(By.cssSelector("h2"));
			//titles.add(titleHeaderContainer.getText());
			titles.add(titleHeaderContainer.getText().substring(0, titleHeaderContainer.getText().indexOf("Number")-1));
		}	
		return titles;
	}
	
	public static String GetWidgetText() {
	List<WebElement> panelContainers = Driver.Instance.findElements(By.cssSelector(".bottomContainer > .panel-display > .panel-panel > .inside > .panel-pane"));
//		List<WebElement> panelContainers = Driver.Instance.findElements(By.cssSelector(".bottomContainer > .panel-display > .panel-panel > div > .panel-pane"));
		for (WebElement panelContainer : panelContainers) {
			WebElement header= panelContainer.findElement(By.tagName("h2"));
			String subTitle = header.getText().substring(0, header.getText().indexOf("Number")-1);
			return subTitle;
			}
		return null;
	}

	
	public static ArrayList<String> GetAllWidgetText() {
		List<WebElement> panelContainers = Driver.Instance.findElements(By.cssSelector(".bottomContainer > .panel-display > .panel-panel > .inside > .panel-pane"));
//			List<WebElement> panelContainers = Driver.Instance.findElements(By.cssSelector(".bottomContainer > .panel-display > .panel-panel > div > .panel-pane"));
			for (WebElement panelContainer : panelContainers) {
				WebElement header= panelContainer.findElement(By.tagName("h2"));
				ArrayList<String> titles = new ArrayList<String>();
				titles.add( header.getText().substring(0, header.getText().indexOf("Number")-1));
				return titles;
				}
			return null;
		}

	
	
	///Transaction count

	public static Integer GetTransactionCount() {
		WebDriverWait wait = new WebDriverWait(Driver.Instance, 30);
		wait.until(ExpectedConditions.visibilityOfElementLocated(By.id("table_706_info")));
		String count = (Driver.Instance.findElement(By.id("table_706_info"))).getText();
		return Helper.GetTotalEntries(count, 9);
	}
		public static Integer GetTransactionCount1() {
			WebDriverWait wait = new WebDriverWait(Driver.Instance, 30);
			wait.until(ExpectedConditions.visibilityOfElementLocated(By.id("table_706_info")));
			String count = (Driver.Instance.findElement(By.id("table_706_info"))).getText();
			return Helper.GetTotalEntries(count, 5);
	}

	
}
