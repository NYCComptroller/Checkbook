package pages.spending;

import pages.home.HomePage;
import helpers.Driver;
import helpers.Helper;

import java.util.ArrayList;
import java.util.List;
import java.util.concurrent.TimeUnit;

import org.openqa.selenium.By;
import org.openqa.selenium.JavascriptExecutor;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.ui.ExpectedConditions;
import org.openqa.selenium.support.ui.WebDriverWait;

public class SpendingPage {	
   
    	public enum WidgetOption{
    		Top5Checks, TopChecks,  Top5PrimeVendors, TopPrimeVendors,Top5Agencies,TopAgencies,
    		Top5ExpenseCategories, TopExpenseCategories, Top5Contracts,TopContracts,Top5Departments,
    		TopDepartments
    	}
    	public static void GoTo() {
    		navigation.TopNavigation.Spending.Select();
            
    	}        	
    
        public static String GetSpendingAmount() {
            WebElement spendingAmt = Driver.Instance.findElement(By.cssSelector(".top-navigation-left .spending > .expense-container > a"));
            return spendingAmt.getText().substring((spendingAmt.getText().indexOf("$")));
        }
        

		
		 public static String GetBottomNavSpendingAmount() {
	            WebElement spendingAmt = Driver.Instance.findElement(By.cssSelector(".nyc_totals_links .active > .positioning > a .dollars"));
	            return spendingAmt.getText().substring((spendingAmt.getText().indexOf("$")));
	        }
		

        public static boolean isAt() {
        	WebElement topTitleCont = Driver.Instance.findElement(By.cssSelector(".top-navigation-left > table > tbody > tr .spending"));
        	Boolean spendingSelected = (topTitleCont.getAttribute("class")).contains("active");	
            //WebElement h2title = Driver.Instance.findElement(By.xpath("//*[@id=\"node-widget-21\"]/div[1]/h2"));
            //Boolean totalSpendingSelected = h2title.getText().equals("Total Spending");    
            return spendingSelected;
        }
    	
        
        ///Widgets counts
    	
    	public static Integer GetTop5WidgetTotalCount(WidgetOption option) {
    		switch (option) {
    		case Top5Checks:	
    			return HomePage.GetWidgetTotalNumber("Top 5 Checks");
    		case TopChecks:
    			return HomePage.GetWidgetTotalNumber("Top Checks");
    		case Top5Contracts:
    			return HomePage.GetWidgetTotalNumber("Top 5 Contracts");
    		case TopContracts:
    			return HomePage.GetWidgetTotalNumber("Top Contracts");
    		case Top5PrimeVendors:
    			return HomePage.GetWidgetTotalNumber("Top 5 Prime Vendors");
    		case TopPrimeVendors:
    			return HomePage.GetWidgetTotalNumber("Top Prime Vendors");
    		case Top5ExpenseCategories:
    			return HomePage.GetWidgetTotalNumber("Top 5 Expense Categories");
    		case TopExpenseCategories:
    			return HomePage.GetWidgetTotalNumber("Top Expense Categories");
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
    			case Top5Contracts:
    				if(!HomePage.IsAtTop5DetailsPage("Top 5 Contracts"))
    					detailsContainer = HomePage.GetWidgetDetailsContainer("Top 5 Contracts");
    				break;
    			case TopContracts:
    				if(!HomePage.IsAtTop5DetailsPage("Top Contracts"))
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
    			case Top5Departments:
    				if(!HomePage.IsAtTop5DetailsPage("Top 5 Departments"))
    					detailsContainer = HomePage.GetWidgetDetailsContainer("Top 5 Departmetns");
    				break;
    			case TopDepartments:
    				if(!HomePage.IsAtTop5DetailsPage("Top Departments"))
    					detailsContainer = HomePage.GetWidgetDetailsContainer("Top Departmetns");
    				break;
    			case Top5ExpenseCategories:
    				if(!HomePage.IsAtTop5DetailsPage("Top 5 Expense Categories"))
    					detailsContainer = HomePage.GetWidgetDetailsContainer("Top 5 Expense Categories");
    				break;
    			case TopExpenseCategories:
    				if(!HomePage.IsAtTop5DetailsPage("Top Expense Categories"))
    					detailsContainer = HomePage.GetWidgetDetailsContainer("Top Expense Categories");
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
    		Driver.Instance.manage().timeouts().implicitlyWait(40, TimeUnit.SECONDS);
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
		WebDriverWait wait = new WebDriverWait(Driver.Instance, 50);
		wait.until(ExpectedConditions.visibilityOfElementLocated(By.id("table_706_info")));
		String count = (Driver.Instance.findElement(By.id("table_706_info"))).getText();
		return Helper.GetTotalEntries(count, 9);
	}
		public static Integer GetTransactionCount1() {
			WebDriverWait wait = new WebDriverWait(Driver.Instance, 50);
			wait.until(ExpectedConditions.visibilityOfElementLocated(By.id("table_706_info")));
			String count = (Driver.Instance.findElement(By.id("table_706_info"))).getText();
			return Helper.GetTotalEntries(count, 5);
	}

	
}
