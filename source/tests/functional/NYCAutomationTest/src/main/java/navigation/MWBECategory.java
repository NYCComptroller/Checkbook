package navigation;

import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.interactions.Actions;

import utility.Driver;

public class MWBECategory {
	
	public enum MWBECategoryOption{
		AsianAmerican, BlackAmerican, Women, HispanicAmerican, TotalMWBE, MWBEHome
	}
	
	public static void select(MWBECategoryOption option) {
		Actions actions = new Actions(Driver.Instance);
		WebElement hoverTriangle = Driver.Instance.findElement(By.cssSelector(".content .expense .mwbe .expense-container .drop-down-menu-triangle"));
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
		case TotalMWBE:
			(hoverTriangle.findElement(By.linkText("Total M/WBE"))).click();
			break;
		case MWBEHome:
			(hoverTriangle.findElement(By.linkText("M/WBE Home"))).click();
			break;
		default:
			break;
		}
	}
}
