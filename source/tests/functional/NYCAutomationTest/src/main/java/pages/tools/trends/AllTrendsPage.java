package pages.tools.trends;

import navigation.PrimaryMenuNavigation;
import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;
import helpers.Driver;

public class AllTrendsPage {

    static final By changesInNetAssetsLink = By.linkText("Changes in Net Assets");

    public enum allTrendsOptions {
        changesInNetAssets
    }

    public static void GoTo() {
        PrimaryMenuNavigation.Tools.Trends.AllTrends();
    }

    public static void GoTo(allTrendsOptions option) {
        if(!isAt()) GoTo();

        switch (option){
            case changesInNetAssets:
                Driver.Instance.findElement(changesInNetAssetsLink).click();
                break;
            default:
                break;
        }
    }

    public static boolean isAt() {
        WebElement h2title = Driver.Instance.findElement(By.id("page-title"));
        return h2title.getText().equals("All Trends");
    }

    public static String changesInNetAssets2015() {
        By generalGovt2015 = By.xpath("//*[@id=\"table_392\"]/tbody/tr[3]/td[2]/div[2]");
        // System.out.println(Driver.Instance.findElement(generalGovt2015).getText());
        return Driver.Instance.findElement(generalGovt2015).getText();
    }

}
