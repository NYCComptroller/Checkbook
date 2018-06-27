package FunctionalContractsMWBE;

import static org.junit.Assert.assertEquals;

import static org.junit.Assert.assertTrue;

import java.sql.SQLException;

import org.junit.Before;
import org.junit.Test;

import pages.contracts.ActiveExpenseContractsPage;
import pages.contracts.ContractsPage;
import pages.contracts.ContractsPage.WidgetOption;
import pages.contracts.RegisteredRevenueContractsPage;
import pages.home.HomePage;
import pages.mwbe.MWBEPage;
import utilities.NYCBaseTest;
import utilities.NYCDatabaseUtil;
import helpers.Helper;
import navigation.MWBECategory.MWBECategoryOption;
import utilities.TestStatusReport;
public class MWBERegisteredRevenueContractsDetailsTest extends TestStatusReport{

	//public class MWBERegisteredRevenueContractsDetailsTest extends NYCBaseTest{
	int year =  Integer.parseInt(NYCBaseTest.prop.getProperty("year"));
	@Before
	public void GoToPage(){
		//if(!MWBEPage.IsAt()){
			MWBEPage.GoTo("Contracts", MWBECategoryOption.MWBEHome);
			navigation.TopNavigation.Contracts.RegisteredRevenueContracts.Select();	
		//}
		if(!(Helper.getCurrentSelectedYear()).equalsIgnoreCase(NYCBaseTest.prop.getProperty("CurrentYear")))
			   HomePage.SelectYear(NYCBaseTest.prop.getProperty("CurrentYear"));
	}

