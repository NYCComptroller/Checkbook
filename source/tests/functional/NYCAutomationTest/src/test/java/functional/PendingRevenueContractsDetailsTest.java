package functional;

import static org.junit.Assert.assertTrue;
import helpers.Helper;

import org.junit.Before;
import org.junit.Test;

import pages.contracts.ContractsPage;
import pages.contracts.ContractsPage.WidgetOption;
import pages.contracts.PendingRevenueContractsPage;
import pages.home.HomePage;
import utilities.NYCBaseTest;

public class PendingRevenueContractsDetailsTest extends NYCBaseTest{

	@Before
    public void GoToPage(){
	   if(!(Helper.getCurrentSelectedYear()).equalsIgnoreCase(NYCBaseTest.prop.getProperty("CurrentYear")))
		   HomePage.SelectYear(NYCBaseTest.prop.getProperty("CurrentYear"));
	   PendingRevenueContractsPage.GoTo();
	   HomePage.ShowWidgetDetails();
    }
	
	/* ***************** Test Widget Transaction Count ****************** */
	@Test
    public void VerifyTop5ContractsTransactionCount(){
		ContractsPage.GoToTop5DetailsPage(WidgetOption.Top5Contracts);
		HomePage.ShowWidgetDetails();
	   assertTrue(PendingRevenueContractsPage.GetTransactionCount() >= 18); 
    }
	/*@Test
    public void VerifyTop5ContractAmountModificationsTransactionCount(){
		ContractsPage.GoToTop5DetailsPage(WidgetOption.Top5ContractAmountModifications);
		HomePage.ShowWidgetDetails();
	   assertTrue(PendingRevenueContractsPage.GetTransactionCount() >= 46); 
    }*/
	@Test
    public void VerifyTop5PrimeVendorsTransactionCount(){
		ContractsPage.GoToTop5DetailsPage(WidgetOption.Top5PrimeVendors);
		HomePage.ShowWidgetDetails();
	   assertTrue(PendingRevenueContractsPage.GetTransactionCount() >= 18); 
    }
	@Test
    public void VerifyTop5AwardMethodsTransactionCount(){
		ContractsPage.GoToTop5DetailsPage(WidgetOption.TopAwardMethods);
		HomePage.ShowWidgetDetails();
	   assertTrue(PendingRevenueContractsPage.GetTransactionCount() >= 18); 
    }
	@Test
    public void VerifyTop5AgenciesTransactionCount(){
		ContractsPage.GoToTop5DetailsPage(WidgetOption.TopAgencies);
		HomePage.ShowWidgetDetails();
	   assertTrue(PendingRevenueContractsPage.GetTransactionCount() >= 18); 
    }
	@Test
    public void VerifyContractsByIndustriesTransactionCount(){
		ContractsPage.GoToTop5DetailsPage(WidgetOption.ContractsByIndustries);
		HomePage.ShowWidgetDetails();
	   assertTrue(PendingRevenueContractsPage.GetTransactionCount() >= 18); 
    }
    @Test
    public void VerifyContractsBySizeTransactionCount(){
		ContractsPage.GoToTop5DetailsPage(WidgetOption.ContractsBySize);
		HomePage.ShowWidgetDetails();
	   assertTrue(PendingRevenueContractsPage.GetTransactionCount() >= 18); 
    }
   
}
