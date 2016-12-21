package pages.contracts;

import navigation.TopNavigation;

public class RegisteredRevenueContractsPage {
	public static void GoTo() {
		if(!ContractsPage.isAt())
			ContractsPage.GoTo();
		TopNavigation.Contracts.RegisteredRevenueContracts.Select();	
	}
}	
