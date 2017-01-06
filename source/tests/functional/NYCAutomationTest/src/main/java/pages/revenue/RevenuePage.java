package pages.revenue;

import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;

import navigation.TopNavigation;
import utility.Driver;

public class RevenuePage {
	public static void GoTo() {
        TopNavigation.Revenue.Select();
    }
	
	public static boolean isAt() {
    	WebElement topTitleCont = Driver.Instance.findElement(By.cssSelector(".top-navigation-left > table > tbody > tr .revenue"));
    	Boolean revenueSelected = (topTitleCont.getAttribute("class")).contains("active");	    
        return revenueSelected;
    }
}
