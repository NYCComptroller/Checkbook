package pages.contracts;

import navigation.TopNavigation;

public class ActiveExpenseContractsPage {
	public static void GoTo() {
		if(!ContractsPage.isAt())
			ContractsPage.GoTo();
		TopNavigation.Contracts.ActiveExpenseContracts.Select();	
	}
}
