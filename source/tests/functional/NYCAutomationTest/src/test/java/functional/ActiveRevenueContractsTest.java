package functional;

import static org.junit.Assert.assertEquals;
import helpers.Helper;

import org.junit.Before;
import org.junit.Test;

import navigation.TopNavigation.Contracts.ActiveRevenueContracts;
import pages.contracts.ActiveRevenueContractsPage;
import pages.contracts.ContractsPage;
import pages.contracts.ContractsPage.WidgetOption;
import pages.home.HomePage;
import utilities.NYCBaseTest;

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
	 	Integer numOfContractsFY2016 = 3144;	 	
        Integer numOfContracts = ContractsPage.GetTop5WidgetTotalCount(WidgetOption.Top5Contracts);
        assertEquals("Number of Contracts in the Active Revenue Contracts did not match", numOfContracts, numOfContractsFY2016);
	}
	
	@Test
	public void VerifyNumOfContractAmountModificationsContracts(){
	 	Integer numOfContractAmountModificationsContractsFY2016 = 46;
        Integer numOfContractAmountModificationsContracts = ContractsPage.GetTop5WidgetTotalCount(WidgetOption.Top5ContractAmountModifications);
        assertEquals("Number of Contract Amount Modifications Contracts in the Active Revenue Contracts did not match", numOfContractAmountModificationsContracts, numOfContractAmountModificationsContractsFY2016);
	}
	
	@Test
	public void VerifyNumOfPrimeVendorsContracts(){
	 	Integer numOfPrimeVendorsContractsFY2016 = 2416;
        Integer numOfPrimeVendorsContracts = ContractsPage.GetTop5WidgetTotalCount(WidgetOption.Top5PrimeVendors);
        assertEquals("Number of Prime Vendors Contracts in the Active Revenue Contracts did not match", numOfPrimeVendorsContracts, numOfPrimeVendorsContractsFY2016);
	}
	
	@Test
	public void VerifyNumOfAwardMethodsContracts(){
	 	Integer numOfAwardMethodsContractsFY2016 = 17;
        Integer numOfAwardMethodsContracts = ContractsPage.GetTop5WidgetTotalCount(WidgetOption.Top5AwardMethods);
        assertEquals("Number of Award Methods Contracts in the Active Revenue Contracts did not match", numOfAwardMethodsContracts, numOfAwardMethodsContractsFY2016);
	}
	
	@Test
	public void VerifyNumOfAgenciesContracts(){
	 	Integer numOfAgenciesContractsFY2016 = 20;
        Integer numOfAgenciesContracts = ContractsPage.GetTop5WidgetTotalCount(WidgetOption.Top5Agencies);
        assertEquals("Number of Agencies Contracts in the Active Revenue Contracts did not match", numOfAgenciesContracts, numOfAgenciesContractsFY2016);
	}
	
	@Test
	public void VerifyNumOfContractsByIndustries(){
	 	Integer numOfContractsByIndustriesFY2016 = 3144;
        Integer numOfContractsByIndustries = ContractsPage.GetTop5WidgetTotalCount(WidgetOption.ContractsByIndustries);
        assertEquals("Number of Contracts By Industries in the Active Revenue Contracts did not match", numOfContractsByIndustries, numOfContractsByIndustriesFY2016);
	}
	
	@Test
	public void VerifyNumOfContractsBySize(){
	 	Integer numOfContractsBySizeFY2016 = 3144;
        Integer numOfContractsBySize = ContractsPage.GetTop5WidgetTotalCount(WidgetOption.ContractsBySize);
        assertEquals("Number of Contracts By Size in the Agencies Revenue did not match", numOfContractsBySize, numOfContractsBySizeFY2016);
	}
	
	
}
