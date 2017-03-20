package pages.contracts;

import org.openqa.selenium.By;
import org.openqa.selenium.support.ui.ExpectedConditions;
import org.openqa.selenium.support.ui.WebDriverWait;

import navigation.TopNavigation;
import utility.Driver;
import utility.Helper;

public class ActiveExpenseContractsPage {
	public static void GoTo() {
		if(!ContractsPage.isAt())
			ContractsPage.GoTo();
		TopNavigation.Contracts.ActiveExpenseContracts.Select();	
	}
	
	public static int GetTransactionCount() {
		WebDriverWait wait = new WebDriverWait(Driver.Instance, 10);
		wait.until(ExpectedConditions.visibilityOfElementLocated(By.id("table_656_info")));
		String count = (Driver.Instance.findElement(By.id("table_656_info"))).getText();
		return Helper.GetTotalEntries(count, 5);
	}
}
