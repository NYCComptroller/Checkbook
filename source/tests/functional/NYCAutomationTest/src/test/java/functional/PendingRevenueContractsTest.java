package functional;

import static org.junit.Assert.assertEquals;

import java.sql.SQLException;

import org.junit.Before;
import org.junit.Test;

import navigation.TopNavigation.Contracts.PendingExpenseContracts;
import navigation.TopNavigation.Contracts.PendingRevenueContracts;
import pages.contracts.ContractsPage;
import pages.contracts.PendingExpenseContractsPage;
import pages.contracts.PendingRevenueContractsPage;
import pages.contracts.ContractsPage.WidgetOption;
import pages.home.HomePage;
import utilities.NYCBaseTest;
import utilities.NYCDatabaseUtil;
import helpers.Helper;

public class PendingRevenueContractsTest extends NYCBaseTest {
	@Before
    public void GoToPage() {
	   if (!PendingRevenueContracts.isAt())
		   PendingRevenueContractsPage.GoTo();
	   HomePage.ShowWidgetDetails();
    }

	/* ***************** Test Widget Counts ****************** */
	@Test
	public void VerifyNumOfContracts() throws SQLException {
	 	Integer numOfContractsFY2016 = NYCDatabaseUtil.getPRContractsCount(2016,'B');
        Integer numOfContracts = ContractsPage.GetTop5WidgetTotalCount(WidgetOption.Top5Contracts);
        assertEquals("Number of Contracts in the Pending Revenue Contracts did not match", numOfContracts, numOfContractsFY2016);
	}
	@Test
	public void VerifyNumOfContractsAmountModificationsContracts() throws SQLException {
	 	Integer numOfContractsAmountModificationsContractsFY2016 =NYCDatabaseUtil.getPRContractsModificationsCount(2016,'B');
        Integer numOfContractsAmountModificationsContracts = ContractsPage.GetTop5WidgetTotalCount(WidgetOption.Top5ContractsAmountModifications);
        assertEquals("Number of Contracts Amount Modifications Contracts in the Pending Revenue Contracts did not match", numOfContractsAmountModificationsContracts, numOfContractsAmountModificationsContractsFY2016);
	}
	@Test
	public void VerifyNumOfPrimeVendorsContracts() throws SQLException {
	 	Integer numOfPrimeVendorsContractsFY2016 = NYCDatabaseUtil.getPRContractsPrimeVendorsCount(2016,'B');
        Integer numOfPrimeVendorsContracts = ContractsPage.GetTop5WidgetTotalCount(WidgetOption.Top5PrimeVendors);
        assertEquals("Number of Prime Vendors Contracts By Industries in the Pending Revenue Contracts did not match", numOfPrimeVendorsContracts, numOfPrimeVendorsContractsFY2016);
	}
	@Test
	public void VerifyNumOfAwardMethodsContracts() throws SQLException {
	 	Integer numOfAwardMethodsContractsFY2016 = NYCDatabaseUtil.getPRContractsAwardMethodsCount(2016,'B');
        Integer numOfAwardMethodsContracts = ContractsPage.GetTop5WidgetTotalCount(WidgetOption.TopAwardMethods);
        assertEquals("Number of Award Methods Contracts in the Pending Revenue Contracts did not match", numOfAwardMethodsContracts, numOfAwardMethodsContractsFY2016);
	}
	@Test
	public void VerifyNumOfAgenciesContracts() throws SQLException {
	 	Integer numOfAgenciesContractsFY2016 = NYCDatabaseUtil.getPRContractsAgenciesCount(2016,'B');
        Integer numOfAgenciesContracts = ContractsPage.GetTop5WidgetTotalCount(WidgetOption.Top5Agencies);
        assertEquals("Number of Agencies Contracts in the Pending Revenue Contracts did not match", numOfAgenciesContracts, numOfAgenciesContractsFY2016);
	}
	@Test
	public void VerifyNumOfContractsByIndustries() throws SQLException {
	 	Integer numOfContractsByIndustriesFY2016 = NYCDatabaseUtil.getPRContractsIndustriesCount(2016,'B');
        Integer numOfContractsByIndustries = ContractsPage.GetTop5WidgetTotalCount(WidgetOption.ContractsByIndustries);
        assertEquals("Number of Contracts By Industries in the Pending Revenue Contracts did not match", numOfContractsByIndustries, numOfContractsByIndustriesFY2016);
	}	
	@Test
	public void VerifyNumOfContractsBySize() throws SQLException {
	 	Integer numOfContractsBySizeFY2016 = NYCDatabaseUtil.getPRContractsSizeCount(2016,'B');
        Integer numOfContractsBySize = ContractsPage.GetTop5WidgetTotalCount(WidgetOption.ContractsBySize);
        assertEquals("Number of Contracts By Size in the Pending Revenue Contracts did not match", numOfContractsBySize, numOfContractsBySizeFY2016);
	}
}
