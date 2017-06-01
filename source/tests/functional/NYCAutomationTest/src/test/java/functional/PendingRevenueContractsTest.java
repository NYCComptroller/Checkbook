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

public class PendingRevenueContractsTest extends NYCBaseTest{
	@Before
    public void GoToPage(){
	 // if(!(Helper.getCurrentSelectedYear()).equalsIgnoreCase(NYCBaseTest.prop.getProperty("CurrentYear")))
		  // HomePage.SelectYear(NYCBaseTest.prop.getProperty("CurrentYear"));
	   if (!PendingRevenueContracts.isAt())
		   PendingRevenueContractsPage.GoTo();
	   HomePage.ShowWidgetDetails();
    }
	

	/* ***************** Test Widget Counts ****************** */
	@Test
	public void VerifyNumOfContracts()throws SQLException{
	 	int numOfContractsFY2016 = NYCDatabaseUtil.getPRContractsCount(2016,'B');
        int numOfContracts = Helper.stringToInt(ContractsPage.GetTop5WidgetTotalCount(WidgetOption.Top5Contracts));
        assertEquals("Number of Contracts in the Pending Revenue Contracts did not match", numOfContracts, numOfContractsFY2016);
	}
	@Test
	public void VerifyNumOfContractsAmountModificationsContracts()throws SQLException{
	 	int numOfContractsAmountModificationsContractsFY2016 =NYCDatabaseUtil.getPRContractsModificationsCount(2016,'B');
        int numOfContractsAmountModificationsContracts = Helper.stringToInt(ContractsPage.GetTop5WidgetTotalCount(WidgetOption.Top5ContractsAmountModifications));
        assertEquals("Number of Contracts Amount Modifications Contracts in the Pending Revenue Contracts did not match", numOfContractsAmountModificationsContracts, numOfContractsAmountModificationsContractsFY2016);
	}
	@Test
	public void VerifyNumOfPrimeVendorsContracts()throws SQLException{
	 	int numOfPrimeVendorsContractsFY2016 = NYCDatabaseUtil.getPRContractsPrimeVendorsCount(2016,'B');
        int numOfPrimeVendorsContracts = Helper.stringToInt(ContractsPage.GetTop5WidgetTotalCount(WidgetOption.Top5PrimeVendors));
        assertEquals("Number of Prime Vendors Contracts By Industries in the Pending Revenue Contracts did not match", numOfPrimeVendorsContracts, numOfPrimeVendorsContractsFY2016);
	}
	@Test
	public void VerifyNumOfAwardMethodsContracts()throws SQLException{
	 	int numOfAwardMethodsContractsFY2016 = NYCDatabaseUtil.getPRContractsAwardMethodsCount(2016,'B');
        int numOfAwardMethodsContracts = Helper.stringToInt(ContractsPage.GetTop5WidgetTotalCount(WidgetOption.TopAwardMethods));
        assertEquals("Number of Award Methods Contracts in the Pending Revenue Contracts did not match", numOfAwardMethodsContracts, numOfAwardMethodsContractsFY2016);
	}
	@Test
	public void VerifyNumOfAgenciesContracts()throws SQLException{
	 	int numOfAgenciesContractsFY2016 = NYCDatabaseUtil.getPRContractsAgenciesCount(2016,'B');
        int numOfAgenciesContracts = Helper.stringToInt(ContractsPage.GetTop5WidgetTotalCount(WidgetOption.Top5Agencies));
        assertEquals("Number of Agencies Contracts in the Pending Revenue Contracts did not match", numOfAgenciesContracts, numOfAgenciesContractsFY2016);
	}
	@Test
	public void VerifyNumOfContractsByIndustries()throws SQLException{
	 	int numOfContractsByIndustriesFY2016 = NYCDatabaseUtil.getPRContractsIndustriesCount(2016,'B');
        int numOfContractsByIndustries = Helper.stringToInt(ContractsPage.GetTop5WidgetTotalCount(WidgetOption.ContractsByIndustries));
        assertEquals("Number of Contracts By Industries in the Pending Revenue Contracts did not match", numOfContractsByIndustries, numOfContractsByIndustriesFY2016);
	}	
	@Test
	public void VerifyNumOfContractsBySize()throws SQLException{
	 	int numOfContractsBySizeFY2016 = NYCDatabaseUtil.getPRContractsSizeCount(2016,'B');
        int numOfContractsBySize = Helper.stringToInt(ContractsPage.GetTop5WidgetTotalCount(WidgetOption.ContractsBySize));
        assertEquals("Number of Contracts By Size in the Pending Revenue Contracts did not match", numOfContractsBySize, numOfContractsBySizeFY2016);
	}
	
}
