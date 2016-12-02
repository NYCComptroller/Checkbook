package navigation;

public class TopNavigation {

	public static class Spending{

		public static void Select() {
			PrimaryTabSelector.Select("spending");
		}
		
		public static class TotalSpending{
			public static void Select() {
				SecondaryTabSelector.Select("Total Spending");	
			}
			public static boolean isAt() {
				return SecondaryTabSelector.isAt("Total Spending");	
			}
		}
		
		public static class PayrollSpending{
			public static void Select() {
				SecondaryTabSelector.Select("Payroll Spending");	
			}
			public static boolean isAt() {
				return SecondaryTabSelector.isAt("Payroll Spending");	
			}
		}
		
		public static class CapitalSpending{
			public static void Select() {
				SecondaryTabSelector.Select("Capital Spending");	
			}
		}
		
		public static class ContractSpending{
			public static void Select() {
				SecondaryTabSelector.Select("Contract Spending");	
			}
		}
		
		public static class TrustAgencySpending{
			public static void Select() {
				SecondaryTabSelector.Select("Trust & Agency Spending");	
			}
		}
		
		public static class OtherSpending{
			public static void Select() {
				SecondaryTabSelector.Select("Other Spending");	
			}
		}
	}
	
	
	
		
	public enum TopNavTabs{
		Spending, Revenue, Budget, Contracts, Payroll
	}

}

