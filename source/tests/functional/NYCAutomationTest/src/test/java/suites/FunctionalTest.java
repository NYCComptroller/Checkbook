package suites;

import org.junit.runner.RunWith;
import org.junit.runners.Suite;
import org.junit.runners.Suite.SuiteClasses;

import FunctionalContracts.*;
import FunctionalSpending.*;
import FunctionalBudget.*;
import FunctionalRevenue.*;
import FunctionalPayroll.*;
import FunctionalSpendingMWBE.*;
import FunctionalSpendingSubVendors.*;
import utilities.NYCBaseTest;
import utilities.TestStatusReport;

/**
 *
 * @author sproddutur
 */
@RunWith(Suite.class)
@SuiteClasses({

	/* Spending */
	TotalSpendingWidgetTest.class,
	TotalSpendingTitlesTest.class,
	TotalSpendingWidgetDetailsTest.class,	
	
	PayrollSpendingWidgetTest.class,
	PayrollSpendingWidgetDetailsTest.class,	
	
    CapitalSpendingWidgetTest.class,
    CapitalSpendingWidgetDetailsTest.class,   

	ContractSpendingWidgetTest.class,
	ContractSpendingWidgetDetailsTest.class,
	
	TrustAgencySpendingWidgetTest.class,
	TrustAgencySpendingWidgetDetailsTest.class,	

	OtherSpendingWidgetTest.class,
	OtherSpendingWidgetDetailsTest.class,
	
	
	
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
	BudgetWidgetCountsTest.class,
	BudgetWidgetTitlesTest.class,
	BudgetWidgetDetailsTransactionCountsTest.class,
	BudgetWidgetDetailsTotalStaticAmountTest.class,		

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
	SubvendorsTotalSpendingTitlesTest.class,		
	SubVendorsTotalSpendingWidgetDetailsTest.class,
	
	SubVendorsContractSpendingWidgetTest.class,
	SubVendorsContractSpendingWidgetDetailsTest.class,
	
	/* MWBE Spending */
	MWBETotalSpendingWidgetTest.class,
	MWBETotalSpendingTitlesTest.class,
	MWBETotalSpendingWidgetDetailsTest.class,
	
	MWBECapitalSpendingWidgetTest.class,
	MWBECapitalSpendingWidgetDetailsTest.class,   

	MWBEContractSpendingWidgetTest.class,
	MWBEContractSpendingWidgetDetailsTest.class,
	
	MWBETrustAgencySpendingWidgetTest.class,
	MWBETrustAgencySpendingWidgetDetailsTest.class,	

	MWBEOtherSpendingWidgetTest.class,
	MWBEOtherSpendingWidgetDetailsTest.class
	

})
public class FunctionalTest extends NYCBaseTest
//public class FunctionalTest extends TestStatusReport

{	
	
}
