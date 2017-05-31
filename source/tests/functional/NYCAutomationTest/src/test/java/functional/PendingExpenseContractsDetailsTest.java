package functional;

import static org.junit.Assert.assertEquals;
import static org.junit.Assert.assertTrue;

import java.sql.SQLException;

import org.junit.Before;
import org.junit.Test;

import pages.contracts.ActiveExpenseContractsPage;
import pages.contracts.ContractsPage;
import pages.contracts.ContractsPage.WidgetOption;
import pages.contracts.PendingExpenseContractsPage;
import pages.home.HomePage;
import utilities.NYCBaseTest;
import utilities.NYCDatabaseUtil;
import helpers.Helper;

public class PendingExpenseContractsDetailsTest extends NYCBaseTest{

	@Before
    public void GoToPage(){
	   if(!(Helper.getCurrentSelectedYear()).equalsIgnoreCase(NYCBaseTest.prop.getProperty("CurrentYear")))
		   HomePage.SelectYear(NYCBaseTest.prop.getProperty("CurrentYear"));
	   PendingExpenseContractsPage.GoTo();
	   HomePage.ShowWidgetDetails();
    }
	
	/* ***************** Test Widget Transaction Count ****************** */
	@Test
    public void VerifyTop5MasterAgreementsTransactionCount() throws SQLException{
		ContractsPage.GoToTop5DetailsPage(WidgetOption.Top5MasterAgreements);
		HomePage.ShowWidgetDetails();
	   //assertTrue(PendingExpenseContractsPage.GetTransactionCount() >= 74); 
		int NumOfPEContractsDetailsCount2016 =  NYCDatabaseUtil.getPEContractsMasterDetailsCount(2016,'B');
		 int numOfPEContractsDetailsCountapp = PendingExpenseContractsPage.GetTransactionCount();
	 
		 assertEquals("Number of Pending Expense  master contracts widget Details page table count did not match", numOfPEContractsDetailsCountapp, NumOfPEContractsDetailsCount2016); 
   }

