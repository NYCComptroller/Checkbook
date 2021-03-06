package pages.contracts;

import org.openqa.selenium.By;
import org.openqa.selenium.support.ui.ExpectedConditions;
import org.openqa.selenium.support.ui.WebDriverWait;

import navigation.TopNavigation;
import helpers.Driver;
import helpers.Helper;

public class PendingExpenseContractsPage {
	public static void GoTo() {
		if(!ContractsPage.isAt())
			ContractsPage.GoTo();
		TopNavigation.Contracts.PendingExpenseContracts.Select();	
	}
	
	public static int GetTransactionCount() {
		WebDriverWait wait = new WebDriverWait(Driver.Instance, 30);
		wait.until(ExpectedConditions.visibilityOfElementLocated(By.id("table_714_info")));
		String count = (Driver.Instance.findElement(By.id("table_714_info"))).getText();
		return Helper.GetTotalEntries(count, 5);
	}
	
}
