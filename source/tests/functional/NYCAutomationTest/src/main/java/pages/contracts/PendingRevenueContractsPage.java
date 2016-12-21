package pages.contracts;

import navigation.TopNavigation;

public class PendingRevenueContractsPage {
	public static void GoTo() {
		if(!ContractsPage.isAt())
			ContractsPage.GoTo();
		TopNavigation.Contracts.PendingRevenueContracts.Select();	
	}
}
