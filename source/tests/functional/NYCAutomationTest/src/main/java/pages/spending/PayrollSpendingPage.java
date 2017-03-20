package pages.spending;

import navigation.TopNavigation;
import pages.home.HomePage;

import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;
import utility.Driver;

public class PayrollSpendingPage {

    public static void GoTo() {
        TopNavigation.Spending.PayrollSpending.Select();
    }
    
    public static boolean isAt() {
        return TopNavigation.Spending.PayrollSpending.isAt();
    }

	public static String GetNumberOfAgencies() {
		return HomePage.GetWidgetTotalNumber("Top 5 Agencies");
	}

}
