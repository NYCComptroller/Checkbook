package pages.tools;

import navigation.PrimaryMenuNavigation;
import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;
import helpers.Driver;

public class MWBE_AgencySummaryPage {
    public static void GoTo() {
        PrimaryMenuNavigation.Tools.MWBEAgencySummary();
    }

    public static boolean isAt() {
        WebElement h2title = Driver.Instance.findElement(By.id("page-title"));
        return h2title.getText().equals("M/WBE Agency Summary");
    }
}
