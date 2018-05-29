package pages.subvendors;



import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.ui.ExpectedConditions;
import org.openqa.selenium.support.ui.WebDriverWait;

import navigation.TopNavigation;
import pages.contracts.ContractsPage;
import helpers.Driver;
import helpers.Helper;

public class RegisteredSubVendorContractsPage {

	

	
		public static void GoTo() {
			if(!ContractsPage.isAt())
				ContractsPage.GoTo();
			    TopNavigation.SubVendors.Select();
			    TopNavigation.SubVendors.RegisteredSubVendors.Select();
		}

		
		
	}


