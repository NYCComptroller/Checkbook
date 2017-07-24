package pages.contracts;

import org.openqa.selenium.By;
import org.openqa.selenium.support.ui.ExpectedConditions;
import org.openqa.selenium.support.ui.WebDriverWait;

import helpers.Driver;
import helpers.Helper;

public class RegisteredRevenueContractsPage {
	public static void GoTo() {
		if(!ContractsPage.isAt())
			ContractsPage.GoTo();
		navigation.TopNavigation.Contracts.RegisteredRevenueContracts.Select();	
	}

	public static int GetTransactionCount() {
		WebDriverWait wait = new WebDriverWait(Driver.Instance, 10);
		wait.until(ExpectedConditions.visibilityOfElementLocated(By.id("table_667_info")));
		String count = (Driver.Instance.findElement(By.id("table_667_info"))).getText();
		return Helper.GetTotalEntries(count, 5);
	}
}	
