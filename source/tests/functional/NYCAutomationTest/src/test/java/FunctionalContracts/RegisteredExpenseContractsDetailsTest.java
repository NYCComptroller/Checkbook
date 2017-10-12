package FunctionalContracts;

import static org.junit.Assert.assertEquals;

import static org.junit.Assert.assertTrue;

import java.sql.SQLException;

import org.junit.Before;
import org.junit.Test;

import pages.contracts.ActiveExpenseContractsPage;
import pages.contracts.ContractsPage;
import pages.contracts.ContractsPage.WidgetOption;
import pages.contracts.RegisteredExpenseContractsPage;
import pages.home.HomePage;
import utilities.NYCBaseTest;
import utilities.NYCDatabaseUtil;
import helpers.Helper;
import utilities.TestStatusReport;
public class RegisteredExpenseContractsDetailsTest extends TestStatusReport{

//public class RegisteredExpenseContractsDetailsTest extends NYCBaseTest {
	int year =  Integer.parseInt(NYCBaseTest.prop.getProperty("year"));
	@Before
	public void GoToPage() {
		RegisteredExpenseContractsPage.GoTo();
		if(!(Helper.getCurrentSelectedYear()).equalsIgnoreCase(NYCBaseTest.prop.getProperty("CurrentYear")))
			HomePage.SelectYear(NYCBaseTest.prop.getProperty("CurrentYear"));
		HomePage.ShowWidgetDetails();
	}

