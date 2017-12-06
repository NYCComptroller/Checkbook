package FunctionalSpendingMWBE;

import static org.junit.Assert.assertEquals;
import helpers.Helper;

import java.sql.SQLException;

import org.junit.Before;
import org.junit.Test;

import navigation.TopNavigation.Spending.ContractSpending;
import pages.spending.ContractSpendingPage;
import pages.spending.SpendingPage;
import pages.spending.TotalSpendingPage;
import pages.spending.SpendingPage.WidgetOption;
import pages.home.HomePage;
import pages.payroll.PayrollPage;
import utilities.NYCBaseTest;
import utilities.TestStatusReport;
import utilities.NYCDatabaseUtil;


//public class ContractSpendingWidgetDetailsTest extends NYCBaseTest {
	public class MWBEContractSpendingWidgetDetailsTest extends TestStatusReport{
	int year =  Integer.parseInt(NYCBaseTest.prop.getProperty("year"));
	@Before
	public void GoToPage(){		
	
			ContractSpendingPage.GoTo();
	
		if(!(Helper.getCurrentSelectedYear()).equalsIgnoreCase(NYCBaseTest.prop.getProperty("CurrentYear")))
			HomePage.SelectYear(NYCBaseTest.prop.getProperty("CurrentYear"));
		HomePage.ShowWidgetDetails();
	}



	/* ***************** Test Widget Transaction Count / title /Total amount ****************** */

	
	@Test
	public void VerifyNumOfchecksWidgetTransactionCount() throws SQLException {
		SpendingPage.GoToTop5DetailsPage(WidgetOption.Top5Checks);
	
		Integer totalChecksWidgetDetailsCountDB = NYCDatabaseUtil.getContractSpendingDetailsCount(year,'B');
		Integer totalChecksWidgetDetailsCountApp = ContractSpendingPage.GetTransactionCount();
		assertEquals("Contracts Spending Checks  widget Details count  did not match with the DB",totalChecksWidgetDetailsCountApp, totalChecksWidgetDetailsCountDB);
		
		String WidgetDetailsTitle =  "Checks Contract Spending Transactions";
		String WidgetDetailsTitleApp = HomePage.DetailsPagetitle();
	    assertEquals("Contracts Spending Checks  widget Details title did not match", WidgetDetailsTitle, WidgetDetailsTitleApp); 
	    
	    String WidgetDetailsAmountDB =  NYCDatabaseUtil.getContractsSpendingDetailsAmount(year,'B');
			String WidgetDetailsAmountApp = HomePage.GetTransactionAmount1();
		    assertEquals("Contracts Spending Checks  widget Details page Total spending amount did not match", WidgetDetailsAmountApp, WidgetDetailsAmountDB);

	}
	@Test
	public void VerifyNumOfAgenciesWidgetTransactionCount() throws SQLException {
		SpendingPage.GoToTop5DetailsPage(WidgetOption.Top5Agencies);
	
		Integer totalAgenciesWidgetDetailsCountDB = NYCDatabaseUtil.getContractSpendingDetailsCount(year,'B');
		Integer totalAgenciesWidgetDetailsCountApp = SpendingPage.GetTransactionCount();
		assertEquals("Contracts Spending Agencies widget Details count did not match with the DB",totalAgenciesWidgetDetailsCountApp, totalAgenciesWidgetDetailsCountDB);
		
		String WidgetDetailsTitle =  "Agencies Contract Spending Transactions";
		String WidgetDetailsTitleApp = HomePage.DetailsPagetitle();
	    assertEquals("Contracts Spending Agencies widget Details title did not match", WidgetDetailsTitle, WidgetDetailsTitleApp); 
	    
	    String WidgetDetailsAmountDB =  NYCDatabaseUtil.getContractsSpendingDetailsAmount(year,'B');
			String WidgetDetailsAmountApp = HomePage.GetTransactionAmount1();
		    assertEquals("Contracts Spending Agencies widget Details page Total Spending Amount did not match", WidgetDetailsAmountApp, WidgetDetailsAmountDB);

	}
	@Test
	public void VerifyNumOfExpenseCategoriesWidgetTransactionCount() throws SQLException{
		SpendingPage.GoToTop5DetailsPage(WidgetOption.Top5ExpenseCategories);
		
		Integer totalExpenseCategoriesWidgetDetailsCountDB = NYCDatabaseUtil.getContractSpendingDetailsCount(year,'B');
		Integer totalExpenseCategoriesWidgetDetailsCountApp = SpendingPage.GetTransactionCount();
		assertEquals("Contracts Spending Exp Categories widget Details count  did not match with the DB",totalExpenseCategoriesWidgetDetailsCountApp, totalExpenseCategoriesWidgetDetailsCountDB);
		
		String WidgetDetailsTitle =  "Expense Categories Contract Spending Transactions";
		String WidgetDetailsTitleApp = HomePage.DetailsPagetitle();
	    assertEquals("Contracts Spending Exp Categories widget Details title did not match", WidgetDetailsTitle, WidgetDetailsTitleApp); 
	    
	    String WidgetDetailsAmountDB =  NYCDatabaseUtil.getContractsSpendingDetailsAmount(year,'B');
			String WidgetDetailsAmountApp = HomePage.GetTransactionAmount1();
		    assertEquals("Contracts Spending Exp Categories widget Detailspage Total Spending Amount did not match", WidgetDetailsAmountApp, WidgetDetailsAmountDB);

	}
	
	@Test
	public void VerifyNumOfPrimeVendorsWidgetTransactionCount() throws SQLException{
		SpendingPage.GoToTop5DetailsPage(WidgetOption.Top5PrimeVendors);
		
		Integer totalPrimeVendorsWidgetDetailsCountDB = NYCDatabaseUtil.getContractSpendingDetailsCount(year,'B');
		Integer totalPrimeVendorsWidgetDetailsCountApp = SpendingPage.GetTransactionCount();
		assertEquals("Contracts Spending Prime Vendor widget Details count  did not match with the DB",totalPrimeVendorsWidgetDetailsCountApp, totalPrimeVendorsWidgetDetailsCountDB);
		
		String WidgetDetailsTitle =  "Prime Vendors Contract Spending Transactions";
		String WidgetDetailsTitleApp = HomePage.DetailsPagetitle();
	    assertEquals("Contracts Spending Prime Vendor widget Details title did not match", WidgetDetailsTitle, WidgetDetailsTitleApp);
	    
	    String WidgetDetailsAmountDB =  NYCDatabaseUtil.getContractsSpendingDetailsAmount(year,'B');
			String WidgetDetailsAmountApp = HomePage.GetTransactionAmount1();
		    assertEquals("Contracts Spending Prime Vendor widget Details page Total Spending Amount did not match", WidgetDetailsAmountApp, WidgetDetailsAmountDB);

	}
	@Test
	public void VerifyNumOfContractsWidgetTransactionCount() throws SQLException{
		SpendingPage.GoToTop5DetailsPage(WidgetOption.Top5Contracts);
		
		Integer totalContractsWidgetDetailsCountDB = NYCDatabaseUtil.getContractSpendingContractsDetailsCount(year,'B');
		Integer totalContractsWidgetDetailsCountApp = SpendingPage.GetTransactionCount();
		assertEquals("Contracts Spending Contracts widget Details count  did not match with the DB",totalContractsWidgetDetailsCountApp, totalContractsWidgetDetailsCountDB);
		
		String WidgetDetailsTitle =  "Contracts Spending Transactions";
		String WidgetDetailsTitleApp = HomePage.DetailsPagetitle();
	    assertEquals("Contracts Spending Contracts widget Details title did not match", WidgetDetailsTitle, WidgetDetailsTitleApp); 
	    
	    String WidgetDetailsAmountDB =  NYCDatabaseUtil.getContractsSpendingDetailsAmount(year,'B');
			String WidgetDetailsAmountApp = HomePage.GetTransactionAmount1();
		    assertEquals("Contracts Spending Contracts widget Details page Total Spending Amount did not match", WidgetDetailsAmountApp, WidgetDetailsAmountDB);

	}
	
	
	//@Test
	//public void VerifyTop5AgenciesbyPayrollTransactionCount() throws SQLException{
	//	PayrollPage.GoToTop5DetailsPage(WidgetOption.Top5AgenciesbyPayroll);
		////HomePage.ShowWidgetDetails();
		//Integer NumOfPayrollDetailsCountyear = NYCDatabaseUtil.getPayrollDetailsCount(year,'B');
		//Integer numOfPayrollDetailsCountapp = PayrollPage.GetTransactionCount();
		//assertEquals("Number of Payroll salaried employees did not match", numOfPayrollDetailsCountapp, NumOfPayrollDetailsCountyear); 
	//}
}




