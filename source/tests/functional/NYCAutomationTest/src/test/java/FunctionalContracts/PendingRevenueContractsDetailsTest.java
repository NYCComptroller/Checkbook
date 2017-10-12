package FunctionalContracts;

import static org.junit.Assert.assertEquals;

import helpers.Helper;

import java.sql.SQLException;

import org.junit.Before;
import org.junit.Test;

import pages.contracts.ContractsPage;
import pages.contracts.PendingExpenseContractsPage;
import pages.contracts.ContractsPage.WidgetOption;
import pages.contracts.PendingRevenueContractsPage;
import pages.home.HomePage;
import utilities.NYCBaseTest;
import utilities.NYCDatabaseUtil;
import utilities.TestStatusReport;
public class PendingRevenueContractsDetailsTest extends TestStatusReport{

//public class PendingRevenueContractsDetailsTest extends NYCBaseTest {
	int year =  Integer.parseInt(NYCBaseTest.prop.getProperty("year"));
	@Before
	public void GoToPage() {
		if(!(Helper.getCurrentSelectedYear()).equalsIgnoreCase(NYCBaseTest.prop.getProperty("CurrentYear")))
			HomePage.SelectYear(NYCBaseTest.prop.getProperty("CurrentYear"));
		PendingRevenueContractsPage.GoTo();
		HomePage.ShowWidgetDetails();
	}

