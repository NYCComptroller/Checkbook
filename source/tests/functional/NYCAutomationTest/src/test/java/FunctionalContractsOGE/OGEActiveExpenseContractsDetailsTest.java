package FunctionalContractsOGE;

import static org.junit.Assert.assertEquals;

import static org.junit.Assert.assertTrue;

import java.sql.SQLException;

import org.junit.After;
import org.junit.Before;
import org.junit.Test;

import pages.contracts.ActiveExpenseContractsPage;
import pages.contracts.ContractsPage;
import pages.contracts.ContractsPage.WidgetOption;
import pages.home.HomePage;

import utilities.OGENYCBaseTest;
import utilities.OGENYCDatabaseUtil;

import helpers.Helper;
import navigation.SecondaryMenuNavigation.OtherGovernmentEntities;
import navigation.TopNavigation.Contracts.ActiveExpenseContracts;
import utilities.TestStatusReport;
public class OGEActiveExpenseContractsDetailsTest extends TestStatusReport{
	//public class OGEActiveExpenseContractsDetailsTest extends OGENYCBaseTest {
	int year =  Integer.parseInt(OGENYCBaseTest.prop.getProperty("year"));
	@Before
	public void GoToPage() {
			if(!OtherGovernmentEntities.IsAt())
			OtherGovernmentEntities.GoTo();
	
		ContractsPage.GoTo();	
		if (!ActiveExpenseContracts.isAt()) {
		ActiveExpenseContractsPage.GoTo();	}	
		if(!(Helper.getCurrentSelectedYear()).equalsIgnoreCase(OGENYCBaseTest.prop.getProperty("CurrentYear")))
			HomePage.SelectYear(OGENYCBaseTest.prop.getProperty("CurrentYear"));
		HomePage.ShowWidgetDetails();
	}
	
		
	/* ***************** Test Widget Transaction Count ****************** */
	@Test
	public void VerifyTop5MasterAgreementsTransactionCount() throws SQLException {
		ContractsPage.GoToTop5DetailsPage(WidgetOption.Top5MasterAgreements);
		HomePage.ShowWidgetDetails();
		int NumOfAEContractsDetailsCountyear =  OGENYCDatabaseUtil.getAEMasterContractsCount(year,'B');
        int numOfAEContractsDetailsCountapp = ActiveExpenseContractsPage.GetOGETransactionCount();
		assertEquals("Active Expense Contracts master contracts widget Details page table count did not match", numOfAEContractsDetailsCountapp, NumOfAEContractsDetailsCountyear); 
		
		String WidgetDetailsTitle =  "Master Agreements Active Expense Contracts Transactions";
		String WidgetDetailsTitleApp = HomePage.DetailsPagetitle();
	  assertEquals("Active Expense Contracts Master Agreement Widget title did not match", WidgetDetailsTitle, WidgetDetailsTitleApp);
	    
	    String WidgetDetailsAmountDB =  OGENYCDatabaseUtil.getAEContractsMasterContractsDetailsAmount(year,'B');
		String WidgetDetailsAmountapp = ActiveExpenseContractsPage.GetTransactionAmount1();
		assertEquals("Active Expense Contracts Master Agreement  Widget Details page total Contract amount did not match", WidgetDetailsAmountapp, WidgetDetailsAmountDB);
	}
	       
	
	
	@Test
	public void VerifyTop5ContractsTransactionCount() throws SQLException {
		ContractsPage.GoToTop5DetailsPage(WidgetOption.Top5Contracts);
		
		int NumOfAEContractsDetailsCountyear =  OGENYCDatabaseUtil.getAEContractsDetailsCount(year,'B');
		int numOfAEContractsDetailsCountapp = ActiveExpenseContractsPage.GetOGETransactionCount();
		assertEquals("Active Expense Contracts contracts widget Details page table count did not match", numOfAEContractsDetailsCountapp, NumOfAEContractsDetailsCountyear); 
		
		String WidgetDetailsTitle =  "Contracts Active Expense Contracts Transactions";
		String WidgetDetailsTitleApp = HomePage.DetailsPagetitle();
	    assertEquals("Active Expense Contracts contracts Widget title did not match", WidgetDetailsTitle, WidgetDetailsTitleApp); 
	    
	    String WidgetDetailsAmountDB =  OGENYCDatabaseUtil.getAEContractsDetailsAmount(year,'B');
		String WidgetDetailsAmountapp = ActiveExpenseContractsPage.GetTransactionAmount1();
		assertEquals("Active Expense Contracts contracts  Widget Details page total Contract amount did not match", WidgetDetailsAmountapp, WidgetDetailsAmountDB);
	}
	
