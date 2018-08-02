package FunctionalContractsSubVendors;

import static org.junit.Assert.assertEquals;

import static org.junit.Assert.assertTrue;

import java.sql.SQLException;
import java.util.Arrays;

import org.junit.Before;
import org.junit.Test;

import navigation.SubVendorCategory.SubVendorCategoryOption;
import navigation.TopNavigation.Contracts.ActiveExpenseContracts;
import pages.contracts.ActiveExpenseContractsPage;
import pages.contracts.ContractsPage;
//import pages.contracts.ContractsPage;
//import pages.contracts.ContractsPage.WidgetOption;
import pages.home.HomePage;
import pages.spending.SpendingPage;
import pages.subvendors.SubVendorsPage;
import pages.subvendors.SubVendorsPage.WidgetOption;
import utilities.NYCBaseTest;
import utilities.NYCDatabaseUtil;
import utilities.TestStatusReport;
import helpers.Helper;

//public class TotalActiveSubVendorContractsWidgetDetailsTest extends NYCBaseTest {
public class TotalActiveSubVendorContractsWidgetDetailsTest extends TestStatusReport{
	int year =  Integer.parseInt(NYCBaseTest.prop.getProperty("year"));
	@Before
	 public void GoToPage(){
		  // if (!SubVendorsPage.IsAt())
				SubVendorsPage.GoTo("Contracts", SubVendorCategoryOption.SubVendorsHome);
			   if(!(Helper.getCurrentSelectedYear()).equalsIgnoreCase(NYCBaseTest.prop.getProperty("CurrentYear")))
			   HomePage.SelectYear(NYCBaseTest.prop.getProperty("CurrentYear"));
	    }

	

	/* ***************** Test Widget Counts ****************** */

	
	@Test
	public void VerifyTop5SubVendorContracts() throws SQLException {
		SubVendorsPage.GoToTop5DetailsPage(WidgetOption.Top5SubContracts);
		
		Integer totalCheckswidgetCountDB = NYCDatabaseUtil.getSubContractsDetailsCount(year,'B');
		Integer totalChecksWidgetCountApp = SubVendorsPage.GetTransactionCount2();
		assertEquals("Sub Vendors Contracts widget count  did not match with the DB",totalChecksWidgetCountApp, totalCheckswidgetCountDB);
		
		String WidgetDetailsTitle =  "Total Active Sub Vendor Contracts Transactions" ;
		String WidgetDetailsTitleApp = HomePage.DetailsPagetitle();
	    assertEquals("Sub Vendors Contracts  Widget title did not match", WidgetDetailsTitle, WidgetDetailsTitleApp); 
	    
	    String WidgetDetailsAmountDB =  NYCDatabaseUtil.getSubContractsDetailsAmount(year,'B');
		String WidgetDetailsAmountApp = ActiveExpenseContractsPage.GetTransactionAmount1();
	    assertEquals("Sub Vendors Contracts Widget Details page total Contract amount did not match", WidgetDetailsAmountApp, WidgetDetailsAmountDB); 
	  

	}

	
	@Test
	public void VerifyTop5SubContractAmountModificationsTransactionCount() throws SQLException {
		SubVendorsPage.GoToTop5DetailsPage(WidgetOption.Top5SubContractAmountModifications);
	
		int NumOfAEContractsDetailsCountDB =  NYCDatabaseUtil.getSubContractsModDetailsCount(year,'B');
		int numOfAEContractsDetailsCountapp = SubVendorsPage.GetTransactionCount2();
		assertEquals("Total Active SubVendor  Sub Contracts amount modification widget Details page table count did not match", numOfAEContractsDetailsCountapp, NumOfAEContractsDetailsCountDB); 
		
		String WidgetDetailsTitle =  "Amount Modifications by Total Active Sub Vendor Contracts Transactions";
		String WidgetDetailsTitleApp = HomePage.DetailsPagetitle();
	   assertEquals("Total Active SubVendor Sub Contracts amount modification Widget title did not match", WidgetDetailsTitle, WidgetDetailsTitleApp); 
	    

	   String WidgetDetailsAmountDB =  NYCDatabaseUtil.getSubContractsModDetailsAmount(year,'B');
		String WidgetDetailsAmountapp = ActiveExpenseContractsPage.GetTransactionAmount1();
		assertEquals("Total Active SubVendor Sub Contracts amount modification  Widget Details page total Contract amount did not match", WidgetDetailsAmountapp, WidgetDetailsAmountDB);
	}
	@Test
	public void VerifyTop5PrimeVendorsTransactionCount() throws SQLException {
		SubVendorsPage.GoToTop5DetailsPage(WidgetOption.Top5PrimeVendors);
	
		int NumOfAEContractsDetailsCountyear =  NYCDatabaseUtil.getSubContractsDetailsCount(year,'B');
		int numOfAEContractsDetailsCountapp = ActiveExpenseContractsPage.GetTransactionCount();
		assertEquals("Total Active SubVendor Contracts  Prime Vendors widget Details page table count did not match", numOfAEContractsDetailsCountapp, NumOfAEContractsDetailsCountyear);
		
		String WidgetDetailsTitle =  "Prime Vendors with Total Active Sub Vendor Contracts Transactions";
		String WidgetDetailsTitleApp = HomePage.DetailsPagetitle();
	    assertEquals("Total Active SubVendor Contracts Prime Vendors Widget title did not match", WidgetDetailsTitle, WidgetDetailsTitleApp); 
	    

	    String WidgetDetailsAmountDB =  NYCDatabaseUtil.getSubContractsDetailsAmount(year,'B');
		String WidgetDetailsAmountapp = ActiveExpenseContractsPage.GetTransactionAmount1();
		assertEquals("Total Active SubVendor Contracts PrimeVendors  Widget Details page total Contract amount did not match", WidgetDetailsAmountapp, WidgetDetailsAmountDB);
	}
	
