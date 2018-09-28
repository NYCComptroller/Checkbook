package pages.contracts;

import org.openqa.selenium.By;
import org.openqa.selenium.support.ui.ExpectedConditions;
import org.openqa.selenium.support.ui.WebDriverWait;

import navigation.TopNavigation;
import helpers.Driver;
import helpers.Helper;

public class ActiveExpenseContractsPage {
	public static void GoTo() {
		if(!ContractsPage.isAt())
			ContractsPage.GoTo();
		TopNavigation.Contracts.ActiveExpenseContracts.Select();	
	}
	
	public static int GetTransactionCount() {
		WebDriverWait wait = new WebDriverWait(Driver.Instance, 50);
		wait.until(ExpectedConditions.visibilityOfElementLocated(By.id("table_939_info")));
		String count = (Driver.Instance.findElement(By.id("table_939_info"))).getText();
		return Helper.GetTotalEntries(count, 5);
	}
	
	public static int GetOGETransactionCount() {
		WebDriverWait wait = new WebDriverWait(Driver.Instance, 50);
		wait.until(ExpectedConditions.visibilityOfElementLocated(By.id("table_634_info")));
		String count = (Driver.Instance.findElement(By.id("table_634_info"))).getText();
		return Helper.GetTotalEntries(count, 5);
	}
	
	public static String GetTransactionAmount1() {
		WebDriverWait wait = new WebDriverWait(Driver.Instance, 20);
		wait.until(ExpectedConditions.visibilityOfElementLocated(By.className("transactions-total-amount")));
		String amount = (Driver.Instance.findElement(By.className("transactions-total-amount"))).getText();	
		//System.out.println(Helper.billionStringToFloat(count));
		//return amount.substring(0,8);		
		   return amount.substring(0,(amount.indexOf("T")-1));		
		//return amount;
		//return Helper.billionStringToFloat(count);
	}
    
}
