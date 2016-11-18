package pages;

import navigation.PrimaryMenuNavigation;
import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;
import utility.Driver;

public class DataFeedsPage {
    public static void GoTo() {
        PrimaryMenuNavigation.DataFeeds();
    }

    public static boolean isAt() {
        WebElement h2title = Driver.Instance.findElement(By.id("page-title"));
        return h2title.getText().equals("Data Feeds");
    }

    public static void submitDataFeedsForm() {
        if(!isAt()) GoTo();

        Driver.Instance.findElement(By.id("edit-type-next")).click();
        Driver.Instance.findElement(By.cssSelector("li.ms-elem-selectable")).click();
        Driver.Instance.findElement(By.id("edit-feeds-spending-next")).click();
        Driver.Instance.findElement(By.id("edit-confirm")).click();
    }
}
