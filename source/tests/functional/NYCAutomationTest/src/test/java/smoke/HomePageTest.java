package smoke;

import static org.junit.Assert.assertTrue;

import org.junit.Before;
import org.junit.Test;
import org.openqa.selenium.By;

import navigation.PrimaryMenuNavigation;
import pages.spending.SpendingPage;
import utility.Helper;

public class HomePageTest {
	@Before
    public void GoToPage(){
		//HomePage.GoTo(NYCBaseTest.prop.getProperty("BaseUrl"));
    }
		 
    @Test
    public void primaryHomeLinkWorks() {
        PrimaryMenuNavigation.select(PrimaryMenuNavigation.home);
        assertTrue(SpendingPage.isAt());
    }

	/* @Test
    public void verifyCreateAlert() {
        HomePage.createAlert();
        assertTrue(HomePage.IsAlertCreated());
    }*/
	 
	 @Test
    public void bannerExists() {
        assertTrue(Helper.isPresent(By.id("logo")));
    }
}