	/* ***************** Test Widget Transaction Total Count ****************** */
	@Test
	public void VerifyTop5MasterAgreementsTransactionCount() throws SQLException {
		ContractsPage.GoToTop5DetailsPage(WidgetOption.Top5MasterAgreements);
		HomePage.ShowWidgetDetails();
		int NumOfREContractsDetailsCount2016 = NYCDatabaseUtil.getREContractsMasterDetailsCount(2016,'B');
		int numOfREContractsDetailsCountapp = RegisteredExpenseContractsPage.GetTransactionCount();
		assertEquals("Number of Registered Expense master contracts widget Details page table count did not match", numOfREContractsDetailsCountapp, NumOfREContractsDetailsCount2016);
		
		String WidgetDetailsTitle =  "Master Agreement Active Expense Contracts Transactions";
		String WidgetDetailsTitleApp = HomePage.DetailsPagetitle();
	    assertEquals("Active Expense Contracts Master Agreement Widget title did not match", WidgetDetailsTitle, WidgetDetailsTitleApp); 
	    
	}
	@Test
	public void VerifyTop5MasterAgreementModificationsTransactionCount() throws SQLException {
		ContractsPage.GoToTop5DetailsPage(WidgetOption.Top5MasterAgreementModifications);
		HomePage.ShowWidgetDetails();
		int NumOfREContractsDetailsCount2016 = NYCDatabaseUtil.getREContractsMasterModificationsDetailsCount(2016,'B');
		int numOfREContractsDetailsCountapp = ActiveExpenseContractsPage.GetTransactionCount();
		assertEquals("Number of Registered Expense master Contracts modification widget Details page table count did not match", numOfREContractsDetailsCountapp, NumOfREContractsDetailsCount2016);
		
		 String WidgetDetailsTitle =  "Master Agreement Modifications Active Expense Contracts Transactions";
		String WidgetDetailsTitleApp = HomePage.DetailsPagetitle();
		assertEquals("Active Expense Contracts Master Agreement Modifications Widget title did not match", WidgetDetailsTitle, WidgetDetailsTitleApp); 
		    
	}
	@Test
	public void VerifyTop5ContractsTransactionCount() throws SQLException {
		ContractsPage.GoToTop5DetailsPage(WidgetOption.Top5Contracts);
		HomePage.ShowWidgetDetails();
		int NumOfREContractsDetailsCount2016 = NYCDatabaseUtil.getREContractsDetailsCount(2016,'B');
		int numOfREContractsDetailsCountapp = RegisteredExpenseContractsPage.GetTransactionCount();
		assertEquals("Number of Registered Expense Contracts  widget Details page table count did not match", numOfREContractsDetailsCountapp, NumOfREContractsDetailsCount2016); 
		
		String WidgetDetailsTitle =  "Active Expense Contracts Transactions";
		String WidgetDetailsTitleApp = HomePage.DetailsPagetitle();
	    assertEquals("Active Expense Contracts contracts Widget title did not match", WidgetDetailsTitle, WidgetDetailsTitleApp); 
	    
	}
	@Test
	public void VerifyTop5ContractAmountModificationsTransactionCount() throws SQLException {
		ContractsPage.GoToTop5DetailsPage(WidgetOption.Top5ContractAmountModifications);
		HomePage.ShowWidgetDetails();
		int NumOfREContractsDetailsCount2016 = NYCDatabaseUtil.getREContractsModificationsDetailsCount(2016,'B');
		int numOfREContractsDetailsCountapp = RegisteredExpenseContractsPage.GetTransactionCount();
		assertEquals("Number of REgistered Expense Contracts modification widget Details page table count did not match", numOfREContractsDetailsCountapp, NumOfREContractsDetailsCount2016); 
		
		String WidgetDetailsTitle =  "Active Expense Contracts Modifications Transactions";
		String WidgetDetailsTitleApp = HomePage.DetailsPagetitle();
	    assertEquals("Active Expense Contracts Modifications Widget title did not match", WidgetDetailsTitle, WidgetDetailsTitleApp); 
	    
	}
	@Test
	public void VerifyTop5PrimeVendorsTransactionCount() throws SQLException {
		ContractsPage.GoToTop5DetailsPage(WidgetOption.Top5PrimeVendors);
		HomePage.ShowWidgetDetails();
		int NumOfREContractsDetailsCount2016 =  NYCDatabaseUtil.getREContractsDetailsCount(2016,'B');
		int numOfREContractsDetailsCountapp = RegisteredExpenseContractsPage.GetTransactionCount();
		assertEquals("Number of Registered Expense Contracts Prime Vendors widget Details page table count did not match", numOfREContractsDetailsCountapp, NumOfREContractsDetailsCount2016); 
		
		String WidgetDetailsTitle =  "Prime Vendors Active Expense Contracts Transactions";
		String WidgetDetailsTitleApp = HomePage.DetailsPagetitle();
	    assertEquals("Active Expense Contracts Prime Vendors Widget title did not match", WidgetDetailsTitle, WidgetDetailsTitleApp); 
	    
	}
	@Test
	public void VerifyTop5AwardMethodsTransactionCount() throws SQLException {
		ContractsPage.GoToTop5DetailsPage(WidgetOption.Top5AwardMethods);
		HomePage.ShowWidgetDetails();
		int NumOfREContractsDetailsCount2016 =  NYCDatabaseUtil.getREContractsDetailsCount(2016,'B');
		int numOfREContractsDetailsCountapp = RegisteredExpenseContractsPage.GetTransactionCount();
		assertEquals("Number of Registered Expense Contracts Award Method widget Details page table count did not match", numOfREContractsDetailsCountapp, NumOfREContractsDetailsCount2016); 
		
		String WidgetDetailsTitle =  "AWard Methods Active Expense Contracts Transactions";
		String WidgetDetailsTitleApp = HomePage.DetailsPagetitle();
	    assertEquals("Active Expense Contracts AWard Method Widget title did not match", WidgetDetailsTitle, WidgetDetailsTitleApp); 
	    
	}
	@Test
	public void VerifyTop5AgenciesTransactionCount() throws SQLException {
		ContractsPage.GoToTop5DetailsPage(WidgetOption.Top5Agencies);
		HomePage.ShowWidgetDetails();
		int NumOfREContractsDetailsCount2016 =  NYCDatabaseUtil.getREContractsDetailsCount(2016,'B');
		int numOfREContractsDetailsCountapp = RegisteredExpenseContractsPage.GetTransactionCount();
		assertEquals("Number of  Registered Expense Contracts Agencies widget Details page table count did not match", numOfREContractsDetailsCountapp, NumOfREContractsDetailsCount2016); 
		
		String WidgetDetailsTitle =  "Agencies Active Expense Contracts Transactions";
		String WidgetDetailsTitleApp = HomePage.DetailsPagetitle();
	    assertEquals("Active Expense Contracts Agencies Widget title did not match", WidgetDetailsTitle, WidgetDetailsTitleApp); 
	    
	}
	
