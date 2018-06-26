package FunctionalSpendingOGE;

import static org.junit.Assert.assertEquals;
import helpers.Helper;

import java.sql.SQLException;

import org.junit.Before;
import org.junit.Test;

import navigation.SecondaryMenuNavigation.OtherGovernmentEntities;
import navigation.TopNavigation.Spending.TotalSpending;
import pages.spending.SpendingPage;
import pages.spending.TotalSpendingPage;
import pages.spending.SpendingPage.WidgetOption;
import pages.home.HomePage;
import pages.payroll.PayrollPage;
import utilities.TestStatusReport;
import utilities.OGENYCDatabaseUtil;
import utilities.OGENYCBaseTest;

public class OGETotalSpendingWidgetDetailsTest extends OGENYCBaseTest {
	//public class TotalSpendingWidgetDetailsTest extends TestStatusReport{
	int year =  Integer.parseInt(OGENYCBaseTest.prop.getProperty("year"));
	
	@Before
	public void GoToPage(){
	//	if(!OtherGovernmentEntities.IsAt())
			OtherGovernmentEntities.GoTo();
		if(!(Helper.getCurrentSelectedYear()).equalsIgnoreCase(OGENYCBaseTest.prop.getProperty("CurrentYear")))
			   HomePage.SelectYear(OGENYCBaseTest.prop.getProperty("CurrentYear"));
	
}


	/* ***************** Test Widget Transaction Count ****************** */

	
	@Test
	public void VerifyNumOfchecksWidgetTransactionCount() throws SQLException {
		SpendingPage.GoToTop5DetailsPage(WidgetOption.Top5Checks);
		
		Integer totalCheckswidgetCountDB = OGENYCDatabaseUtil.getTotalSpendingDetailsCount(year,'B');
		Integer totalChecksWidgetCountApp = SpendingPage.GetOGETransactionCount1();
		assertEquals("Total Spending Checks  widget count  did not match with the DB",totalChecksWidgetCountApp, totalCheckswidgetCountDB);
		
		String WidgetDetailsTitle =  "Checks Total Spending Transactions";
		String WidgetDetailsTitleApp = HomePage.DetailsPagetitle();
	    assertEquals("Total Spending Checks Widget title did not match", WidgetDetailsTitle, WidgetDetailsTitleApp); 
	    
	    String WidgetDetailsAmountDB =  OGENYCDatabaseUtil.getTotalSpendingDetailsAmount(year,'B');
		String WidgetDetailsAmountApp = HomePage.GetTransactionAmount2();
	    assertEquals("Total Spending checks Widget Details page total spending amount did not match", WidgetDetailsAmountApp, WidgetDetailsAmountDB); 
	  

	}
	@Test
	public void VerifyNumOfDepartmentsWidgetTransactionCount() throws SQLException {
		SpendingPage.GoToTop5DetailsPage(WidgetOption.TopDepartments);
	
		Integer totalAgencieswidgetCountDB = OGENYCDatabaseUtil.getTotalSpendingDetailsCount(year,'B');
		Integer totalAgenciesWidgetCountApp = SpendingPage.GetOGETransactionCount1();
		assertEquals("Total Spending agencies widget count  did not match with the DB",totalAgenciesWidgetCountApp, totalAgencieswidgetCountDB);
		
		String WidgetDetailsTitle =  "Departments Total Spending Transactions";
		String WidgetDetailsTitleApp = HomePage.DetailsPagetitle();
	    assertEquals("Total Spending Departments Widget title did not match", WidgetDetailsTitle, WidgetDetailsTitleApp); 
	    
	    String WidgetDetailsAmountDB =  OGENYCDatabaseUtil.getTotalSpendingDetailsAmount(year,'B');
		String WidgetDetailsAmountapp = HomePage.GetTransactionAmount2();
		assertEquals("Total Spending Departments Widget Details page total spending amount did not match", WidgetDetailsAmountapp, WidgetDetailsAmountDB);
	}
	
