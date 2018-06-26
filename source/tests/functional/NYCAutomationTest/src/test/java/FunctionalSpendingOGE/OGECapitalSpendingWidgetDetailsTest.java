package FunctionalSpendingOGE;

import static org.junit.Assert.assertEquals;
import helpers.Helper;

import java.sql.SQLException;

import org.junit.Before;
import org.junit.Test;

import navigation.SecondaryMenuNavigation.OtherGovernmentEntities;
import pages.spendingoge.CapitalSpendingPage;
import pages.spending.SpendingPage;
import pages.spending.SpendingPage.WidgetOption;
import pages.home.HomePage;
import utilities.TestStatusReport;
import utilities.OGENYCDatabaseUtil;
import utilities.OGENYCBaseTest;


public class OGECapitalSpendingWidgetDetailsTest extends OGENYCBaseTest {
	//public class CapitalSpendingWidgetDetailsTest extends TestStatusReport{
	int year =  Integer.parseInt(OGENYCBaseTest.prop.getProperty("year"));
		
		@Before
		public void GoToPage(){
			//if(!OtherGovernmentEntities.IsAt())
				OtherGovernmentEntities.GoTo();
			CapitalSpendingPage.GoToBottomNavSpendinglink() ; 
			
			if(!(Helper.getCurrentSelectedYear()).equalsIgnoreCase(OGENYCBaseTest.prop.getProperty("CurrentYear")))
				   HomePage.SelectYear(OGENYCBaseTest.prop.getProperty("CurrentYear"));
		
}



	/* ***************** Test Widget Transaction Count ****************** */
	
	@Test
	public void VerifyNumOfchecksWidgetTransactionPage() throws SQLException {
		
		SpendingPage.GoToTop5DetailsPage(WidgetOption.Top5Checks);
		
		Integer totalChecksWidgetDetailsCountDB = OGENYCDatabaseUtil.getCapitalSpendingDetailsCount(year,'B');
		Integer totalChecksWidgetDetailsCountApp = SpendingPage.GetOGETransactionCount1();
		assertEquals("Capital Spending checks Widget details count  did not match with the DB",totalChecksWidgetDetailsCountApp, totalChecksWidgetDetailsCountDB);
		//Test Widget details page title
		
		String WidgetDetailsTitle =  "Checks Capital Spending Transactions";
		String WidgetDetailsTitleApp = HomePage.DetailsPagetitle();
	    assertEquals("Capital Spending checks Widget title did not match", WidgetDetailsTitle, WidgetDetailsTitleApp); 
	    
	    String WidgetDetailsAmountDB =  OGENYCDatabaseUtil.getCapitalContractsSpendingDetailsAmount(year,'B');
		String WidgetDetailsAmountApp = HomePage.GetTransactionAmount2();
	    assertEquals("Capital Spending checks Widget Details page total spending amount did not match", WidgetDetailsAmountApp, WidgetDetailsAmountDB); 
	  	}
	