	@Test
	public void VerifyContractsByIndustriesTransactionCount() throws SQLException {
		ContractsPage.GoToTop5DetailsPage(WidgetOption.ContractsByIndustries);
		HomePage.ShowWidgetDetails();
		int NumOfREContractsDetailsCount2016 =  NYCDatabaseUtil.getREContractsDetailsCount(2016,'B');
		int numOfREContractsDetailsCountapp = RegisteredExpenseContractsPage.GetTransactionCount();
		assertEquals("Number of  Registered Expense Contracts by Industies widget Details page table count did not match", numOfREContractsDetailsCountapp, NumOfREContractsDetailsCount2016); 
		
		String WidgetDetailsTitle =  "Contracts by Industries Active Expense Contracts Transactions";
		String WidgetDetailsTitleApp = HomePage.DetailsPagetitle();
	    assertEquals("Active Expense Contracts Industries Widget title did not match", WidgetDetailsTitle, WidgetDetailsTitleApp); 
	    
	}
	@Test
	public void VerifyContractsBySizeTransactionCount() throws SQLException {
		ContractsPage.GoToTop5DetailsPage(WidgetOption.ContractsBySize);
		HomePage.ShowWidgetDetails();
		int NumOfREContractsDetailsCount2016 =  NYCDatabaseUtil.getREContractsDetailsCount(2016,'B');
		int numOfREContractsDetailsCountapp = RegisteredExpenseContractsPage.GetTransactionCount();
		assertEquals("Number of Registered Expense Contracts by size  widget Details page table count did not match", numOfREContractsDetailsCountapp, NumOfREContractsDetailsCount2016); 
		
		String WidgetDetailsTitle =  "Contracts by Size Active Expense Contracts Transactions";
		String WidgetDetailsTitleApp = HomePage.DetailsPagetitle();
	    assertEquals("Active Expense Contracts Contracts by Sizes Widget title did not match", WidgetDetailsTitle, WidgetDetailsTitleApp); 
	    
	}

	/* ***************** Test Widget Transaction Count ****************** */
	/*
	 @Test
    public void VerifyTop5MasterAgreementsTransactionCount(){
		ContractsPage.GoToTop5DetailsPage(WidgetOption.Top5MasterAgreements);
		HomePage.ShowWidgetDetails();
	   assertTrue(RegisteredExpenseContractsPage.GetTransactionCount() >= 847); 
    }
	@Test
    public void VerifyTop5MasterAgreementModificationsTransactionCount(){
		ContractsPage.GoToTop5DetailsPage(WidgetOption.Top5MasterAgreementModifications);
		HomePage.ShowWidgetDetails();
	   assertTrue(RegisteredExpenseContractsPage.GetTransactionCount() >= 25); 
    }
	@Test
    public void VerifyTop5ContractsTransactionCount(){
		ContractsPage.GoToTop5DetailsPage(WidgetOption.Top5Contracts);
		HomePage.ShowWidgetDetails();
	   assertTrue(RegisteredExpenseContractsPage.GetTransactionCount() >= 12681); 
    }
	@Test
    public void VerifyTop5ContractAmountModificationsTransactionCount(){
		ContractsPage.GoToTop5DetailsPage(WidgetOption.TopContractAmountModifications);
		HomePage.ShowWidgetDetails();
	   assertTrue(RegisteredExpenseContractsPage.GetTransactionCount() >= 1246); 
    }
	@Test
    public void VerifyTop5PrimeVendorsTransactionCount(){
		ContractsPage.GoToTop5DetailsPage(WidgetOption.Top5PrimeVendors);
		HomePage.ShowWidgetDetails();
	   assertTrue(RegisteredExpenseContractsPage.GetTransactionCount() >= 13339); 
    }
	@Test
    public void VerifyTop5AwardMethodsTransactionCount(){
		ContractsPage.GoToTop5DetailsPage(WidgetOption.Top5AwardMethods);
		HomePage.ShowWidgetDetails();
	   assertTrue(RegisteredExpenseContractsPage.GetTransactionCount() >= 13339); 
    }
	@Test
    public void VerifyTop5AgenciesTransactionCount(){
		ContractsPage.GoToTop5DetailsPage(WidgetOption.Top5Agencies);
		HomePage.ShowWidgetDetails();
	   assertTrue(RegisteredExpenseContractsPage.GetTransactionCount() >= 13339); 
    }
	@Test
    public void VerifyContractsByIndustriesTransactionCount(){
		ContractsPage.GoToTop5DetailsPage(WidgetOption.ContractsByIndustries);
		HomePage.ShowWidgetDetails();
	   assertTrue(RegisteredExpenseContractsPage.GetTransactionCount() >= 13339); 
    }
    @Test
    public void VerifyContractsBySizeTransactionCount(){
		ContractsPage.GoToTop5DetailsPage(WidgetOption.ContractsBySize);
		HomePage.ShowWidgetDetails();
	   assertTrue(RegisteredExpenseContractsPage.GetTransactionCount() >= 13339); 
    }*/

