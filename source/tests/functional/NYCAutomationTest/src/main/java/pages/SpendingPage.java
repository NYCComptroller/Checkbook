package pages;

import navigation.TopNavigation;
import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;
import utility.Driver;

public class SpendingPage {

    public static void GoTo() {
        TopNavigation.Spending.Select();

    }

    public static String GetSpendingAmount() {
        WebElement spendingAmt = Driver.Instance.findElement(By.xpath("//*[@id=\"node-widget-482\"]/div[1]/div/table/tbody/tr/td[1]/div[1]/a/span"));
        return spendingAmt.getText();
    }

    public static boolean isAt() {
        WebElement h2title = Driver.Instance.findElement(By.xpath("//*[@id=\"node-widget-21\"]/div[1]/h2"));
        return h2title.getText().equals("Total Spending");

    }

}
