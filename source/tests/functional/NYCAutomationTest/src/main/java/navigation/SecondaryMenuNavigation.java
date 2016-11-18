package navigation;

import org.openqa.selenium.By;

public class SecondaryMenuNavigation {

    public static final By cityWideAgenciesBy = By.xpath("//*[@id=\"all-agency-list-open\"]");
    public static final By otherGovtAgenciesBy = By.xpath("//*[@id=\"nice-menu-1\"]//span[contains(text(),'Tools')]");

    private enum secondaryMenuOptions {
        cityWideAgencies, otherGovtAgencies
    }

    public static void openTab(secondaryMenuOptions option) {
        switch (option) {
            case cityWideAgencies:
//                WebElement tab =
                break;

            case otherGovtAgencies:
                break;

            default:
                System.out.println("Invalid Secondary Menu Option!");
                break;
        }
    }
    public static class CitywideAgencies {
    }
}
