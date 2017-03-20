package functional;

import static org.junit.Assert.assertEquals;

import org.junit.Before;
import org.junit.Test;

import navigation.TopNavigation.Contracts.ActiveRevenueContracts;
import pages.contracts.ActiveRevenueContractsPage;
import pages.contracts.ContractsPage;
import pages.contracts.ContractsPage.WidgetOption;
import pages.home.HomePage;
import utilities.NYCBaseTest;
import utility.Helper;

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
	public void VerifyNumOfContracts(){
	 	int numOfContractsFY2016 = 3144;	 	
        int numOfContracts = Helper.stringToInt(ContractsPage.GetTop5WidgetTotalCount(WidgetOption.Top5Contracts));
        assertEquals("Number of Contracts in the Active Revenue Contracts did not match", numOfContracts, numOfContractsFY2016);
	}
	
	@Test
	public void VerifyNumOfContractAmountModificationsContracts(){
	 	int numOfContractAmountModificationsContractsFY2016 = 46;
        int numOfContractAmountModificationsContracts = Helper.stringToInt(ContractsPage.GetTop5WidgetTotalCount(WidgetOption.Top5ContractAmountModifications));
        assertEquals("Number of Contract Amount Modifications Contracts in the Active Revenue Contracts did not match", numOfContractAmountModificationsContracts, numOfContractAmountModificationsContractsFY2016);
	}
	
	@Test
	public void VerifyNumOfPrimeVendorsContracts(){
	 	int numOfPrimeVendorsContractsFY2016 = 2416;
        int numOfPrimeVendorsContracts = Helper.stringToInt(ContractsPage.GetTop5WidgetTotalCount(WidgetOption.Top5PrimeVendors));
        assertEquals("Number of Prime Vendors Contracts in the Active Revenue Contracts did not match", numOfPrimeVendorsContracts, numOfPrimeVendorsContractsFY2016);
	}
	
	@Test
	public void VerifyNumOfAwardMethodsContracts(){
	 	int numOfAwardMethodsContractsFY2016 = 17;
        int numOfAwardMethodsContracts = Helper.stringToInt(ContractsPage.GetTop5WidgetTotalCount(WidgetOption.Top5AwardMethods));
        assertEquals("Number of Award Methods Contracts in the Active Revenue Contracts did not match", numOfAwardMethodsContracts, numOfAwardMethodsContractsFY2016);
	}
	
	@Test
	public void VerifyNumOfAgenciesContracts(){
	 	int numOfAgenciesContractsFY2016 = 20;
        int numOfAgenciesContracts = Helper.stringToInt(ContractsPage.GetTop5WidgetTotalCount(WidgetOption.Top5Agencies));
        assertEquals("Number of Agencies Contracts in the Active Revenue Contracts did not match", numOfAgenciesContracts, numOfAgenciesContractsFY2016);
	}
	
	@Test
	public void VerifyNumOfContractsByIndustries(){
	 	int numOfContractsByIndustriesFY2016 = 3144;
        int numOfContractsByIndustries = Helper.stringToInt(ContractsPage.GetTop5WidgetTotalCount(WidgetOption.ContractsByIndustries));
        assertEquals("Number of Contracts By Industries in the Active Revenue Contracts did not match", numOfContractsByIndustries, numOfContractsByIndustriesFY2016);
	}
	
	@Test
	public void VerifyNumOfContractsBySize(){
	 	int numOfContractsBySizeFY2016 = 3144;
        int numOfContractsBySize = Helper.stringToInt(ContractsPage.GetTop5WidgetTotalCount(WidgetOption.ContractsBySize));
        assertEquals("Number of Contracts By Size in the Agencies Revenue did not match", numOfContractsBySize, numOfContractsBySizeFY2016);
	}
	
	
}
