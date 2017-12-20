package suites;

import org.junit.runner.RunWith;
import org.junit.runners.Suite;
import org.junit.runners.Suite.SuiteClasses;

import FunctionalContracts.*;
import FunctionalSpending.*;
import FunctionalBudget.*;
import FunctionalRevenue.*;
import FunctionalPayroll.*;
//import FunctionalSpendingMWBE.*;
import FunctionalSpendingSubVendors.*;
import utilities.NYCBaseTest;
import utilities.TestStatusReport;


@RunWith(Suite.class)
@SuiteClasses({

	/* Spending */
	TotalSpendingWidgetTest.class,
	TotalSpendingTitlesTest.class,
	TotalSpendingWidgetDetailsTest.class,
	
	PayrollSpendingWidgetDetailsTest.class,
	PayrollSpendingWidgetTest.class,
	
	CapitalSpendingWidgetDetailsTest.class,
    CapitalSpendingWidgetTest.class,
   
	ContractSpendingWidgetDetailsTest.class,
	ContractSpendingWidgetTest.class,
	
	TrustAgencySpendingWidgetDetailsTest.class,
	TrustAgencySpendingWidgetTest.class,
	
	OtherSpendingWidgetDetailsTest.class,
	OtherSpendingWidgetTest.class,
	
	
	
	/*Contracts */
	
	ActiveExpenseContractsTest.class,
	ActiveExpenseContractsDetailsTest.class,
	ActiveRevenueContractsTest.class,
	ActiveRevenueContractsDetailsTest.class,
	PendingExpenseContractsTest.class,
    PendingExpenseContractsDetailsTest.class,
	PendingRevenueContractsTest.class,
	PendingRevenueContractsDetailsTest.class,
	RegisteredExpenseContractsTest.class,
	RegisteredExpenseContractsDetailsTest.class,
	RegisteredRevenueContractsTest.class,
	RegisteredRevenueContractsDetailsTest.class,
 
	/* Budget */
	
	
	BudgetWidgetDetailsPageTitlesTest.class,
	BudgetWidgetDetailsTransactionCountsTest.class,
	BudgetWidgetDetailsTotalStaticAmountTest.class,
	
	BudgetWidgetCountsTest.class,
	BudgetWidgetTitlesTest.class,

	

	/* Revenue */
	RevenueWidgetTest.class,
    RevenueWidgetTitles.class,
	RevenueWidgetDetailsTest.class,
	RevenueWidgetDetailsAmountTest.class,
	RevenueWidgetDetailsPageTitlesTest.class,

	/* Payroll */
	PayrollWidgetCountsTest.class,
	PayrollWidgetTitlesTest.class,
	PayrollWidgetDetailsPageTitlesTest.class,	
	PayrollWidgetDetailsTransactionCountsTest.class,
	
	
	/* Sub Vendors Spending */
	SubVendorsTotalSpendingWidgetTest.class,
	SubVendorsTotalSpendingWidgetDetailsTest.class,
	SubvendorsTotalSpendingTitlesTest.class,
	SubVendorsContractSpendingWidgetDetailsTest.class,
	SubVendorsContractSpendingWidgetTest.class,
	

})
public class FunctionalTest extends NYCBaseTest
//public class FunctionalTest extends TestStatusReport

{	
	
}
