package suites;

import org.junit.runner.RunWith;
import org.junit.runners.Suite;
import org.junit.runners.Suite.SuiteClasses;

import FunctionalContracts.*;
import FunctionalContractsMWBE.*;
import FunctionalContractsSubVendors.*;
import FunctionalSpending.*;
import FunctionalBudget.*;
import FunctionalRevenue.*;
import FunctionalPayroll.*;
import FunctionalSpendingMWBE.*;
import FunctionalSpendingSubVendors.*;
import FunctionalSpendingOGE.*;
import FunctionalContractsOGE.*;
import utilities.NYCBaseTest;
import utilities.OGENYCBaseTest;
import utilities.TestStatusReport;

/**
 *
 * @author sproddutur
 */
@RunWith(Suite.class)
@SuiteClasses({
/*
	/* Spending 
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
	
	
	
	/*Contracts 
	
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
	
	
/*MWBE Contracts */
	
	MWBEActiveExpenseContractsTest.class,
	MWBEActiveExpenseContractsDetailsTest.class,
	MWBEActiveRevenueContractsTest.class,
	MWBEActiveRevenueContractsDetailsTest.class,
	MWBEPendingExpenseContractsTest.class,
	MWBEPendingExpenseContractsDetailsTest.class,
	/*MWBEPendingRevenueContractsTest.class,
	MWBEPendingRevenueContractsDetailsTest.class,*/
	MWBERegisteredExpenseContractsTest.class,
	MWBERegisteredExpenseContractsDetailsTest.class,
	MWBERegisteredRevenueContractsTest.class,
	MWBERegisteredRevenueContractsDetailsTest.class,
	
	
	/* Budget 
	
	
	BudgetWidgetDetailsPageTitlesTest.class,
	BudgetWidgetCountsTest.class,
	BudgetWidgetTitlesTest.class,
	BudgetWidgetDetailsTransactionCountsTest.class,
	BudgetWidgetDetailsTotalStaticAmountTest.class,		

	/* Revenue 
	RevenueWidgetTest.class,
    RevenueWidgetTitles.class,
	RevenueWidgetDetailsTest.class,
	RevenueWidgetDetailsAmountTest.class,
	RevenueWidgetDetailsPageTitlesTest.class,

	/* Payroll 
	PayrollWidgetCountsTest.class,
	PayrollWidgetTitlesTest.class,
	PayrollWidgetDetailsPageTitlesTest.class,	
	PayrollWidgetDetailsTransactionCountsTest.class,
	
	
	/* Sub Vendors Spending 
	SubVendorsTotalSpendingWidgetTest.class,
	SubvendorsTotalSpendingTitlesTest.class,		
	SubVendorsTotalSpendingWidgetDetailsTest.class,
	
	SubVendorsContractSpendingWidgetTest.class,
	SubVendorsContractSpendingWidgetDetailsTest.class,
	
	/* MWBE Spending 
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
	MWBEOtherSpendingWidgetDetailsTest.class,
	
/* Sub vendors Contracts 
	
	NewSubVendorContractsbyFiscalYearTest.class,
	NewSubVendorContractsbyFiscalYearDetailsTest.class,
	StatusofSubVendorContractsbyPrimeVendorWidgetTest.class,
	StatusofSubVendorContractsbyPrimeVendorWidgetDetailsTest.class,
	TotalActiveSubVendorContractsWidgetTest.class,
	TotalActiveSubVendorContractsWidgetDetailsTest.class,
 
	
	/* OGE Spending 
	OGETotalSpendingWidgetTest.class,	
	OGETotalSpendingWidgetDetailsTest.class,
	
	OGECapitalSpendingWidgetTest.class,
	OGECapitalSpendingWidgetDetailsTest.class,   

	OGEContractSpendingWidgetTest.class,
	OGEContractSpendingWidgetDetailsTest.class,
	
	/* OGE Contracts*/
	OGEActiveExpenseContractsTest.class,
	OGEActiveExpenseContractsDetailsTest.class,
	OGERegisteredExpenseContractsTest.class,
	OGERegisteredExpenseContractsDetailsTest.class, 
	
	


})
public class FunctionalTest extends NYCBaseTest
//public class FunctionalTest extends TestStatusReport

{	
	
}
