package functional;

import static org.junit.Assert.assertEquals;
import helpers.Helper;

import org.junit.Before;
import org.junit.Test;

import navigation.TopNavigation.Contracts.PendingExpenseContracts;
import pages.contracts.ContractsPage;
import pages.contracts.PendingExpenseContractsPage;
import pages.contracts.ContractsPage.WidgetOption;
import pages.home.HomePage;
import utilities.NYCBaseTest;

public class PendingExpenseContractsTest extends NYCBaseTest{
	@Before
    public void GoToPage(){
	   if (!PendingExpenseContracts.isAt())
		   PendingExpenseContractsPage.GoTo();
	   if(!(Helper.getCurrentSelectedYear()).equalsIgnoreCase(NYCBaseTest.prop.getProperty("CurrentYear")))
		   HomePage.SelectYear(NYCBaseTest.prop.getProperty("CurrentYear"));
	   HomePage.ShowWidgetDetails();
    }
	/* ***************** Test Widget Counts ****************** */
	@Test
	public void VerifyNumOfMasterAgreementsContracts(){
	 	Integer numOfMasterAgreementContractsFY2016 = 3725;	 	
        Integer numOfMasterAgreementContracts = ContractsPage.GetTop5WidgetTotalCount(WidgetOption.Top5MasterAgreements);
        assertEquals("Number of Master Agreement Contracts in the Pending Expense Contracts did not match", numOfMasterAgreementContracts, numOfMasterAgreementContractsFY2016);
	}
	
	@Test
	public void VerifyNumOfMasterAgreementModificationsContracts(){
	 	Integer numOfMasterAgreementModificationsContractsFY2016 = 324;
        Integer numOfMasterAgreementModificationsContracts = ContractsPage.GetTop5WidgetTotalCount(WidgetOption.Top5MasterAgreementModifications);
        assertEquals("Number of Contract Amount Modifications Contracts in the Pending Expense Contracts did not match", numOfMasterAgreementModificationsContracts, numOfMasterAgreementModificationsContractsFY2016);
	}
	
	@Test
	public void VerifyNumOfContracts(){
	 	Integer numOfContractsFY2016 = 22641;
        Integer numOfContracts = ContractsPage.GetTop5WidgetTotalCount(WidgetOption.Top5Contracts);
        assertEquals("Number of Contracts in the Pending Expense Contracts did not match", numOfContracts, numOfContractsFY2016);
	}
	
	@Test
	public void VerifyNumOfContractsAmountModificationsContracts(){
	 	Integer numOfContractsAmountModificationsContractsFY2016 = 5199;
        Integer numOfContractsAmountModificationsContracts = ContractsPage.GetTop5WidgetTotalCount(WidgetOption.Top5ContractAmountModifications);
        assertEquals("Number of Contracts Amount Modifications Contracts in the Pending Expense Contracts did not match", numOfContractsAmountModificationsContracts, numOfContractsAmountModificationsContractsFY2016);
	}
		
	@Test
	public void VerifyNumOfPrimeVendorsContracts(){
	 	Integer numOfPrimeVendorsContractsFY2016 = 8845;
        Integer numOfPrimeVendorsContracts = ContractsPage.GetTop5WidgetTotalCount(WidgetOption.Top5PrimeVendors);
        assertEquals("Number of Prime Vendors Contracts By Industries in the Pending Expense Contracts did not match", numOfPrimeVendorsContracts, numOfPrimeVendorsContractsFY2016);
	}
	
	@Test
	public void VerifyNumOfAwardMethodsContracts(){
	 	Integer numOfAwardMethodsContractsFY2016 = 61;
        Integer numOfAwardMethodsContracts = ContractsPage.GetTop5WidgetTotalCount(WidgetOption.Top5AwardMethods);
        assertEquals("Number of Award Methods Contracts in the Pending Expense Contracts did not match", numOfAwardMethodsContracts, numOfAwardMethodsContractsFY2016);
	}
	
	@Test
	public void VerifyNumOfAgenciesContracts(){
	 	Integer numOfAgenciesContractsFY2016 = 88;
        Integer numOfAgenciesContracts = ContractsPage.GetTop5WidgetTotalCount(WidgetOption.Top5Agencies);
        assertEquals("Number of Agencies Contracts in the Pending Expense Contracts did not match", numOfAgenciesContracts, numOfAgenciesContractsFY2016);
	}
	
	@Test
	public void VerifyNumOfContractsByIndustries(){
	 	Integer numOfContractsByIndustriesFY2016 = 25874;
        Integer numOfContractsByIndustries = ContractsPage.GetTop5WidgetTotalCount(WidgetOption.ContractsByIndustries);
        assertEquals("Number of Contracts By Industries in the Pending Expense Contracts did not match", numOfContractsByIndustries, numOfContractsByIndustriesFY2016);
	}
	
	@Test
	public void VerifyNumOfContractsBySize(){
	 	Integer numOfContractsBySizeFY2016 = 25874;
        Integer numOfContractsBySize = ContractsPage.GetTop5WidgetTotalCount(WidgetOption.ContractsBySize);
        assertEquals("Number of Contracts By Size in the Pending Expense Contracts did not match", numOfContractsBySize, numOfContractsBySizeFY2016);
	}
	
	
	
}
