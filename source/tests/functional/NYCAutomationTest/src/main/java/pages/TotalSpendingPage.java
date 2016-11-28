package pages;

import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;

import navigation.TopNavigation;
import pages.help.Helper;
import utility.Driver;

public class TotalSpendingPage{

	public static void GoTo() {
		TopNavigation.Spending.TotalSpending.Select();	
	}

	public static void GoToTop5ChecksDetailsPage() {
		WebElement detailsContainer = Driver.Instance.findElement(By.cssSelector("#nyc-spending .bottomContainer > .panel-display .top-chart"));
		WebElement detailsAnchor = detailsContainer.findElement(By.partialLinkText("Details"));
		detailsAnchor.click();
	}

	public static String GetChecksDetailsPageTitle() {
		System.out.println((Driver.Instance.findElement(By.className("contract-title"))).getText());
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
}