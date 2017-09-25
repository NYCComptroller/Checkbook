package pages.spending;

import org.openqa.selenium.By;
import org.openqa.selenium.support.ui.ExpectedConditions;
import org.openqa.selenium.support.ui.WebDriverWait;

import helpers.Driver;
import helpers.Helper;
import navigation.TopNavigation.Spending;
import pages.spending.SpendingPage;
import pages.home.HomePage;

public class ContractSpendingPage {

    public static void GoTo() {
    	navigation.TopNavigation.Spending.ContractSpending.Select();
    }
    
    public static boolean isAt() {
        return navigation.TopNavigation.Spending.ContractSpending.isAt();
    }

	public static int GetNumberOfAgencies() {
		return HomePage.GetWidgetTotalNumber("Top 5 Agencies");
	}

		
		public static int GetTransactionCount() {
			WebDriverWait wait = new WebDriverWait(Driver.Instance, 20);
			wait.until(ExpectedConditions.visibilityOfElementLocated(By.id("table_706_info")));
			String count = (Driver.Instance.findElement(By.id("table_706_info"))).getText();
			return Helper.GetTotalEntries(count, 9);
		}
}
