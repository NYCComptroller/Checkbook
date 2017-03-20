package functional;

import static org.junit.Assert.assertEquals;

import org.junit.Before;
import org.junit.Test;

import navigation.TopNavigation.Contracts.RegisteredExpenseContracts;
import pages.contracts.ContractsPage;
import pages.contracts.RegisteredExpenseContractsPage;
import pages.contracts.ContractsPage.WidgetOption;
import pages.home.HomePage;
import utilities.NYCBaseTest;
import utility.Helper;

public class RegisteredExpenseContractsTest extends NYCBaseTest{
	@Before
    public void GoToPage(){
	   if (!RegisteredExpenseContracts.isAt())
		   RegisteredExpenseContractsPage.GoTo();
	   if(!(Helper.getCurrentSelectedYear()).equalsIgnoreCase(NYCBaseTest.prop.getProperty("CurrentYear")))
		   HomePage.SelectYear(NYCBaseTest.prop.getProperty("CurrentYear"));
	   HomePage.ShowWidgetDetails();
    }
	/* ***************** Test Widget Counts ****************** */
	@Test
	public void VerifyNumOfMasterAgreementsContracts(){
	 	int numOfMasterAgreementContractsFY2016 = 847;	 	
        int numOfMasterAgreementContracts = Helper.stringToInt(ContractsPage.GetTop5WidgetTotalCount(WidgetOption.Top5MasterAgreements));
        assertEquals("Number of Master Agreement Contracts in the Registered Expense Contracts did not match", numOfMasterAgreementContracts, numOfMasterAgreementContractsFY2016);
	}
	
	@Test
	public void VerifyNumOfMasterAgreementModificationsContracts(){
	 	int numOfMasterAgreementModificationsContractsFY2016 = 25;
        int numOfMasterAgreementModificationsContracts = Helper.stringToInt(ContractsPage.GetTop5WidgetTotalCount(WidgetOption.Top5MasterAgreementModifications));
        assertEquals("Number of Contract Amount Modifications Contracts in the Registered Expense Contracts did not match", numOfMasterAgreementModificationsContracts, numOfMasterAgreementModificationsContractsFY2016);
	}
	
	@Test
	public void VerifyNumOfContracts(){
	 	int numOfContractsFY2016 = 12681;
        int numOfContracts = Helper.stringToInt(ContractsPage.GetTop5WidgetTotalCount(WidgetOption.Top5Contracts));
        assertEquals("Number of Contracts in the Registered Expense Contracts did not match", numOfContracts, numOfContractsFY2016);
	}
	
	@Test
	public void VerifyNumOfContractsAmountModificationsContracts(){
	 	int numOfContractsAmountModificationsContractsFY2016 = 1246;
        int numOfContractsAmountModificationsContracts = Helper.stringToInt(ContractsPage.GetTop5WidgetTotalCount(WidgetOption.Top5ContractAmountModifications));
        assertEquals("Number of Contracts Amount Modifications Contracts in the Registered Expense Contracts did not match", numOfContractsAmountModificationsContracts, numOfContractsAmountModificationsContractsFY2016);
	}
		
	@Test
	public void VerifyNumOfPrimeVendorsContracts(){
	 	int numOfPrimeVendorsContractsFY2016 = 5257;
        int numOfPrimeVendorsContracts = Helper.stringToInt(ContractsPage.GetTop5WidgetTotalCount(WidgetOption.Top5PrimeVendors));
        assertEquals("Number of Prime Vendors Contracts By Industries in the Registered Expense Contracts did not match", numOfPrimeVendorsContracts, numOfPrimeVendorsContractsFY2016);
	}
	
	@Test
	public void VerifyNumOfAwardMethodsContracts(){
	 	int numOfAwardMethodsContractsFY2016 = 54;
        int numOfAwardMethodsContracts = Helper.stringToInt(ContractsPage.GetTop5WidgetTotalCount(WidgetOption.Top5AwardMethods));
        assertEquals("Number of Award Methods Contracts in the Registered Expense Contracts did not match", numOfAwardMethodsContracts, numOfAwardMethodsContractsFY2016);
	}
	
	@Test
	public void VerifyNumOfAgenciesContracts(){
	 	int numOfAgenciesContractsFY2016 = 69;
        int numOfAgenciesContracts = Helper.stringToInt(ContractsPage.GetTop5WidgetTotalCount(WidgetOption.Top5Agencies));
        assertEquals("Number of Agencies Contracts in the Registered Expense Contracts did not match", numOfAgenciesContracts, numOfAgenciesContractsFY2016);
	}
	
	@Test
	public void VerifyNumOfContractsByIndustries(){
	 	int numOfContractsByIndustriesFY2016 = 13339;
        int numOfContractsByIndustries = Helper.stringToInt(ContractsPage.GetTop5WidgetTotalCount(WidgetOption.ContractsByIndustries));
        assertEquals("Number of Contracts By Industries in the Registered Expense Contracts did not match", numOfContractsByIndustries, numOfContractsByIndustriesFY2016);
	}
	
	@Test
	public void VerifyNumOfContractsBySize(){
	 	int numOfContractsBySizeFY2016 = 13339;
        int numOfContractsBySize = Helper.stringToInt(ContractsPage.GetTop5WidgetTotalCount(WidgetOption.ContractsBySize));
        assertEquals("Number of Contracts By Size in the Registered Expense Contracts did not match", numOfContractsBySize, numOfContractsBySizeFY2016);
	}
	
}
