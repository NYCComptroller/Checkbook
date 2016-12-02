package pages.spending;

import navigation.TopNavigation;
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

    public static String GetTotalNumOfAgencies() {
        WebElement numAgenciesCont = Driver.Instance.findElement(By.cssSelector("#node-widget-23 > .content > .tableHeader > .contCount"));
        String numAgencyText = numAgenciesCont.getText();
        String numAgency = numAgencyText.substring(numAgencyText.indexOf(":") + 1).trim();
        return numAgency;
    }

}
