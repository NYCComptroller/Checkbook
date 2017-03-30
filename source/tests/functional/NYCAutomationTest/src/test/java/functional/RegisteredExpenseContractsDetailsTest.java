package functional;

import static org.junit.Assert.assertTrue;
import helpers.Helper;

import org.junit.Before;
import org.junit.Test;

import pages.contracts.ContractsPage;
import pages.contracts.ContractsPage.WidgetOption;
import pages.contracts.RegisteredExpenseContractsPage;
import pages.home.HomePage;
import utilities.NYCBaseTest;

public class RegisteredExpenseContractsDetailsTest extends NYCBaseTest{

	@Before
    public void GoToPage(){
		RegisteredExpenseContractsPage.GoTo();
	   if(!(Helper.getCurrentSelectedYear()).equalsIgnoreCase(NYCBaseTest.prop.getProperty("CurrentYear")))
		   HomePage.SelectYear(NYCBaseTest.prop.getProperty("CurrentYear"));
	   HomePage.ShowWidgetDetails();
    }
	
	/* ***************** Test Widget Transaction Count ****************** */
	@Test
    public void VerifyTop5MasterAgreementsTransactionCount(){
		ContractsPage.GoToTop5DetailsPage(WidgetOption.Top5MasterAgreements);
		HomePage.ShowWidgetDetails();
	   assertTrue(RegisteredExpenseContractsPage.GetTransactionCount() >= 847); 
    }
	@Test
    public void VerifyTop5MasterAgreementModificationsTransactionCount(){
		ContractsPage.GoToTop5DetailsPage(WidgetOption.Top5MasterAgreementModifications);
		HomePage.ShowWidgetDetails();
	   assertTrue(RegisteredExpenseContractsPage.GetTransactionCount() >= 25); 
    }
	@Test
    public void VerifyTop5ContractsTransactionCount(){
		ContractsPage.GoToTop5DetailsPage(WidgetOption.Top5Contracts);
		HomePage.ShowWidgetDetails();
	   assertTrue(RegisteredExpenseContractsPage.GetTransactionCount() >= 12681); 
    }
	@Test
    public void VerifyTop5ContractAmountModificationsTransactionCount(){
		ContractsPage.GoToTop5DetailsPage(WidgetOption.TopContractAmountModifications);
		HomePage.ShowWidgetDetails();
	   assertTrue(RegisteredExpenseContractsPage.GetTransactionCount() >= 1246); 
    }
	@Test
    public void VerifyTop5PrimeVendorsTransactionCount(){
		ContractsPage.GoToTop5DetailsPage(WidgetOption.Top5PrimeVendors);
		HomePage.ShowWidgetDetails();
	   assertTrue(RegisteredExpenseContractsPage.GetTransactionCount() >= 13339); 
    }
	@Test
    public void VerifyTop5AwardMethodsTransactionCount(){
		ContractsPage.GoToTop5DetailsPage(WidgetOption.Top5AwardMethods);
		HomePage.ShowWidgetDetails();
	   assertTrue(RegisteredExpenseContractsPage.GetTransactionCount() >= 13339); 
    }
	@Test
    public void VerifyTop5AgenciesTransactionCount(){
		ContractsPage.GoToTop5DetailsPage(WidgetOption.Top5Agencies);
		HomePage.ShowWidgetDetails();
	   assertTrue(RegisteredExpenseContractsPage.GetTransactionCount() >= 13339); 
    }
	@Test
    public void VerifyContractsByIndustriesTransactionCount(){
		ContractsPage.GoToTop5DetailsPage(WidgetOption.ContractsByIndustries);
		HomePage.ShowWidgetDetails();
	   assertTrue(RegisteredExpenseContractsPage.GetTransactionCount() >= 13339); 
    }
    @Test
    public void VerifyContractsBySizeTransactionCount(){
		ContractsPage.GoToTop5DetailsPage(WidgetOption.ContractsBySize);
		HomePage.ShowWidgetDetails();
	   assertTrue(RegisteredExpenseContractsPage.GetTransactionCount() >= 13339); 
    }
    
    /* ***************** Test Widget Transaction Total Amount ****************** */
    @Test
    public void VerifyTop5MasterAgreementsTransactionAmount(){
    	Float transactionAmt = 6.16f;
		ContractsPage.GoToTop5DetailsPage(WidgetOption.Top5MasterAgreements);
		HomePage.ShowWidgetDetails();
		assertTrue(HomePage.GetTransactionAmount()>= transactionAmt);
    }
    @Test
    public void VerifyTop5MasterAgreementModificationsTransactionAmount(){
    	Float transactionAmt = 52.28f;
		ContractsPage.GoToTop5DetailsPage(WidgetOption.Top5MasterAgreementModifications);
		HomePage.ShowWidgetDetails();
		assertTrue(HomePage.GetTransactionAmount()>= transactionAmt);
    }
	@Test
    public void VerifyTop5ContractsTransactionAmount(){
		Float transactionAmt = 14.09f;
		ContractsPage.GoToTop5DetailsPage(WidgetOption.Top5Contracts);
		HomePage.ShowWidgetDetails();
		assertTrue(HomePage.GetTransactionAmount()>= transactionAmt);
    }
	@Test
    public void VerifyTop5ContractAmountModificationsTransactionAmount(){
		Float transactionAmt = 2.1f;
		ContractsPage.GoToTop5DetailsPage(WidgetOption.Top5ContractAmountModifications);
		HomePage.ShowWidgetDetails();
		assertTrue(HomePage.GetTransactionAmount()>= transactionAmt);
    }
	@Test
    public void VerifyTop5PrimeVendorsTransactionAmount(){
		Float transactionAmt = 16.71f;
		ContractsPage.GoToTop5DetailsPage(WidgetOption.Top5PrimeVendors);
		HomePage.ShowWidgetDetails();
		assertTrue(HomePage.GetTransactionAmount()>= transactionAmt);
    }
	@Test
    public void VerifyTop5AwardMethodsTransactionAmount(){
		Float transactionAmt = 16.71f;
		ContractsPage.GoToTop5DetailsPage(WidgetOption.Top5AwardMethods);
		HomePage.ShowWidgetDetails();
		assertTrue(HomePage.GetTransactionAmount()>= transactionAmt);
    }
	@Test
    public void VerifyTop5AgenciesTransactionAmount(){
		Float transactionAmt = 16.71f;
		ContractsPage.GoToTop5DetailsPage(WidgetOption.Top5Agencies);
		HomePage.ShowWidgetDetails();
		assertTrue(HomePage.GetTransactionAmount()>= transactionAmt);
    }
	@Test
    public void VerifyContractsByIndustriesTransactionAmount(){
		Float transactionAmt = 16.71f;
		ContractsPage.GoToTop5DetailsPage(WidgetOption.ContractsByIndustries);
		HomePage.ShowWidgetDetails();
		assertTrue(HomePage.GetTransactionAmount()>= transactionAmt); 
    }
    @Test
    public void VerifyContractsBySizeTransactionAmount(){
    	Float transactionAmt = 16.71f;
		ContractsPage.GoToTop5DetailsPage(WidgetOption.ContractsBySize);
		HomePage.ShowWidgetDetails();
		assertTrue(HomePage.GetTransactionAmount()>= transactionAmt);
    }
}
