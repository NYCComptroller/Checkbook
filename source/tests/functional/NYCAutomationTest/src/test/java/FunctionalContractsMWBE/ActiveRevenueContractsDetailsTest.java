package FunctionalContractsMWBE;

import static org.junit.Assert.assertEquals;

import static org.junit.Assert.assertTrue;

import java.sql.SQLException;

import org.junit.Before;
import org.junit.Test;

import pages.contracts.ActiveExpenseContractsPage;
import pages.contracts.ActiveRevenueContractsPage;
import pages.contracts.ContractsPage;
import pages.contracts.ContractsPage.WidgetOption;
import pages.home.HomePage;
import pages.mwbe.MWBEPage;
import utilities.NYCBaseTest;
import utilities.NYCDatabaseUtil;
import helpers.Helper;
import navigation.MWBECategory.MWBECategoryOption;
import utilities.TestStatusReport;
//public class ActiveRevenueContractsDetailsTest extends TestStatusReport{

	public class ActiveRevenueContractsDetailsTest extends NYCBaseTest{
	int year =  Integer.parseInt(NYCBaseTest.prop.getProperty("year"));
	@Before

	
	public void GoToPage(){
		if(!MWBEPage.IsAt()){
			MWBEPage.GoTo("Contracts", MWBECategoryOption.MWBEHome);
			ActiveRevenueContractsPage.GoTo();
		}
		if(!(Helper.getCurrentSelectedYear()).equalsIgnoreCase(NYCBaseTest.prop.getProperty("CurrentYear")))
			   HomePage.SelectYear(NYCBaseTest.prop.getProperty("CurrentYear"));
	}


	/* ***************** Test Widget Transaction Count ****************** */
	@Test
	public void VerifyTop5ContractsTransactionCount() throws SQLException {
		ContractsPage.GoToTop5DetailsPage(WidgetOption.Top5Contracts);
		HomePage.ShowWidgetDetails();
		
		int NumOfARContractsDetailsCountDB =  NYCDatabaseUtil.getARContractsDetailsCount(year,'B');
		int numOfARContractsDetailsCountapp = ActiveRevenueContractsPage.GetTransactionCount();
		assertEquals(" Active Revenue Contracts  widget Details page table count did not match", numOfARContractsDetailsCountapp, NumOfARContractsDetailsCountDB); 
		
		String WidgetDetailsTitle =  "Contracts Active Revenue Contracts Transactions";
		String WidgetDetailsTitleApp = HomePage.DetailsPagetitle();
	    assertEquals("Active Revenue Contracts contracts Widget title did not match", WidgetDetailsTitle, WidgetDetailsTitleApp); 
	    
	    
	    String WidgetDetailsAmountDB =  NYCDatabaseUtil.getARContractsDetailsAmount(year,'B');
		String WidgetDetailsAmountapp = ActiveExpenseContractsPage.GetTransactionAmount1();
		assertEquals("Active Revenue Contracts Contracts  Widget Details page total Contract amount did not match", WidgetDetailsAmountapp, WidgetDetailsAmountDB);
	     
	}
	@Test
	public void VerifyTop5ContractAmountModificationsTransactionCount() throws SQLException {
		ContractsPage.GoToTop5DetailsPage(WidgetOption.Top5ContractAmountModifications);
		HomePage.ShowWidgetDetails();
		int NumOfARContractsDetailsCountDB =  NYCDatabaseUtil.getARContractsModificationsDetailsCount(year,'B');
		int numOfARContractsDetailsCountapp = ActiveRevenueContractsPage.GetTransactionCount();
		assertEquals(" Active Revenue Contracts Modifications widget Details page table count did not match", numOfARContractsDetailsCountapp, NumOfARContractsDetailsCountDB);
		
		String WidgetDetailsTitle =  "Contract Amount Modifications Active Revenue Contracts Transactions";
		String WidgetDetailsTitleApp = HomePage.DetailsPagetitle();
	    assertEquals("Active Revenue Contracts Modifications Widget title did not match", WidgetDetailsTitle, WidgetDetailsTitleApp); 
	    
	    String WidgetDetailsAmountDB =  NYCDatabaseUtil.getARContractsModificationDetailsAmount(year,'B');
		String WidgetDetailsAmountapp = ActiveExpenseContractsPage.GetTransactionAmount1();
		assertEquals("Active Revenue Contracts Contracts  Widget Details page total Contract amount did not match", WidgetDetailsAmountapp, WidgetDetailsAmountDB);
	     
	}
	       
	@Test
	public void VerifyTop5PrimeVendorsTransactionCount() throws SQLException {
		ContractsPage.GoToTop5DetailsPage(WidgetOption.Top5PrimeVendors);
		HomePage.ShowWidgetDetails();
		int NumOfARContractsDetailsCountDB =  NYCDatabaseUtil.getARContractsDetailsCount(year,'B');
		int numOfARContractsDetailsCountapp = ActiveRevenueContractsPage.GetTransactionCount(); 
		assertEquals("Active Revenue Contracts Prime Vendors  widget Details page table count did not match", numOfARContractsDetailsCountapp, NumOfARContractsDetailsCountDB);
		
		String WidgetDetailsTitle =  "Prime Vendors Active Revenue Contracts Transactions";
		String WidgetDetailsTitleApp = HomePage.DetailsPagetitle();
	    assertEquals("Active Revenue Contracts Prime Vendors Widget title did not match", WidgetDetailsTitle, WidgetDetailsTitleApp); 
	    
	    String WidgetDetailsAmountDB =  NYCDatabaseUtil.getARContractsDetailsAmount(year,'B');
		String WidgetDetailsAmountapp = ActiveExpenseContractsPage.GetTransactionAmount1();
		assertEquals("Active Revenue Contracts Prime Vendors  Widget Details page total Contract amount did not match", WidgetDetailsAmountapp, WidgetDetailsAmountDB);
	     
	}
	@Test
	public void VerifyTop5AwardMethodsTransactionCount() throws SQLException {
		ContractsPage.GoToTop5DetailsPage(WidgetOption.Top5AwardMethods);
		HomePage.ShowWidgetDetails();
		int NumOfARContractsDetailsCountDB =  NYCDatabaseUtil.getARContractsDetailsCount(year,'B');
		int numOfARContractsDetailsCountapp = ActiveRevenueContractsPage.GetTransactionCount();
		assertEquals(" Active Revenue Contracts Award Method  widget Details page table count did not match", numOfARContractsDetailsCountapp, NumOfARContractsDetailsCountDB); 
		
		String WidgetDetailsTitle =  "Award Methods Active Revenue Contracts Transactions";
		String WidgetDetailsTitleApp = HomePage.DetailsPagetitle();
	    assertEquals("Active Revenue Contracts AWard Method Widget title did not match", WidgetDetailsTitle, WidgetDetailsTitleApp); 
	    
	    String WidgetDetailsAmountDB =  NYCDatabaseUtil.getARContractsDetailsAmount(year,'B');
		String WidgetDetailsAmountapp = ActiveExpenseContractsPage.GetTransactionAmount1();
		assertEquals("Active Revenue Contracts Award Method Widget Details page total Contract amount did not match", WidgetDetailsAmountapp, WidgetDetailsAmountDB);
	     
	}
	@Test
	public void VerifyTop5AgenciesTransactionCount() throws SQLException {
		ContractsPage.GoToTop5DetailsPage(WidgetOption.Top5Agencies);
		HomePage.ShowWidgetDetails();
		
		int NumOfARContractsDetailsCountDB =  NYCDatabaseUtil.getARContractsDetailsCount(year,'B');
		int numOfARContractsDetailsCountapp = ActiveRevenueContractsPage.GetTransactionCount();
		assertEquals(" Active Revenue Contracts Agencies  widget Details page table count did not match", numOfARContractsDetailsCountapp, NumOfARContractsDetailsCountDB);
		
		String WidgetDetailsTitle =  "Agencies Active Revenue Contracts Transactions";
		String WidgetDetailsTitleApp = HomePage.DetailsPagetitle();
	    assertEquals("Active Revenue Contracts Agencies Widget title did not match", WidgetDetailsTitle, WidgetDetailsTitleApp); 
	    
	    String WidgetDetailsAmountDB =  NYCDatabaseUtil.getARContractsDetailsAmount(year,'B');
		String WidgetDetailsAmountapp = ActiveExpenseContractsPage.GetTransactionAmount1();
		assertEquals("Active Revenue Contracts Agencies  Widget Details page total Contract amount did not match", WidgetDetailsAmountapp, WidgetDetailsAmountDB);
	     
	}
	@Test
	public void VerifyContractsByIndustriesTransactionCount() throws SQLException {
		ContractsPage.GoToTop5DetailsPage(WidgetOption.ContractsByIndustries);
		HomePage.ShowWidgetDetails();
		int NumOfARContractsDetailsCountDB =  NYCDatabaseUtil.getARContractsDetailsCount(year,'B');
		int numOfARContractsDetailsCountapp = ActiveRevenueContractsPage.GetTransactionCount();
		assertEquals(" Active Revenue Contracts Industries  widget Details page table count did not match", numOfARContractsDetailsCountapp, NumOfARContractsDetailsCountDB);
		
		String WidgetDetailsTitle =  "Contracts by Industries Active Revenue Contracts Transactions";
		String WidgetDetailsTitleApp = HomePage.DetailsPagetitle();
	    assertEquals("Active Revenue Contracts Industries Widget title did not match", WidgetDetailsTitle, WidgetDetailsTitleApp); 
	    
	    String WidgetDetailsAmountDB =  NYCDatabaseUtil.getARContractsDetailsAmount(year,'B');
		String WidgetDetailsAmountapp = ActiveExpenseContractsPage.GetTransactionAmount1();
		assertEquals("Active Revenue Contracts Industries  Widget Details page total Contract amount did not match", WidgetDetailsAmountapp, WidgetDetailsAmountDB);
	     
	}
	@Test
	public void VerifyContractsBySizeTransactionCount() throws SQLException {
		ContractsPage.GoToTop5DetailsPage(WidgetOption.ContractsBySize);
		HomePage.ShowWidgetDetails();
		
		int NumOfARContractsDetailsCountDB =  NYCDatabaseUtil.getARContractsDetailsCount(year,'B');
		int numOfARContractsDetailsCountapp = ActiveRevenueContractsPage.GetTransactionCount();
		assertEquals(" Active Revenue Contracts size  widget Details page table count did not match", numOfARContractsDetailsCountapp, NumOfARContractsDetailsCountDB);
		
		String WidgetDetailsTitle =  "Contracts by Size Active Revenue Contracts Transactions";
		String WidgetDetailsTitleApp = HomePage.DetailsPagetitle();
	    assertEquals("Active Revenue Contracts Contracts by Size Widget title did not match", WidgetDetailsTitle, WidgetDetailsTitleApp); 
	    
		
	    String WidgetDetailsAmountDB =  NYCDatabaseUtil.getARContractsDetailsAmount(year,'B');
		String WidgetDetailsAmountapp = ActiveExpenseContractsPage.GetTransactionAmount1();
		assertEquals("Active Revenue Contracts Size Widget Details page total Contract amount did not match", WidgetDetailsAmountapp, WidgetDetailsAmountDB);
	     
	}


/*
	@Test
	public void VerifyContractsBySizeTransactionAmount() {
		Float transactionAmt = 7.28f;
		ContractsPage.GoToTop5DetailsPage(WidgetOption.ContractsBySize);
		HomePage.ShowWidgetDetails();
		assertTrue(HomePage.GetTransactionAmount()>= transactionAmt);
	}
	
	*/
}
