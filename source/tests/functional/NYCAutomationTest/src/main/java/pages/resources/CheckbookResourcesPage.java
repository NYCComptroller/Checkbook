package pages.resources;

import navigation.PrimaryMenuNavigation;
import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;
import helpers.Driver;

public class CheckbookResourcesPage {
    public static void GoTo() {
        PrimaryMenuNavigation.Resources.CheckbookResources();
    }

    public static boolean isAt() {
        WebElement h2title = Driver.Instance.findElement(By.id("page-title"));
        return h2title.getText().equals("Resources");
    }
}