	@Test
    public void VerifyTop5MasterAgreementModificationsTransactionCount()throws SQLException{
		ContractsPage.GoToTop5DetailsPage(WidgetOption.Top5MasterAgreementModifications);
		HomePage.ShowWidgetDetails();
	  // assertTrue(PendingExpenseContractsPage.GetTransactionCount() >= 13); 
		int NumOfPEContractsDetailsCount2016 =  NYCDatabaseUtil.getPEContractsMasterModificationsDetailsCount(2016,'B');
		 int numOfPEContractsDetailsCountapp = PendingExpenseContractsPage.GetTransactionCount();
		 
	  		 assertEquals("Number of Pending Expense master contracts modification widget Details page table count did not match", numOfPEContractsDetailsCountapp, NumOfPEContractsDetailsCount2016); 
  }
	@Test
    public void VerifyTop5ContractsTransactionCount()throws SQLException{
		ContractsPage.GoToTop5DetailsPage(WidgetOption.Top5Contracts);
		HomePage.ShowWidgetDetails();
	   //assertTrue(PendingExpenseContractsPage.GetTransactionCount() >= 849); 
		int NumOfPEContractsDetailsCount2016 =  NYCDatabaseUtil.getPEContractsDetailsCount(2016,'B');
		 int numOfPEContractsDetailsCountapp = PendingExpenseContractsPage.GetTransactionCount();
		 
		 assertEquals("Number ofPending Expense contracts widget Details page table count did not match", numOfPEContractsDetailsCountapp, NumOfPEContractsDetailsCount2016); 
  }
	@Test
    public void VerifyTop5ContractAmountModificationsTransactionCount()throws SQLException{
		ContractsPage.GoToTop5DetailsPage(WidgetOption.Top5ContractsAmountModifications);
		HomePage.ShowWidgetDetails();
	 //  assertTrue(PendingExpenseContractsPage.GetTransactionCount() >= 400); 
		int NumOfPEContractsDetailsCount2016 =  NYCDatabaseUtil.getPEContractsModificationsDetailsCount(2016,'B');
		 int numOfPEContractsDetailsCountapp = PendingExpenseContractsPage.GetTransactionCount();
		 
		 assertEquals("Number of Pending Expense contracts widget Details page table count did not match", numOfPEContractsDetailsCountapp, NumOfPEContractsDetailsCount2016); 
 }
	@Test
    public void VerifyTop5PrimeVendorsTransactionCount()throws SQLException{
		ContractsPage.GoToTop5DetailsPage(WidgetOption.Top5PrimeVendors);
		HomePage.ShowWidgetDetails();
	  // assertTrue(PendingExpenseContractsPage.GetTransactionCount() >= 923); 
		int NumOfPEContractsDetailsCount2016 =  NYCDatabaseUtil.getPEContractsDetailsCount(2016,'B');
		 int numOfPEContractsDetailsCountapp = PendingExpenseContractsPage.GetTransactionCount();
		 
		 assertEquals("Number of Pending Expense contracts Prime Vendor widget Details page table count did not match", numOfPEContractsDetailsCountapp, NumOfPEContractsDetailsCount2016); 
}
	@Test
    public void VerifyTop5AwardMethodsTransactionCount()throws SQLException{
		ContractsPage.GoToTop5DetailsPage(WidgetOption.Top5AwardMethods);
		HomePage.ShowWidgetDetails();
	   //assertTrue(PendingExpenseContractsPage.GetTransactionCount() >= 923); 
		int NumOfPEContractsDetailsCount2016 =  NYCDatabaseUtil.getPEContractsDetailsCount(2016,'B');
		 int numOfPEContractsDetailsCountapp = PendingExpenseContractsPage.GetTransactionCount();
		 
		 assertEquals("Number of PendingExpense  contracts Award Method widget Details page table count did not match", numOfPEContractsDetailsCountapp, NumOfPEContractsDetailsCount2016); 
}
	@Test
    public void VerifyTop5AgenciesTransactionCount()throws SQLException{
		ContractsPage.GoToTop5DetailsPage(WidgetOption.Top5Agencies);
		HomePage.ShowWidgetDetails();
	   //assertTrue(PendingExpenseContractsPage.GetTransactionCount() >= 923); 
		int NumOfPEContractsDetailsCount2016 =  NYCDatabaseUtil.getPEContractsDetailsCount(2016,'B');
		 int numOfPEContractsDetailsCountapp = PendingExpenseContractsPage.GetTransactionCount();
		 
		 assertEquals("Number of Pending Expense contracts Agencies widget Details page table count did not match", numOfPEContractsDetailsCountapp, NumOfPEContractsDetailsCount2016); 
}
	@Test
    public void VerifyContractsByIndustriesTransactionCount()throws SQLException{
		ContractsPage.GoToTop5DetailsPage(WidgetOption.ContractsByIndustries);
		HomePage.ShowWidgetDetails();
	   //assertTrue(PendingExpenseContractsPage.GetTransactionCount() >= 923); 
		int NumOfPEContractsDetailsCount2016 =  NYCDatabaseUtil.getPEContractsDetailsCount(2016,'B');
		 int numOfPEContractsDetailsCountapp = PendingExpenseContractsPage.GetTransactionCount();
		 
		 assertEquals("Number of Pending Expense contracts Industries widget Details page table count did not match", numOfPEContractsDetailsCountapp, NumOfPEContractsDetailsCount2016); 
}
    @Test
    public void VerifyContractsBySizeTransactionCount()throws SQLException{
		ContractsPage.GoToTop5DetailsPage(WidgetOption.ContractsBySize);
		HomePage.ShowWidgetDetails();
	   //assertTrue(PendingExpenseContractsPage.GetTransactionCount() >= 923); 
		int NumOfPEContractsDetailsCount2016 =  NYCDatabaseUtil.getPEContractsDetailsCount(2016,'B');
		 int numOfPEContractsDetailsCountapp = PendingExpenseContractsPage.GetTransactionCount();
		 
		 assertEquals("Number of Pending Expense contracts by size widget Details page table count did not match", numOfPEContractsDetailsCountapp, NumOfPEContractsDetailsCount2016); 
}
}
