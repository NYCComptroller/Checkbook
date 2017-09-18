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

//public class ActiveExpenseContractsTest extends NYCBaseTest {
	public class ActiveExpenseContractsTest extends TestStatusReport{

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
		Integer activeExpenseContractsNumOfMasterAgreementContractsFY2016 = NYCDatabaseUtil.getAEMasterContractsCount(2016, 'B');
		Integer numOfMasterAgreementContracts = ContractsPage.GetTop5WidgetTotalCount(WidgetOption.Top5MasterAgreements);
		assertEquals("Number of Master Agreement Contracts in the Active Expense Contracts did not match",numOfMasterAgreementContracts,activeExpenseContractsNumOfMasterAgreementContractsFY2016);
	}
	@Test
	public void VerifyNumOfMasterAgreementModificationsContracts() throws SQLException {
		Integer activeExpenseContractsNumOfMasterAgreementModificationsContractsFY2016 = NYCDatabaseUtil.getAEMasterContractsModificationCount(2016, 'B');
		Integer numOfMasterAgreementContracts = ContractsPage.GetTop5WidgetTotalCount(WidgetOption.Top5MasterAgreementModifications);
		assertEquals("Number of Master Agreement Modifications Contracts in the Active Expense Contracts did not match",numOfMasterAgreementContracts,activeExpenseContractsNumOfMasterAgreementModificationsContractsFY2016);
	}
	@Test
	public void VerifyNumOfContracts() throws SQLException {
		Integer activeExpenseContractsNumOfContractsFY2016 = NYCDatabaseUtil.getAEContractsCount(2016, 'B');
		Integer numOfContracts = ContractsPage.GetTop5WidgetTotalCount(WidgetOption.Top5Contracts);
		assertEquals("Number of Contracts in the Active Expense Contracts did not match",numOfContracts, activeExpenseContractsNumOfContractsFY2016);
	}
	@Test
	public void VerifyNumOfContractAmountModificationsContracts() throws SQLException {
		Integer activeExpenseContractsNumOfContractAmountModificationsContractsFY2016 = NYCDatabaseUtil.getAEContractsModificationCount(2016, 'B');
		Integer numOfContractAmountModificationsContracts = ContractsPage.GetTop5WidgetTotalCount(WidgetOption.Top5ContractAmountModifications);
		assertEquals("Number of Contract modifications in the Active Expense Contracts did not match",numOfContractAmountModificationsContracts,activeExpenseContractsNumOfContractAmountModificationsContractsFY2016);
	}
	@Test
	public void VerifyNumOfPrimeVendorsContracts() throws SQLException {
		Integer activeExpenseContractsNumOfPrimeVendorsContractsFY2016 = NYCDatabaseUtil.getAEContractsPrimeVendorsCount(2016, 'B');
		Integer numOfPrimeVendorsContracts = ContractsPage.GetTop5WidgetTotalCount(WidgetOption.Top5PrimeVendors);
		assertEquals("Number of Prime vendors in the Active Expense Contracts did not match",numOfPrimeVendorsContracts,activeExpenseContractsNumOfPrimeVendorsContractsFY2016);
	}
	@Test
	public void VerifyNumOfAwardMethodsContracts() throws SQLException {
		Integer activeExpenseContractsNumOfAwardMethodsContractsFY2016 = NYCDatabaseUtil.getAEContractsAwardMethodsCount(2016, 'B');
		Integer numOfAwardMethodsContracts = ContractsPage.GetTop5WidgetTotalCount(WidgetOption.Top5AwardMethods);
		assertEquals("Number of Awardmethods in the Active Expense Contracts did not match",numOfAwardMethodsContracts,activeExpenseContractsNumOfAwardMethodsContractsFY2016);
	}
	@Test
	public void VerifyNumOfAgenciesContracts() throws SQLException {
		Integer activeExpenseContractsNumOfAgenciesContractsFY2016 = NYCDatabaseUtil.getAEContractsAgenciesCount(2016, 'B');
		Integer numOfAgenciesContracts = ContractsPage.GetTop5WidgetTotalCount(WidgetOption.Top5Agencies);
		assertEquals("Number of Agencies in the Active Expense Contracts did not match",numOfAgenciesContracts,activeExpenseContractsNumOfAgenciesContractsFY2016);
	}
	@Test
	public void VerifyNumOfContractsByIndustries() throws SQLException {
		Integer activeExpenseContractsNumOfContractsByIndustriesFY2016 = NYCDatabaseUtil.getAEContractsIndustriesCount(2016, 'B');
		Integer numOfContractsByIndustries = ContractsPage.GetTop5WidgetTotalCount(WidgetOption.ContractsByIndustries);
		assertEquals("Number of Contracts in the  Active Expense contract by Industry  widget did not match",numOfContractsByIndustries,activeExpenseContractsNumOfContractsByIndustriesFY2016);
	}
	@Test
	public void VerifyNumOfContractsBySize() throws SQLException {
		Integer activeExpenseContractsNumOfContractsBySizeFY2016 = NYCDatabaseUtil.getAEContractsSizeCount(2016, 'B');
		Integer numOfContractsBySize = ContractsPage.GetTop5WidgetTotalCount(WidgetOption.ContractsBySize);
		assertEquals("Number of Contracts in the  Active Expense Contracts by Size widget did not match",numOfContractsBySize,activeExpenseContractsNumOfContractsBySizeFY2016);
	}
}