	/* ***************** Test Widget Transaction Count ****************** */
	@Test
	public void VerifyTop5ContractsTransactionCount() throws SQLException {
		ContractsPage.GoToTop5DetailsPage(WidgetOption.TopContracts);
		HomePage.ShowWidgetDetails();
		
		Integer NumOfRRContractsDetailsCountDB =  NYCDatabaseUtil.getMWBERRContractsDetailsCount(year,'B');
		Integer numOfRRContractsDetailsCountapp = RegisteredRevenueContractsPage.GetTransactionCount();
		assertEquals(" Registered Revenue Contracts  widget Details page table count did not match", numOfRRContractsDetailsCountapp, NumOfRRContractsDetailsCountDB); 
		
		String WidgetDetailsTitle =  "M/WBE Contracts Registered Revenue Contracts Transactions";
		String WidgetDetailsTitleApp = HomePage.DetailsPagetitle();
	    assertEquals("Registered Revenue Contracts contracts Widget title did not match", WidgetDetailsTitle, WidgetDetailsTitleApp); 
	    
	    
	    String WidgetDetailsAmountDB =  NYCDatabaseUtil.getMWBERRContractsDetailsAmount(year,'B');
		String WidgetDetailsAmountapp = ContractsPage.GetTransactionAmount();
		assertEquals("Active Revenue Contracts Contracts  Widget Details page total Contract amount did not match", WidgetDetailsAmountapp, WidgetDetailsAmountDB);
	     
	}
	/*@Test
	public void VerifyTop5ContractAmountModificationsTransactionCount() throws SQLException {
		ContractsPage.GoToTop5DetailsPage(WidgetOption.TopContractAmountModifications);
		HomePage.ShowWidgetDetails();
		Integer NumOfRRContractsDetailsCountDB =  NYCDatabaseUtil.getMWBERRContractsModificationsDetailsCount(year,'B');
		Integer numOfRRContractsDetailsCountapp = RegisteredRevenueContractsPage.GetTransactionCount();
		assertEquals(" Registered Revenue Contracts Modfications  widget Details page table count did not match", numOfRRContractsDetailsCountapp, NumOfRRContractsDetailsCountDB);
		

		String WidgetDetailsTitle =  "M/WBE Contract Amount Modifications Registered Revenue Contracts Transactions";
		String WidgetDetailsTitleApp = HomePage.DetailsPagetitle();
	    assertEquals("Registered Revenue Contracts Modifications Widget title did not match", WidgetDetailsTitle, WidgetDetailsTitleApp); 
	    
	    String WidgetDetailsAmountDB =  NYCDatabaseUtil.getMWBERRContractsModificationDetailsAmount(year,'B');
		String WidgetDetailsAmountapp = ContractsPage.GetTransactionAmount();
		assertEquals("Active Revenue Contracts ContractsModifications  Widget Details page total Contract amount did not match", WidgetDetailsAmountapp, WidgetDetailsAmountDB);
	     
	}*/
	@Test
	public void VerifyTop5PrimeVendorsTransactionCount() throws SQLException {
		ContractsPage.GoToTop5DetailsPage(WidgetOption.TopPrimeVendors);
		HomePage.ShowWidgetDetails();
		Integer NumOfRRContractsDetailsCountDB =  NYCDatabaseUtil.getMWBERRContractsDetailsCount(year,'B');
		Integer numOfRRContractsDetailsCountapp = RegisteredRevenueContractsPage.GetTransactionCount();
		assertEquals(" Registered Revenue Contracts Prime Vendors widget Details page table count did not match", numOfRRContractsDetailsCountapp, NumOfRRContractsDetailsCountDB);
		
		String WidgetDetailsTitle =  "M/WBE Prime Vendors Registered Revenue Contracts Transactions";
		String WidgetDetailsTitleApp = HomePage.DetailsPagetitle();
	    assertEquals("Registered Revenue Contracts Prime Vendors Widget title did not match", WidgetDetailsTitle, WidgetDetailsTitleApp); 
		
	    
	    String WidgetDetailsAmountDB =  NYCDatabaseUtil.getMWBERRContractsDetailsAmount(year,'B');
		String WidgetDetailsAmountapp = ContractsPage.GetTransactionAmount();
		assertEquals("Active Revenue Contracts PrimeVendors  Widget Details page total Contract amount did not match", WidgetDetailsAmountapp, WidgetDetailsAmountDB);
	     
	}
	@Test
	public void VerifyTop5AwardMethodsTransactionCount() throws SQLException {
		ContractsPage.GoToTop5DetailsPage(WidgetOption.TopAwardMethods);
		HomePage.ShowWidgetDetails();
		Integer NumOfRRContractsDetailsCountDB =  NYCDatabaseUtil.getMWBERRContractsDetailsCount(year,'B');
		Integer numOfRRContractsDetailsCountapp = RegisteredRevenueContractsPage.GetTransactionCount();
		assertEquals(" Registered Revenue Contracts Award Method  widget Details page table count did not match", numOfRRContractsDetailsCountapp, NumOfRRContractsDetailsCountDB);
		
		String WidgetDetailsTitle =  "M/WBE Award Methods Registered Revenue Contracts Transactions";
		String WidgetDetailsTitleApp = HomePage.DetailsPagetitle();
	    assertEquals("Registered Revenue Contracts AWard Method Widget title did not match", WidgetDetailsTitle, WidgetDetailsTitleApp); 
	    
	    String WidgetDetailsAmountDB =  NYCDatabaseUtil.getMWBERRContractsDetailsAmount(year,'B');
		String WidgetDetailsAmountapp = ContractsPage.GetTransactionAmount();
		assertEquals("Active Revenue Contracts AwardMethods Widget Details page total Contract amount did not match", WidgetDetailsAmountapp, WidgetDetailsAmountDB);
	     
	}
	@Test
	public void VerifyTop5AgenciesTransactionCount() throws SQLException {
		ContractsPage.GoToTop5DetailsPage(WidgetOption.TopAgencies);
		HomePage.ShowWidgetDetails();
		Integer NumOfRRContractsDetailsCountDB =  NYCDatabaseUtil.getMWBERRContractsDetailsCount(year,'B');
		Integer numOfRRContractsDetailsCountapp = RegisteredRevenueContractsPage.GetTransactionCount();
		assertEquals(" Registered Revenue Contracts Agencies widget Details page table count did not match", numOfRRContractsDetailsCountapp, NumOfRRContractsDetailsCountDB); 
		
		String WidgetDetailsTitle =  "M/WBE Agencies Registered Revenue Contracts Transactions";
		String WidgetDetailsTitleApp = HomePage.DetailsPagetitle();
	    assertEquals("Registered Revenue Contracts Agencies Widget title did not match", WidgetDetailsTitle, WidgetDetailsTitleApp); 
	    
	    String WidgetDetailsAmountDB =  NYCDatabaseUtil.getMWBERRContractsDetailsAmount(year,'B');
		String WidgetDetailsAmountapp = ContractsPage.GetTransactionAmount();
		assertEquals("Active Revenue Contracts Agencie  Widget Details page total Contract amount did not match", WidgetDetailsAmountapp, WidgetDetailsAmountDB);
	     
	}
	@Test
	public void VerifyContractsByIndustriesTransactionCount() throws SQLException {
		ContractsPage.GoToTop5DetailsPage(WidgetOption.ContractsByIndustries);
		HomePage.ShowWidgetDetails();
		Integer NumOfRRContractsDetailsCountDB =  NYCDatabaseUtil.getMWBERRContractsDetailsCount(year,'B');
		Integer numOfRRContractsDetailsCountapp = RegisteredRevenueContractsPage.GetTransactionCount();
		assertEquals(" Registered Revenue Contracts Industries widget Details page table count did not match", numOfRRContractsDetailsCountapp, NumOfRRContractsDetailsCountDB);
		
		String WidgetDetailsTitle =  "M/WBE Contracts by Industries Registered Revenue Contracts Transactions";
		String WidgetDetailsTitleApp = HomePage.DetailsPagetitle();
	    assertEquals("Registered Revenue Contracts Industries Widget title did not match", WidgetDetailsTitle, WidgetDetailsTitleApp); 
	    
	    String WidgetDetailsAmountDB =  NYCDatabaseUtil.getMWBERRContractsDetailsAmount(year,'B');
		String WidgetDetailsAmountapp = ContractsPage.GetTransactionAmount();
		assertEquals("Active Revenue Contracts ContractsByIndustries  Widget Details page total Contract amount did not match", WidgetDetailsAmountapp, WidgetDetailsAmountDB);
	     
	}
	@Test
	public void VerifyContractsBySizeTransactionCount() throws SQLException {
		ContractsPage.GoToTop5DetailsPage(WidgetOption.ContractsBySize);
		HomePage.ShowWidgetDetails();
		Integer NumOfRRContractsDetailsCountDB =  NYCDatabaseUtil.getMWBERRContractsDetailsCount(year,'B');
		Integer numOfRRContractsDetailsCountapp = RegisteredRevenueContractsPage.GetTransactionCount();
		assertEquals(" Registered Revenue Contracts Size  widget Details page table count did not match", numOfRRContractsDetailsCountapp, NumOfRRContractsDetailsCountDB); 
		
		String WidgetDetailsTitle =  "M/WBE Contracts by Size Registered Revenue Contracts Transactions";
		String WidgetDetailsTitleApp = HomePage.DetailsPagetitle();
	    assertEquals("Registered Revenue Contracts Contracts by Sizes Widget title did not match", WidgetDetailsTitle, WidgetDetailsTitleApp); 
	   
	    String WidgetDetailsAmountDB =  NYCDatabaseUtil.getMWBERRContractsDetailsAmount(year,'B');
		String WidgetDetailsAmountapp = ContractsPage.GetTransactionAmount();
		assertEquals("Active Revenue Contracts ContractsBySize  Widget Details page total Contract amount did not match", WidgetDetailsAmountapp, WidgetDetailsAmountDB);
	     
	}

