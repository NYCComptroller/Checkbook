package pages.budget;

import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;

import navigation.TopNavigation;
import utility.Driver;

public class BudgetPage {
	public static void GoTo() {
        TopNavigation.Budget.Select();
    }
	
	public static boolean isAt() {
    	WebElement topTitleCont = Driver.Instance.findElement(By.cssSelector(".top-navigation-left > table > tbody > tr .budget"));
    	Boolean budgetSelected = (topTitleCont.getAttribute("class")).contains("active");	    
        return budgetSelected;
    }
}
