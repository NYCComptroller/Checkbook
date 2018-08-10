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

//public class  StatusofSubVendorContractsbyPrimeVendorWidgetTest extends NYCBaseTest {
	public class StatusofSubVendorContractsbyPrimeVendorWidgetTest extends TestStatusReport{
	int year =  Integer.parseInt(NYCBaseTest.prop.getProperty("year"));
	@Before
	 public void GoToPage(){
		   if (!SubVendorsPage.IsAt())
			   
				SubVendorsPage.GoTo("Contracts", SubVendorCategoryOption.SubVendorsHome);
			 //  StatusOfSubVendorContractsPage.GoTo();  
			   if(!(Helper.getCurrentSelectedYear()).equalsIgnoreCase(NYCBaseTest.prop.getProperty("CurrentYear")))
			   HomePage.SelectYear(NYCBaseTest.prop.getProperty("CurrentYear"));
			   SubVendorsPage.GoToBottomNavContractslink3();
	    }



	/* ***************** Test Widget Counts ****************** */
	
	

	@Test
	public void VerifyNumOfSubContractStatusbyPrimeContractID() throws SQLException {
		Integer numOfContractsDB = NYCDatabaseUtil.getSubContractStatusbyPrimeContractIDCount(year, 'B');
		Integer numOfContractsApp = SubVendorsPage.GetTop5WidgetTotalCount(WidgetOption.SubContractStatusbyPrimeContractID);
		assertEquals(" SubContractStatusbyPrimeContractID   widget Count did not match with DB",numOfContractsApp,numOfContractsDB);
	}
	@Test
	public void VerifyNumOfSummaryofPrimeContractSubVendorReporting() throws SQLException {
		Integer NumberOfContractsDB = NYCDatabaseUtil.getPrimeContractSubVendorReportingCount(year, 'B');
		Integer NumberOfContractsApp = SubVendorsPage.GetTop5WidgetTotalCount(WidgetOption.SummaryofPrimeContractSubVendorReporting);
		assertEquals("SummaryofPrimeContractSubVendorReporting  widget count did not match DB", NumberOfContractsApp,NumberOfContractsDB);
	}
	
	@Test
    public void VerifyNumofSummaryofSubVendorContractsbyPrimeContracts() throws SQLException {
        Integer NumberOfContractsDB = NYCDatabaseUtil.getSubContractStatusbyPrimeContractIDCount(year, 'B');
        Integer NumberOfContractsApp =  SubVendorsPage.GetTop5WidgetTotalCount(WidgetOption.SummaryofSubVendorContractsbyPrimeContracts);
       
      	 assertEquals("SummaryofSubVendorContractsbyPrimeContracts widget count  did not match with DB", NumberOfContractsApp, NumberOfContractsDB);

    }
	/*
	@Test
    public void VerifyNumofReportedPrimeContractswithSubVendor() throws SQLException {
        Integer TotalContractAmtDB = NYCDatabaseUtil.getReportedPrimeContracts(year, 'B');
        Integer TotalContractAmtApp =  ContractsPage.GetTop5WidgetTotalCount(WidgetOption.SubContractStatus);
       
    	System.out.println(TotalContractAmtApp);
    	 assertEquals("Active Expense Contracts Bottom navigation Count did not match", TotalContractAmtApp, TotalContractAmtDB);

    }
    */
	/////// amounts and titles

	@Test
    public void VerifyTopNavContractAmount() throws SQLException {
        String TotalContractAmtDB = NYCDatabaseUtil.getContractsTopAmount(year, 'B');
        String TotalContractAmtApp = ContractsPage.GetContractsAmount();
        System.out.println(TotalContractAmtApp);
        assertEquals("Active Expense Contracts Top navigation amount did not match", TotalContractAmtApp, TotalContractAmtDB);
    }



	@Test
    public void VerifyContractsVisualizationsTitles(){
	    String[] sliderTitles= {"Sub Vendor Reporting by Prime Contracts",
	    	                     	"Sub Vendor Contracts Status by Prime Contracts"
	    						};
	 //  System.out.println( ContractsPage.VisualizationTitles());
    	assertTrue(Arrays.equals(sliderTitles, ContractsPage.SubVendorVisualizationTitles().toArray()));
    	System.out.println( ContractsPage.SubVendorVisualizationTitles());
    }

	@Test
    public void VerifySubVendorContractsSpendingWidgetTitles(){
	   String[] widgetTitles = {"Sub Contract Status by Prime Contract ID",
	    						"Summary of Prime Contract Sub Vendor Reporting",
	    						"Summary of Sub Vendor Contracts by Prime Contracts"
	    						};

		   System.out.println( ContractsPage.WidgetTitles());

    	assertTrue(Arrays.equals(widgetTitles, ContractsPage.WidgetTitles().toArray()));

     }

}