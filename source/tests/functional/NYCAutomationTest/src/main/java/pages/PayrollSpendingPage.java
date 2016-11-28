package pages;

import navigation.TopNavigation;
import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;
import utility.Driver;

public class PayrollSpendingPage {

    public static void GoTo() {
        TopNavigation.Spending.PayrollSpending.Select();
        //Driver.Instance.manage().timeouts().implicitlyWait(2, TimeUnit.SECONDS);

    }

    public static String GetTotalNumOfAgencies() {
        WebElement numAgenciesCont = Driver.Instance.findElement(By.cssSelector("#node-widget-23 > .content > .tableHeader > .contCount"));
        String numAgencyText = numAgenciesCont.getText();
        String numAgency = numAgencyText.substring(numAgencyText.indexOf(":") + 1).trim();
        return numAgency;
    }


    public static boolean isAt() {
        WebElement h2title = Driver.Instance.findElement(By.xpath("//*[@id=\"node-widget-21\"]/div[1]/h2"));

        return h2title.getText().equals("Payroll Spending");
    }
}
