package pages.spendingoge;

import helpers.Driver;
import helpers.Helper;

import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;

import pages.home.HomePage;

public class TotalSpendingPage{

	public static void GoTo() {
		navigation.TopNavigation.Spending.TotalSpending.Select();	
	}

	public static void GoToTop5ChecksDetailsPage() {
		if(!IsAtTop5ChecksDetailsPage()){
			WebElement detailsContainer = Driver.Instance.findElement(By.cssSelector("#nyc-spending .bottomContainer > .panel-display .top-chart"));
			WebElement detailsAnchor = detailsContainer.findElement(By.partialLinkText("Details"));
			detailsAnchor.click();
		}
		
	}
	
	public static boolean IsAtTop5ChecksDetailsPage() {
		if(Driver.Instance.findElements(By.className("contract-title")).size() > 0){
			return ((Driver.Instance.findElement(By.className("contract-title"))).getText()).equalsIgnoreCase("Checks Total Spending Transactions");
		}else
			return false;
	}

	public static String GetChecksDetailsPageTitle() {
		return (Driver.Instance.findElement(By.className("contract-title"))).getText();
		
	}

	public static int GetChecksTransactionCount() {
		String count = (Driver.Instance.findElement(By.id("table_706_info"))).getText();
		return Helper.GetTotalEntries(count, 9);
	}

	public static Number GetTotalSpendingAmount() {
		String amt = (Driver.Instance.findElement(By.cssSelector(".total-spending-amount"))).getText();
		return Helper.billionStringToNumber(amt);
	}
	
	public static boolean isAt(){
		return navigation.TopNavigation.Spending.TotalSpending.isAt();    
	}

	public static void ExportAllTransactions() {
		HomePage.ShowWidgetDetails();
		WebElement export = Driver.Instance.findElement(By.cssSelector(".export"));
		export.click();
		WebElement downloadBtn = Driver.Instance.findElement(By.cssSelector(".ui-dialog-buttonset .ui-button-text"));
		downloadBtn.click();
		
	}
}