	/* ***************** Test Widget Transaction Amount ****************** */ 
	@Test
	public void VerifyTop5MasterAgreementsTransactionAmount(){
		Float transactionAmt = 6.16f;
		ContractsPage.GoToTop5DetailsPage(WidgetOption.Top5MasterAgreements);
		HomePage.ShowWidgetDetails();
		assertTrue(HomePage.GetTransactionAmount()>= transactionAmt);
	}
	@Test
	public void VerifyTop5MasterAgreementModificationsTransactionAmount(){
		Float transactionAmt = 52.28f;
		ContractsPage.GoToTop5DetailsPage(WidgetOption.Top5MasterAgreementModifications);
		HomePage.ShowWidgetDetails();
		assertTrue(HomePage.GetTransactionAmount()>= transactionAmt);
	}
	@Test
	public void VerifyTop5ContractsTransactionAmount(){
		Float transactionAmt = 14.09f;
		ContractsPage.GoToTop5DetailsPage(WidgetOption.Top5Contracts);
		HomePage.ShowWidgetDetails();
		assertTrue(HomePage.GetTransactionAmount()>= transactionAmt);
	}
	@Test
	public void VerifyTop5ContractAmountModificationsTransactionAmount(){
		Float transactionAmt = 2.1f;
		ContractsPage.GoToTop5DetailsPage(WidgetOption.Top5ContractAmountModifications);
		HomePage.ShowWidgetDetails();
		assertTrue(HomePage.GetTransactionAmount()>= transactionAmt);
	}
	@Test
	public void VerifyTop5PrimeVendorsTransactionAmount(){
		Float transactionAmt = 16.71f;
		ContractsPage.GoToTop5DetailsPage(WidgetOption.Top5PrimeVendors);
		HomePage.ShowWidgetDetails();
		assertTrue(HomePage.GetTransactionAmount()>= transactionAmt);
	}
	@Test
	public void VerifyTop5AwardMethodsTransactionAmount(){
		Float transactionAmt = 16.71f;
		ContractsPage.GoToTop5DetailsPage(WidgetOption.Top5AwardMethods);
		HomePage.ShowWidgetDetails();
		assertTrue(HomePage.GetTransactionAmount()>= transactionAmt);
	}
	@Test
	public void VerifyTop5AgenciesTransactionAmount(){
		Float transactionAmt = 16.71f;
		ContractsPage.GoToTop5DetailsPage(WidgetOption.Top5Agencies);
		HomePage.ShowWidgetDetails();
		assertTrue(HomePage.GetTransactionAmount()>= transactionAmt);
	}
	@Test
	public void VerifyContractsByIndustriesTransactionAmount(){
		Float transactionAmt = 16.71f;
		ContractsPage.GoToTop5DetailsPage(WidgetOption.ContractsByIndustries);
		HomePage.ShowWidgetDetails();
		assertTrue(HomePage.GetTransactionAmount()>= transactionAmt); 
	}
	@Test
	public void VerifyContractsBySizeTransactionAmount(){
		Float transactionAmt = 16.71f;
		ContractsPage.GoToTop5DetailsPage(WidgetOption.ContractsBySize);
		HomePage.ShowWidgetDetails();
		assertTrue(HomePage.GetTransactionAmount()>= transactionAmt);
	}
}
