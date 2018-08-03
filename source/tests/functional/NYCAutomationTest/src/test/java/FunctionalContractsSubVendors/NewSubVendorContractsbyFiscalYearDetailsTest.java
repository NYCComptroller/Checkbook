package FunctionalContractsSubVendors;

import static org.junit.Assert.assertEquals;

import static org.junit.Assert.assertTrue;

import java.sql.SQLException;

import org.junit.After;
import org.junit.Before;
import org.junit.Test;
import org.openqa.selenium.support.ui.WebDriverWait;

import pages.contracts.ActiveExpenseContractsPage;
//import pages.contracts.ContractsPage;
//import pages.contracts.ContractsPage.WidgetOption;
import pages.home.HomePage;
import pages.mwbe.MWBEPage;
import pages.revenue.RevenuePage;
import pages.subvendors.RegisteredSubVendorContractsPage;
import pages.subvendors.SubVendorsPage;
import pages.subvendors.SubVendorsPage.WidgetOption;
import utilities.NYCBaseTest;
import utilities.NYCDatabaseUtil;
import helpers.Driver;
import helpers.Helper;
import navigation.MWBECategory.MWBECategoryOption;
import navigation.SubVendorCategory.SubVendorCategoryOption;
import utilities.TestStatusReport;
public class NewSubVendorContractsbyFiscalYearDetailsTest extends TestStatusReport{
	//public class NewSubVendorContractsbyFiscalYearDetailsTest extends NYCBaseTest {
	int year =  Integer.parseInt(NYCBaseTest.prop.getProperty("year"));
	@Before

	  
		 public void GoToPage(){
			 //  if (!SubVendorsPage.IsAt())
		SubVendorsPage.GoTo("Contracts", SubVendorCategoryOption.SubVendorsHome);
				   //RegisteredSubVendorContractsPage.GoTo();  
					   if(!(Helper.getCurrentSelectedYear()).equalsIgnoreCase(NYCBaseTest.prop.getProperty("CurrentYear")))
				   HomePage.SelectYear(NYCBaseTest.prop.getProperty("CurrentYear"));
				//   WebDriverWait wait = new WebDriverWait(Driver.Instance, 30);
					SubVendorsPage.GoToBottomNavContractslink();		
		    }
	
		
	/* ***************** Test Widget Transaction Count ****************** */
	
	
	@Test
	public void VerifyTop5SubVendorContracts() throws SQLException {
		SubVendorsPage.GoToTop5DetailsPage(WidgetOption.Top5SubContracts);
		
		Integer totalCheckswidgetCountDB = NYCDatabaseUtil.getSubContractsRegisteredDetailsCount(year,'B');
		Integer totalChecksWidgetCountApp = SubVendorsPage.GetTransactionCount2();
		assertEquals("Sub Vendors Contracts widget count  did not match with the DB",totalChecksWidgetCountApp, totalCheckswidgetCountDB);
		
		String WidgetDetailsTitle =  "New Sub Vendor Contracts by Fiscal Year Transactions" ;
		String WidgetDetailsTitleApp = HomePage.DetailsPagetitle();
	    assertEquals("Sub Vendors Contracts  Widget title did not match", WidgetDetailsTitle, WidgetDetailsTitleApp); 
	    
	    String WidgetDetailsAmountDB =  NYCDatabaseUtil.getSubContractsRegisteredDetailsAmount(year,'B');
		String WidgetDetailsAmountApp = ActiveExpenseContractsPage.GetTransactionAmount1();
	    assertEquals("Sub Vendors Contracts Widget Details page total Contract amount did not match", WidgetDetailsAmountApp, WidgetDetailsAmountDB); 
	  

	}

	
	@Test
	public void VerifyTop5SubContractAmountModificationsTransactionCount() throws SQLException {
		SubVendorsPage.GoToTop5DetailsPage(WidgetOption.Top5SubContractAmountModifications);
	
		int NumOfAEContractsDetailsCountDB =  NYCDatabaseUtil.getSubContractsModRegisteredDetailsCount(year,'B');
		int numOfAEContractsDetailsCountapp = SubVendorsPage.GetTransactionCount2();
		assertEquals("Total Active SubVendor  Sub Contracts amount modification widget Details page table count did not match", numOfAEContractsDetailsCountapp, NumOfAEContractsDetailsCountDB); 
		
		String WidgetDetailsTitle =  "Amount Modifications by New Sub Vendor Contracts by Fiscal Year Transactions";
		String WidgetDetailsTitleApp = HomePage.DetailsPagetitle();
	   assertEquals("Total Active SubVendor Sub Contracts amount modification Widget title did not match", WidgetDetailsTitle, WidgetDetailsTitleApp); 
	    

	   String WidgetDetailsAmountDB =  NYCDatabaseUtil.getSubContractsModRegisteredDetailsAmount(year,'B');
		String WidgetDetailsAmountapp = ActiveExpenseContractsPage.GetTransactionAmount1();
		assertEquals("Total Active SubVendor Sub Contracts amount modification  Widget Details page total Contract amount did not match", WidgetDetailsAmountapp, WidgetDetailsAmountDB);
	}
	@Test
	public void VerifyTop5PrimeVendorsTransactionCount() throws SQLException {
		SubVendorsPage.GoToTop5DetailsPage(WidgetOption.Top5PrimeVendors);
	
		int NumOfAEContractsDetailsCountyear =  NYCDatabaseUtil.getSubContractsRegisteredDetailsCount(year,'B');
		int numOfAEContractsDetailsCountapp = ActiveExpenseContractsPage.GetTransactionCount();
		assertEquals("Total Active SubVendor Contracts  Prime Vendors widget Details page table count did not match", numOfAEContractsDetailsCountapp, NumOfAEContractsDetailsCountyear);
		
		String WidgetDetailsTitle =  "Prime Vendors with New Sub Vendor Contracts by Fiscal Year Transactions";
		String WidgetDetailsTitleApp = HomePage.DetailsPagetitle();
	    assertEquals("Total Active SubVendor Contracts Prime Vendors Widget title did not match", WidgetDetailsTitle, WidgetDetailsTitleApp); 
	    

	    String WidgetDetailsAmountDB =  NYCDatabaseUtil.getSubContractsRegisteredDetailsAmount(year,'B');
		String WidgetDetailsAmountapp = ActiveExpenseContractsPage.GetTransactionAmount1();
		assertEquals("Total Active SubVendor Contracts PrimeVendors  Widget Details page total Contract amount did not match", WidgetDetailsAmountapp, WidgetDetailsAmountDB);
	}
	