	@Test
	public void VerifyNumOfExpenseCategoriesWidgetTransactionCount() throws SQLException{
		SpendingPage.GoToTop5DetailsPage(WidgetOption.Top5ExpenseCategories);
		Integer totalExpenseCategorieswidgetCountDB = OGENYCDatabaseUtil.getTotalSpendingDetailsCount(year,'B');
		Integer totalExpenseCategoriesWidgetCountApp = SpendingPage.GetOGETransactionCount1();
		assertEquals("Total Spending Exp categories  widget count  did not match with the DB",totalExpenseCategoriesWidgetCountApp, totalExpenseCategorieswidgetCountDB);
		
		String WidgetDetailsTitle =  "Expense Categories Total Spending Transactions";
		String WidgetDetailsTitleApp = HomePage.DetailsPagetitle();
	    assertEquals("Total Spending Exp categories Widget title did not match", WidgetDetailsTitle, WidgetDetailsTitleApp); 
	    
	    
	    String WidgetDetailsAmountDB =  OGENYCDatabaseUtil.getTotalSpendingDetailsAmount(year,'B');
		String WidgetDetailsAmountApp = HomePage.GetTransactionAmount2();
		assertEquals("Total Spending Exp categories  widget details  page total spening amount did not match", WidgetDetailsAmountApp, WidgetDetailsAmountDB);
	}
	
	
	@Test
	public void VerifyNumOfPrimeVendorsWidgetTransactionCount() throws SQLException{
		SpendingPage.GoToTop5DetailsPage(WidgetOption.Top5PrimeVendors);
		Integer totalPrimeVendorswidgetCountDB = OGENYCDatabaseUtil.getTotalSpendingDetailsCount(year,'B');
		Integer totalPrimeVendorsWidgetCountApp = SpendingPage.GetOGETransactionCount1();
		assertEquals("Total Spending Prime Vendor  widget count  did not match with the DB",totalPrimeVendorsWidgetCountApp, totalPrimeVendorswidgetCountDB);
		
		String WidgetDetailsTitle =  "Prime Vendors Total Spending Transactions";
		String WidgetDetailsTitleApp = HomePage.DetailsPagetitle();
	    assertEquals("Total Spending Prime Vendor Widget title did not match", WidgetDetailsTitle, WidgetDetailsTitleApp); 
	    
	    String WidgetDetailsAmountDB =  OGENYCDatabaseUtil.getTotalSpendingDetailsAmount(year,'B');
	    String WidgetDetailsAmountApp = HomePage.GetTransactionAmount2();
		assertEquals("Total Spending Prime Vendor  widget details total spending amount did not match", WidgetDetailsAmountApp, WidgetDetailsAmountDB);
	}
	
	@Test
	public void VerifyNumOfContractsWidgetTransactionCount() throws SQLException{
		SpendingPage.GoToTop5DetailsPage(WidgetOption.Top5Contracts);
		
		Integer totalContractswidgetCountDB = OGENYCDatabaseUtil.getTotalSpendingDetailsCount(year,'B');
		Integer totalContractsWidgetCountApp = SpendingPage.GetOGETransactionCount1();
		assertEquals("Total Spending Contracts  widget count  did not match with the DB",totalContractsWidgetCountApp, totalContractswidgetCountDB);
		
		String WidgetDetailsTitle =  "Contracts Total Spending Transactions";
		String WidgetDetailsTitleApp = HomePage.DetailsPagetitle();
	    assertEquals("Total Spending Contracts Widget title did not match", WidgetDetailsTitle, WidgetDetailsTitleApp); 
	    
	    
	    String WidgetDetailsAmountDB =  OGENYCDatabaseUtil.getTotalSpendingContractsDetailsAmount(year, 'B');
		String WidgetDetailsAmountApp = HomePage.GetTransactionAmount2();
	    assertEquals("Total Spending  Contracts  widget details  page total spending amount did not match", WidgetDetailsAmountApp, WidgetDetailsAmountDB);
}
	
	//@Test
	//public void VerifyTop5AgenciesbyPayrollTransactionCount() throws SQLException{
	//	PayrollPage.GoToTop5DetailsPage(WidgetOption.Top5AgenciesbyPayroll);
		////HomePage.ShowWidgetDetails();
		//Integer NumOfPayrollDetailsCount2016 = NYCDatabaseUtil.getPayrollDetailsCount(2016,'B');
		//Integer numOfPayrollDetailsCountapp = PayrollPage.GetTransactionCount();
		//assertEquals("Total Spending Payroll salaried employees did not match", numOfPayrollDetailsCountapp, NumOfPayrollDetailsCount2016); 
	//}
	/*
	@Test
	public void VerifySpendingTransactionTitle() throws SQLException {
		//Float transactionAmt = 26.3f;
		SpendingPage.GoToTop5DetailsPage(WidgetOption.Top5Agencies);
		HomePage.ShowWidgetDetails();
	//assertTrue(HomePage.GetTransactionAmount1()== transactionAmt);
		String AgenciesTitle =  "Agencies Total Spending Transactions";
		String SpendingAgenciesTitleApp = HomePage.DetailsPagetitle();
	assertEquals("Total Spending Agencies Widget title did not match", AgenciesTitle, SpendingAgenciesTitleApp); 
	}
	*/
}




