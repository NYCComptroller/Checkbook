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
			public static boolean isAt() {
				return SecondaryTabSelector.isAt("Capital Spending");	
			}
		}
		
		public static class ContractSpending{
			public static void Select() {
				SecondaryTabSelector.Select("Contract Spending");	
			}
			public static boolean isAt() {
				return SecondaryTabSelector.isAt("Contract Spending");	
			}
		}
		
		public static class TrustAgencySpending{
			public static void Select() {
				SecondaryTabSelector.Select("Trust & Agency Spending");	
			}
			public static boolean isAt() {
				return SecondaryTabSelector.isAt("Trust & Agency Spending");	
			}
		}
		
		public static class OtherSpending{
			public static void Select() {
				SecondaryTabSelector.Select("Other Spending");	
			}
			public static boolean isAt() {
				return SecondaryTabSelector.isAt("Other Spending");	
			}
		}
	}
	
	
	public static class Contracts{
		public static void Select() {
			PrimaryTabSelector.Select("contracts");
		}
		
		public static class ActiveExpenseContracts{
			public static void Select() {
				SecondaryTabSelector.Select("Active Expense Contracts");	
			}
			public static boolean isAt() {
				return SecondaryTabSelector.isAt("Active Expense Contracts");	
			}
		}
		
		public static class RegisteredExpenseContracts{
			public static void Select() {
				SecondaryTabSelector.Select("Registered Expense Contracts");	
			}
			public static boolean isAt() {
				return SecondaryTabSelector.isAt("Registered Expense Contracts");	
			}
		}
		
		public static class ActiveRevenueContracts{
			public static void Select() {
				SecondaryTabSelector.Select("Active Revenue Contracts");	
			}
			public static boolean isAt() {
				return SecondaryTabSelector.isAt("Active Revenue Contracts");	
			}
		}
		
		public static class RegisteredRevenueContracts{
			public static void Select() {
				SecondaryTabSelector.Select("Registered Revenue Contracts");	
			}
			public static boolean isAt() {
				return SecondaryTabSelector.isAt("Registered Revenue Contracts");	
			}
		}
		
		public static class PendingExpenseContracts{
			public static void Select() {
				SecondaryTabSelector.Select("Pending Expense Contracts");	
			}
			public static boolean isAt() {
				return SecondaryTabSelector.isAt("Pending Expense Contracts");	
			}
		}
		
		public static class PendingRevenueContracts{
			public static void Select() {
				SecondaryTabSelector.Select("Pending Revenue Contracts");	
			}
			public static boolean isAt() {
				return SecondaryTabSelector.isAt("Pending Revenue Contracts");	
			}
		}
		
		
	}
	
	
	public static class Budget{
		public static void Select() {
			PrimaryTabSelector.Select("budget");
		}
	}
	
	public static class Revenue{
		public static void Select() {
			PrimaryTabSelector.Select("revenue");
		}
	}
	
	public static class Payroll{
		public static void Select() {
			PrimaryTabSelector.Select("employees");
		}
	}
	public static  class SubVendors{
		
		public static void Select() {
			PrimaryTabSelector.SelectTopRightNavigation("subvendors");
		}	
		public static class RegisteredSubVendors{
			public static void Select() {
				 SecondarySubVendorTabSelector.Select("New Sub Vendor Contracts by Fiscal Year");	
			}
			public static boolean isAt() {
				return  SecondarySubVendorTabSelector.isAt("New Sub Vendor Contracts by Fiscal Year");	
			}
		}
		public static class StatusSubVendors{
			public static void Select() {
				StatusSubVendorTabSelector.Select("Status of Sub Vendor Contracts by Prime Vendor");
			}
			public static boolean isAt() {
				return  StatusSubVendorTabSelector.isAt("Status of Sub Vendor Contracts by Prime Vendor");	
			}
		}
	}
	
		
	public enum TopNavTabs{
		Spending, Revenue, Budget, Contracts, Payroll,MWBE ,SubVendors
	}

}

