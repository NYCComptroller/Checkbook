package pages.tools.trends;

import navigation.PrimaryMenuNavigation;
import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;
import helpers.Driver;

public class FeaturedTrendsPage {

    public static void GoTo() {
        PrimaryMenuNavigation.Tools.Trends.FeaturedTrends();
    }

    public static boolean isAt() {
        WebElement h2title = Driver.Instance.findElement(By.id("page-title"));
        return h2title.getText().equals("FEATURED TRENDS");
    }


//    public static String featuredTrends2015orange() {
////        By highChart = By.xpath("//*[@id=\"highcharts-0\"]/svg/g[20]/g[1]/rect[10]");
////        By highChartInfo = By.xpath("//*[@id=\"highcharts-0\"]/svg/g[19]/text/tspan[2]");
////
////        WebDriverWait wait = new WebDriverWait(Driver.Instance, 15);
////
////        Actions hover = new Actions(Driver.Instance);
////        hover.moveToElement(Driver.Instance.findElement(highChart)).perform();
////
////        wait.until(ExpectedConditions.visibilityOfElementLocated(highChartInfo));
//
//
//        System.out.println(Driver.Instance.findElement(By.xpath("//*[@id=\"highcharts-0\"]/svg/g[19]/text/tspan[2]")).getText());
//        return Driver.Instance.findElement(By.cssSelector("g.highcharts-tooltip:nth-child(24) > text:nth-child(5) > tspan:nth-child(2)")).getText();
//    }
}
