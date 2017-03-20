package functional;

import static org.junit.Assert.assertTrue;

import org.junit.Before;
import org.junit.Test;

import pages.contracts.ActiveExpenseContractsPage;
import pages.contracts.ActiveRevenueContractsPage;
import pages.contracts.ContractsPage;
import pages.contracts.ContractsPage.WidgetOption;
import pages.home.HomePage;
import utilities.NYCBaseTest;
import utility.Helper;

public class ActiveRevenueContractsDetailsTest extends NYCBaseTest{

	@Before
    public void GoToPage(){
		ActiveRevenueContractsPage.GoTo();
	   if(!(Helper.getCurrentSelectedYear()).equalsIgnoreCase(NYCBaseTest.prop.getProperty("CurrentYear")))
		   HomePage.SelectYear(NYCBaseTest.prop.getProperty("CurrentYear"));
	   HomePage.ShowWidgetDetails();
    }
	
	/* ***************** Test Widget Transaction Count ****************** */
	@Test
    public void VerifyTop5ContractsTransactionCount(){
		ContractsPage.GoToTop5DetailsPage(WidgetOption.Top5Contracts);
		HomePage.ShowWidgetDetails();
	   assertTrue(ActiveRevenueContractsPage.GetTransactionCount() >= 3144); 
    }
	@Test
    public void VerifyTop5ContractAmountModificationsTransactionCount(){
		ContractsPage.GoToTop5DetailsPage(WidgetOption.Top5ContractAmountModifications);
		HomePage.ShowWidgetDetails();
	   assertTrue(ActiveRevenueContractsPage.GetTransactionCount() >= 46); 
    }
	@Test
    public void VerifyTop5PrimeVendorsTransactionCount(){
		ContractsPage.GoToTop5DetailsPage(WidgetOption.Top5PrimeVendors);
		HomePage.ShowWidgetDetails();
	   assertTrue(ActiveRevenueContractsPage.GetTransactionCount() >= 3144); 
    }
	@Test
    public void VerifyTop5AwardMethodsTransactionCount(){
		ContractsPage.GoToTop5DetailsPage(WidgetOption.Top5AwardMethods);
		HomePage.ShowWidgetDetails();
	   assertTrue(ActiveRevenueContractsPage.GetTransactionCount() >= 3144); 
    }
	@Test
    public void VerifyTop5AgenciesTransactionCount(){
		ContractsPage.GoToTop5DetailsPage(WidgetOption.Top5Agencies);
		HomePage.ShowWidgetDetails();
	   assertTrue(ActiveRevenueContractsPage.GetTransactionCount() >= 3144); 
    }
	@Test
    public void VerifyContractsByIndustriesTransactionCount(){
		ContractsPage.GoToTop5DetailsPage(WidgetOption.ContractsByIndustries);
		HomePage.ShowWidgetDetails();
	   assertTrue(ActiveRevenueContractsPage.GetTransactionCount() >= 3144); 
    }
    @Test
    public void VerifyContractsBySizeTransactionCount(){
		ContractsPage.GoToTop5DetailsPage(WidgetOption.ContractsBySize);
		HomePage.ShowWidgetDetails();
	   assertTrue(ActiveRevenueContractsPage.GetTransactionCount() >= 3144); 
    }
    
    /* ***************** Test Widget Transaction Total Amount ****************** */
	@Test
    public void VerifyTop5ContractsTransactionAmount(){
		Float transactionAmt = 7.28f;
		ContractsPage.GoToTop5DetailsPage(WidgetOption.Top5Contracts);
		HomePage.ShowWidgetDetails();
		assertTrue(HomePage.GetTransactionAmount()>= transactionAmt);
    }
	@Test
    public void VerifyTop5ContractAmountModificationsTransactionAmount(){
		Float transactionAmt = 1.95f;
		ContractsPage.GoToTop5DetailsPage(WidgetOption.Top5ContractAmountModifications);
		HomePage.ShowWidgetDetails();
		assertTrue(HomePage.GetTransactionAmount()>= transactionAmt);
    }
	@Test
    public void VerifyTop5PrimeVendorsTransactionAmount(){
		Float transactionAmt = 7.28f;
		ContractsPage.GoToTop5DetailsPage(WidgetOption.Top5PrimeVendors);
		HomePage.ShowWidgetDetails();
		assertTrue(HomePage.GetTransactionAmount()>= transactionAmt);
    }
	@Test
    public void VerifyTop5AwardMethodsTransactionAmount(){
		Float transactionAmt = 7.28f;
		ContractsPage.GoToTop5DetailsPage(WidgetOption.Top5AwardMethods);
		HomePage.ShowWidgetDetails();
		assertTrue(HomePage.GetTransactionAmount()>= transactionAmt);
    }
	@Test
    public void VerifyTop5AgenciesTransactionAmount(){
		Float transactionAmt = 7.28f;
		ContractsPage.GoToTop5DetailsPage(WidgetOption.Top5Agencies);
		HomePage.ShowWidgetDetails();
		assertTrue(HomePage.GetTransactionAmount()>= transactionAmt);
    }
	@Test
    public void VerifyContractsByIndustriesTransactionAmount(){
		Float transactionAmt = 7.28f;
		ContractsPage.GoToTop5DetailsPage(WidgetOption.ContractsByIndustries);
		HomePage.ShowWidgetDetails();
		assertTrue(HomePage.GetTransactionAmount()>= transactionAmt); 
    }
    @Test
    public void VerifyContractsBySizeTransactionAmount(){
    	Float transactionAmt = 7.28f;
		ContractsPage.GoToTop5DetailsPage(WidgetOption.ContractsBySize);
		HomePage.ShowWidgetDetails();
		assertTrue(HomePage.GetTransactionAmount()>= transactionAmt);
    }
}
