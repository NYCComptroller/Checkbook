package FunctionalContractsMWBE;

import static org.junit.Assert.assertEquals;

import java.sql.SQLException;

import org.junit.Before;
import org.junit.Test;

import pages.contracts.ContractsPage;
import pages.contracts.ContractsPage.WidgetOption;
import pages.contracts.PendingExpenseContractsPage;
import pages.home.HomePage;
import pages.mwbe.MWBEPage;
import utilities.NYCBaseTest;
import utilities.NYCDatabaseUtil;
import helpers.Helper;
import navigation.MWBECategory.MWBECategoryOption;
import utilities.TestStatusReport;
public class MWBEPendingExpenseContractsDetailsTest extends TestStatusReport{

	//public class MWBEPendingExpenseContractsDetailsTest extends NYCBaseTest {
	int year =  Integer.parseInt(NYCBaseTest.prop.getProperty("year"));
	@Before
	public void GoToPage() {
		//if(!MWBEPage.IsAt()){	
				MWBEPage.GoTo("Contracts", MWBECategoryOption.MWBEHome);
				navigation.TopNavigation.Contracts.PendingExpenseContracts.Select();
		//}
	}
	/* ***************** Test Widget Transaction Count ****************** */
	@Test
	public void VerifyTop5MasterAgreementsTransactionPage() throws SQLException {
		ContractsPage.GoToTop5DetailsPage(WidgetOption.Top5MasterAgreements);
		HomePage.ShowWidgetDetails();
		int NumOfPEContractsDetailsCountDB =  NYCDatabaseUtil.getMWBEPEContractsMasterDetailsCount(year,'B');
		int numOfPEContractsDetailsCountapp = PendingExpenseContractsPage.GetTransactionCount();
		assertEquals(" Pending Expense  master contracts widget Details page table count did not match", numOfPEContractsDetailsCountapp, NumOfPEContractsDetailsCountDB); 
		
		String WidgetDetailsTitle =  "M/WBE Master Agreements Pending Expense Contracts Transactions";
		String WidgetDetailsTitleApp = HomePage.DetailsPagetitle();
	    assertEquals("Pending Expense Contracts Master Agreement Widget title did not match", WidgetDetailsTitle, WidgetDetailsTitleApp); 
	    
	}
	@Test
	public void VerifyTop5MasterAgreementModificationsTransactionPage() throws SQLException {
		ContractsPage.GoToTop5DetailsPage(WidgetOption.TopMasterAgreementModifications);
		HomePage.ShowWidgetDetails();
		int NumOfPEContractsDetailsCountDB =  NYCDatabaseUtil.getMWBEPEContractsMasterModificationsDetailsCount(year,'B');
		int numOfPEContractsDetailsCountapp = PendingExpenseContractsPage.GetTransactionCount();
		assertEquals(" Pending Expense master contracts modification widget Details page table count did not match", numOfPEContractsDetailsCountapp, NumOfPEContractsDetailsCountDB);
		
		String WidgetDetailsTitle =  "M/WBE Master Agreement Modifications Pending Expense Contracts Transactions";
		String WidgetDetailsTitleApp = HomePage.DetailsPagetitle();
	    assertEquals("Pending Expense Contracts Master Agreement Modifications Widget title did not match", WidgetDetailsTitle, WidgetDetailsTitleApp); 
	    
	}
	@Test
	public void VerifyTop5ContractsTransactionPage() throws SQLException {
		ContractsPage.GoToTop5DetailsPage(WidgetOption.Top5Contracts);
		HomePage.ShowWidgetDetails();
		int NumOfPEContractsDetailsCountDB =  NYCDatabaseUtil.getMWBEPEContractsDetailsCount(year,'B');
		int numOfPEContractsDetailsCountapp = PendingExpenseContractsPage.GetTransactionCount();
		assertEquals("Pending Expense contracts widget Details page table count did not match", numOfPEContractsDetailsCountapp, NumOfPEContractsDetailsCountDB); 
		
		String WidgetDetailsTitle =  "M/WBE Contracts Pending Expense Contracts Transactions";
		String WidgetDetailsTitleApp = HomePage.DetailsPagetitle();
	    assertEquals("Pending Expense Contracts contracts Widget title did not match", WidgetDetailsTitle, WidgetDetailsTitleApp); 
	    
	}
	@Test
	public void VerifyTop5ContractAmountModificationsTransactionPage() throws SQLException {
		ContractsPage.GoToTop5DetailsPage(WidgetOption.Top5ContractAmountModifications);
		HomePage.ShowWidgetDetails();
		int NumOfPEContractsDetailsCountDB =  NYCDatabaseUtil.getMWBEPEContractsModificationsDetailsCount(year,'B');
		int numOfPEContractsDetailsCountapp = PendingExpenseContractsPage.GetTransactionCount();
		assertEquals(" Pending Expense contracts widget Details page table count did not match", numOfPEContractsDetailsCountapp, NumOfPEContractsDetailsCountDB); 
		

		String WidgetDetailsTitle =  "M/WBE Contract Amount Modifications Pending Expense Contracts Transactions";
		String WidgetDetailsTitleApp = HomePage.DetailsPagetitle();
	    assertEquals("Pending Expense Contracts Modifications Widget title did not match", WidgetDetailsTitle, WidgetDetailsTitleApp); 
	    
	}
	@Test
	public void VerifyTop5PrimeVendorsTransactionPage() throws SQLException {
		ContractsPage.GoToTop5DetailsPage(WidgetOption.Top5PrimeVendors);
		HomePage.ShowWidgetDetails();
		int NumOfPEContractsDetailsCountDB =  NYCDatabaseUtil.getMWBEPEAllContractsDetailsCount(year,'B');
		int numOfPEContractsDetailsCountapp = PendingExpenseContractsPage.GetTransactionCount();
		assertEquals(" Pending Expense contracts Prime Vendor widget Details page table count did not match", numOfPEContractsDetailsCountapp, NumOfPEContractsDetailsCountDB); 
		
		String WidgetDetailsTitle =  "M/WBE Prime Vendors Pending Expense Contracts Transactions";
		String WidgetDetailsTitleApp = HomePage.DetailsPagetitle();
	    assertEquals("Pending Expense Contracts Prime Vendors Widget title did not match", WidgetDetailsTitle, WidgetDetailsTitleApp); 
	    
	}
	@Test
	public void VerifyTop5AwardMethodsTransactionPage() throws SQLException {
		ContractsPage.GoToTop5DetailsPage(WidgetOption.Top5AwardMethods);
		HomePage.ShowWidgetDetails();
		int NumOfPEContractsDetailsCountDB =  NYCDatabaseUtil.getMWBEPEAllContractsDetailsCount(year,'B');
		int numOfPEContractsDetailsCountapp = PendingExpenseContractsPage.GetTransactionCount();
		assertEquals(" PendingExpense  contracts Award Method widget Details page table count did not match", numOfPEContractsDetailsCountapp, NumOfPEContractsDetailsCountDB); 
		

		String WidgetDetailsTitle =  "M/WBE Award Methods Pending Expense Contracts Transactions";
		String WidgetDetailsTitleApp = HomePage.DetailsPagetitle();
	    assertEquals("Pending Expense Contracts AWard Method Widget title did not match", WidgetDetailsTitle, WidgetDetailsTitleApp); 
	    
	}
	@Test
	public void VerifyTop5AgenciesTransactionPage() throws SQLException {
		ContractsPage.GoToTop5DetailsPage(WidgetOption.Top5Agencies);
		HomePage.ShowWidgetDetails();
		int NumOfPEContractsDetailsCountDB =  NYCDatabaseUtil.getMWBEPEAllContractsDetailsCount(year,'B');
		int numOfPEContractsDetailsCountapp = PendingExpenseContractsPage.GetTransactionCount();
		assertEquals(" Pending Expense contracts Agencies widget Details page table count did not match", numOfPEContractsDetailsCountapp, NumOfPEContractsDetailsCountDB); 
		
		String WidgetDetailsTitle =  "M/WBE Agencies Pending Expense Contracts Transactions";
		String WidgetDetailsTitleApp = HomePage.DetailsPagetitle();
	    assertEquals("Pending Expense Contracts Agencies Widget title did not match", WidgetDetailsTitle, WidgetDetailsTitleApp); 
	    
	}
	@Test
	public void VerifyContractsByIndustriesTransactionPage() throws SQLException {
		ContractsPage.GoToTop5DetailsPage(WidgetOption.ContractsByIndustries);
		HomePage.ShowWidgetDetails();
		int NumOfPEContractsDetailsCountDB =  NYCDatabaseUtil.getMWBEPEAllContractsDetailsCount(year,'B');
		int numOfPEContractsDetailsCountapp = PendingExpenseContractsPage.GetTransactionCount();
		assertEquals("Pending Expense contracts Industries widget Details page table count did not match", numOfPEContractsDetailsCountapp, NumOfPEContractsDetailsCountDB);
		
		String WidgetDetailsTitle =  "M/WBE Contracts by Industries Pending Expense Contracts Transactions";
		String WidgetDetailsTitleApp = HomePage.DetailsPagetitle();
	    assertEquals("Pending Expense Contracts Industries Widget title did not match", WidgetDetailsTitle, WidgetDetailsTitleApp); 
	    
	}
	@Test
	public void VerifyContractsBySizeTransactionPage() throws SQLException {
		ContractsPage.GoToTop5DetailsPage(WidgetOption.ContractsBySize);
		HomePage.ShowWidgetDetails();
		int NumOfPEContractsDetailsCountDB =  NYCDatabaseUtil.getMWBEPEAllContractsDetailsCount(year,'B');
		int numOfPEContractsDetailsCountapp = PendingExpenseContractsPage.GetTransactionCount();
		assertEquals(" Pending Expense contracts by size widget Details page table count did not match", numOfPEContractsDetailsCountapp, NumOfPEContractsDetailsCountDB); 
		
		String WidgetDetailsTitle =  "M/WBE Contracts by Size Pending Expense Contracts Transactions";
		String WidgetDetailsTitleApp = HomePage.DetailsPagetitle();
	    assertEquals("Pending Expense Contracts Contracts by Sizes Widget title did not match", WidgetDetailsTitle, WidgetDetailsTitleApp); 
	    
	}
}
