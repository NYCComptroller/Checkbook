package FunctionalContractsSubVendors;

import static org.junit.Assert.assertEquals;

import static org.junit.Assert.assertTrue;

import java.sql.SQLException;
import java.util.Arrays;

import org.junit.Before;
import org.junit.Test;

import navigation.SecondarySubVendorTabSelector;
import navigation.SecondaryTabSelector;
import navigation.TopNavigation;
import navigation.SubVendorCategory.SubVendorCategoryOption;
import navigation.TopNavigation.Contracts.ActiveExpenseContracts;
import pages.contracts.ActiveExpenseContractsPage;
import pages.contracts.ContractsPage;
//import pages.contracts.ContractsPage.WidgetOption;
import pages.home.HomePage;
import pages.spending.SpendingPage;
import pages.subvendors.RegisteredSubVendorContractsPage;
import pages.subvendors.StatusOfSubVendorContractsPage;
import pages.subvendors.SubVendorsPage;
import pages.subvendors.SubVendorsPage.WidgetOption;
import utilities.NYCBaseTest;
import utilities.NYCDatabaseUtil;
import utilities.TestStatusReport;
import helpers.Helper;

public class  StatusofSubVendorContractsbyPrimeVendorWidgetDetailsTest extends NYCBaseTest {
//public class StatusofSubVendorContractsbyPrimeVendorWidgetDetailsTest extends TestStatusReport{
	int year =  Integer.parseInt(NYCBaseTest.prop.getProperty("year"));
	@Before
	 public void GoToPage(){
		  // if (!SubVendorsPage.IsAt())
			 //  StatusOfSubVendorContractsPage.GoTo(); 
		
		SubVendorsPage.GoTo("Contracts", SubVendorCategoryOption.SubVendorsHome);
			   if(!(Helper.getCurrentSelectedYear()).equalsIgnoreCase(NYCBaseTest.prop.getProperty("CurrentYear")))
			   HomePage.SelectYear(NYCBaseTest.prop.getProperty("CurrentYear"));
			   SubVendorsPage.GoToBottomNavContractslink3();	   
	    }



	/* ***************** Test Widget Counts ****************** */
	
	

	@Test
	public void VerifyTop5SubVendorContracts() throws SQLException {
		SubVendorsPage.GoToTop5DetailsPage(WidgetOption.SubContractStatusbyPrimeContractID);
		
		Integer totalCheckswidgetCountDB = NYCDatabaseUtil.getSubContractsDetailsCount(year,'B');
		Integer totalChecksWidgetCountApp = SubVendorsPage.GetTransactionCount3();
		assertEquals("Sub Vendors Contracts widget count  did not match with the DB",totalChecksWidgetCountApp, totalCheckswidgetCountDB);
		
		String WidgetDetailsTitle =  "Sub Contract Status by Prime Contract ID" ;
		String WidgetDetailsTitleApp = HomePage.DetailsPagetitle();
	    assertEquals("Sub Vendors Contracts  Widget title did not match", WidgetDetailsTitle, WidgetDetailsTitleApp); 
	    
	    String WidgetDetailsAmountDB =  NYCDatabaseUtil.getSubContractsDetailsAmount(year,'B');
		String WidgetDetailsAmountApp = ActiveExpenseContractsPage.GetTransactionAmount1();
	    assertEquals("Sub Vendors Contracts Widget Details page total Contract amount did not match", WidgetDetailsAmountApp, WidgetDetailsAmountDB); 
	  

	}

}