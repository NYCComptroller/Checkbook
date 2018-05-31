package FunctionalContractsMWBE;

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
import pages.mwbe.MWBEPage;
import pages.revenue.RevenuePage;
import utilities.NYCBaseTest;
import utilities.NYCDatabaseUtil;
import helpers.Driver;
import helpers.Helper;
import navigation.MWBECategory.MWBECategoryOption;
import utilities.TestStatusReport;
//public class MWBEActiveExpenseContractsDetailsTest extends TestStatusReport{
	public class MWBEActiveExpenseContractsDetailsTest extends NYCBaseTest {
	int year =  Integer.parseInt(NYCBaseTest.prop.getProperty("year"));
	@Before
	public void GoToPage(){
		if(!MWBEPage.IsAt()){
			MWBEPage.GoTo("Contracts", MWBECategoryOption.MWBEHome);		
		}
		if(!(Helper.getCurrentSelectedYear()).equalsIgnoreCase(NYCBaseTest.prop.getProperty("CurrentYear")))
			   HomePage.SelectYear(NYCBaseTest.prop.getProperty("CurrentYear"));
	}
	
		
	/* ***************** Test Widget Transaction Count ****************** */
	@Test
	public void VerifyTop5MasterAgreementsTransactionCount() throws SQLException {
		ContractsPage.GoToTop5DetailsPage(WidgetOption.Top5MasterAgreements);
		HomePage.ShowWidgetDetails();
		int NumOfAEContractsDetailsCountyear =  NYCDatabaseUtil.getAEMasterContractsDetailsCount(year,'B');
        int numOfAEContractsDetailsCountapp = ActiveExpenseContractsPage.GetTransactionCount();
		assertEquals("Active Expense Contracts master contracts widget Details page table count did not match", numOfAEContractsDetailsCountapp, NumOfAEContractsDetailsCountyear); 
		
		String WidgetDetailsTitle =  "Master Agreements Active Expense Contracts Transactions";
		String WidgetDetailsTitleApp = HomePage.DetailsPagetitle();
	  assertEquals("Active Expense Contracts Master Agreement Widget title did not match", WidgetDetailsTitle, WidgetDetailsTitleApp);
	    
	    String WidgetDetailsAmountDB =  NYCDatabaseUtil.getAEContractsMasterContractsDetailsAmount(year,'B');
		String WidgetDetailsAmountapp = ActiveExpenseContractsPage.GetTransactionAmount1();
		assertEquals("Active Expense Contracts Master Agreement  Widget Details page total Contract amount did not match", WidgetDetailsAmountapp, WidgetDetailsAmountDB);
	}
	       
	
	@Test
	public void VerifyTop5MasterAgreementModificationsTransactionCount() throws SQLException {
		ContractsPage.GoToTop5DetailsPage(WidgetOption.Top5MasterAgreementModifications);
		HomePage.ShowWidgetDetails();
		int NumOfAEContractsDetailsCountyear =  NYCDatabaseUtil.getAEMasterContractsModificationDetailsCount(year,'B');
		int numOfAEContractsDetailsCountapp = ActiveExpenseContractsPage.GetTransactionCount();
		assertEquals("Active Expense Contracts master Contracts modification widget Details page table count did not match", numOfAEContractsDetailsCountapp, NumOfAEContractsDetailsCountyear);
		
			    
	    String WidgetDetailsTitle =  "Master Agreement Modifications Active Expense Contracts Transactions";
		String WidgetDetailsTitleApp = HomePage.DetailsPagetitle();
	    assertEquals("Active Expense Contracts Master Agreement Modifications Widget title did not match", WidgetDetailsTitle, WidgetDetailsTitleApp); 
	    
	    String WidgetDetailsAmountDB =  NYCDatabaseUtil.getAEContractsMasterModificationDetailsAmount(year,'B');
		String WidgetDetailsAmountapp = ActiveExpenseContractsPage.GetTransactionAmount1();
		assertEquals("Active Expense Contracts Master Agreement modifications  Widget Details page total Contract amount did not match", WidgetDetailsAmountapp, WidgetDetailsAmountDB);
	}
	@Test
	public void VerifyTop5ContractsTransactionCount() throws SQLException {
		ContractsPage.GoToTop5DetailsPage(WidgetOption.Top5Contracts);
		
		int NumOfAEContractsDetailsCountyear =  NYCDatabaseUtil.getAEContractsDetailsCount(year,'B');
		int numOfAEContractsDetailsCountapp = ActiveExpenseContractsPage.GetTransactionCount();
		assertEquals("Active Expense Contracts contracts widget Details page table count did not match", numOfAEContractsDetailsCountapp, NumOfAEContractsDetailsCountyear); 
		
		String WidgetDetailsTitle =  "Contracts Active Expense Contracts Transactions";
		String WidgetDetailsTitleApp = HomePage.DetailsPagetitle();
	    assertEquals("Active Expense Contracts contracts Widget title did not match", WidgetDetailsTitle, WidgetDetailsTitleApp); 
	    
	    String WidgetDetailsAmountDB =  NYCDatabaseUtil.getAEContractsDetailsAmount(year,'B');
		String WidgetDetailsAmountapp = ActiveExpenseContractsPage.GetTransactionAmount1();
		assertEquals("Active Expense Contracts contracts  Widget Details page total Contract amount did not match", WidgetDetailsAmountapp, WidgetDetailsAmountDB);
	}
	@Test
	public void VerifyTop5ContractAmountModificationsTransactionCount() throws SQLException {
		ContractsPage.GoToTop5DetailsPage(WidgetOption.Top5ContractAmountModifications);
	
		int NumOfAEContractsDetailsCountyear =  NYCDatabaseUtil.getAEContractsModificationDetailsCount(year,'B');
		int numOfAEContractsDetailsCountapp = ActiveExpenseContractsPage.GetTransactionCount();
		assertEquals("Active Expense Contracts master Contracts modification widget Details page table count did not match", numOfAEContractsDetailsCountapp, NumOfAEContractsDetailsCountyear); 
		
		String WidgetDetailsTitle =  "Contract Amount Modifications Active Expense Contracts Transactions";
		String WidgetDetailsTitleApp = HomePage.DetailsPagetitle();
	   assertEquals("Active Expense Contracts Modifications Widget title did not match", WidgetDetailsTitle, WidgetDetailsTitleApp); 
	    

	   String WidgetDetailsAmountDB =  NYCDatabaseUtil.getAEContractsModificationDetailsAmount(year,'B');
		String WidgetDetailsAmountapp = ActiveExpenseContractsPage.GetTransactionAmount1();
		assertEquals("Active Expense Contracts ContractAmountModifications  Widget Details page total Contract amount did not match", WidgetDetailsAmountapp, WidgetDetailsAmountDB);
	}
	@Test
	public void VerifyTop5PrimeVendorsTransactionCount() throws SQLException {
		ContractsPage.GoToTop5DetailsPage(WidgetOption.Top5PrimeVendors);
	
		int NumOfAEContractsDetailsCountyear =  NYCDatabaseUtil.getAEAllContractsDetailsCount(year,'B');
		int numOfAEContractsDetailsCountapp = ActiveExpenseContractsPage.GetTransactionCount();
		assertEquals("Active Expense Contracts master Contracts Prime Vendors widget Details page table count did not match", numOfAEContractsDetailsCountapp, NumOfAEContractsDetailsCountyear);
		
		String WidgetDetailsTitle =  "Prime Vendors Active Expense Contracts Transactions";
		String WidgetDetailsTitleApp = HomePage.DetailsPagetitle();
	    assertEquals("Active Expense Contracts Prime Vendors Widget title did not match", WidgetDetailsTitle, WidgetDetailsTitleApp); 
	    

	    String WidgetDetailsAmountDB =  NYCDatabaseUtil.getAEContractsAllDetailsAmount(year,'B');
		String WidgetDetailsAmountapp = ActiveExpenseContractsPage.GetTransactionAmount1();
		assertEquals("Active Expense Contracts PrimeVendors  Widget Details page total Contract amount did not match", WidgetDetailsAmountapp, WidgetDetailsAmountDB);
	}
	@Test
	public void VerifyTop5AwardMethodsTransactionCount() throws SQLException {
		ContractsPage.GoToTop5DetailsPage(WidgetOption.Top5AwardMethods);
		HomePage.ShowWidgetDetails();
		int NumOfAEContractsDetailsCountyear =  NYCDatabaseUtil.getAEAllContractsDetailsCount(year,'B');
		int numOfAEContractsDetailsCountapp = ActiveExpenseContractsPage.GetTransactionCount();
		assertEquals("Active Expense Contracts  Award Method widget Details page table count did not match", numOfAEContractsDetailsCountapp, NumOfAEContractsDetailsCountyear);

		String WidgetDetailsTitle =  "Award Methods Active Expense Contracts Transactions";
		String WidgetDetailsTitleApp = HomePage.DetailsPagetitle();
	    assertEquals("Active Expense Contracts AWard Method Widget title did not match", WidgetDetailsTitle, WidgetDetailsTitleApp); 
	    

	    String WidgetDetailsAmountDB =  NYCDatabaseUtil.getAEContractsAllDetailsAmount(year,'B');
		String WidgetDetailsAmountapp = ActiveExpenseContractsPage.GetTransactionAmount1();
		assertEquals("Active Expense Contracts AwardMethods Widget Details page total Contract amount did not match", WidgetDetailsAmountapp, WidgetDetailsAmountDB);
	}
	@Test
	public void VerifyTop5AgenciesTransactionCount() throws SQLException {
		ContractsPage.GoToTop5DetailsPage(WidgetOption.Top5Agencies);
		HomePage.ShowWidgetDetails();
		
		int NumOfAEContractsDetailsCountyear =  NYCDatabaseUtil.getAEAllContractsDetailsCount(year,'B');
		int numOfAEContractsDetailsCountapp = ActiveExpenseContractsPage.GetTransactionCount();
		assertEquals("Active Expense Contracts  Agencies widget Details page table count did not match", numOfAEContractsDetailsCountapp, NumOfAEContractsDetailsCountyear); 
		
		String WidgetDetailsTitle =  "Agencies Active Expense Contracts Transactions";
		String WidgetDetailsTitleApp = HomePage.DetailsPagetitle();
	    assertEquals("Active Expense Contracts Agencies Widget title did not match", WidgetDetailsTitle, WidgetDetailsTitleApp); 
	    

	    String WidgetDetailsAmountDB =  NYCDatabaseUtil.getAEContractsAllDetailsAmount(year,'B');
		String WidgetDetailsAmountapp = ActiveExpenseContractsPage.GetTransactionAmount1();
		assertEquals("Active Expense Contracts Agencies  Widget Details page total Contract amount did not match", WidgetDetailsAmountapp, WidgetDetailsAmountDB);
	}
	@Test
	public void VerifyContractsByIndustriesTransactionCount() throws SQLException {
		ContractsPage.GoToTop5DetailsPage(WidgetOption.ContractsByIndustries);
		HomePage.ShowWidgetDetails();
		int NumOfAEContractsDetailsCountyear =  NYCDatabaseUtil.getAEAllContractsDetailsCount(year,'B');
		int numOfAEContractsDetailsCountapp = ActiveExpenseContractsPage.GetTransactionCount();
		assertEquals("Active Expense Contracts  Contracts by Industies widget Details page table count did not match", numOfAEContractsDetailsCountapp, NumOfAEContractsDetailsCountyear); 
		
		String WidgetDetailsTitle =  "Contracts by Industries Active Expense Contracts Transactions";
		String WidgetDetailsTitleApp = HomePage.DetailsPagetitle();
	    assertEquals("Active Expense Contracts Industries Widget title did not match", WidgetDetailsTitle, WidgetDetailsTitleApp); 
	    

	    String WidgetDetailsAmountDB =  NYCDatabaseUtil.getAEContractsAllDetailsAmount(year,'B');
		String WidgetDetailsAmountapp = ActiveExpenseContractsPage.GetTransactionAmount1();
		assertEquals("Active Expense Contracts ContractsByIndustries  Widget Details page total Contract amount did not match", WidgetDetailsAmountapp, WidgetDetailsAmountDB);
	}
	@Test
	public void VerifyContractsBySizeTransactionCount() throws SQLException {
		ContractsPage.GoToTop5DetailsPage(WidgetOption.ContractsBySize);
	
		int NumOfAEContractsDetailsCountyear =  NYCDatabaseUtil.getAEAllContractsDetailsCount(year,'B');
		int numOfAEContractsDetailsCountapp = ActiveExpenseContractsPage.GetTransactionCount();
		assertEquals("Active Expense Contracts Contracts by size  widget Details page table count did not match", numOfAEContractsDetailsCountapp, NumOfAEContractsDetailsCountyear);
		
		String WidgetDetailsTitle =  "Contracts by Size Active Expense Contracts Transactions";
		String WidgetDetailsTitleApp = HomePage.DetailsPagetitle();
	    assertEquals("Active Expense Contracts Contracts by Sizes Widget title did not match", WidgetDetailsTitle, WidgetDetailsTitleApp); 
	    

	    String WidgetDetailsAmountDB =  NYCDatabaseUtil.getAEContractsAllDetailsAmount(year,'B');
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