	/* ***************** Test Widget Transaction Count ****************** */
	@Test
	public void VerifyTop5ContractsTransactionCount() throws SQLException {
		ContractsPage.GoToTop5DetailsPage(WidgetOption.Top5Contracts);
		HomePage.ShowWidgetDetails();
		int NumOfPRContractsDetailsCount2016 = NYCDatabaseUtil.getPRContractsDetailsCount(2016,'B');
		int numOfPRContractsDetailsCountapp = PendingExpenseContractsPage.GetTransactionCount();
		assertEquals(" Pending Revenue contracts widget Details page table count did not match", numOfPRContractsDetailsCountapp, NumOfPRContractsDetailsCount2016);
		
		String WidgetDetailsTitle =  "Pending Revenue Contracts Transactions";
		String WidgetDetailsTitleApp = HomePage.DetailsPagetitle();
	    assertEquals("Pending Revenue Contracts contracts Widget title did not match", WidgetDetailsTitle, WidgetDetailsTitleApp); 
	    
	}
	@Test
	public void VerifyTop5ContractAmountModificationsTransactionCount() throws SQLException{
		ContractsPage.GoToTop5DetailsPage(WidgetOption.Top5ContractAmountModifications);
		HomePage.ShowWidgetDetails();
		int NumOfPRContractsDetailsCount2016 = NYCDatabaseUtil.getPRContractsModificationsDetailsCount(2016,'B');
		int numOfPRContractsDetailsCountapp = PendingExpenseContractsPage.GetTransactionCount();
		assertEquals(" Pending Revenue contracts Modifications widget Details page table count did not match", numOfPRContractsDetailsCountapp, NumOfPRContractsDetailsCount2016);
		

		String WidgetDetailsTitle =  "Pending Revenue Contracts Modifications Transactions";
		String WidgetDetailsTitleApp = HomePage.DetailsPagetitle();
	    assertEquals("Pending Revenue Contracts Modifications Widget title did not match", WidgetDetailsTitle, WidgetDetailsTitleApp); 
	    
	}
	@Test
	public void VerifyTop5PrimeVendorsTransactionCount() throws SQLException {
		ContractsPage.GoToTop5DetailsPage(WidgetOption.Top5PrimeVendors);
		HomePage.ShowWidgetDetails();
		int NumOfPRContractsDetailsCount2016 = NYCDatabaseUtil.getPRContractsModificationsDetailsCount(2016,'B');
		int numOfPRContractsDetailsCountapp = PendingExpenseContractsPage.GetTransactionCount();
		assertEquals(" Pending Revenue Prime Vendors widget Details page table count did not match", numOfPRContractsDetailsCountapp, NumOfPRContractsDetailsCount2016); 
		
		String WidgetDetailsTitle =  "Prime Vendors Pending Revenue Contracts Transactions";
		String WidgetDetailsTitleApp = HomePage.DetailsPagetitle();
	    assertEquals("Pending Revenue Contracts Prime Vendors Widget title did not match", WidgetDetailsTitle, WidgetDetailsTitleApp); 
	}
	@Test
	public void VerifyTop5AwardMethodsTransactionCount() throws SQLException{
		ContractsPage.GoToTop5DetailsPage(WidgetOption.TopAwardMethods);
		HomePage.ShowWidgetDetails();
		int NumOfPRContractsDetailsCount2016 = NYCDatabaseUtil.getPRContractsModificationsDetailsCount(2016,'B');
		int numOfPRContractsDetailsCountapp = PendingExpenseContractsPage.GetTransactionCount();
		assertEquals(" Pending Revenue contracts Award Method widget Details page table count did not match", numOfPRContractsDetailsCountapp, NumOfPRContractsDetailsCount2016); 
		
		String WidgetDetailsTitle =  "AWard Methods Pending Revenue Contracts Transactions";
		String WidgetDetailsTitleApp = HomePage.DetailsPagetitle();
	    assertEquals("Pending Revenue Contracts AWard Method Widget title did not match", WidgetDetailsTitle, WidgetDetailsTitleApp); 
	}
	@Test
	public void VerifyTop5AgenciesTransactionCount() throws SQLException{
		ContractsPage.GoToTop5DetailsPage(WidgetOption.TopAgencies);
		HomePage.ShowWidgetDetails();
		int NumOfPRContractsDetailsCount2016 = NYCDatabaseUtil.getPRContractsModificationsDetailsCount(2016,'B');
		int numOfPRContractsDetailsCountapp = PendingExpenseContractsPage.GetTransactionCount();
		assertEquals(" Pending Revenue contracts Agencies widget Details page table count did not match", numOfPRContractsDetailsCountapp, NumOfPRContractsDetailsCount2016); 
		
		String WidgetDetailsTitle =  "Agencies Pending Revenue Contracts Transactions";
		String WidgetDetailsTitleApp = HomePage.DetailsPagetitle();
	    assertEquals("Pending Revenue Contracts Agencies Widget title did not match", WidgetDetailsTitle, WidgetDetailsTitleApp); 
	    
	}
	@Test
	public void VerifyContractsByIndustriesTransactionCount() throws SQLException{
		ContractsPage.GoToTop5DetailsPage(WidgetOption.ContractsByIndustries);
		HomePage.ShowWidgetDetails();
		
		int NumOfPRContractsDetailsCount2016 = NYCDatabaseUtil.getPRContractsModificationsDetailsCount(2016,'B');
		int numOfPRContractsDetailsCountapp = PendingExpenseContractsPage.GetTransactionCount();
		assertEquals(" Pending Revenue contracts by Industries widget Details page table count did not match", numOfPRContractsDetailsCountapp, NumOfPRContractsDetailsCount2016);
		
		String WidgetDetailsTitle =  "Contracts by Industries Pending Revenue Contracts Transactions";
		String WidgetDetailsTitleApp = HomePage.DetailsPagetitle();
	    assertEquals("Pending Revenue Contracts Industries Widget title did not match", WidgetDetailsTitle, WidgetDetailsTitleApp); 
	    
		
	}
	@Test
	public void VerifyContractsBySizeTransactionCount() throws SQLException{
		ContractsPage.GoToTop5DetailsPage(WidgetOption.ContractsBySize);
		HomePage.ShowWidgetDetails();
		int NumOfPRContractsDetailsCount2016 = NYCDatabaseUtil.getPRContractsModificationsDetailsCount(2016,'B');
		int numOfPRContractsDetailsCountapp = PendingExpenseContractsPage.GetTransactionCount();
		assertEquals(" Pending Revenue contracts by size widget Details page table count did not match", numOfPRContractsDetailsCountapp, NumOfPRContractsDetailsCount2016);
		
		String WidgetDetailsTitle =  "Contracts by Size Pending Revenue Contracts Transactions";
		String WidgetDetailsTitleApp = HomePage.DetailsPagetitle();
	    assertEquals("Pending Revenue Contracts Contracts by Sizes Widget title did not match", WidgetDetailsTitle, WidgetDetailsTitleApp); 
	}
}
