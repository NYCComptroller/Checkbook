package FunctionalSpendingOGE;

import static org.junit.Assert.assertEquals;
import helpers.Helper;

import java.sql.SQLException;

import org.junit.Before;
import org.junit.Test;

import navigation.SecondaryMenuNavigation.OtherGovernmentEntities;
import navigation.TopNavigation.Spending.ContractSpending;
import pages.spendingoge.ContractSpendingPage;
import pages.spending.SpendingPage;
import pages.spending.TotalSpendingPage;
import pages.spending.SpendingPage.WidgetOption;
import pages.home.HomePage;
import utilities.OGENYCBaseTest;
import utilities.TestStatusReport;
import utilities.OGENYCDatabaseUtil;

public class ContractSpendingWidgetDetailsTest extends OGENYCBaseTest {

	//public class ContractSpendingWidgetDetailsTest extends TestStatusReport{
	int year =  Integer.parseInt(OGENYCBaseTest.prop.getProperty("year"));
	@Before
	public void GoToPage(){		
	
		OtherGovernmentEntities.GoTo();
		ContractSpendingPage.GoToBottomNavSpendinglink() ; 
	
		if(!(Helper.getCurrentSelectedYear()).equalsIgnoreCase(OGENYCBaseTest.prop.getProperty("CurrentYear")))
			HomePage.SelectYear(OGENYCBaseTest.prop.getProperty("CurrentYear"));
		HomePage.ShowWidgetDetails();
	}



	/* ***************** Test Widget Transaction Count / title /Total amount ****************** */

	
	@Test
	public void VerifyNumOfchecksWidgetTransactionCount() throws SQLException {
		SpendingPage.GoToTop5DetailsPage(WidgetOption.Top5Checks);
	
		Integer totalChecksWidgetDetailsCountDB = OGENYCDatabaseUtil.getContractSpendingDetailsCount(year,'B');
		Integer totalChecksWidgetDetailsCountApp = SpendingPage.GetOGETransactionCount1();
		assertEquals("Contracts Spending Checks  widget Details count  did not match with the DB",totalChecksWidgetDetailsCountApp, totalChecksWidgetDetailsCountDB);
		
		String WidgetDetailsTitle =  "Checks Contract Spending Transactions";
		String WidgetDetailsTitleApp = HomePage.DetailsPagetitle();
	    assertEquals("Contracts Spending Checks  widget Details title did not match", WidgetDetailsTitle, WidgetDetailsTitleApp); 
	    
	    String WidgetDetailsAmountDB =  OGENYCDatabaseUtil.getContractsSpendingDetailsAmount(year,'B');
			String WidgetDetailsAmountApp = HomePage.GetTransactionAmount1();
		    assertEquals("Contracts Spending Checks  widget Details page Total spending amount did not match", WidgetDetailsAmountApp, WidgetDetailsAmountDB);

	}
	@Test
	public void VerifyNumOfDepartmentsWidgetTransactionCount() throws SQLException {
		SpendingPage.GoToTop5DetailsPage(WidgetOption.TopDepartments);
	
		Integer totalAgenciesWidgetDetailsCountDB = OGENYCDatabaseUtil.getContractSpendingDetailsCount(year,'B');
		Integer totalAgenciesWidgetDetailsCountApp = SpendingPage.GetOGETransactionCount1();
		assertEquals("Contracts Spending Agencies widget Details count did not match with the DB",totalAgenciesWidgetDetailsCountApp, totalAgenciesWidgetDetailsCountDB);
		
		String WidgetDetailsTitle =  "Departments Contract Spending Transactions";
		String WidgetDetailsTitleApp = HomePage.DetailsPagetitle();
	    assertEquals("Contracts Spending Agencies widget Details title did not match", WidgetDetailsTitle, WidgetDetailsTitleApp); 
	    
	    String WidgetDetailsAmountDB =  OGENYCDatabaseUtil.getContractsSpendingDetailsAmount(year,'B');
			String WidgetDetailsAmountApp = HomePage.GetTransactionAmount1();
		    assertEquals("Contracts Spending Agencies widget Details page Total Spending Amount did not match", WidgetDetailsAmountApp, WidgetDetailsAmountDB);

	}
	@Test
	public void VerifyNumOfExpenseCategoriesWidgetTransactionCount() throws SQLException{
		SpendingPage.GoToTop5DetailsPage(WidgetOption.TopExpenseCategories);
		
		Integer totalExpenseCategoriesWidgetDetailsCountDB = OGENYCDatabaseUtil.getContractSpendingDetailsCount(year,'B');
		Integer totalExpenseCategoriesWidgetDetailsCountApp = SpendingPage.GetOGETransactionCount1();
		assertEquals("Contracts Spending Exp Categories widget Details count  did not match with the DB",totalExpenseCategoriesWidgetDetailsCountApp, totalExpenseCategoriesWidgetDetailsCountDB);
		
		String WidgetDetailsTitle =  "Expense Categories Contract Spending Transactions";
		String WidgetDetailsTitleApp = HomePage.DetailsPagetitle();
	    assertEquals("Contracts Spending Exp Categories widget Details title did not match", WidgetDetailsTitle, WidgetDetailsTitleApp); 
	    
	    String WidgetDetailsAmountDB =  OGENYCDatabaseUtil.getContractsSpendingDetailsAmount(year,'B');
			String WidgetDetailsAmountApp = HomePage.GetTransactionAmount1();
		    assertEquals("Contracts Spending Exp Categories widget Detailspage Total Spending Amount did not match", WidgetDetailsAmountApp, WidgetDetailsAmountDB);

	}
	