	@Test
	public void VerifyTop5SubVendorsTransactionCount() throws SQLException {
		SubVendorsPage.GoToTop5DetailsPage(WidgetOption.Top5SubVendors);
	
		int NumOfAEContractsDetailsCountyear =  NYCDatabaseUtil.getSubContractsDetailsCount(year,'B');
		int numOfAEContractsDetailsCountapp = ActiveExpenseContractsPage.GetTransactionCount();
		assertEquals("Total Active SubVendor Contracts  Sub Vendors widget Details page table count did not match", numOfAEContractsDetailsCountapp, NumOfAEContractsDetailsCountyear);
		
		String WidgetDetailsTitle =  "Total Active Sub Vendor Contracts Transactions";
		String WidgetDetailsTitleApp = HomePage.DetailsPagetitle();
	    assertEquals("Total Active SubVendor Contracts  Sub  Vendors Widget title did not match", WidgetDetailsTitle, WidgetDetailsTitleApp); 
	    

	    String WidgetDetailsAmountDB =  NYCDatabaseUtil.getSubContractsDetailsAmount(year,'B');
		String WidgetDetailsAmountapp = ActiveExpenseContractsPage.GetTransactionAmount1();
		assertEquals("Total Active SubVendor Contracts  Sub Vendors Widget Details page total Contract amount did not match", WidgetDetailsAmountapp, WidgetDetailsAmountDB);
	}
	