	@Test
	public void VerifyDepartmentsWidgetTransactionPage() throws SQLException {
		SpendingPage.GoToTop5DetailsPage(WidgetOption.TopDepartments);
		
		Integer totalAgenciesWidgetDetailsCountDB = OGENYCDatabaseUtil.getCapitalSpendingDetailsCount(year,'B');
		Integer totalAgenciesWidgetDetailsCountApp = SpendingPage.GetOGETransactionCount1();
		assertEquals("Capital Spending Departments Widget Details page total spending amount did not match with the DB",totalAgenciesWidgetDetailsCountApp, totalAgenciesWidgetDetailsCountDB);
		
		String WidgetDetailsTitle =  "Departments Capital Spending Transactions";
		String WidgetDetailsTitleApp = HomePage.DetailsPagetitle();
	    assertEquals("Capital Spending Departments Widget details page title did not match", WidgetDetailsTitle, WidgetDetailsTitleApp); 
	    
	    String WidgetDetailsAmountDB =  OGENYCDatabaseUtil.getCapitalContractsSpendingDetailsAmount(year,'B');
		String WidgetDetailsAmountapp = HomePage.GetTransactionAmount2();
		assertEquals("Capital Spending Departments Widget Details page total spending amount did not match", WidgetDetailsAmountapp, WidgetDetailsAmountDB);
	}
	@Test
	public void VerifyExpenseCategoriesWidgetTransactionPage() throws SQLException{
		SpendingPage.GoToTop5DetailsPage(WidgetOption.Top5ExpenseCategories);
		
		Integer totalExpenseCategoriesWidgetDetailsCountDB = OGENYCDatabaseUtil.getCapitalSpendingDetailsCount(year,'B');
		Integer totalExpenseCategoriesWidgetDetailsCountApp = SpendingPage.GetOGETransactionCount1();
		assertEquals("Capital Spending Exp categories  widget details count  did not match with the DB",totalExpenseCategoriesWidgetDetailsCountApp, totalExpenseCategoriesWidgetDetailsCountDB);
		
		String WidgetDetailsTitle =  "Expense Categories Capital Spending Transactions";
		String WidgetDetailsTitleApp = HomePage.DetailsPagetitle();
	    assertEquals("Capital Spending Exp categories  widget details  title did not match", WidgetDetailsTitle, WidgetDetailsTitleApp); 
	    
	    String WidgetDetailsAmountDB =  OGENYCDatabaseUtil.getCapitalContractsSpendingDetailsAmount(year,'B');
		String WidgetDetailsAmountApp = HomePage.GetTransactionAmount2();
		assertEquals("Capital Spending Exp categories  widget details  page total spening amount did not match", WidgetDetailsAmountApp, WidgetDetailsAmountDB);
	}
	
	@Test
	public void VerifyPrimeVendorsWidgetTransactionPage() throws SQLException{
		SpendingPage.GoToTop5DetailsPage(WidgetOption.Top5PrimeVendors);
		
		Integer totalPrimeVendorsWidgetDetailsCountDB = OGENYCDatabaseUtil.getCapitalSpendingDetailsCount(year,'B');
		Integer totalPrimeVendorsWidgetDetailsCountApp = SpendingPage.GetOGETransactionCount1();
		assertEquals("Capital Spending Prime Vendor  widget details count  did not match with the DB",totalPrimeVendorsWidgetDetailsCountApp, totalPrimeVendorsWidgetDetailsCountDB);
		
		String WidgetDetailsTitle =  "Prime Vendors Capital Spending Transactions";
		String WidgetDetailsTitleApp = HomePage.DetailsPagetitle();
	    assertEquals("Capital Spending Prime Vendor  widget details  title did not match", WidgetDetailsTitle, WidgetDetailsTitleApp); 
	    
	    String WidgetDetailsAmountDB =  OGENYCDatabaseUtil.getCapitalContractsSpendingDetailsAmount(year,'B');
	    String WidgetDetailsAmountApp = HomePage.GetTransactionAmount2();
		assertEquals("Capital Spending Prime Vendor  widget details total spending amount did not match", WidgetDetailsAmountApp, WidgetDetailsAmountDB);
	}
	@Test
	public void VerifyContractsWidgetTransactionPage() throws SQLException{
		SpendingPage.GoToTop5DetailsPage(WidgetOption.Top5Contracts);
		
		Integer totalContractsWidgetDetailsCountDB = OGENYCDatabaseUtil.getCapitalSpendingContractsDetailsCount(year,'B');
		Integer totalContractsWidgetDetailsCountApp = SpendingPage.GetOGETransactionCount1();
		assertEquals("Capital Spending  Contracts  widget details count  did not match with the DB",totalContractsWidgetDetailsCountApp, totalContractsWidgetDetailsCountDB);
		
		String WidgetDetailsTitle =  "Contracts Capital Spending Transactions";
		String WidgetDetailsTitleApp = HomePage.DetailsPagetitle();
	    assertEquals("Capital Spending  Contracts  widget details did not match", WidgetDetailsTitle, WidgetDetailsTitleApp); 
	    
	    String WidgetDetailsAmountDB =  OGENYCDatabaseUtil.getCapitalContractsSpendingContractsDetailsAmount(year, 'B');
			String WidgetDetailsAmountApp = HomePage.GetTransactionAmount2();
		    assertEquals("Capital Spending  Contracts  widget details  page total spending amount did not match", WidgetDetailsAmountApp, WidgetDetailsAmountDB);
	}
	//*/
}
;