	@Test
	public void VerifyNumOfPrimeVendorsWidgetTransactionCount() throws SQLException{
		SpendingPage.GoToTop5DetailsPage(WidgetOption.TopPrimeVendors);
		
		Integer totalPrimeVendorsWidgetDetailsCountDB = OGENYCDatabaseUtil.getContractSpendingDetailsCount(year,'B');
		Integer totalPrimeVendorsWidgetDetailsCountApp = SpendingPage.GetOGETransactionCount1();
		assertEquals("Contracts Spending Prime Vendor widget Details count  did not match with the DB",totalPrimeVendorsWidgetDetailsCountApp, totalPrimeVendorsWidgetDetailsCountDB);
		
		String WidgetDetailsTitle =  "Prime Vendors Contract Spending Transactions";
		String WidgetDetailsTitleApp = HomePage.DetailsPagetitle();
	    assertEquals("Contracts Spending Prime Vendor widget Details title did not match", WidgetDetailsTitle, WidgetDetailsTitleApp);
	    
	    String WidgetDetailsAmountDB =  OGENYCDatabaseUtil.getContractsSpendingDetailsAmount(year,'B');
			String WidgetDetailsAmountApp = HomePage.GetTransactionAmount1();
		    assertEquals("Contracts Spending Prime Vendor widget Details page Total Spending Amount did not match", WidgetDetailsAmountApp, WidgetDetailsAmountDB);

	}
	@Test
	public void VerifyNumOfContractsWidgetTransactionCount() throws SQLException{
		SpendingPage.GoToTop5DetailsPage(WidgetOption.Top5Contracts);
		
		Integer totalContractsWidgetDetailsCountDB = OGENYCDatabaseUtil.getContractSpendingContractsDetailsCount(year,'B');
		Integer totalContractsWidgetDetailsCountApp = SpendingPage.GetOGETransactionCount1();
		assertEquals("Contracts Spending Contracts widget Details count  did not match with the DB",totalContractsWidgetDetailsCountApp, totalContractsWidgetDetailsCountDB);
		
		String WidgetDetailsTitle =  "Contracts Spending Transactions";
		String WidgetDetailsTitleApp = HomePage.DetailsPagetitle();
	    assertEquals("Contracts Spending Contracts widget Details title did not match", WidgetDetailsTitle, WidgetDetailsTitleApp); 
	    
	    String WidgetDetailsAmountDB =  OGENYCDatabaseUtil.getContractsSpendingContractsDetailsAmount(year,'B');
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




