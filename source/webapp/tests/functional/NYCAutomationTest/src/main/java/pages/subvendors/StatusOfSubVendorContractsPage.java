package pages.subvendors;

import navigation.TopNavigation;
import pages.contracts.ContractsPage;

public class StatusOfSubVendorContractsPage {

	public static void GoTo() {
		if(!ContractsPage.isAt())
			ContractsPage.GoTo();
		    TopNavigation.SubVendors.Select();
		    TopNavigation.SubVendors.StatusSubVendors.Select();
	}
}