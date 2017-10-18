package FunctionalContracts;

import static org.junit.Assert.assertEquals;

import static org.junit.Assert.assertTrue;

import java.sql.SQLException;

import org.junit.Before;
import org.junit.Test;

import navigation.TopNavigation.Contracts.ActiveExpenseContracts;
import pages.contracts.ActiveExpenseContractsPage;
import pages.contracts.ContractsPage;
import pages.contracts.ContractsPage.WidgetOption;
import pages.home.HomePage;
import utilities.NYCBaseTest;
import utilities.NYCDatabaseUtil;
import utilities.TestStatusReport;
import helpers.Helper;

public class ActiveExpenseContractsTest extends NYCBaseTest {
	//public class ActiveExpenseContractsTest extends TestStatusReport{
	int year =  Integer.parseInt(NYCBaseTest.prop.getProperty("year"));
	@Before
	public void GoToPage() {
		ContractsPage.GoTo();
		if (!ActiveExpenseContracts.isAt()) {
			ActiveExpenseContractsPage.GoTo();
		}
		if (!(Helper.getCurrentSelectedYear())
				.equalsIgnoreCase(NYCBaseTest.prop.getProperty("CurrentYear")))
			HomePage.SelectYear(NYCBaseTest.prop.getProperty("CurrentYear"));
		HomePage.ShowWidgetDetails();
	}

	/* ***************** Test Widget Counts ****************** */
	@Test
	public void VerifyNumOfMasterAgreementContracts() throws SQLException {
		Integer activeExpenseContractsNumOfMasterAgreementContractsDB = NYCDatabaseUtil.getAEMasterContractsCount(year, 'B');
		Integer numOfMasterAgreementContractsApp = ContractsPage.GetTop5WidgetTotalCount(WidgetOption.Top5MasterAgreements);
		assertEquals("Number of Master Agreement Contracts in the Active Expense Contracts did not match",numOfMasterAgreementContractsApp,activeExpenseContractsNumOfMasterAgreementContractsDB);
	}
	@Test
	public void VerifyNumOfMasterAgreementModificationsContracts() throws SQLException {
		Integer activeExpenseContractsNumOfMasterAgreementModificationsContractsDB = NYCDatabaseUtil.getAEMasterContractsModificationCount(year, 'B');
		Integer numOfMasterAgreementContractsApp = ContractsPage.GetTop5WidgetTotalCount(WidgetOption.Top5MasterAgreementModifications);
		assertEquals("Number of Master Agreement Modifications Contracts in the Active Expense Contracts did not match",numOfMasterAgreementContractsApp,activeExpenseContractsNumOfMasterAgreementModificationsContractsDB);
	}
	@Test
	public void VerifyNumOfContracts() throws SQLException {
		Integer activeExpenseContractsNumOfContractsDB = NYCDatabaseUtil.getAEContractsCount(year, 'B');
		Integer numOfContractsApp = ContractsPage.GetTop5WidgetTotalCount(WidgetOption.Top5Contracts);
		assertEquals("Number of Contracts in the Active Expense Contracts did not match",numOfContractsApp, activeExpenseContractsNumOfContractsDB);
	}
	@Test
	public void VerifyNumOfContractAmountModificationsContracts() throws SQLException {
		Integer activeExpenseContractsNumOfContractAmountModificationsContractsDB = NYCDatabaseUtil.getAEContractsModificationCount(year, 'B');
		Integer numOfContractAmountModificationsContractsApp = ContractsPage.GetTop5WidgetTotalCount(WidgetOption.Top5ContractAmountModifications);
		assertEquals("Number of Contract modifications in the Active Expense Contracts did not match",numOfContractAmountModificationsContractsApp,activeExpenseContractsNumOfContractAmountModificationsContractsDB);
	}
	@Test
	public void VerifyNumOfPrimeVendorsContracts() throws SQLException {
		Integer activeExpenseContractsNumOfPrimeVendorsContractsDB = NYCDatabaseUtil.getAEContractsPrimeVendorsCount(year, 'B');
		Integer numOfPrimeVendorsContractsApp = ContractsPage.GetTop5WidgetTotalCount(WidgetOption.Top5PrimeVendors);
		assertEquals("Number of Prime vendors in the Active Expense Contracts did not match",numOfPrimeVendorsContractsApp,activeExpenseContractsNumOfPrimeVendorsContractsDB);
	}
	@Test
	public void VerifyNumOfAwardMethodsContracts() throws SQLException {
		Integer activeExpenseContractsNumOfAwardMethodsContractsDB = NYCDatabaseUtil.getAEContractsAwardMethodsCount(year, 'B');
		Integer numOfAwardMethodsContractsApp = ContractsPage.GetTop5WidgetTotalCount(WidgetOption.Top5AwardMethods);
		assertEquals("Number of Awardmethods in the Active Expense Contracts did not match",numOfAwardMethodsContractsApp,activeExpenseContractsNumOfAwardMethodsContractsDB);
	}
	@Test
	public void VerifyNumOfAgenciesContracts() throws SQLException {
		Integer activeExpenseContractsNumOfAgenciesContractsDB = NYCDatabaseUtil.getAEContractsAgenciesCount(year, 'B');
		Integer numOfAgenciesContractsApp = ContractsPage.GetTop5WidgetTotalCount(WidgetOption.Top5Agencies);
		assertEquals("Number of Agencies in the Active Expense Contracts did not match",numOfAgenciesContractsApp,activeExpenseContractsNumOfAgenciesContractsDB);
	}
	@Test
	public void VerifyNumOfContractsByIndustries() throws SQLException {
		Integer activeExpenseContractsNumOfContractsByIndustriesDB = NYCDatabaseUtil.getAEContractsIndustriesCount(year, 'B');
		Integer numOfContractsByIndustries = ContractsPage.GetTop5WidgetTotalCount(WidgetOption.ContractsByIndustries);
		assertEquals("Number of Contracts in the  Active Expense contract by Industry  widget did not match",numOfContractsByIndustries,activeExpenseContractsNumOfContractsByIndustriesDB);
	}
	@Test
	public void VerifyNumOfContractsBySize() throws SQLException {
		Integer activeExpenseContractsNumOfContractsBySizeDB = NYCDatabaseUtil.getAEContractsSizeCount(year, 'B');
		Integer numOfContractsBySize = ContractsPage.GetTop5WidgetTotalCount(WidgetOption.ContractsBySize);
		assertEquals("Number of Contracts in the  Active Expense Contracts by Size widget did not match",numOfContractsBySize,activeExpenseContractsNumOfContractsBySizeDB);
	}
}
