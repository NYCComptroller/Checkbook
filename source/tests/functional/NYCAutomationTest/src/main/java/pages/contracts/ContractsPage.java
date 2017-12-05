package pages.contracts;

import java.util.ArrayList;
import java.util.List;
import java.util.concurrent.TimeUnit;

import org.openqa.selenium.By;
import org.openqa.selenium.JavascriptExecutor;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.ui.ExpectedConditions;
import org.openqa.selenium.support.ui.WebDriverWait;

import navigation.TopNavigation;
import pages.home.HomePage;
import helpers.Driver;
import helpers.Helper;

public class ContractsPage {
	public enum WidgetOption{
		Top5MasterAgreements, Top5MasterAgreementModifications, Top5Contracts, Top5ContractAmountModifications, Top5ContractsAmountModifications, Top5PrimeVendors, 
		Top5AwardMethods, Top5Agencies, ContractsByIndustries, ContractsBySize, TopContractAmountModifications, TopContractsAmountModifications, TopPrimeVendors, TopAwardMethods, TopAgencies
	}
	public static void GoTo() {
        TopNavigation.Contracts.Select();
    }
	
	  public static String GetContractsAmount() {
          WebElement contractsAmt = Driver.Instance.findElement(By.cssSelector(".top-navigation-left .contracts > .expense-container > a"));
          return contractsAmt.getText().substring((contractsAmt.getText().indexOf("$")));
      }
	
	 public static String GetBottomNavContractAmount() {
           WebElement contractAmt = Driver.Instance.findElement(By.cssSelector(".nyc_totals_links .active > .positioning > a .dollars"));
           return contractAmt.getText().substring((contractAmt.getText().indexOf("$")));
       }
	 
	 public static int GetBottomNavContractCount() {
         WebElement contractCount = Driver.Instance.findElement(By.cssSelector(".nyc_totals_links .active > .positioning > a .count"));
         return Helper.stringToInt(contractCount.getText());
     }
	 
		public static String GetTransactionAmount() {
			WebDriverWait wait = new WebDriverWait(Driver.Instance, 20);
			wait.until(ExpectedConditions.visibilityOfElementLocated(By.className("transactions-total-amount")));
			String amount = (Driver.Instance.findElement(By.className("transactions-total-amount"))).getText();	
			//System.out.println(Helper.billionStringToFloat(count));
			//return amount.substring(0,8);		
			   return amount.substring(0,(amount.indexOf("T")-1));		
			//return amount;
			//return Helper.billionStringToFloat(count);
		}
	 
	 
	
	   
	    public static ArrayList<String> VisualizationTitles() {
			ArrayList<String> titles = new ArrayList<String>();
			List<WebElement> titleContainers = Driver.Instance.findElements(By.cssSelector("#nyc-contracts > .top-chart > .inside > .panel-pane"));
			for(int i=0; i < titleContainers.size(); i++){
				selectVisualizationSlider(i);
				WebElement titleClass = titleContainers.get(i).findElement(By.cssSelector(".pane-content .chart-title"));
				if(titleClass.isDisplayed()){
					String title = titleClass.getText();
					titles.add(title);
				}
			}	
			return titles;
		}
		
		public static void selectVisualizationSlider(int sliderPosition){
			List<WebElement> sliderContainer = Driver.Instance.findElements(By.cssSelector("#nyc-contracts > .top-chart > .slider-pager > a"));
			sliderContainer.get(sliderPosition).click();
			Driver.Instance.manage().timeouts().implicitlyWait(2, TimeUnit.SECONDS);
		}
		
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
	public static boolean isAt() {
    	WebElement topTitleCont = Driver.Instance.findElement(By.cssSelector(".top-navigation-left > table > tbody > tr .contracts"));
    	Boolean contractsSelected = (topTitleCont.getAttribute("class")).contains("active");	
        //WebElement h2title = Driver.Instance.findElement(By.xpath("//*[@id=\"node-widget-21\"]/div[1]/h2"));
        //Boolean totalSpendingSelected = h2title.getText().equals("Total Spending");    
        return contractsSelected;
    }
	public static Integer GetTop5WidgetTotalCount(WidgetOption option) {
		switch (option) {
			case Top5MasterAgreements:	
				return HomePage.GetWidgetTotalNumber("Top 5 Master Agreements");
			case Top5MasterAgreementModifications:
				return HomePage.GetWidgetTotalNumber("Top 5 Master Agreement Modifications");
			case Top5Contracts:
				return HomePage.GetWidgetTotalNumber("Top 5 Contracts");
			case Top5ContractAmountModifications:
				return HomePage.GetWidgetTotalNumber("Top 5 Contract Amount Modifications");
			case Top5ContractsAmountModifications:
				return HomePage.GetWidgetTotalNumber("Top 5 Contracts Amount Modifications");
			case TopContractsAmountModifications:
				return HomePage.GetWidgetTotalNumber("Top Contracts Amount Modifications");
			case TopContractAmountModifications:
				return HomePage.GetWidgetTotalNumber("Top Contract Amount Modifications");
			case Top5PrimeVendors:
				return HomePage.GetWidgetTotalNumber("Top 5 Prime Vendors");
			case TopPrimeVendors:
				return HomePage.GetWidgetTotalNumber("Top Prime Vendors");
			case Top5AwardMethods:
				return HomePage.GetWidgetTotalNumber("Top 5 Award Methods");
			case TopAwardMethods:
				return HomePage.GetWidgetTotalNumber("Top Award Methods");
			case Top5Agencies:
				return HomePage.GetWidgetTotalNumber("Top 5 Agencies");
			case TopAgencies:
				return HomePage.GetWidgetTotalNumber("Top Agencies");
			case ContractsByIndustries:
				return HomePage.GetWidgetTotalNumber("Contracts by Industries");
			case ContractsBySize:
				return HomePage.GetWidgetTotalNumber("Contracts by Size");	
			default:
				return null;
		}
	}
	
