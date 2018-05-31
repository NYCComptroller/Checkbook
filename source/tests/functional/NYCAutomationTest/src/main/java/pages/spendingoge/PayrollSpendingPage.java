package pages.spendingoge;

import org.openqa.selenium.By;
import org.openqa.selenium.JavascriptExecutor;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.ui.ExpectedConditions;
import org.openqa.selenium.support.ui.WebDriverWait;

import helpers.Driver;
import helpers.Helper;
import pages.home.HomePage;

public class PayrollSpendingPage {

    public static void GoTo() {
    	navigation.TopNavigation.Spending.PayrollSpending.Select();
    }
    
    public static boolean isAt() {
        return navigation.TopNavigation.Spending.PayrollSpending.isAt();
    }

	public static Integer GetNumberOfAgencies() {
		return HomePage.GetWidgetTotalNumber("Top 5 Agencies");
	}
	
	public static int GetTransactionCount() {
		WebDriverWait wait = new WebDriverWait(Driver.Instance, 20);
		wait.until(ExpectedConditions.visibilityOfElementLocated(By.id("table_706_info")));
		String count = (Driver.Instance.findElement(By.id("table_706_info"))).getText();
		return Helper.GetTotalEntries(count, 9);
	}
	
	public static void GoToBottomNavSpendinglink() {
        //  WebElement tab = Driver.Instance.findElement(By.cssSelector(".nyc_totals_links. td"));
      // WebElement tabAnchor = tab.findElement(By.tagName("a"));
         // WebElement tabAnchor = tab.findElement(By.linkText("Contract Spending"));
        //  WebElement tabAnchor = tab.findElement(By.className("dollars"));
          
          WebElement tabAnchor =Driver.Instance.findElement(By.xpath("/html/body/div[2]/div[3]/div[2]/div/div/div/div/div[1]/div/div/div/div[4]/div/div[1]/div/div/div[1]/div/table/tbody/tr/td[2]/div[1]/a"));
                      
         // driver.findElement(By.linkText("App Configuration")).click();
          ((JavascriptExecutor) Driver.Instance).executeScript("arguments[0].scrollIntoView(true);", tabAnchor);
        tabAnchor.click();
      }
	
	
	

}
