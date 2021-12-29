package FunctionalContractsOGE;

import static org.junit.Assert.assertEquals;

import static org.junit.Assert.assertTrue;

import java.sql.SQLException;

import org.junit.Before;
import org.junit.Test;

import pages.contracts.ActiveExpenseContractsPage;
import pages.contracts.ContractsPage;
import pages.contracts.ContractsPage.WidgetOption;
import pages.contracts.RegisteredExpenseContractsPage;
import pages.home.HomePage;
import utilities.OGENYCBaseTest;
import utilities.OGENYCDatabaseUtil;
import helpers.Helper;
import navigation.SecondaryMenuNavigation.OtherGovernmentEntities;
import utilities.TestStatusReport;
public class OGERegisteredExpenseContractsDetailsTest extends TestStatusReport{
	//	public class OGERegisteredExpenseContractsDetailsTest extends OGENYCBaseTest {
	int year =  Integer.parseInt(OGENYCBaseTest.prop.getProperty("year"));
	@Before
	public void GoToPage() {
		
		if(!OtherGovernmentEntities.IsAt())
			OtherGovernmentEntities.GoTo();
	
		ContractsPage.GoTo();	
	
		if(!(Helper.getCurrentSelectedYear()).equalsIgnoreCase(OGENYCBaseTest.prop.getProperty("CurrentYear")))
			HomePage.SelectYear(OGENYCBaseTest.prop.getProperty("CurrentYear"));
		HomePage.ShowWidgetDetails();
		RegisteredExpenseContractsPage.GoTo();
	}

	/* ***************** Test Widget Transaction Total Count ****************** */
	@Test
	public void VerifyTop5MasterAgreementsTransactionCount() throws SQLException {
		ContractsPage.GoToTop5DetailsPage(WidgetOption.TopMasterAgreements);
		HomePage.ShowWidgetDetails();
		int NumOfREContractsDetailsCountDB = OGENYCDatabaseUtil.getOGEREContractsMasterDetailsCount(year,'B');
		int numOfREContractsDetailsCountapp = RegisteredExpenseContractsPage.GetOGETransactionCount();
		assertEquals(" Registered Expense master contracts widget Details page table count did not match", numOfREContractsDetailsCountapp, NumOfREContractsDetailsCountDB);
		
		String WidgetDetailsTitle =  "Master Agreements Registered Expense Contracts Transactions";
		String WidgetDetailsTitleApp = HomePage.DetailsPagetitle();
	    assertEquals("Registered Expense Contracts Master Agreement Widget title did not match", WidgetDetailsTitle, WidgetDetailsTitleApp); 
	    
	    
	    String WidgetDetailsAmountDB =  OGENYCDatabaseUtil.getOGEREContractsMasterContractsDetailsAmount(year,'B');
		String WidgetDetailsAmountapp = ActiveExpenseContractsPage.GetTransactionAmount1();
		assertEquals("Registered  Expense Contracts Master Agreement  Widget Details page total Contract amount did not match", WidgetDetailsAmountapp, WidgetDetailsAmountDB);
	}
	     
	
	@Test
	public void VerifyTop5ContractsTransactionCount() throws SQLException {
		ContractsPage.GoToTop5DetailsPage(WidgetOption.Top5Contracts);
		HomePage.ShowWidgetDetails();
		int NumOfREContractsDetailsCountDB = OGENYCDatabaseUtil.getOGEREContractsDetailsCount(year,'B');
		int numOfREContractsDetailsCountapp = RegisteredExpenseContractsPage.GetOGETransactionCount();
		assertEquals(" Registered Expense Contracts  widget Details page table count did not match", numOfREContractsDetailsCountapp, NumOfREContractsDetailsCountDB); 
		
		String WidgetDetailsTitle =  "Contracts Registered Expense Contracts Transactions";
		String WidgetDetailsTitleApp = HomePage.DetailsPagetitle();
	    assertEquals("Registered Expense Contracts contracts Widget title did not match", WidgetDetailsTitle, WidgetDetailsTitleApp); 
	    
	    String WidgetDetailsAmountDB =  OGENYCDatabaseUtil.getOGEREContractsDetailsAmount(year,'B');
		String WidgetDetailsAmountapp = ActiveExpenseContractsPage.GetTransactionAmount1();
		assertEquals("Registered  Expense Contracts Contracts  Widget Details page total Contract amount did not match", WidgetDetailsAmountapp, WidgetDetailsAmountDB);
		}
	
