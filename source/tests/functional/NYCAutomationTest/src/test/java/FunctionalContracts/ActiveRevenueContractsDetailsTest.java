package FunctionalContracts;

import static org.junit.Assert.assertEquals;

import static org.junit.Assert.assertTrue;

import java.sql.SQLException;

import org.junit.Before;
import org.junit.Test;

import pages.contracts.ActiveRevenueContractsPage;
import pages.contracts.ContractsPage;
import pages.contracts.ContractsPage.WidgetOption;
import pages.home.HomePage;
import utilities.NYCBaseTest;
import utilities.NYCDatabaseUtil;
import helpers.Helper;
import utilities.TestStatusReport;
public class ActiveRevenueContractsDetailsTest extends TestStatusReport{

//public class ActiveRevenueContractsDetailsTest extends NYCBaseTest{

	@Before
	public void GoToPage() {
		ActiveRevenueContractsPage.GoTo();
		if(!(Helper.getCurrentSelectedYear()).equalsIgnoreCase(NYCBaseTest.prop.getProperty("CurrentYear")))
			HomePage.SelectYear(NYCBaseTest.prop.getProperty("CurrentYear"));
		HomePage.ShowWidgetDetails();
	}

	/* ***************** Test Widget Transaction Count ****************** */
	@Test
	public void VerifyTop5ContractsTransactionCount() throws SQLException {
		ContractsPage.GoToTop5DetailsPage(WidgetOption.Top5Contracts);
		HomePage.ShowWidgetDetails();
		int NumOfARContractsDetailsCount2016 =  NYCDatabaseUtil.getARContractsDetailsCount(2016,'B');
		int numOfARContractsDetailsCountapp = ActiveRevenueContractsPage.GetTransactionCount();
		assertEquals("Number of Active Revenue Contracts  widget Details page table count did not match", numOfARContractsDetailsCountapp, NumOfARContractsDetailsCount2016); 
	}
	@Test
	public void VerifyTop5ContractAmountModificationsTransactionCount() throws SQLException {
		ContractsPage.GoToTop5DetailsPage(WidgetOption.Top5ContractAmountModifications);
		HomePage.ShowWidgetDetails();
		int NumOfARContractsDetailsCount2016 =  NYCDatabaseUtil.getARContractsModificationsDetailsCount(2016,'B');
		int numOfARContractsDetailsCountapp = ActiveRevenueContractsPage.GetTransactionCount();
		assertEquals("Number of Active Revenue Contracts Modifications widget Details page table count did not match", numOfARContractsDetailsCountapp, NumOfARContractsDetailsCount2016); 
	}
	@Test
	public void VerifyTop5PrimeVendorsTransactionCount() throws SQLException {
		ContractsPage.GoToTop5DetailsPage(WidgetOption.Top5PrimeVendors);
		HomePage.ShowWidgetDetails();
		int NumOfARContractsDetailsCount2016 =  NYCDatabaseUtil.getARContractsDetailsCount(2016,'B');
		int numOfARContractsDetailsCountapp = ActiveRevenueContractsPage.GetTransactionCount(); 
		assertEquals("Number of Active Revenue Contracts Prime Vendors  widget Details page table count did not match", numOfARContractsDetailsCountapp, NumOfARContractsDetailsCount2016); 
	}
	@Test
	public void VerifyTop5AwardMethodsTransactionCount() throws SQLException {
		ContractsPage.GoToTop5DetailsPage(WidgetOption.Top5AwardMethods);
		HomePage.ShowWidgetDetails();
		int NumOfARContractsDetailsCount2016 =  NYCDatabaseUtil.getARContractsDetailsCount(2016,'B');
		int numOfARContractsDetailsCountapp = ActiveRevenueContractsPage.GetTransactionCount();
		assertEquals("Number of Active Revenue Contracts Award Method  widget Details page table count did not match", numOfARContractsDetailsCountapp, NumOfARContractsDetailsCount2016); 
	}
	@Test
	public void VerifyTop5AgenciesTransactionCount() throws SQLException {
		ContractsPage.GoToTop5DetailsPage(WidgetOption.Top5Agencies);
		HomePage.ShowWidgetDetails();
		int NumOfARContractsDetailsCount2016 =  NYCDatabaseUtil.getARContractsDetailsCount(2016,'B');
		int numOfARContractsDetailsCountapp = ActiveRevenueContractsPage.GetTransactionCount();
		assertEquals("Number of Active Revenue Contracts Agencies  widget Details page table count did not match", numOfARContractsDetailsCountapp, NumOfARContractsDetailsCount2016); 
	}
	@Test
	public void VerifyContractsByIndustriesTransactionCount() throws SQLException {
		ContractsPage.GoToTop5DetailsPage(WidgetOption.ContractsByIndustries);
		HomePage.ShowWidgetDetails();
		int NumOfARContractsDetailsCount2016 =  NYCDatabaseUtil.getARContractsDetailsCount(2016,'B');
		int numOfARContractsDetailsCountapp = ActiveRevenueContractsPage.GetTransactionCount();
		assertEquals("Number of Active Revenue Contracts Industries  widget Details page table count did not match", numOfARContractsDetailsCountapp, NumOfARContractsDetailsCount2016); 
	}
	@Test
	public void VerifyContractsBySizeTransactionCount() throws SQLException {
		ContractsPage.GoToTop5DetailsPage(WidgetOption.ContractsBySize);
		HomePage.ShowWidgetDetails();
		int NumOfARContractsDetailsCount2016 =  NYCDatabaseUtil.getARContractsDetailsCount(2016,'B');
		int numOfARContractsDetailsCountapp = ActiveRevenueContractsPage.GetTransactionCount();
		assertEquals("Number of Active Revenue Contracts size  widget Details page table count did not match", numOfARContractsDetailsCountapp, NumOfARContractsDetailsCount2016); 
	}

