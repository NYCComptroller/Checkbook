package pages.spending;

import pages.home.HomePage;

public class PayrollSpendingPage {

    public static void GoTo() {
    	navigation.TopNavigation.Spending.PayrollSpending.Select();
    }
    
    public static boolean isAt() {
        return navigation.TopNavigation.Spending.PayrollSpending.isAt();
    }

	public static Integer GetNumberOfAgencies() {
		return HomePage.GetWidgetTotalNumber("Top 5 Agencies");
	}

}