	@Test
	public void VerifyTop5PrimeVendorsTransactionCount() throws SQLException {
		ContractsPage.GoToTop5DetailsPage(WidgetOption.Top5PrimeVendors);
	
		int NumOfAEContractsDetailsCountyear =  OGENYCDatabaseUtil.getAEContractsDetailsCount(year,'B');
		int numOfAEContractsDetailsCountapp = ActiveExpenseContractsPage.GetOGETransactionCount();
		assertEquals("Active Expense Contracts master Contracts Prime Vendors widget Details page table count did not match", numOfAEContractsDetailsCountapp, NumOfAEContractsDetailsCountyear);
		
		String WidgetDetailsTitle =  "Prime Vendors Active Expense Contracts Transactions";
		String WidgetDetailsTitleApp = HomePage.DetailsPagetitle();
	    assertEquals("Active Expense Contracts Prime Vendors Widget title did not match", WidgetDetailsTitle, WidgetDetailsTitleApp); 
	    

	    String WidgetDetailsAmountDB =  OGENYCDatabaseUtil.getAEContractsDetailsAmount(year,'B');
		String WidgetDetailsAmountapp = ActiveExpenseContractsPage.GetTransactionAmount1();
		assertEquals("Active Expense Contracts PrimeVendors  Widget Details page total Contract amount did not match", WidgetDetailsAmountapp, WidgetDetailsAmountDB);
	}
	@Test
	public void VerifyTop5AwardMethodsTransactionCount() throws SQLException {
		ContractsPage.GoToTop5DetailsPage(WidgetOption.Top5AwardMethods);
		HomePage.ShowWidgetDetails();
		int NumOfAEContractsDetailsCountyear =  OGENYCDatabaseUtil.getAEContractsDetailsCount(year,'B');
		int numOfAEContractsDetailsCountapp = ActiveExpenseContractsPage.GetOGETransactionCount();
		assertEquals("Active Expense Contracts  Award Method widget Details page table count did not match", numOfAEContractsDetailsCountapp, NumOfAEContractsDetailsCountyear);

		String WidgetDetailsTitle =  "Award Methods Active Expense Contracts Transactions";
		String WidgetDetailsTitleApp = HomePage.DetailsPagetitle();
	    assertEquals("Active Expense Contracts AWard Method Widget title did not match", WidgetDetailsTitle, WidgetDetailsTitleApp); 
	    

	    String WidgetDetailsAmountDB =  OGENYCDatabaseUtil.getAEContractsDetailsAmount(year,'B');
		String WidgetDetailsAmountapp = ActiveExpenseContractsPage.GetTransactionAmount1();
		assertEquals("Active Expense Contracts AwardMethods Widget Details page total Contract amount did not match", WidgetDetailsAmountapp, WidgetDetailsAmountDB);
	}
	@Test
	public void VerifyTop5DepartmentsTransactionCount() throws SQLException {
		ContractsPage.GoToTop5DetailsPage(WidgetOption.TopDepartments);
		HomePage.ShowWidgetDetails();
		
		int NumOfAEContractsDetailsCountyear =  OGENYCDatabaseUtil.getAEContractsDetailsCount(year,'B');
		int numOfAEContractsDetailsCountapp = ActiveExpenseContractsPage.GetOGETransactionCount();
		assertEquals("Active Expense Contracts  Agencies widget Details page table count did not match", numOfAEContractsDetailsCountapp, NumOfAEContractsDetailsCountyear); 
		
		String WidgetDetailsTitle =  "Agencies Active Expense Contracts Transactions";
		String WidgetDetailsTitleApp = HomePage.DetailsPagetitle();
	    assertEquals("Active Expense Contracts Agencies Widget title did not match", WidgetDetailsTitle, WidgetDetailsTitleApp); 
	    

	    String WidgetDetailsAmountDB =  OGENYCDatabaseUtil.getAEContractsDetailsAmount(year,'B');
		String WidgetDetailsAmountapp = ActiveExpenseContractsPage.GetTransactionAmount1();
		assertEquals("Active Expense Contracts Agencies  Widget Details page total Contract amount did not match", WidgetDetailsAmountapp, WidgetDetailsAmountDB);
	}
	@Test
	public void VerifyContractsByIndustriesTransactionCount() throws SQLException {
		ContractsPage.GoToTop5DetailsPage(WidgetOption.ContractsByIndustries);
		HomePage.ShowWidgetDetails();
		int NumOfAEContractsDetailsCountyear =  OGENYCDatabaseUtil.getAEContractsDetailsCount(year,'B');
		int numOfAEContractsDetailsCountapp = ActiveExpenseContractsPage.GetOGETransactionCount();
		assertEquals("Active Expense Contracts  Contracts by Industies widget Details page table count did not match", numOfAEContractsDetailsCountapp, NumOfAEContractsDetailsCountyear); 
		
		String WidgetDetailsTitle =  "Contracts by Industries Active Expense Contracts Transactions";
		String WidgetDetailsTitleApp = HomePage.DetailsPagetitle();
	    assertEquals("Active Expense Contracts Industries Widget title did not match", WidgetDetailsTitle, WidgetDetailsTitleApp); 
	    

	    String WidgetDetailsAmountDB =  OGENYCDatabaseUtil.getAEContractsDetailsAmount(year,'B');
		String WidgetDetailsAmountapp = ActiveExpenseContractsPage.GetTransactionAmount1();
		assertEquals("Active Expense Contracts ContractsByIndustries  Widget Details page total Contract amount did not match", WidgetDetailsAmountapp, WidgetDetailsAmountDB);
	}
	@Test
	public void VerifyContractsBySizeTransactionCount() throws SQLException {
		ContractsPage.GoToTop5DetailsPage(WidgetOption.ContractsBySize);
	
		int NumOfAEContractsDetailsCountyear =  OGENYCDatabaseUtil.getAEContractsDetailsCount(year,'B');
		int numOfAEContractsDetailsCountapp = ActiveExpenseContractsPage.GetOGETransactionCount();
		assertEquals("Active Expense Contracts Contracts by size  widget Details page table count did not match", numOfAEContractsDetailsCountapp, NumOfAEContractsDetailsCountyear);
		
		String WidgetDetailsTitle =  "Contracts by Size Active Expense Contracts Transactions";
		String WidgetDetailsTitleApp = HomePage.DetailsPagetitle();
	    assertEquals("Active Expense Contracts Contracts by Sizes Widget title did not match", WidgetDetailsTitle, WidgetDetailsTitleApp); 
	    

	    String WidgetDetailsAmountDB =  OGENYCDatabaseUtil.getAEContractsDetailsAmount(year,'B');
		String WidgetDetailsAmountapp = ActiveExpenseContractsPage.GetTransactionAmount1();
		assertEquals("Active Expense Contracts ContractsBySize  Widget Details page total Contract amount did not match", WidgetDetailsAmountapp, WidgetDetailsAmountDB);
	}

	/* ***************** Test Widget Transaction Total Amount ****************** */
	
	/*
	@Test
	public void VerifyTop5MasterAgreementsTransactionAmount(){
		Float transactionAmt = 26.3f;
		ContractsPage.GoToTop5DetailsPage(WidgetOption.Top5MasterAgreements);
		HomePage.ShowWidgetDetails();
		assertTrue(HomePage.GetTransactionAmount()>= transactionAmt);
	}
		@Test
	public void VerifyContractsTransactionTitle() throws SQLException {
			ContractsPage.GoToTop5DetailsPage(WidgetOption.Top5Agencies);
		HomePage.ShowWidgetDetails();
	//assertTrue(HomePage.GetTransactionAmount1()== transactionAmt);
		String AgenciesTitle =  "Agencies Active Expense Contracts Transactions";
		String RevenueAgenciesTitleApp = HomePage.DetailsPagetitle();
	assertEquals("Revenue Agencies Widget details page title did not match", AgenciesTitle, RevenueAgenciesTitleApp); 
	}
	
	@After
	public void EndProgram()
	{
		Driver.Instance.quit();
	}
	
	*/
}
