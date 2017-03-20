package functional;

import static org.junit.Assert.assertTrue;

import org.junit.Before;
import org.junit.Test;

import pages.contracts.ActiveExpenseContractsPage;
import pages.contracts.ContractsPage;
import pages.contracts.ContractsPage.WidgetOption;
import pages.home.HomePage;
import utilities.NYCBaseTest;
import utility.Helper;

public class ActiveExpenseContractsDetailsTest extends NYCBaseTest{

	@Before
    public void GoToPage(){
		ActiveExpenseContractsPage.GoTo();
	   if(!(Helper.getCurrentSelectedYear()).equalsIgnoreCase(NYCBaseTest.prop.getProperty("CurrentYear")))
		   HomePage.SelectYear(NYCBaseTest.prop.getProperty("CurrentYear"));
	   HomePage.ShowWidgetDetails();
    }
	
	/* ***************** Test Widget Transaction Count ****************** */
	@Test
    public void VerifyTop5MasterAgreementsTransactionCount(){
		ContractsPage.GoToTop5DetailsPage(WidgetOption.Top5MasterAgreements);
		HomePage.ShowWidgetDetails();
	   assertTrue(ActiveExpenseContractsPage.GetTransactionCount() >= 3725); 
    }
	@Test
    public void VerifyTop5MasterAgreementModificationsTransactionCount(){
		ContractsPage.GoToTop5DetailsPage(WidgetOption.Top5MasterAgreementModifications);
		HomePage.ShowWidgetDetails();
	   assertTrue(ActiveExpenseContractsPage.GetTransactionCount() >= 324); 
    }
	@Test
    public void VerifyTop5ContractsTransactionCount(){
		ContractsPage.GoToTop5DetailsPage(WidgetOption.Top5Contracts);
		HomePage.ShowWidgetDetails();
	   assertTrue(ActiveExpenseContractsPage.GetTransactionCount() >= 22659); 
    }
	@Test
    public void VerifyTop5ContractAmountModificationsTransactionCount(){
		ContractsPage.GoToTop5DetailsPage(WidgetOption.Top5ContractAmountModifications);
		HomePage.ShowWidgetDetails();
	   assertTrue(ActiveExpenseContractsPage.GetTransactionCount() >= 5201); 
    }
	@Test
    public void VerifyTop5PrimeVendorsTransactionCount(){
		ContractsPage.GoToTop5DetailsPage(WidgetOption.Top5PrimeVendors);
		HomePage.ShowWidgetDetails();
	   assertTrue(ActiveExpenseContractsPage.GetTransactionCount() >= 25892); 
    }
	@Test
    public void VerifyTop5AwardMethodsTransactionCount(){
		ContractsPage.GoToTop5DetailsPage(WidgetOption.Top5AwardMethods);
		HomePage.ShowWidgetDetails();
	   assertTrue(ActiveExpenseContractsPage.GetTransactionCount() >= 25892); 
    }
	@Test
    public void VerifyTop5AgenciesTransactionCount(){
		ContractsPage.GoToTop5DetailsPage(WidgetOption.Top5Agencies);
		HomePage.ShowWidgetDetails();
	   assertTrue(ActiveExpenseContractsPage.GetTransactionCount() >= 25892); 
    }
	@Test
    public void VerifyContractsByIndustriesTransactionCount(){
		ContractsPage.GoToTop5DetailsPage(WidgetOption.ContractsByIndustries);
		HomePage.ShowWidgetDetails();
	   assertTrue(ActiveExpenseContractsPage.GetTransactionCount() >= 25892); 
    }
    @Test
    public void VerifyContractsBySizeTransactionCount(){
		ContractsPage.GoToTop5DetailsPage(WidgetOption.ContractsBySize);
		HomePage.ShowWidgetDetails();
	   assertTrue(ActiveExpenseContractsPage.GetTransactionCount() >= 25892); 
    }
    
    /* ***************** Test Widget Transaction Total Amount ****************** */
    @Test
    public void VerifyTop5MasterAgreementsTransactionAmount(){
    	Float transactionAmt = 26.3f;
		ContractsPage.GoToTop5DetailsPage(WidgetOption.Top5MasterAgreements);
		HomePage.ShowWidgetDetails();
		assertTrue(HomePage.GetTransactionAmount()>= transactionAmt);
    }
    @Test
    public void VerifyTop5MasterAgreementModificationsTransactionAmount(){
    	Float transactionAmt = 8.74f;
		ContractsPage.GoToTop5DetailsPage(WidgetOption.Top5MasterAgreementModifications);
		HomePage.ShowWidgetDetails();
		assertTrue(HomePage.GetTransactionAmount()>= transactionAmt);
    }
	@Test
    public void VerifyTop5ContractsTransactionAmount(){
		Float transactionAmt = 107.02f;
		ContractsPage.GoToTop5DetailsPage(WidgetOption.Top5Contracts);
		HomePage.ShowWidgetDetails();
		assertTrue(HomePage.GetTransactionAmount()>= transactionAmt);
    }
	@Test
    public void VerifyTop5ContractAmountModificationsTransactionAmount(){
		Float transactionAmt = 46.15f;
		ContractsPage.GoToTop5DetailsPage(WidgetOption.Top5ContractAmountModifications);
		HomePage.ShowWidgetDetails();
		assertTrue(HomePage.GetTransactionAmount()>= transactionAmt);
    }
	@Test
    public void VerifyTop5PrimeVendorsTransactionAmount(){
		Float transactionAmt = 124.89f;
		ContractsPage.GoToTop5DetailsPage(WidgetOption.Top5PrimeVendors);
		HomePage.ShowWidgetDetails();
		assertTrue(HomePage.GetTransactionAmount()>= transactionAmt);
    }
	@Test
    public void VerifyTop5AwardMethodsTransactionAmount(){
		Float transactionAmt = 124.89f;
		ContractsPage.GoToTop5DetailsPage(WidgetOption.Top5AwardMethods);
		HomePage.ShowWidgetDetails();
		assertTrue(HomePage.GetTransactionAmount()>= transactionAmt);
    }
	@Test
    public void VerifyTop5AgenciesTransactionAmount(){
		Float transactionAmt = 124.89f;
		ContractsPage.GoToTop5DetailsPage(WidgetOption.Top5Agencies);
		HomePage.ShowWidgetDetails();
		assertTrue(HomePage.GetTransactionAmount()>= transactionAmt);
    }
	@Test
    public void VerifyContractsByIndustriesTransactionAmount(){
		Float transactionAmt = 124.89f;
		ContractsPage.GoToTop5DetailsPage(WidgetOption.ContractsByIndustries);
		HomePage.ShowWidgetDetails();
		assertTrue(HomePage.GetTransactionAmount()>= transactionAmt); 
    }
    @Test
    public void VerifyContractsBySizeTransactionAmount(){
    	Float transactionAmt = 124.89f;
		ContractsPage.GoToTop5DetailsPage(WidgetOption.ContractsBySize);
		HomePage.ShowWidgetDetails();
		assertTrue(HomePage.GetTransactionAmount()>= transactionAmt);
    }
}
