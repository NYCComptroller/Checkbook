package functional;

import static org.junit.Assert.assertTrue;
import helpers.Helper;

import org.junit.Before;
import org.junit.Test;

import pages.contracts.ContractsPage;
import pages.contracts.ContractsPage.WidgetOption;
import pages.contracts.RegisteredRevenueContractsPage;
import pages.home.HomePage;
import utilities.NYCBaseTest;

public class RegisteredRevenueContractsDetailsTest extends NYCBaseTest{

	@Before
    public void GoToPage(){
		RegisteredRevenueContractsPage.GoTo();
	   if(!(Helper.getCurrentSelectedYear()).equalsIgnoreCase(NYCBaseTest.prop.getProperty("CurrentYear")))
		   HomePage.SelectYear(NYCBaseTest.prop.getProperty("CurrentYear"));
	   HomePage.ShowWidgetDetails();
    }
	
	/* ***************** Test Widget Transaction Count ****************** */
	@Test
    public void VerifyTop5ContractsTransactionCount(){
		ContractsPage.GoToTop5DetailsPage(WidgetOption.Top5Contracts);
		HomePage.ShowWidgetDetails();
	   assertTrue(RegisteredRevenueContractsPage.GetTransactionCount() >= 937); 
    }
	@Test
    public void VerifyTop5ContractAmountModificationsTransactionCount(){
		ContractsPage.GoToTop5DetailsPage(WidgetOption.TopContractAmountModifications);
		HomePage.ShowWidgetDetails();
	   assertTrue(RegisteredRevenueContractsPage.GetTransactionCount() >= 1); 
    }
	@Test
    public void VerifyTop5PrimeVendorsTransactionCount(){
		ContractsPage.GoToTop5DetailsPage(WidgetOption.Top5PrimeVendors);
		HomePage.ShowWidgetDetails();
	   assertTrue(RegisteredRevenueContractsPage.GetTransactionCount() >= 937); 
    }
	@Test
    public void VerifyTop5AwardMethodsTransactionCount(){
		ContractsPage.GoToTop5DetailsPage(WidgetOption.Top5AwardMethods);
		HomePage.ShowWidgetDetails();
	   assertTrue(RegisteredRevenueContractsPage.GetTransactionCount() >= 937); 
    }
	@Test
    public void VerifyTop5AgenciesTransactionCount(){
		ContractsPage.GoToTop5DetailsPage(WidgetOption.Top5Agencies);
		HomePage.ShowWidgetDetails();
	   assertTrue(RegisteredRevenueContractsPage.GetTransactionCount() >= 937); 
    }
	@Test
    public void VerifyContractsByIndustriesTransactionCount(){
		ContractsPage.GoToTop5DetailsPage(WidgetOption.ContractsByIndustries);
		HomePage.ShowWidgetDetails();
	   assertTrue(RegisteredRevenueContractsPage.GetTransactionCount() >= 937); 
    }
    @Test
    public void VerifyContractsBySizeTransactionCount(){
		ContractsPage.GoToTop5DetailsPage(WidgetOption.ContractsBySize);
		HomePage.ShowWidgetDetails();
	   assertTrue(RegisteredRevenueContractsPage.GetTransactionCount() >= 937); 
    }
    
    /* ***************** Test Widget Transaction Total Amount ****************** */
	@Test
    public void VerifyTop5ContractsTransactionAmount(){
		Float transactionAmt = 465.32f;
		ContractsPage.GoToTop5DetailsPage(WidgetOption.Top5Contracts);
		HomePage.ShowWidgetDetails();
		assertTrue(HomePage.GetTransactionAmount()>= transactionAmt);
    }
	@Test
    public void VerifyTop5ContractAmountModificationsTransactionAmount(){
		Float transactionAmt = 700.0f;
		ContractsPage.GoToTop5DetailsPage(WidgetOption.TopContractAmountModifications);
		HomePage.ShowWidgetDetails();
		assertTrue(HomePage.GetTransactionAmount()>= transactionAmt);
    }
	@Test
    public void VerifyTop5PrimeVendorsTransactionAmount(){
		Float transactionAmt = 465.32f;
		ContractsPage.GoToTop5DetailsPage(WidgetOption.Top5PrimeVendors);
		HomePage.ShowWidgetDetails();
		assertTrue(HomePage.GetTransactionAmount()>= transactionAmt);
    }
	@Test
    public void VerifyTop5AwardMethodsTransactionAmount(){
		Float transactionAmt = 465.32f;
		ContractsPage.GoToTop5DetailsPage(WidgetOption.Top5AwardMethods);
		HomePage.ShowWidgetDetails();
		assertTrue(HomePage.GetTransactionAmount()>= transactionAmt);
    }
	@Test
    public void VerifyTop5AgenciesTransactionAmount(){
		Float transactionAmt = 465.32f;
		ContractsPage.GoToTop5DetailsPage(WidgetOption.Top5Agencies);
		HomePage.ShowWidgetDetails();
		assertTrue(HomePage.GetTransactionAmount()>= transactionAmt);
    }
	@Test
    public void VerifyContractsByIndustriesTransactionAmount(){
		Float transactionAmt = 465.32f;
		ContractsPage.GoToTop5DetailsPage(WidgetOption.ContractsByIndustries);
		HomePage.ShowWidgetDetails();
		assertTrue(HomePage.GetTransactionAmount()>= transactionAmt); 
    }
    @Test
    public void VerifyContractsBySizeTransactionAmount(){
    	Float transactionAmt = 465.32f;
		ContractsPage.GoToTop5DetailsPage(WidgetOption.ContractsBySize);
		HomePage.ShowWidgetDetails();
		assertTrue(HomePage.GetTransactionAmount()>= transactionAmt);
    }
}
