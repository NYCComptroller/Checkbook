package pages.contracts;

import navigation.TopNavigation;

public class PendingExpenseContractsPage {
	public static void GoTo() {
		if(!ContractsPage.isAt())
			ContractsPage.GoTo();
		TopNavigation.Contracts.PendingExpenseContracts.Select();	
	}
}