	@Test
	public void VerifyTop5PrimeVendorsTransactionCount() throws SQLException {
		ContractsPage.GoToTop5DetailsPage(WidgetOption.Top5PrimeVendors);
		HomePage.ShowWidgetDetails();
		int NumOfREContractsDetailsCountDB =  OGENYCDatabaseUtil.getOGEREContractsDetailsCount(year,'B');
		int numOfREContractsDetailsCountapp = RegisteredExpenseContractsPage.GetOGETransactionCount();
		assertEquals(" Registered Expense Contracts Prime Vendors widget Details page table count did not match", numOfREContractsDetailsCountapp, NumOfREContractsDetailsCountDB); 
		
		String WidgetDetailsTitle =  "Prime Vendors Registered Expense Contracts Transactions";
		String WidgetDetailsTitleApp = HomePage.DetailsPagetitle();
	    assertEquals("Registered Expense Contracts Prime Vendors Widget title did not match", WidgetDetailsTitle, WidgetDetailsTitleApp); 
	    
	    String WidgetDetailsAmountDB =  OGENYCDatabaseUtil.getOGEREContractsDetailsAmount(year,'B');
		String WidgetDetailsAmountapp = ActiveExpenseContractsPage.GetTransactionAmount1();
		assertEquals("Registered Expense Contracts PrimeVendors  Widget Details page total Contract amount did not match", WidgetDetailsAmountapp, WidgetDetailsAmountDB);
		}
	@Test
	public void VerifyTopAwardMethodsTransactionCount() throws SQLException {
		ContractsPage.GoToTop5DetailsPage(WidgetOption.TopAwardMethods);
		HomePage.ShowWidgetDetails();
		int NumOfREContractsDetailsCountDB =  OGENYCDatabaseUtil.getOGEREContractsDetailsCount(year,'B');
		int numOfREContractsDetailsCountapp = RegisteredExpenseContractsPage.GetOGETransactionCount();
		assertEquals(" Registered Expense Contracts Award Method widget Details page table count did not match", numOfREContractsDetailsCountapp, NumOfREContractsDetailsCountDB); 
		
		String WidgetDetailsTitle =  "Award Methods Registered Expense Contracts Transactions";
		String WidgetDetailsTitleApp = HomePage.DetailsPagetitle();
	    assertEquals("Registered Expense Contracts AWard Method Widget title did not match", WidgetDetailsTitle, WidgetDetailsTitleApp); 
	    
	    String WidgetDetailsAmountDB =  OGENYCDatabaseUtil.getOGEREContractsDetailsAmount(year,'B');
		String WidgetDetailsAmountapp = ActiveExpenseContractsPage.GetTransactionAmount1();
		assertEquals("Registered  Expense Contracts AwardMethods  Widget Details page total Contract amount did not match", WidgetDetailsAmountapp, WidgetDetailsAmountDB);
		}
	@Test
	public void VerifyTop5DepartmentsTransactionCount() throws SQLException {
		ContractsPage.GoToTop5DetailsPage(WidgetOption.TopDepartments);
		HomePage.ShowWidgetDetails();
		int NumOfREContractsDetailsCountDB =  OGENYCDatabaseUtil.getOGEREContractsDetailsCount(year,'B');
		int numOfREContractsDetailsCountapp = RegisteredExpenseContractsPage.GetOGETransactionCount();
		assertEquals("  Registered Expense Contracts Agencies widget Details page table count did not match", numOfREContractsDetailsCountapp, NumOfREContractsDetailsCountDB); 
		
		String WidgetDetailsTitle =  "Departments Registered Expense Contracts Transactions";
		String WidgetDetailsTitleApp = HomePage.DetailsPagetitle();
	    assertEquals("Registered Expense Contracts Agencies Widget title did not match", WidgetDetailsTitle, WidgetDetailsTitleApp); 
	    
	    String WidgetDetailsAmountDB =  OGENYCDatabaseUtil.getOGEREContractsDetailsAmount(year,'B');
		String WidgetDetailsAmountapp = ActiveExpenseContractsPage.GetTransactionAmount1();
		assertEquals("Registered  Expense Contracts Agencies  Widget Details page total Contract amount did not match", WidgetDetailsAmountapp, WidgetDetailsAmountDB);
		}
	@Test
	public void VerifyContractsByIndustriesTransactionCount() throws SQLException {
		ContractsPage.GoToTop5DetailsPage(WidgetOption.ContractsByIndustries);
		HomePage.ShowWidgetDetails();
		int NumOfREContractsDetailsCountDB =  OGENYCDatabaseUtil.getOGEREContractsDetailsCount(year,'B');
		int numOfREContractsDetailsCountapp = RegisteredExpenseContractsPage.GetOGETransactionCount();
		assertEquals("  Registered Expense Contracts by Industies widget Details page table count did not match", numOfREContractsDetailsCountapp, NumOfREContractsDetailsCountDB); 
		
		String WidgetDetailsTitle =  "Contracts by Industries Registered Expense Contracts Transactions";
		String WidgetDetailsTitleApp = HomePage.DetailsPagetitle();
	    assertEquals("Registered Expense Contracts Industries Widget title did not match", WidgetDetailsTitle, WidgetDetailsTitleApp); 
	    
	    String WidgetDetailsAmountDB =  OGENYCDatabaseUtil.getOGEREContractsDetailsAmount(year,'B');
		String WidgetDetailsAmountapp = ActiveExpenseContractsPage.GetTransactionAmount1();
		assertEquals("Registered  Expense Contracts ContractsByIndustries  Widget Details page total Contract amount did not match", WidgetDetailsAmountapp, WidgetDetailsAmountDB);
		}
	@Test
	public void VerifyContractsBySizeTransactionCount() throws SQLException {
		ContractsPage.GoToTop5DetailsPage(WidgetOption.ContractsBySize);
		HomePage.ShowWidgetDetails();
		int NumOfREContractsDetailsCountDB =  OGENYCDatabaseUtil.getOGEREContractsDetailsCount(year,'B');
		int numOfREContractsDetailsCountapp = RegisteredExpenseContractsPage.GetOGETransactionCount();
		assertEquals(" Registered Expense Contracts by size  widget Details page table count did not match", numOfREContractsDetailsCountapp, NumOfREContractsDetailsCountDB); 
		
		String WidgetDetailsTitle =  "Contracts by Size Registered Expense Contracts Transactions";
		String WidgetDetailsTitleApp = HomePage.DetailsPagetitle();
	    assertEquals("Registered Expense Contracts Contracts by Sizes Widget title did not match", WidgetDetailsTitle, WidgetDetailsTitleApp); 
	    
	    String WidgetDetailsAmountDB =  OGENYCDatabaseUtil.getOGEREContractsDetailsAmount(year,'B');
		String WidgetDetailsAmountapp = ActiveExpenseContractsPage.GetTransactionAmount1();
		assertEquals("Registered  Expense Contracts ContractsBySize  Widget Details page total Contract amount did not match", WidgetDetailsAmountapp, WidgetDetailsAmountDB);
		}

	/* ***************** Test Widget Transaction Count ****************** */
	/*
	 
    @Test
    public void VerifyContractsBySizeTransactionCount(){
		ContractsPage.GoToTop5DetailsPage(WidgetOption.ContractsBySize);
		HomePage.ShowWidgetDetails();
	   assertTrue(RegisteredExpenseContractsPage.GetTransactionCount() >= 13339); 
    }

	/* ***************** Test Widget Transaction Amount *************** 
	@Test
	public void VerifyTop5MasterAgreementsTransactionAmount(){
		Float transactionAmt = 6.16f;
		ContractsPage.GoToTop5DetailsPage(WidgetOption.Top5MasterAgreements);
		HomePage.ShowWidgetDetails();
		assertTrue(HomePage.GetTransactionAmount()>= transactionAmt);}

	*/

}
