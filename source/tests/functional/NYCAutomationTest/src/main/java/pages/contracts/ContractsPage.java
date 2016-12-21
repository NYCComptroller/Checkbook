package pages.contracts;

import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;

import navigation.TopNavigation;
import utility.Driver;

public class ContractsPage {
	public static void GoTo() {
        TopNavigation.Contracts.Select();
    }
	public static boolean isAt() {
    	WebElement topTitleCont = Driver.Instance.findElement(By.cssSelector(".top-navigation-left > table > tbody > tr .contracts"));
    	Boolean contractsSelected = (topTitleCont.getAttribute("class")).contains("active");	
        //WebElement h2title = Driver.Instance.findElement(By.xpath("//*[@id=\"node-widget-21\"]/div[1]/h2"));
        //Boolean totalSpendingSelected = h2title.getText().equals("Total Spending");    
        return contractsSelected;
    }
}
