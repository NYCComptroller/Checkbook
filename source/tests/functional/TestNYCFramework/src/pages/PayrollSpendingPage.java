package pages;

import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;

import navigation.TopNavigation;
import utility.Driver;

public class PayrollSpendingPage {

	public static void GoTo() {
		/*try {
			Thread.sleep(2000);
		} catch (InterruptedException e) {
			e.printStackTrace();
		}*/
		TopNavigation.Spending.PayrollSpending.Select();
		//Driver.Instance.manage().timeouts().implicitlyWait(2, TimeUnit.SECONDS);
	}

	public static String GetTotalNumOfAgencies() {
		WebElement numAgenciesCont = Driver.Instance.findElement(By.cssSelector("#node-widget-23 > .content > .tableHeader > .contCount"));
		String numAgencyText = numAgenciesCont.getText();
		String numAgency = numAgencyText.substring(numAgencyText.indexOf(":")+1).trim();
		return numAgency;
	}
	
}