	/* ***************** Test Widget Transaction Total Amount ****************** 
	@Test
	public void VerifyTop5ContractsTransactionAmount(){
		Float transactionAmt = 465.32f;
		ContractsPage.GoToTop5DetailsPage(WidgetOption.Top5Contracts);
		HomePage.ShowWidgetDetails();
		assertTrue(HomePage.GetTransactionAmount()>= transactionAmt);
	}
	@Test
	public void VerifyTop5ContractAmountModificationsTransactionAmount(){
		Float transactionAmt = 700.0f;
		ContractsPage.GoToTop5DetailsPage(WidgetOption.TopContractAmountModifications);
		HomePage.ShowWidgetDetails();
		assertTrue(HomePage.GetTransactionAmount()>= transactionAmt);
	}
	@Test
	public void VerifyTop5PrimeVendorsTransactionAmount(){
		Float transactionAmt = 465.32f;
		ContractsPage.GoToTop5DetailsPage(WidgetOption.Top5PrimeVendors);
		HomePage.ShowWidgetDetails();
		assertTrue(HomePage.GetTransactionAmount()>= transactionAmt);
	}
	@Test
	public void VerifyTop5AwardMethodsTransactionAmount(){
		Float transactionAmt = 465.32f;
		ContractsPage.GoToTop5DetailsPage(WidgetOption.Top5AwardMethods);
		HomePage.ShowWidgetDetails();
		assertTrue(HomePage.GetTransactionAmount()>= transactionAmt);
	}
	@Test
	public void VerifyTop5AgenciesTransactionAmount(){
		Float transactionAmt = 465.32f;
		ContractsPage.GoToTop5DetailsPage(WidgetOption.Top5Agencies);
		HomePage.ShowWidgetDetails();
		assertTrue(HomePage.GetTransactionAmount()>= transactionAmt);
	}
	@Test
	public void VerifyContractsByIndustriesTransactionAmount(){
		Float transactionAmt = 465.32f;
		ContractsPage.GoToTop5DetailsPage(WidgetOption.ContractsByIndustries);
		HomePage.ShowWidgetDetails();
		assertTrue(HomePage.GetTransactionAmount()>= transactionAmt); 
	}
	@Test
	public void VerifyContractsBySizeTransactionAmount(){
		Float transactionAmt = 465.32f;
		ContractsPage.GoToTop5DetailsPage(WidgetOption.ContractsBySize);
		HomePage.ShowWidgetDetails();
		assertTrue(HomePage.GetTransactionAmount()>= transactionAmt);
	}
	
	*/
}
