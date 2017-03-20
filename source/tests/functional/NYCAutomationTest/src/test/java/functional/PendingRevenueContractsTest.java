package functional;

import static org.junit.Assert.assertEquals;

import org.junit.Before;
import org.junit.Test;

import navigation.TopNavigation.Contracts.PendingRevenueContracts;
import pages.contracts.ContractsPage;
import pages.contracts.PendingRevenueContractsPage;
import pages.contracts.ContractsPage.WidgetOption;
import pages.home.HomePage;
import utilities.NYCBaseTest;
import utility.Helper;

public class PendingRevenueContractsTest extends NYCBaseTest{
	@Before
    public void GoToPage(){
	   if(!(Helper.getCurrentSelectedYear()).equalsIgnoreCase(NYCBaseTest.prop.getProperty("CurrentYear")))
		   HomePage.SelectYear(NYCBaseTest.prop.getProperty("CurrentYear"));
	   if (!PendingRevenueContracts.isAt())
		   PendingRevenueContractsPage.GoTo();
	   HomePage.ShowWidgetDetails();
    }
	/* ***************** Test Widget Counts ****************** */
	@Test
	public void VerifyNumOfContracts(){
	 	int numOfContractsFY2016 = 5;
        int numOfContracts = Helper.stringToInt(ContractsPage.GetTop5WidgetTotalCount(WidgetOption.Top5Contracts));
        assertEquals("Number of Contracts in the Pending Revenue Contracts did not match", numOfContracts, numOfContractsFY2016);
	}
	@Test
	public void VerifyNumOfContractsAmountModificationsContracts(){
	 	int numOfContractsAmountModificationsContractsFY2016 = 0;
        int numOfContractsAmountModificationsContracts = Helper.stringToInt(ContractsPage.GetTop5WidgetTotalCount(WidgetOption.TopContractsAmountModifications));
        assertEquals("Number of Contracts Amount Modifications Contracts in the Pending Revenue Contracts did not match", numOfContractsAmountModificationsContracts, numOfContractsAmountModificationsContractsFY2016);
	}
	@Test
	public void VerifyNumOfPrimeVendorsContracts(){
	 	int numOfPrimeVendorsContractsFY2016 = 4;
        int numOfPrimeVendorsContracts = Helper.stringToInt(ContractsPage.GetTop5WidgetTotalCount(WidgetOption.TopPrimeVendors));
        assertEquals("Number of Prime Vendors Contracts By Industries in the Pending Revenue Contracts did not match", numOfPrimeVendorsContracts, numOfPrimeVendorsContractsFY2016);
	}
	@Test
	public void VerifyNumOfAwardMethodsContracts(){
	 	int numOfAwardMethodsContractsFY2016 = 3;
        int numOfAwardMethodsContracts = Helper.stringToInt(ContractsPage.GetTop5WidgetTotalCount(WidgetOption.TopAwardMethods));
        assertEquals("Number of Award Methods Contracts in the Pending Revenue Contracts did not match", numOfAwardMethodsContracts, numOfAwardMethodsContractsFY2016);
	}
	@Test
	public void VerifyNumOfAgenciesContracts(){
	 	int numOfAgenciesContractsFY2016 = 4;
        int numOfAgenciesContracts = Helper.stringToInt(ContractsPage.GetTop5WidgetTotalCount(WidgetOption.TopAgencies));
        assertEquals("Number of Agencies Contracts in the Pending Revenue Contracts did not match", numOfAgenciesContracts, numOfAgenciesContractsFY2016);
	}
	@Test
	public void VerifyNumOfContractsByIndustries(){
	 	int numOfContractsByIndustriesFY2016 = 5;
        int numOfContractsByIndustries = Helper.stringToInt(ContractsPage.GetTop5WidgetTotalCount(WidgetOption.ContractsByIndustries));
        assertEquals("Number of Contracts By Industries in the Pending Revenue Contracts did not match", numOfContractsByIndustries, numOfContractsByIndustriesFY2016);
	}	
	@Test
	public void VerifyNumOfContractsBySize(){
	 	int numOfContractsBySizeFY2016 = 5;
        int numOfContractsBySize = Helper.stringToInt(ContractsPage.GetTop5WidgetTotalCount(WidgetOption.ContractsBySize));
        assertEquals("Number of Contracts By Size in the Pending Revenue Contracts did not match", numOfContractsBySize, numOfContractsBySizeFY2016);
	}
	
}