	/* ***************** Test Widget Transaction Total Amount ****************** */
	@Test
	public void VerifyTop5ContractsTransactionAmount() {
		Float transactionAmt = 7.28f;
		ContractsPage.GoToTop5DetailsPage(WidgetOption.Top5Contracts);
		HomePage.ShowWidgetDetails();
		assertTrue(HomePage.GetTransactionAmount()>= transactionAmt);
	}
	@Test
	public void VerifyTop5ContractAmountModificationsTransactionAmount() {
		Float transactionAmt = 1.95f;
		ContractsPage.GoToTop5DetailsPage(WidgetOption.Top5ContractAmountModifications);
		HomePage.ShowWidgetDetails();
		assertTrue(HomePage.GetTransactionAmount()>= transactionAmt);
	}
	@Test
	public void VerifyTop5PrimeVendorsTransactionAmount() {
		Float transactionAmt = 7.28f;
		ContractsPage.GoToTop5DetailsPage(WidgetOption.Top5PrimeVendors);
		HomePage.ShowWidgetDetails();
		assertTrue(HomePage.GetTransactionAmount()>= transactionAmt);
	}
	@Test
	public void VerifyTop5AwardMethodsTransactionAmount() {
		Float transactionAmt = 7.28f;
		ContractsPage.GoToTop5DetailsPage(WidgetOption.Top5AwardMethods);
		HomePage.ShowWidgetDetails();
		assertTrue(HomePage.GetTransactionAmount()>= transactionAmt);
	}
	@Test
	public void VerifyTop5AgenciesTransactionAmount() {
		Float transactionAmt = 7.28f;
		ContractsPage.GoToTop5DetailsPage(WidgetOption.Top5Agencies);
		HomePage.ShowWidgetDetails();
		assertTrue(HomePage.GetTransactionAmount()>= transactionAmt);
	}
	@Test
	public void VerifyContractsByIndustriesTransactionAmount() {
		Float transactionAmt = 7.28f;
		ContractsPage.GoToTop5DetailsPage(WidgetOption.ContractsByIndustries);
		HomePage.ShowWidgetDetails();
		assertTrue(HomePage.GetTransactionAmount()>= transactionAmt); 
	}
	@Test
	public void VerifyContractsBySizeTransactionAmount() {
		Float transactionAmt = 7.28f;
		ContractsPage.GoToTop5DetailsPage(WidgetOption.ContractsBySize);
		HomePage.ShowWidgetDetails();
		assertTrue(HomePage.GetTransactionAmount()>= transactionAmt);
	}
}
