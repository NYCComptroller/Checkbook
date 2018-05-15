package pages.contracts;

import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.ui.ExpectedConditions;
import org.openqa.selenium.support.ui.WebDriverWait;

import navigation.TopNavigation;
import helpers.Driver;
import helpers.Helper;

public class RegisteredExpenseContractsPage {
	public static void GoTo() {
		if(!ContractsPage.isAt())
			ContractsPage.GoTo();
		TopNavigation.Contracts.RegisteredExpenseContracts.Select();	
	}

	public static int GetTransactionCount() {
		WebDriverWait wait = new WebDriverWait(Driver.Instance, 100);
		
	   wait.until(ExpectedConditions.visibilityOfElementLocated(By.id("table_939_info")));
		//wait.until(ExpectedConditions.visibilityOfElementLocated(By.id("table_939_info")));
		String count = (Driver.Instance.findElement(By.id("table_939_info"))).getText();
		return Helper.GetTotalEntries(count, 5);
	}
	
}
