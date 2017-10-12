package FunctionalContracts;

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
import pages.revenue.RevenuePage;
import utilities.NYCBaseTest;
import utilities.NYCDatabaseUtil;
import helpers.Driver;
import helpers.Helper;
import utilities.TestStatusReport;
//public class ActiveExpenseContractsDetailsTest extends TestStatusReport{
public class ActiveExpenseContractsDetailsTest extends NYCBaseTest {
	int year =  Integer.parseInt(NYCBaseTest.prop.getProperty("year"));
	@Before
	public void GoToPage() {
		ActiveExpenseContractsPage.GoTo();
		if(!(Helper.getCurrentSelectedYear()).equalsIgnoreCase(NYCBaseTest.prop.getProperty("CurrentYear")))
			HomePage.SelectYear(NYCBaseTest.prop.getProperty("CurrentYear"));
		HomePage.ShowWidgetDetails();
	}
	
	@After
	public void EndProgram()
	{
		Driver.Instance.quit();
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
	
	/* ***************** Test Widget Transaction Count ****************** */
	@Test
	public void VerifyTop5MasterAgreementsTransactionCount() throws SQLException {
		ContractsPage.GoToTop5DetailsPage(WidgetOption.Top5MasterAgreements);
		HomePage.ShowWidgetDetails();
		int NumOfAEContractsDetailsCount2016 =  NYCDatabaseUtil.getAEMasterContractsDetailsCount(2016,'B');
		int numOfAEContractsDetailsCountapp = ActiveExpenseContractsPage.GetTransactionCount();
		assertEquals("Number of master contracts widget Details page table count did not match", numOfAEContractsDetailsCountapp, NumOfAEContractsDetailsCount2016); 
		
		String WidgetDetailsTitle =  "Checks Capital Spending Transactions";
		String WidgetDetailsTitleApp = HomePage.DetailsPagetitle();
	    assertEquals("Total Spending Agencies Widget title did not match", WidgetDetailsTitle, WidgetDetailsTitleApp); 
	    
	    String NumOfBudgetDetailsAmountDB =  NYCDatabaseUtil.getBudgetDetailsAmount(2016,'B');
		String numOfBudgetDetailsAmountapp = HomePage.GetTransactionAmount1();
	    assertEquals("Number ofRevenue widget Details page table count did not match", numOfBudgetDetailsAmountapp, NumOfBudgetDetailsAmountDB); 
	}
	@Test
	public void VerifyTop5MasterAgreementModificationsTransactionCount() throws SQLException {
		ContractsPage.GoToTop5DetailsPage(WidgetOption.Top5MasterAgreementModifications);
		HomePage.ShowWidgetDetails();
		int NumOfAEContractsDetailsCount2016 =  NYCDatabaseUtil.getAEMasterContractsModificationDetailsCount(2016,'B');
		int numOfAEContractsDetailsCountapp = ActiveExpenseContractsPage.GetTransactionCount();
		assertEquals("Number of master Contracts modification widget Details page table count did not match", numOfAEContractsDetailsCountapp, NumOfAEContractsDetailsCount2016);
		
		String WidgetDetailsTitle =  "Checks Capital Spending Transactions";
		String WidgetDetailsTitleApp = HomePage.DetailsPagetitle();
	    assertEquals("Total Spending Agencies Widget title did not match", WidgetDetailsTitle, WidgetDetailsTitleApp); 
	    
	    String NumOfBudgetDetailsAmountDB =  NYCDatabaseUtil.getBudgetDetailsAmount(2016,'B');
		String numOfBudgetDetailsAmountapp = HomePage.GetTransactionAmount1();
	    assertEquals("Number ofRevenue widget Details page table count did not match", numOfBudgetDetailsAmountapp, NumOfBudgetDetailsAmountDB); 
	}
	@Test
	public void VerifyTop5ContractsTransactionCount() throws SQLException {
		ContractsPage.GoToTop5DetailsPage(WidgetOption.Top5Contracts);
		
		int NumOfAEContractsDetailsCount2016 =  NYCDatabaseUtil.getAEContractsDetailsCount(2016,'B');
		int numOfAEContractsDetailsCountapp = ActiveExpenseContractsPage.GetTransactionCount();
		assertEquals("Number of master Contracts  widget Details page table count did not match", numOfAEContractsDetailsCountapp, NumOfAEContractsDetailsCount2016); 
		
		String WidgetDetailsTitle =  "Checks Capital Spending Transactions";
		String WidgetDetailsTitleApp = HomePage.DetailsPagetitle();
	    assertEquals("Total Spending Agencies Widget title did not match", WidgetDetailsTitle, WidgetDetailsTitleApp); 
	    
	    String NumOfBudgetDetailsAmountDB =  NYCDatabaseUtil.getBudgetDetailsAmount(2016,'B');
		String numOfBudgetDetailsAmountapp = HomePage.GetTransactionAmount1();
	    assertEquals("Number ofRevenue widget Details page table count did not match", numOfBudgetDetailsAmountapp, NumOfBudgetDetailsAmountDB); 
	}
	@Test
	public void VerifyTop5ContractAmountModificationsTransactionCount() throws SQLException {
		ContractsPage.GoToTop5DetailsPage(WidgetOption.Top5ContractAmountModifications);
	
		int NumOfAEContractsDetailsCount2016 =  NYCDatabaseUtil.getAEContractsModificationDetailsCount(2016,'B');
		int numOfAEContractsDetailsCountapp = ActiveExpenseContractsPage.GetTransactionCount();
		assertEquals("Number of master Contracts modification widget Details page table count did not match", numOfAEContractsDetailsCountapp, NumOfAEContractsDetailsCount2016); 
		
		String WidgetDetailsTitle =  "Checks Capital Spending Transactions";
		String WidgetDetailsTitleApp = HomePage.DetailsPagetitle();
	    assertEquals("Total Spending Agencies Widget title did not match", WidgetDetailsTitle, WidgetDetailsTitleApp); 
	    
	    String NumOfBudgetDetailsAmountDB =  NYCDatabaseUtil.getBudgetDetailsAmount(2016,'B');
		String numOfBudgetDetailsAmountapp = HomePage.GetTransactionAmount1();
	    assertEquals("Number ofRevenue widget Details page table count did not match", numOfBudgetDetailsAmountapp, NumOfBudgetDetailsAmountDB); 
	}
	@Test
	public void VerifyTop5PrimeVendorsTransactionCount() throws SQLException {
		ContractsPage.GoToTop5DetailsPage(WidgetOption.Top5PrimeVendors);
	
		int NumOfAEContractsDetailsCount2016 =  NYCDatabaseUtil.getAEContractsDetailsCount(2016,'B');
		int numOfAEContractsDetailsCountapp = ActiveExpenseContractsPage.GetTransactionCount();
		assertEquals("Number of master Contracts Prime Vendors widget Details page table count did not match", numOfAEContractsDetailsCountapp, NumOfAEContractsDetailsCount2016);
		
		String WidgetDetailsTitle =  "Checks Capital Spending Transactions";
		String WidgetDetailsTitleApp = HomePage.DetailsPagetitle();
	    assertEquals("Total Spending Agencies Widget title did not match", WidgetDetailsTitle, WidgetDetailsTitleApp); 
	    
	    String NumOfBudgetDetailsAmountDB =  NYCDatabaseUtil.getBudgetDetailsAmount(2016,'B');
		String numOfBudgetDetailsAmountapp = HomePage.GetTransactionAmount1();
	    assertEquals("Number ofRevenue widget Details page table count did not match", numOfBudgetDetailsAmountapp, NumOfBudgetDetailsAmountDB); 
	}
	@Test
	public void VerifyTop5AwardMethodsTransactionCount() throws SQLException {
		ContractsPage.GoToTop5DetailsPage(WidgetOption.Top5AwardMethods);
		HomePage.ShowWidgetDetails();
		int NumOfAEContractsDetailsCount2016 =  NYCDatabaseUtil.getAEContractsDetailsCount(2016,'B');
		int numOfAEContractsDetailsCountapp = ActiveExpenseContractsPage.GetTransactionCount();
		assertEquals("Number of master Contracts Award Method widget Details page table count did not match", numOfAEContractsDetailsCountapp, NumOfAEContractsDetailsCount2016);

		String WidgetDetailsTitle =  "Checks Capital Spending Transactions";
		String WidgetDetailsTitleApp = HomePage.DetailsPagetitle();
	    assertEquals("Total Spending Agencies Widget title did not match", WidgetDetailsTitle, WidgetDetailsTitleApp); 
	    
	    String NumOfBudgetDetailsAmountDB =  NYCDatabaseUtil.getBudgetDetailsAmount(2016,'B');
		String numOfBudgetDetailsAmountapp = HomePage.GetTransactionAmount1();
	    assertEquals("Number ofRevenue widget Details page table count did not match", numOfBudgetDetailsAmountapp, NumOfBudgetDetailsAmountDB); 
	}
	@Test
	public void VerifyTop5AgenciesTransactionCount() throws SQLException {
		ContractsPage.GoToTop5DetailsPage(WidgetOption.Top5Agencies);
		HomePage.ShowWidgetDetails();
		int NumOfAEContractsDetailsCount2016 =  NYCDatabaseUtil.getAEContractsDetailsCount(2016,'B');
		int numOfAEContractsDetailsCountapp = ActiveExpenseContractsPage.GetTransactionCount();
		assertEquals("Number of Contracts Agencies widget Details page table count did not match", numOfAEContractsDetailsCountapp, NumOfAEContractsDetailsCount2016); 
		
		String WidgetDetailsTitle =  "Checks Capital Spending Transactions";
		String WidgetDetailsTitleApp = HomePage.DetailsPagetitle();
	    assertEquals("Total Spending Agencies Widget title did not match", WidgetDetailsTitle, WidgetDetailsTitleApp); 
	    
	    String NumOfBudgetDetailsAmountDB =  NYCDatabaseUtil.getBudgetDetailsAmount(2016,'B');
		String numOfBudgetDetailsAmountapp = HomePage.GetTransactionAmount1();
	    assertEquals("Number ofRevenue widget Details page table count did not match", numOfBudgetDetailsAmountapp, NumOfBudgetDetailsAmountDB); 
	}
	@Test
	public void VerifyContractsByIndustriesTransactionCount() throws SQLException {
		ContractsPage.GoToTop5DetailsPage(WidgetOption.ContractsByIndustries);
		HomePage.ShowWidgetDetails();
		int NumOfAEContractsDetailsCount2016 =  NYCDatabaseUtil.getAEContractsDetailsCount(2016,'B');
		int numOfAEContractsDetailsCountapp = ActiveExpenseContractsPage.GetTransactionCount();
		assertEquals("Number of  Contracts by Industies widget Details page table count did not match", numOfAEContractsDetailsCountapp, NumOfAEContractsDetailsCount2016); 
		
		String WidgetDetailsTitle =  "Checks Capital Spending Transactions";
		String WidgetDetailsTitleApp = HomePage.DetailsPagetitle();
	    assertEquals("Total Spending Agencies Widget title did not match", WidgetDetailsTitle, WidgetDetailsTitleApp); 
	    
	    String NumOfBudgetDetailsAmountDB =  NYCDatabaseUtil.getBudgetDetailsAmount(2016,'B');
		String numOfBudgetDetailsAmountapp = HomePage.GetTransactionAmount1();
	    assertEquals("Number ofRevenue widget Details page table count did not match", numOfBudgetDetailsAmountapp, NumOfBudgetDetailsAmountDB); 
	}
	@Test
	public void VerifyContractsBySizeTransactionCount() throws SQLException {
		ContractsPage.GoToTop5DetailsPage(WidgetOption.ContractsBySize);
	
		int NumOfAEContractsDetailsCount2016 =  NYCDatabaseUtil.getAEContractsDetailsCount(2016,'B');
		int numOfAEContractsDetailsCountapp = ActiveExpenseContractsPage.GetTransactionCount();
		assertEquals("Number of Contracts by size  widget Details page table count did not match", numOfAEContractsDetailsCountapp, NumOfAEContractsDetailsCount2016);
		
		String WidgetDetailsTitle =  "Checks Capital Spending Transactions";
		String WidgetDetailsTitleApp = HomePage.DetailsPagetitle();
	    assertEquals("Total Spending Agencies Widget title did not match", WidgetDetailsTitle, WidgetDetailsTitleApp); 
	    
	    String NumOfBudgetDetailsAmountDB =  NYCDatabaseUtil.getBudgetDetailsAmount(2016,'B');
		String numOfBudgetDetailsAmountapp = HomePage.GetTransactionAmount1();
	    assertEquals("Number ofRevenue widget Details page table count did not match", numOfBudgetDetailsAmountapp, NumOfBudgetDetailsAmountDB); 
	}

	/* ***************** Test Widget Transaction Total Amount ****************** */
	@Test
	public void VerifyTop5MasterAgreementsTransactionAmount(){
		Float transactionAmt = 26.3f;
		ContractsPage.GoToTop5DetailsPage(WidgetOption.Top5MasterAgreements);
		HomePage.ShowWidgetDetails();
		assertTrue(HomePage.GetTransactionAmount()>= transactionAmt);
	}
}
