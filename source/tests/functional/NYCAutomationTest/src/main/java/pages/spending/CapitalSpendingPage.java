package pages.spending;

import java.util.List;

import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.ui.ExpectedConditions;
import org.openqa.selenium.support.ui.WebDriverWait;

import helpers.Driver;
import helpers.Helper;
import navigation.TopNavigation.Spending.CapitalSpending;
import pages.spending.SpendingPage;
import pages.home.HomePage;

public class CapitalSpendingPage {

    public static void GoTo() {
    	navigation.TopNavigation.Spending.CapitalSpending.Select();
    }
    
    public static boolean isAt() {
        return navigation.TopNavigation.Spending.CapitalSpending.isAt();
    }		
		public static int GetTransactionCount() {
			WebDriverWait wait = new WebDriverWait(Driver.Instance, 40);
			wait.until(ExpectedConditions.visibilityOfElementLocated(By.id("table_706_info")));
			String count = (Driver.Instance.findElement(By.id("table_706_info"))).getText();
			return Helper.GetTotalEntries(count, 5);
		}
		

}
