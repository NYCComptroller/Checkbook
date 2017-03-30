package navigation;

import java.util.concurrent.TimeUnit;

import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;
import helpers.Driver;

public class SecondaryMenuNavigation {

    public static final By cityWideAgenciesBy = By.xpath("//*[@id=\"all-agency-list-open\"]");
    public static final By otherGovtAgenciesBy = By.cssSelector("#agency-list-other > .agency-list-open");
    public static final By otherGovtAgenciesOptionBy = By.linkText("NEW YORK CITY ECONOMIC DEVELOPMENT CORPORATION");
    
    private enum secondaryMenuOptions {
        cityWideAgencies, otherGovtAgencies
    }

    public static void openTab(secondaryMenuOptions option) {
        switch (option) {
            case cityWideAgencies:
//                WebElement tab =
                break;

            case otherGovtAgencies:
            	WebElement tab = Driver.Instance.findElement(otherGovtAgenciesBy);
            	tab.click();
            	Driver.Instance.manage().timeouts().implicitlyWait(3,TimeUnit.SECONDS);
            	WebElement optionAnchor = Driver.Instance.findElement(otherGovtAgenciesOptionBy);
            	optionAnchor.click();
                break;

            default:
                System.out.println("Invalid Secondary Menu Option!");
                break;
        }
    }
    public static class CitywideAgencies {
    }
    
    public static class OtherGovernmentEntities{
		public static void GoTo() {
			openTab(secondaryMenuOptions.otherGovtAgencies);	
		}

		public static boolean IsAt() {
			return Driver.Instance.getCurrentUrl().contains("checkbook_oge");
		}	
    }
}
