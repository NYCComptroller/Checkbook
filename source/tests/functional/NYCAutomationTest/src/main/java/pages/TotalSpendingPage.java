package pages;

import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;

import navigation.TopNavigation;
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

	public static String GetTitle() {
		System.out.println((Driver.Instance.findElement(By.className("contract-title"))).getText());
		return (Driver.Instance.findElement(By.className("contract-title"))).getText();
		
	}
}