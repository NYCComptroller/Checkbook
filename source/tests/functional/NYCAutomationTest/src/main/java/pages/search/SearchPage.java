package pages.search;

import org.openqa.selenium.By;
import org.openqa.selenium.support.ui.ExpectedConditions;
import org.openqa.selenium.support.ui.Select;
import org.openqa.selenium.support.ui.WebDriverWait;

import helpers.Driver;
import helpers.Helper;

public class SearchPage {
    public static void GoToSmartSearch() {
        Driver.Instance.findElement(By.xpath("//*[@id=\"edit-submit\"]")).click();
    }

    /*public static boolean isAt() {
        String h3title = Driver.Instance.findElement(By.xpath("" +
                "//*[@id=\"block-system-main\"]/div/div/div[2]/ol/li[1]/h3[contains(text(), 'Transaction #1: spending')]"))
                .getText();
        return h3title.equals("TRANSACTION #1: SPENDING");
    }*/

    public static boolean isAt() {
    	return Driver.Instance.getCurrentUrl().contains("smart_search");
    }

    public static int getTotalSearchEntries() {
        String[] entries = Driver.Instance.findElement(By.xpath("//*[@id=\"smart-search-transactions\"]"))
                .getText().split(" ");

        return Helper.stringToInt(entries[5]);
    }

    public static void openTypeOfData() {
        if (!Driver.Instance.getCurrentUrl().contains("smart_search")) GoToSmartSearch();
        Driver.Instance.findElement(By.cssSelector("div.filter-title > span")).click();
    }

    public static int[] typeOfDataTotals() {

        int[] totalsInt = {
        		Helper.stringToInt(Driver.Instance.findElement(By.xpath("//*[@id=\"block-system-main\"]/div/div/div[1]/div/div[2]/div[2]/div[2]/div/div[1]/div[3]/span")).getText()),
        		Helper.stringToInt(Driver.Instance.findElement(By.xpath("//*[@id=\"block-system-main\"]/div/div/div[1]/div/div[2]/div[2]/div[2]/div/div[2]/div[3]/span")).getText()),
        		Helper.stringToInt(Driver.Instance.findElement(By.xpath("//*[@id=\"block-system-main\"]/div/div/div[1]/div/div[2]/div[2]/div[2]/div/div[3]/div[3]/span")).getText()),
        		Helper.stringToInt(Driver.Instance.findElement(By.xpath("//*[@id=\"block-system-main\"]/div/div/div[1]/div/div[2]/div[2]/div[2]/div/div[4]/div[3]/span")).getText()),
        		Helper.stringToInt(Driver.Instance.findElement(By.xpath("//*[@id=\"block-system-main\"]/div/div/div[1]/div/div[2]/div[2]/div[2]/div/div[5]/div[3]/span")).getText())
        };

        return totalsInt;
    }

    public static boolean intArrayElementsAllGreaterThan(int[] intArray, int greaterThan) {
        for (int num : intArray) {
            if (num < greaterThan) return false;
        }

        return true;
    }

    public static class AdvancedSearch {
        public static void GoTo() {
            Driver.Instance.findElement(By.linkText("Advanced Search")).click();
        }


        public static int activeExpenseContractsTransactionsCount() {
            Driver.Instance.findElement(By.id("edit-spending-advanced-search-domain-filter-checkbook")).click();

            Driver.Instance.findElement(By.xpath("//form[@id='checkbook-advanced-search-form']/div/div[3]/h3[4]/span")).click();

            Driver.Instance.findElement(By.xpath("//form[@id='checkbook-advanced-search-form']/div/div[3]/h3[4]/span")).click();

            WebDriverWait wait = new WebDriverWait(Driver.Instance, 60);
            wait.until(ExpectedConditions.visibilityOfElementLocated(By.id("edit-checkbook-contracts-year")));

            new Select(Driver.Instance.findElement(By.id("edit-checkbook-contracts-year"))).selectByVisibleText("FY 2016");
            Driver.Instance.findElement(By.id("edit-contracts-submit")).click();

            wait.until(ExpectedConditions.visibilityOfElementLocated(By.xpath("//*[@id=\"table_939_info\"]")));
            String totalTransactionsString = Driver.Instance.findElement(By.xpath("//*[@id=\"table_939_info\"]")).getText();
            int totalTransactions = Helper.GetTotalEntries(totalTransactionsString, 5);		
            		
            
            return totalTransactions;
        }
    }
}
