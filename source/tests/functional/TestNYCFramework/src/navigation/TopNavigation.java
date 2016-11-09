package navigation;

public class TopNavigation {

	public static class Spending{

		public static void Select() {
			PrimaryTabSelector.Select("spending");
		}
		
		public static class PayrollSpending{
			public static void Select() {
				SecondaryTabSelector.Select("Payroll Spending");	
			}
		}	
	}
		
	public enum TopNavTabs{
		Spending, Revenue, Budget, Contracts, Payroll
	}

}

