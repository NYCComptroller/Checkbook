package pages;

import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;

import navigation.TopNavigation;
import utility.Driver;

public class SpendingPage {

	public static void GoTo() {
		TopNavigation.Spending.Select();	
		
	}

	public static String GetSpendingAmount() {
		 WebElement spendingAmt = Driver.Instance.findElement(By.cssSelector(".nyc_totals_links td.active > .positioning .dollars"));
		 return spendingAmt.getText();
	}
	

}
