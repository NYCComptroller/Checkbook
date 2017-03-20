package functional;

import static org.junit.Assert.assertEquals;
import static org.junit.Assert.assertTrue;

import org.junit.Before;
import org.junit.Test;

import navigation.TopNavigation.Contracts.ActiveExpenseContracts;
import pages.contracts.ActiveExpenseContractsPage;
import pages.contracts.ContractsPage;
import pages.contracts.ContractsPage.WidgetOption;
import pages.home.HomePage;
import utilities.NYCBaseTest;
import utility.Helper;

public class ActiveExpenseContractsTest extends NYCBaseTest{

	@Before
    public void GoToPage(){
		ContractsPage.GoTo();
	   if (!ActiveExpenseContracts.isAt()){
		   ActiveExpenseContractsPage.GoTo();
	   }
	   if(!(Helper.getCurrentSelectedYear()).equalsIgnoreCase(NYCBaseTest.prop.getProperty("CurrentYear")))
		   HomePage.SelectYear(NYCBaseTest.prop.getProperty("CurrentYear"));
	   HomePage.ShowWidgetDetails();
    }
	
	/* ***************** Test Widget Counts ****************** */
	@Test
	public void VerifyNumOfMasterAgreementContracts(){
	 	int activeExpenseContractsNumOfMasterAgreementContractsFY2016 = 3725;
        int numOfMasterAgreementContracts = Helper.stringToInt(ContractsPage.GetTop5WidgetTotalCount(WidgetOption.Top5MasterAgreements));
        assertEquals("Number of Master Agreement Contracts in the Active Expense Contracts did not match", numOfMasterAgreementContracts, activeExpenseContractsNumOfMasterAgreementContractsFY2016);
	}	
	@Test
	public void VerifyNumOfMasterAgreementModificationsContracts(){
	 	int activeExpenseContractsNumOfMasterAgreementModificationsContractsFY2016 = 324;
        int numOfMasterAgreementContracts = Helper.stringToInt(ContractsPage.GetTop5WidgetTotalCount(WidgetOption.Top5MasterAgreementModifications));
        assertEquals("Number of Master Agreement Modifications Contracts in the Active Expense Contracts did not match", numOfMasterAgreementContracts, activeExpenseContractsNumOfMasterAgreementModificationsContractsFY2016);
	}
	@Test
	public void VerifyNumOfContracts(){
	 	int activeExpenseContractsNumOfContractsFY2016 = 22640;
        int numOfContracts = Helper.stringToInt(ContractsPage.GetTop5WidgetTotalCount(WidgetOption.Top5Contracts));
        assertEquals("Number of Contracts in the Active Expense Contracts did not match", numOfContracts, activeExpenseContractsNumOfContractsFY2016);
	}
	@Test
	public void VerifyNumOfContractAmountModificationsContracts(){
	 	int activeExpenseContractsNumOfContractAmountModificationsContractsFY2016 = 5196;
        int numOfContractAmountModificationsContracts = Helper.stringToInt(ContractsPage.GetTop5WidgetTotalCount(WidgetOption.Top5ContractAmountModifications));
        assertEquals("Number of Contracts in the Active Expense Contracts did not match", numOfContractAmountModificationsContracts, activeExpenseContractsNumOfContractAmountModificationsContractsFY2016);
	}
	@Test
	public void VerifyNumOfPrimeVendorsContracts(){
	 	int activeExpenseContractsNumOfPrimeVendorsContractsFY2016 = 8844;
        int numOfPrimeVendorsContracts = Helper.stringToInt(ContractsPage.GetTop5WidgetTotalCount(WidgetOption.Top5PrimeVendors));
        assertEquals("Number of Contracts in the Active Expense Contracts did not match", numOfPrimeVendorsContracts, activeExpenseContractsNumOfPrimeVendorsContractsFY2016);
	}
	@Test
	public void VerifyNumOfAwardMethodsContracts(){
	 	int activeExpenseContractsNumOfAwardMethodsContractsFY2016 = 61;
        int numOfAwardMethodsContracts = Helper.stringToInt(ContractsPage.GetTop5WidgetTotalCount(WidgetOption.Top5AwardMethods));
        assertEquals("Number of Contracts in the Active Expense Contracts did not match", numOfAwardMethodsContracts, activeExpenseContractsNumOfAwardMethodsContractsFY2016);
	}
	@Test
	public void VerifyNumOfAgenciesContracts(){
	 	int activeExpenseContractsNumOfAgenciesContractsFY2016 = 88;
        int numOfAgenciesContracts = Helper.stringToInt(ContractsPage.GetTop5WidgetTotalCount(WidgetOption.Top5Agencies));
        assertEquals("Number of Contracts in the Agencies Contracts did not match", numOfAgenciesContracts, activeExpenseContractsNumOfAgenciesContractsFY2016);
	}
	@Test
	public void VerifyNumOfContractsByIndustries(){
	 	int activeExpenseContractsNumOfContractsByIndustriesFY2016 = 25873;
        int numOfContractsByIndustries = Helper.stringToInt(ContractsPage.GetTop5WidgetTotalCount(WidgetOption.ContractsByIndustries));
        assertEquals("Number of Contracts in the Agencies Contracts did not match", numOfContractsByIndustries, activeExpenseContractsNumOfContractsByIndustriesFY2016);
	}
	@Test
	public void VerifyNumOfContractsBySize(){
	 	int activeExpenseContractsNumOfContractsBySizeFY2016 = 25873;
        int numOfContractsBySize = Helper.stringToInt(ContractsPage.GetTop5WidgetTotalCount(WidgetOption.ContractsBySize));
        assertEquals("Number of Contracts in the Agencies Contracts did not match", numOfContractsBySize, activeExpenseContractsNumOfContractsBySizeFY2016);
	}
	
}
