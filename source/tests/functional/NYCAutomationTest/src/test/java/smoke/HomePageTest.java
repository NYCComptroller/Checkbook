package smoke;

import static org.junit.Assert.assertTrue;
import helpers.Helper;

import org.junit.Before;
import org.junit.Test;
import org.openqa.selenium.By;

import navigation.PrimaryMenuNavigation;
import pages.spending.SpendingPage;
import pages.home.HomePage;
import utilities.NYCBaseTest;
import utilities.TestStatusReport;

public class HomePageTest extends TestStatusReport{
	@Before
    public void GoToPage(){
		HomePage.GoTo(NYCBaseTest.prop.getProperty("BaseUrl"));
    }
		 
    @Test
    public void primaryHomeLinkWorks() {
        PrimaryMenuNavigation.select(PrimaryMenuNavigation.home);
        assertTrue(SpendingPage.isAt());
    }

	 @Test
    public void verifyCreateAlert() {
        HomePage.createAlert();
        assertTrue(HomePage.IsAlertCreated());
    }
	 
	 @Test
    public void bannerExists() {
        assertTrue(Helper.isPresent(By.id("logo")));
    }
}