	@Test
	public void VerifyTop5SubVendorsTransactionCount() throws SQLException {
		SubVendorsPage.GoToTop5DetailsPage(WidgetOption.Top5SubVendors);
	
		int NumOfAEContractsDetailsCountyear =  NYCDatabaseUtil.getSubContractsRegisteredDetailsCount(year,'B');
		int numOfAEContractsDetailsCountapp = ActiveExpenseContractsPage.GetTransactionCount();
		assertEquals("Total Active SubVendor Contracts  Sub Vendors widget Details page table count did not match", numOfAEContractsDetailsCountapp, NumOfAEContractsDetailsCountyear);
		
		String WidgetDetailsTitle =  "New Sub Vendor Contracts by Fiscal Year Transactions";
		String WidgetDetailsTitleApp = HomePage.DetailsPagetitle();
	    assertEquals("Total Active SubVendor Contracts  Sub  Vendors Widget title did not match", WidgetDetailsTitle, WidgetDetailsTitleApp); 
	    

	    String WidgetDetailsAmountDB =  NYCDatabaseUtil.getSubContractsRegisteredDetailsAmount(year,'B');
		String WidgetDetailsAmountapp = ActiveExpenseContractsPage.GetTransactionAmount1();
		assertEquals("Total Active SubVendor Contracts  Sub Vendors Widget Details page total Contract amount did not match", WidgetDetailsAmountapp, WidgetDetailsAmountDB);
	}
	
