package FunctionalContractsMWBE;

import static org.junit.Assert.assertEquals;

import helpers.Helper;
import navigation.MWBECategory.MWBECategoryOption;

import java.sql.SQLException;

import org.junit.Before;
import org.junit.Test;

import pages.contracts.ContractsPage;
import pages.contracts.PendingExpenseContractsPage;
import pages.contracts.ContractsPage.WidgetOption;
import pages.contracts.PendingRevenueContractsPage;
import pages.home.HomePage;
import pages.mwbe.MWBEPage;
import utilities.NYCBaseTest;
import utilities.NYCDatabaseUtil;
import utilities.TestStatusReport;
public class MWBEPendingRevenueContractsDetailsTest extends TestStatusReport{

	//public class MWBEPendingRevenueContractsDetailsTest extends NYCBaseTest {
	int year =  Integer.parseInt(NYCBaseTest.prop.getProperty("year"));
	@Before
	public void GoToPage() {
		MWBEPage.GoTo("Contracts", MWBECategoryOption.MWBEHome);
		navigation.TopNavigation.Contracts.PendingRevenueContracts.Select();
		HomePage.ShowWidgetDetails();
	}

	/* ***************** Test Widget Transaction Count ****************** */
	@Test
	public void VerifyTop5ContractsTransactionPage() throws SQLException {
		ContractsPage.GoToTop5DetailsPage(WidgetOption.Top5Contracts);
		HomePage.ShowWidgetDetails();
		int NumOfPRContractsDetailsCountDB = NYCDatabaseUtil.getMWBEPRContractsDetailsCount(year,'B');
		int numOfPRContractsDetailsCountapp = PendingExpenseContractsPage.GetTransactionCount();
		assertEquals(" Pending Revenue contracts widget Details page table count did not match", numOfPRContractsDetailsCountapp, NumOfPRContractsDetailsCountDB);
		
		String WidgetDetailsTitle =  "M/WBE Contracts Pending Revenue Contracts Transactions";
		String WidgetDetailsTitleApp = HomePage.DetailsPagetitle();
	    assertEquals("Pending Revenue Contracts contracts Widget title did not match", WidgetDetailsTitle, WidgetDetailsTitleApp); 
	    
	}
	@Test
	public void VerifyTop5ContractAmountModificationsTransactionPage() throws SQLException{
		ContractsPage.GoToTop5DetailsPage(WidgetOption.TopContractAmountModifications);
		HomePage.ShowWidgetDetails();
		int NumOfPRContractsDetailsCountDB = NYCDatabaseUtil.getMWBEPRContractsModificationsDetailsCount(year,'B');
		int numOfPRContractsDetailsCountapp = PendingExpenseContractsPage.GetTransactionCount();
		assertEquals(" Pending Revenue contracts Modifications widget Details page table count did not match", numOfPRContractsDetailsCountapp, NumOfPRContractsDetailsCountDB);
		

		String WidgetDetailsTitle =  "M/WBE Contract Amount Modifications Pending Revenue Contracts Transactions";
		String WidgetDetailsTitleApp = HomePage.DetailsPagetitle();
	    assertEquals("Pending Revenue Contracts Modifications Widget title did not match", WidgetDetailsTitle, WidgetDetailsTitleApp); 
	    
	}
	@Test
	public void VerifyTop5PrimeVendorsTransactionPage() throws SQLException {
		ContractsPage.GoToTop5DetailsPage(WidgetOption.Top5PrimeVendors);
		HomePage.ShowWidgetDetails();
		int NumOfPRContractsDetailsCountDB = NYCDatabaseUtil.getMWBEPRContractsDetailsCount(year,'B');
		int numOfPRContractsDetailsCountapp = PendingExpenseContractsPage.GetTransactionCount();
		assertEquals(" Pending Revenue Prime Vendors widget Details page table count did not match", numOfPRContractsDetailsCountapp, NumOfPRContractsDetailsCountDB); 
		
		String WidgetDetailsTitle =  "M/WBE Prime Vendors Pending Revenue Contracts Transactions";
		String WidgetDetailsTitleApp = HomePage.DetailsPagetitle();
	    assertEquals("Pending Revenue Contracts Prime Vendors Widget title did not match", WidgetDetailsTitle, WidgetDetailsTitleApp); 
	}
	@Test
	public void VerifyTop5AwardMethodsTransactionPage() throws SQLException{
		ContractsPage.GoToTop5DetailsPage(WidgetOption.Top5AwardMethods);
		HomePage.ShowWidgetDetails();
		int NumOfPRContractsDetailsCountDB = NYCDatabaseUtil.getMWBEPRContractsDetailsCount(year,'B');
		int numOfPRContractsDetailsCountapp = PendingExpenseContractsPage.GetTransactionCount();
		assertEquals(" Pending Revenue contracts Award Method widget Details page table count did not match", numOfPRContractsDetailsCountapp, NumOfPRContractsDetailsCountDB); 
		
		String WidgetDetailsTitle =  "M/WBE Award Methods Pending Revenue Contracts Transactions";
		String WidgetDetailsTitleApp = HomePage.DetailsPagetitle();
	    assertEquals("Pending Revenue Contracts AWard Method Widget title did not match", WidgetDetailsTitle, WidgetDetailsTitleApp); 
	}
	@Test
	public void VerifyTop5AgenciesTransactionPage() throws SQLException{
		ContractsPage.GoToTop5DetailsPage(WidgetOption.Top5Agencies);
		HomePage.ShowWidgetDetails();
		int NumOfPRContractsDetailsCountDB = NYCDatabaseUtil.getMWBEPRContractsDetailsCount(year,'B');
		int numOfPRContractsDetailsCountapp = PendingExpenseContractsPage.GetTransactionCount();
		assertEquals(" Pending Revenue contracts Agencies widget Details page table count did not match", numOfPRContractsDetailsCountapp, NumOfPRContractsDetailsCountDB); 
		
		String WidgetDetailsTitle =  "M/WBE Agencies Pending Revenue Contracts Transactions";
		String WidgetDetailsTitleApp = HomePage.DetailsPagetitle();
	    assertEquals("Pending Revenue Contracts Agencies Widget title did not match", WidgetDetailsTitle, WidgetDetailsTitleApp); 
	    
	}
	@Test
	public void VerifyContractsByIndustriesTransactionPage() throws SQLException{
		ContractsPage.GoToTop5DetailsPage(WidgetOption.ContractsByIndustries);
		HomePage.ShowWidgetDetails();
		
		int NumOfPRContractsDetailsCountDB = NYCDatabaseUtil.getMWBEPRContractsDetailsCount(year,'B');
		int numOfPRContractsDetailsCountapp = PendingExpenseContractsPage.GetTransactionCount();
		assertEquals(" Pending Revenue contracts by Industries widget Details page table count did not match", numOfPRContractsDetailsCountapp, NumOfPRContractsDetailsCountDB);
		
		String WidgetDetailsTitle =  "M/WBE Contracts by Industries Pending Revenue Contracts Transactions";
		String WidgetDetailsTitleApp = HomePage.DetailsPagetitle();
	    assertEquals("Pending Revenue Contracts Industries Widget title did not match", WidgetDetailsTitle, WidgetDetailsTitleApp); 
	    
		
	}
	@Test
	public void VerifyContractsBySizeTransactionPage() throws SQLException{
		ContractsPage.GoToTop5DetailsPage(WidgetOption.ContractsBySize);
		HomePage.ShowWidgetDetails();
		int NumOfPRContractsDetailsCountDB = NYCDatabaseUtil.getMWBEPRContractsDetailsCount(year,'B');
		int numOfPRContractsDetailsCountapp = PendingExpenseContractsPage.GetTransactionCount();
		assertEquals(" Pending Revenue contracts by size widget Details page table count did not match", numOfPRContractsDetailsCountapp, NumOfPRContractsDetailsCountDB);
		
		String WidgetDetailsTitle =  "M/WBE Contracts by Size Pending Revenue Contracts Transactions";
		String WidgetDetailsTitleApp = HomePage.DetailsPagetitle();
	    assertEquals("Pending Revenue Contracts Contracts by Sizes Widget title did not match", WidgetDetailsTitle, WidgetDetailsTitleApp); 
	}
}
