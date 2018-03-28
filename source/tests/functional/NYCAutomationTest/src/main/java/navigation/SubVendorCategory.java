package navigation;

import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.interactions.Actions;
import helpers.Driver;

public class SubVendorCategory {
	
	public enum SubVendorCategoryOption{
		AsianAmerican, BlackAmerican, Women, HispanicAmerican, TotalSubVendors, SubVendorsHome
	}
	
	public static void select(SubVendorCategoryOption option) {
		Actions actions = new Actions(Driver.Instance);
		WebElement hoverTriangle = Driver.Instance.findElement(By.cssSelector(".content .expense .mwbe.subvendors .expense-container .drop-down-menu-triangle"));
		actions.moveToElement(hoverTriangle).perform();
		
		switch (option) {
		case AsianAmerican:	
			(hoverTriangle.findElement(By.linkText("Asian American"))).click();;
			break;
		case BlackAmerican:
			(hoverTriangle.findElement(By.linkText("Black American"))).click();
			break;
		case Women:
			(hoverTriangle.findElement(By.linkText("Women"))).click();
			break;
		case HispanicAmerican:
			(hoverTriangle.findElement(By.linkText("Hispanic American"))).click();
			break;
		case TotalSubVendors:
			(hoverTriangle.findElement(By.linkText("Total Sub Vendors"))).click();
			break;
		case SubVendorsHome:
			(hoverTriangle.findElement(By.linkText("Sub Vendors Home"))).click();
			break;
		default:
			break;
		}
	}
}