	@Test
	public void VerifyTop5AwardMethodsTransactionCount() throws SQLException {
		SubVendorsPage.GoToTop5DetailsPage(WidgetOption.Top5AwardMethods);
		HomePage.ShowWidgetDetails();
		int NumOfAEContractsDetailsCountyear =  NYCDatabaseUtil.getSubContractsRegisteredDetailsCount(year,'B');
		int numOfAEContractsDetailsCountapp = ActiveExpenseContractsPage.GetTransactionCount();
		assertEquals("Total Active SubVendor Contracts  Award Method widget Details page table count did not match", numOfAEContractsDetailsCountapp, NumOfAEContractsDetailsCountyear);

		String WidgetDetailsTitle =  "Award Methods by New Sub Vendor Contracts by Fiscal Year Transactions";
		String WidgetDetailsTitleApp = HomePage.DetailsPagetitle();
	    assertEquals("Total Active SubVendor Contracts  AWard Method Widget title did not match", WidgetDetailsTitle, WidgetDetailsTitleApp); 
	    

	    String WidgetDetailsAmountDB =  NYCDatabaseUtil.getSubContractsRegisteredDetailsAmount(year,'B');
		String WidgetDetailsAmountapp = ActiveExpenseContractsPage.GetTransactionAmount1();
		assertEquals("Total Active SubVendor Contracts  AwardMethods Widget Details page total Contract amount did not match", WidgetDetailsAmountapp, WidgetDetailsAmountDB);
	}
	@Test
	public void VerifyTop5AgenciesTransactionCount() throws SQLException {
		SubVendorsPage.GoToTop5DetailsPage(WidgetOption.Top5Agencies);
		HomePage.ShowWidgetDetails();
		
		int NumOfAEContractsDetailsCountyear =  NYCDatabaseUtil.getSubContractsRegisteredDetailsCount(year,'B');
		int numOfAEContractsDetailsCountapp = ActiveExpenseContractsPage.GetTransactionCount();
		assertEquals("Total Active SubVendor Contracts  Agencies widget Details page table count did not match", numOfAEContractsDetailsCountapp, NumOfAEContractsDetailsCountyear); 
		
		String WidgetDetailsTitle =  "Agencies by New Sub Vendor Contracts by Fiscal Year Transactions";
		String WidgetDetailsTitleApp = HomePage.DetailsPagetitle();
	    assertEquals("Total Active SubVendor Contracts   Agencies Widget title did not match", WidgetDetailsTitle, WidgetDetailsTitleApp); 
	    

	    String WidgetDetailsAmountDB =  NYCDatabaseUtil.getSubContractsRegisteredDetailsAmount(year,'B');
		String WidgetDetailsAmountapp = ActiveExpenseContractsPage.GetTransactionAmount1();
		assertEquals("Total Active SubVendor Contracts   Agencies  Widget Details page total Contract amount did not match", WidgetDetailsAmountapp, WidgetDetailsAmountDB);
	}
	@Test
	public void VerifyContractsByIndustriesTransactionCount() throws SQLException {
		SubVendorsPage.GoToTop5DetailsPage(WidgetOption.ContractsbyIndustries);
		HomePage.ShowWidgetDetails();
		int NumOfAEContractsDetailsCountyear =  NYCDatabaseUtil.getSubContractsRegisteredDetailsCount(year,'B');
		int numOfAEContractsDetailsCountapp = ActiveExpenseContractsPage.GetTransactionCount();
		assertEquals("Total Active SubVendor Contracts by Industies widget Details page table count did not match", numOfAEContractsDetailsCountapp, NumOfAEContractsDetailsCountyear); 
		
		String WidgetDetailsTitle =  "Contracts by Industries by New Sub Vendor Contracts by Fiscal Year Transactions";
		String WidgetDetailsTitleApp = HomePage.DetailsPagetitle();
	    assertEquals("Total Active SubVendor Contracts Industries Widget title did not match", WidgetDetailsTitle, WidgetDetailsTitleApp); 
	    

	    String WidgetDetailsAmountDB =  NYCDatabaseUtil.getSubContractsRegisteredDetailsAmount(year,'B');
		String WidgetDetailsAmountapp = ActiveExpenseContractsPage.GetTransactionAmount1();
		assertEquals("Total Active SubVendor Contracts ContractsByIndustries  Widget Details page total Contract amount did not match", WidgetDetailsAmountapp, WidgetDetailsAmountDB);
	}
	@Test
	public void VerifyContractsBySizeTransactionCount() throws SQLException {
		SubVendorsPage.GoToTop5DetailsPage(WidgetOption.ContractsbySize);
	
		int NumOfAEContractsDetailsCountyear =  NYCDatabaseUtil.getSubContractsRegisteredDetailsCount(year,'B');
		int numOfAEContractsDetailsCountapp = ActiveExpenseContractsPage.GetTransactionCount();
		assertEquals("Total Active SubVendor Contracts Contracts by size  widget Details page table count did not match", numOfAEContractsDetailsCountapp, NumOfAEContractsDetailsCountyear);
		
		String WidgetDetailsTitle =  "Contracts by Size by New Sub Vendor Contracts by Fiscal Year Transactions";
		String WidgetDetailsTitleApp = HomePage.DetailsPagetitle();
	    assertEquals("Total Active SubVendor Contracts Contracts by Sizes Widget title did not match", WidgetDetailsTitle, WidgetDetailsTitleApp); 
	    

	    String WidgetDetailsAmountDB =  NYCDatabaseUtil.getSubContractsRegisteredDetailsAmount(year,'B');
		String WidgetDetailsAmountapp = ActiveExpenseContractsPage.GetTransactionAmount1();
		assertEquals("Total Active SubVendor Contracts ContractsBySize  Widget Details page total Contract amount did not match", WidgetDetailsAmountapp, WidgetDetailsAmountDB);
	}


	/* ***************** Test Widget Transaction Total Amount ****************** */
	
	
}
