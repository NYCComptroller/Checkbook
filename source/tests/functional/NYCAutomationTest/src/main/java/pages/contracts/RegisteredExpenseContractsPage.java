package pages.contracts;

import navigation.TopNavigation;

public class RegisteredExpenseContractsPage {
	public static void GoTo() {
		if(!ContractsPage.isAt())
			ContractsPage.GoTo();
		TopNavigation.Contracts.RegisteredExpenseContracts.Select();	
	}
}
