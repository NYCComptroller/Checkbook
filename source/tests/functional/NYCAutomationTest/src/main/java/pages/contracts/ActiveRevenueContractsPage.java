package pages.contracts;

import navigation.TopNavigation;

public class ActiveRevenueContractsPage {
	public static void GoTo() {
		if(!ContractsPage.isAt())
			ContractsPage.GoTo();
		TopNavigation.Contracts.ActiveRevenueContracts.Select();	
	}
}
