package pages.contracts;

import helpers.Driver;
import helpers.Helper;

import org.openqa.selenium.By;
import org.openqa.selenium.support.ui.ExpectedConditions;
import org.openqa.selenium.support.ui.WebDriverWait;


public class ActiveRevenueContractsPage {
	public static void GoTo() {
		if(!ContractsPage.isAt())
			ContractsPage.GoTo();
		navigation.TopNavigation.Contracts.ActiveRevenueContracts.Select();	
	}
	
	public static int GetTransactionCount() {
		WebDriverWait wait = new WebDriverWait(Driver.Instance, 30);
		wait.until(ExpectedConditions.visibilityOfElementLocated(By.id("table_667_info")));
		String count = (Driver.Instance.findElement(By.id("table_667_info"))).getText();
		return Helper.GetTotalEntries(count, 5);
	}

	
}
