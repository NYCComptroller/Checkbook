package functional;

import static org.junit.Assert.assertEquals;

import java.sql.SQLException;

import org.junit.Before;
import org.junit.Test;

import navigation.TopNavigation.Contracts.ActiveRevenueContracts;
import pages.contracts.ActiveRevenueContractsPage;
import pages.contracts.ContractsPage;
import pages.contracts.ContractsPage.WidgetOption;
import pages.home.HomePage;
import utilities.NYCBaseTest;
import utilities.NYCDatabaseUtil;
import helpers.Helper;

public class ActiveRevenueContractsTest extends NYCBaseTest{
	@Before
    public void GoToPage(){
	   if (!ActiveRevenueContracts.isAt()){
		   ActiveRevenueContractsPage.GoTo();
	   }
	   if(!(Helper.getCurrentSelectedYear()).equalsIgnoreCase(NYCBaseTest.prop.getProperty("CurrentYear")))
		   HomePage.SelectYear(NYCBaseTest.prop.getProperty("CurrentYear"));
	   HomePage.ShowWidgetDetails();
    }
	/* ***************** Test Widget Counts ****************** */
	@Test
	public void VerifyNumOfContracts()throws SQLException{
	 	int numOfContractsFY2016 = NYCDatabaseUtil.getARContractsCount(2016,'B');	 	
        int numOfContracts = Helper.stringToInt(ContractsPage.GetTop5WidgetTotalCount(WidgetOption.Top5Contracts));
        assertEquals("Number of Contracts in the Active Revenue Contracts did not match", numOfContracts, numOfContractsFY2016);
	}
	
	@Test
	public void VerifyNumOfContractAmountModificationsContracts()throws SQLException{
	 	int numOfContractAmountModificationsContractsFY2016 = NYCDatabaseUtil.getARContractsModificationsCount(2016,'B');	
        int numOfContractAmountModificationsContracts = Helper.stringToInt(ContractsPage.GetTop5WidgetTotalCount(WidgetOption.Top5ContractAmountModifications));
        assertEquals("Number of Contract Amount Modifications Contracts in the Active Revenue Contracts did not match", numOfContractAmountModificationsContracts, numOfContractAmountModificationsContractsFY2016);
	}
	
	@Test
	public void VerifyNumOfPrimeVendorsContracts()throws SQLException{
	 	int numOfPrimeVendorsContractsFY2016 = NYCDatabaseUtil.getARContractsPrimeVendorsCount(2016,'B');	
        int numOfPrimeVendorsContracts = Helper.stringToInt(ContractsPage.GetTop5WidgetTotalCount(WidgetOption.Top5PrimeVendors));
        assertEquals("Number of Prime Vendors Contracts in the Active Revenue Contracts did not match", numOfPrimeVendorsContracts, numOfPrimeVendorsContractsFY2016);
	}
	
	@Test
	public void VerifyNumOfAwardMethodsContracts()throws SQLException{
	 	int numOfAwardMethodsContractsFY2016 = NYCDatabaseUtil.getARContractsAwardMethodsCount(2016,'B');	
        int numOfAwardMethodsContracts = Helper.stringToInt(ContractsPage.GetTop5WidgetTotalCount(WidgetOption.Top5AwardMethods));
        assertEquals("Number of Award Methods Contracts in the Active Revenue Contracts did not match", numOfAwardMethodsContracts, numOfAwardMethodsContractsFY2016);
	}
	
	@Test
	public void VerifyNumOfAgenciesContracts()throws SQLException{
	 	int numOfAgenciesContractsFY2016 = NYCDatabaseUtil.getARContractsAgenciesCount(2016,'B');	
        int numOfAgenciesContracts = Helper.stringToInt(ContractsPage.GetTop5WidgetTotalCount(WidgetOption.Top5Agencies));
        assertEquals("Number of Agencies Contracts in the Active Revenue Contracts did not match", numOfAgenciesContracts, numOfAgenciesContractsFY2016);
	}
	
	@Test
	public void VerifyNumOfContractsByIndustries()throws SQLException{
	 	int numOfContractsByIndustriesFY2016 = NYCDatabaseUtil.getARContractsIndustriesCount(2016,'B');	
        int numOfContractsByIndustries = Helper.stringToInt(ContractsPage.GetTop5WidgetTotalCount(WidgetOption.ContractsByIndustries));
        assertEquals("Number of Contracts By Industries in the Active Revenue Contracts did not match", numOfContractsByIndustries, numOfContractsByIndustriesFY2016);
	}
	
	@Test
	public void VerifyNumOfContractsBySize()throws SQLException{
	 	int numOfContractsBySizeFY2016 = NYCDatabaseUtil.getARContractsSizeCount(2016,'B');	
        int numOfContractsBySize = Helper.stringToInt(ContractsPage.GetTop5WidgetTotalCount(WidgetOption.ContractsBySize));
        assertEquals("Number of Contracts By Size in the Agencies Revenue did not match", numOfContractsBySize, numOfContractsBySizeFY2016);
	}
	
	
}