	public static void GoToTop5DetailsPage(WidgetOption option) {
		WebElement detailsContainer = null;
		switch (option) {
			case Top5MasterAgreements:	
				if(!HomePage.IsAtTop5DetailsPage("Top 5 Master Agreements"))
					detailsContainer = HomePage.GetWidgetDetailsContainer("Top 5 Master Agreements");
				break;
			case Top5MasterAgreementModifications:
				if(!HomePage.IsAtTop5DetailsPage("Top 5 Master Agreement Modifications"))
					detailsContainer = HomePage.GetWidgetDetailsContainer("Top 5 Master Agreement Modifications");
				break;
			case Top5Contracts:
				if(!HomePage.IsAtTop5DetailsPage("Top 5 Contracts"))
					detailsContainer = HomePage.GetWidgetDetailsContainer("Top 5 Contracts");
				break;
			case Top5ContractAmountModifications:
				if(!HomePage.IsAtTop5DetailsPage("Top 5 Contract Amount Modifications"))
					detailsContainer = HomePage.GetWidgetDetailsContainer("Top 5 Contract Amount Modifications");
				break;
			case Top5ContractsAmountModifications:
				if(!HomePage.IsAtTop5DetailsPage("Top 5 Contracts Amount Modifications"))
					detailsContainer = HomePage.GetWidgetDetailsContainer("Top 5 Contracts Amount Modifications");
				break;
			case TopContractsAmountModifications:
				if(!HomePage.IsAtTop5DetailsPage("Top Contracts Amount Modifications"))
					detailsContainer = HomePage.GetWidgetDetailsContainer("Top Contracts Amount Modifications");
				break;
			case TopContractAmountModifications:
				if(!HomePage.IsAtTop5DetailsPage("Top Contract Amount Modifications"))
					detailsContainer = HomePage.GetWidgetDetailsContainer("Top Contract Amount Modifications");
				break;
			case Top5PrimeVendors:
				if(!HomePage.IsAtTop5DetailsPage("Top 5 Prime Vendors"))
					detailsContainer = HomePage.GetWidgetDetailsContainer("Top 5 Prime Vendors");
				break;
			case TopPrimeVendors:
				if(!HomePage.IsAtTop5DetailsPage("Top Prime Vendors"))
					detailsContainer = HomePage.GetWidgetDetailsContainer("Top Prime Vendors");
				break;
			case Top5AwardMethods:
				if(!HomePage.IsAtTop5DetailsPage("Top 5 Award Methods"))
					detailsContainer = HomePage.GetWidgetDetailsContainer("Top 5 Award Methods");
				break;
			case TopAwardMethods:
				if(!HomePage.IsAtTop5DetailsPage("Top Award Methods"))
					detailsContainer = HomePage.GetWidgetDetailsContainer("Top Award Methods");
				break;
			case Top5Agencies:
				if(!HomePage.IsAtTop5DetailsPage("Top 5 Agencies"))
					detailsContainer = HomePage.GetWidgetDetailsContainer("Top 5 Agencies");
				break;
			case TopAgencies:
				if(!HomePage.IsAtTop5DetailsPage("Top Agencies"))
					detailsContainer = HomePage.GetWidgetDetailsContainer("Top Agencies");
				break;
			case ContractsByIndustries:
				if(!HomePage.IsAtTop5DetailsPage("Contracts by Industries"))
					detailsContainer = HomePage.GetWidgetDetailsContainer("Contracts by Industries");
				break;
			case ContractsBySize:
				if(!HomePage.IsAtTop5DetailsPage("Contracts by Size"))
					detailsContainer = HomePage.GetWidgetDetailsContainer("Contracts by Size");
				break;
			default:
				break;
		}
		WebElement detailsAnchor = detailsContainer.findElement(By.partialLinkText("Details"));
		((JavascriptExecutor) Driver.Instance).executeScript("arguments[0].scrollIntoView(true);", detailsAnchor);
		detailsAnchor.click();	
		Driver.Instance.manage().timeouts().implicitlyWait(30, TimeUnit.SECONDS);
	}
	
}
