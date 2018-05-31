package FunctionalContractsOGE;

import static org.junit.Assert.assertEquals;

import java.sql.SQLException;

import org.junit.Before;
import org.junit.Test;

import pages.contracts.ContractsPage;
import pages.contracts.ContractsPage.WidgetOption;
import pages.contracts.PendingExpenseContractsPage;
import pages.home.HomePage;
import utilities.NYCBaseTest;
import utilities.NYCDatabaseUtil;
import helpers.Helper;
import utilities.TestStatusReport;
public class PendingExpenseContractsDetailsTest extends TestStatusReport{

//public class PendingExpenseContractsDetailsTest extends NYCBaseTest {
	int year =  Integer.parseInt(NYCBaseTest.prop.getProperty("year"));
	@Before
	public void GoToPage(){
		if(!(Helper.getCurrentSelectedYear()).equalsIgnoreCase(NYCBaseTest.prop.getProperty("CurrentYear")))
			HomePage.SelectYear(NYCBaseTest.prop.getProperty("CurrentYear"));
		PendingExpenseContractsPage.GoTo();
		HomePage.ShowWidgetDetails();
	}

	/* ***************** Test Widget Transaction Count ****************** */
	@Test
	public void VerifyTop5MasterAgreementsTransactionPage() throws SQLException {
		ContractsPage.GoToTop5DetailsPage(WidgetOption.Top5MasterAgreements);
		HomePage.ShowWidgetDetails();
		int NumOfPEContractsDetailsCountDB =  NYCDatabaseUtil.getPEContractsMasterDetailsCount(year,'B');
		int numOfPEContractsDetailsCountapp = PendingExpenseContractsPage.GetTransactionCount();
		assertEquals(" Pending Expense  master contracts widget Details page table count did not match", numOfPEContractsDetailsCountapp, NumOfPEContractsDetailsCountDB); 
		
		String WidgetDetailsTitle =  "Master Agreements Pending Expense Contracts Transactions";
		String WidgetDetailsTitleApp = HomePage.DetailsPagetitle();
	    assertEquals("Pending Expense Contracts Master Agreement Widget title did not match", WidgetDetailsTitle, WidgetDetailsTitleApp); 
	    
	}
	@Test
	public void VerifyTop5MasterAgreementModificationsTransactionPage() throws SQLException {
		ContractsPage.GoToTop5DetailsPage(WidgetOption.Top5MasterAgreementModifications);
		HomePage.ShowWidgetDetails();
		int NumOfPEContractsDetailsCountDB =  NYCDatabaseUtil.getPEContractsMasterModificationsDetailsCount(year,'B');
		int numOfPEContractsDetailsCountapp = PendingExpenseContractsPage.GetTransactionCount();
		assertEquals(" Pending Expense master contracts modification widget Details page table count did not match", numOfPEContractsDetailsCountapp, NumOfPEContractsDetailsCountDB);
		
		String WidgetDetailsTitle =  "Master Agreement Modifications Pending Expense Contracts Transactions";
		String WidgetDetailsTitleApp = HomePage.DetailsPagetitle();
	    assertEquals("Pending Expense Contracts Master Agreement Modifications Widget title did not match", WidgetDetailsTitle, WidgetDetailsTitleApp); 
	    
	}
	@Test
	public void VerifyTop5ContractsTransactionPage() throws SQLException {
		ContractsPage.GoToTop5DetailsPage(WidgetOption.Top5Contracts);
		HomePage.ShowWidgetDetails();
		int NumOfPEContractsDetailsCountDB =  NYCDatabaseUtil.getPEContractsDetailsCount(year,'B');
		int numOfPEContractsDetailsCountapp = PendingExpenseContractsPage.GetTransactionCount();
		assertEquals("Pending Expense contracts widget Details page table count did not match", numOfPEContractsDetailsCountapp, NumOfPEContractsDetailsCountDB); 
		
		String WidgetDetailsTitle =  "Contracts Pending Expense Contracts Transactions";
		String WidgetDetailsTitleApp = HomePage.DetailsPagetitle();
	    assertEquals("Pending Expense Contracts contracts Widget title did not match", WidgetDetailsTitle, WidgetDetailsTitleApp); 
	    
	}
	@Test
	public void VerifyTop5ContractAmountModificationsTransactionPage() throws SQLException {
		ContractsPage.GoToTop5DetailsPage(WidgetOption.Top5ContractAmountModifications);
		HomePage.ShowWidgetDetails();
		int NumOfPEContractsDetailsCountDB =  NYCDatabaseUtil.getPEContractsModificationsDetailsCount(year,'B');
		int numOfPEContractsDetailsCountapp = PendingExpenseContractsPage.GetTransactionCount();
		assertEquals(" Pending Expense contracts widget Details page table count did not match", numOfPEContractsDetailsCountapp, NumOfPEContractsDetailsCountDB); 
		

		String WidgetDetailsTitle =  "Contract Amount Modifications Pending Expense Contracts Transactions";
		String WidgetDetailsTitleApp = HomePage.DetailsPagetitle();
	    assertEquals("Pending Expense Contracts Modifications Widget title did not match", WidgetDetailsTitle, WidgetDetailsTitleApp); 
	    
	}
	@Test
	public void VerifyTop5PrimeVendorsTransactionPage() throws SQLException {
		ContractsPage.GoToTop5DetailsPage(WidgetOption.Top5PrimeVendors);
		HomePage.ShowWidgetDetails();
		int NumOfPEContractsDetailsCountDB =  NYCDatabaseUtil.getPEAllContractsDetailsCount(year,'B');
		int numOfPEContractsDetailsCountapp = PendingExpenseContractsPage.GetTransactionCount();
		assertEquals(" Pending Expense contracts Prime Vendor widget Details page table count did not match", numOfPEContractsDetailsCountapp, NumOfPEContractsDetailsCountDB); 
		
		String WidgetDetailsTitle =  "Prime Vendors Pending Expense Contracts Transactions";
		String WidgetDetailsTitleApp = HomePage.DetailsPagetitle();
	    assertEquals("Pending Expense Contracts Prime Vendors Widget title did not match", WidgetDetailsTitle, WidgetDetailsTitleApp); 
	    
	}
	@Test
	public void VerifyTop5AwardMethodsTransactionPage() throws SQLException {
		ContractsPage.GoToTop5DetailsPage(WidgetOption.Top5AwardMethods);
		HomePage.ShowWidgetDetails();
		int NumOfPEContractsDetailsCountDB =  NYCDatabaseUtil.getPEAllContractsDetailsCount(year,'B');
		int numOfPEContractsDetailsCountapp = PendingExpenseContractsPage.GetTransactionCount();
		assertEquals(" PendingExpense  contracts Award Method widget Details page table count did not match", numOfPEContractsDetailsCountapp, NumOfPEContractsDetailsCountDB); 
		

		String WidgetDetailsTitle =  "Award Methods Pending Expense Contracts Transactions";
		String WidgetDetailsTitleApp = HomePage.DetailsPagetitle();
	    assertEquals("Pending Expense Contracts AWard Method Widget title did not match", WidgetDetailsTitle, WidgetDetailsTitleApp); 
	    
	}
	@Test
	public void VerifyTop5AgenciesTransactionPage() throws SQLException {
		ContractsPage.GoToTop5DetailsPage(WidgetOption.Top5Agencies);
		HomePage.ShowWidgetDetails();
		int NumOfPEContractsDetailsCountDB =  NYCDatabaseUtil.getPEAllContractsDetailsCount(year,'B');
		int numOfPEContractsDetailsCountapp = PendingExpenseContractsPage.GetTransactionCount();
		assertEquals(" Pending Expense contracts Agencies widget Details page table count did not match", numOfPEContractsDetailsCountapp, NumOfPEContractsDetailsCountDB); 
		
		String WidgetDetailsTitle =  "Agencies Pending Expense Contracts Transactions";
		String WidgetDetailsTitleApp = HomePage.DetailsPagetitle();
	    assertEquals("Pending Expense Contracts Agencies Widget title did not match", WidgetDetailsTitle, WidgetDetailsTitleApp); 
	    
	}
	@Test
	public void VerifyContractsByIndustriesTransactionPage() throws SQLException {
		ContractsPage.GoToTop5DetailsPage(WidgetOption.ContractsByIndustries);
		HomePage.ShowWidgetDetails();
		int NumOfPEContractsDetailsCountDB =  NYCDatabaseUtil.getPEAllContractsDetailsCount(year,'B');
		int numOfPEContractsDetailsCountapp = PendingExpenseContractsPage.GetTransactionCount();
		assertEquals("Pending Expense contracts Industries widget Details page table count did not match", numOfPEContractsDetailsCountapp, NumOfPEContractsDetailsCountDB);
		
		String WidgetDetailsTitle =  "Contracts by Industries Pending Expense Contracts Transactions";
		String WidgetDetailsTitleApp = HomePage.DetailsPagetitle();
	    assertEquals("Pending Expense Contracts Industries Widget title did not match", WidgetDetailsTitle, WidgetDetailsTitleApp); 
	    
	}
	@Test
	public void VerifyContractsBySizeTransactionPage() throws SQLException {
		ContractsPage.GoToTop5DetailsPage(WidgetOption.ContractsBySize);
		HomePage.ShowWidgetDetails();
		int NumOfPEContractsDetailsCountDB =  NYCDatabaseUtil.getPEAllContractsDetailsCount(year,'B');
		int numOfPEContractsDetailsCountapp = PendingExpenseContractsPage.GetTransactionCount();
		assertEquals(" Pending Expense contracts by size widget Details page table count did not match", numOfPEContractsDetailsCountapp, NumOfPEContractsDetailsCountDB); 
		
		String WidgetDetailsTitle =  "Contracts by Size Pending Expense Contracts Transactions";
		String WidgetDetailsTitleApp = HomePage.DetailsPagetitle();
	    assertEquals("Pending Expense Contracts Contracts by Sizes Widget title did not match", WidgetDetailsTitle, WidgetDetailsTitleApp); 
	    
	}
}
