package pages.payroll;

import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;

import navigation.TopNavigation;
import utility.Driver;

public class PayrollPage {
	public static void GoTo() {
        TopNavigation.Payroll.Select();
    }
	
	public static boolean isAt() {
    	WebElement topTitleCont = Driver.Instance.findElement(By.cssSelector(".top-navigation-left > table > tbody > tr .employees"));
    	Boolean payrollSelected = (topTitleCont.getAttribute("class")).contains("active");	    
        return payrollSelected;
    }
}