	@Test
	public void VerifyTop5AwardMethodsTransactionCount() throws SQLException {
		SubVendorsPage.GoToTop5DetailsPage(WidgetOption.Top5AwardMethods);
		HomePage.ShowWidgetDetails();
		int NumOfAEContractsDetailsCountyear =  NYCDatabaseUtil.getSubContractsDetailsCount(year,'B');
		int numOfAEContractsDetailsCountapp = ActiveExpenseContractsPage.GetTransactionCount();
		assertEquals("Total Active SubVendor Contracts  Award Method widget Details page table count did not match", numOfAEContractsDetailsCountapp, NumOfAEContractsDetailsCountyear);

		String WidgetDetailsTitle =  "Award Methods by Total Active Sub Vendor Contracts Transactions";
		String WidgetDetailsTitleApp = HomePage.DetailsPagetitle();
	    assertEquals("Total Active SubVendor Contracts  AWard Method Widget title did not match", WidgetDetailsTitle, WidgetDetailsTitleApp); 
	    

	    String WidgetDetailsAmountDB =  NYCDatabaseUtil.getSubContractsDetailsAmount(year,'B');
		String WidgetDetailsAmountapp = ActiveExpenseContractsPage.GetTransactionAmount1();
		assertEquals("Total Active SubVendor Contracts  AwardMethods Widget Details page total Contract amount did not match", WidgetDetailsAmountapp, WidgetDetailsAmountDB);
	}
	@Test
	public void VerifyTop5AgenciesTransactionCount() throws SQLException {
		SubVendorsPage.GoToTop5DetailsPage(WidgetOption.Top5Agencies);
		HomePage.ShowWidgetDetails();
		
		int NumOfAEContractsDetailsCountyear =  NYCDatabaseUtil.getSubContractsDetailsCount(year,'B');
		int numOfAEContractsDetailsCountapp = ActiveExpenseContractsPage.GetTransactionCount();
		assertEquals("Total Active SubVendor Contracts  Agencies widget Details page table count did not match", numOfAEContractsDetailsCountapp, NumOfAEContractsDetailsCountyear); 
		
		String WidgetDetailsTitle =  "Agencies by Total Active Sub Vendor Contracts Transactions";
		String WidgetDetailsTitleApp = HomePage.DetailsPagetitle();
	    assertEquals("Total Active SubVendor Contracts   Agencies Widget title did not match", WidgetDetailsTitle, WidgetDetailsTitleApp); 
	    

	    String WidgetDetailsAmountDB =  NYCDatabaseUtil.getSubContractsDetailsAmount(year,'B');
		String WidgetDetailsAmountapp = ActiveExpenseContractsPage.GetTransactionAmount1();
		assertEquals("Total Active SubVendor Contracts   Agencies  Widget Details page total Contract amount did not match", WidgetDetailsAmountapp, WidgetDetailsAmountDB);
	}
	@Test
	public void VerifyContractsByIndustriesTransactionCount() throws SQLException {
		SubVendorsPage.GoToTop5DetailsPage(WidgetOption.ContractsbyIndustries);
		HomePage.ShowWidgetDetails();
		int NumOfAEContractsDetailsCountyear =  NYCDatabaseUtil.getSubContractsDetailsCount(year,'B');
		int numOfAEContractsDetailsCountapp = ActiveExpenseContractsPage.GetTransactionCount();
		assertEquals("Total Active SubVendor Contracts by Industies widget Details page table count did not match", numOfAEContractsDetailsCountapp, NumOfAEContractsDetailsCountyear); 
		
		String WidgetDetailsTitle =  "Contracts by Industries by Total Active Sub Vendor Contracts Transactions";
		String WidgetDetailsTitleApp = HomePage.DetailsPagetitle();
	    assertEquals("Total Active SubVendor Contracts Industries Widget title did not match", WidgetDetailsTitle, WidgetDetailsTitleApp); 
	    

	    String WidgetDetailsAmountDB =  NYCDatabaseUtil.getSubContractsDetailsAmount(year,'B');
		String WidgetDetailsAmountapp = ActiveExpenseContractsPage.GetTransactionAmount1();
		assertEquals("Total Active SubVendor Contracts ContractsByIndustries  Widget Details page total Contract amount did not match", WidgetDetailsAmountapp, WidgetDetailsAmountDB);
	}
	@Test
	public void VerifyContractsBySizeTransactionCount() throws SQLException {
		SubVendorsPage.GoToTop5DetailsPage(WidgetOption.ContractsbySize);
	
		int NumOfAEContractsDetailsCountyear =  NYCDatabaseUtil.getSubContractsDetailsCount(year,'B');
		int numOfAEContractsDetailsCountapp = ActiveExpenseContractsPage.GetTransactionCount();
		assertEquals("Total Active SubVendor Contracts Contracts by size  widget Details page table count did not match", numOfAEContractsDetailsCountapp, NumOfAEContractsDetailsCountyear);
		
		String WidgetDetailsTitle =  "Contracts by Size by Total Active Sub Vendor Contracts Transactions";
		String WidgetDetailsTitleApp = HomePage.DetailsPagetitle();
	    assertEquals("Total Active SubVendor Contracts Contracts by Sizes Widget title did not match", WidgetDetailsTitle, WidgetDetailsTitleApp); 
	    

	    String WidgetDetailsAmountDB =  NYCDatabaseUtil.getSubContractsDetailsAmount(year,'B');
		String WidgetDetailsAmountapp = ActiveExpenseContractsPage.GetTransactionAmount1();
		assertEquals("Total Active SubVendor Contracts ContractsBySize  Widget Details page total Contract amount did not match", WidgetDetailsAmountapp, WidgetDetailsAmountDB);
	}


	
}