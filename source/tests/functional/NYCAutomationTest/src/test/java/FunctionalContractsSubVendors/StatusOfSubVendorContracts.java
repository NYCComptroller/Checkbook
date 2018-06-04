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
import pages.contracts.ContractsPage.WidgetOption;
import pages.home.HomePage;
import pages.spending.SpendingPage;
import pages.subvendors.RegisteredSubVendorContractsPage;
import pages.subvendors.StatusOfSubVendorContractsPage;
import pages.subvendors.SubVendorsPage;
import utilities.NYCBaseTest;
import utilities.NYCDatabaseUtil;
import utilities.TestStatusReport;
import helpers.Helper;

//public class  StatusOfSubVendorContracts extends NYCBaseTest {
public class StatusOfSubVendorContracts extends TestStatusReport{
	int year =  Integer.parseInt(NYCBaseTest.prop.getProperty("year"));
	@Before
	 public void GoToPage(){
		   if (!SubVendorsPage.IsAt())
			   StatusOfSubVendorContractsPage.GoTo();  
			   if(!(Helper.getCurrentSelectedYear()).equalsIgnoreCase(NYCBaseTest.prop.getProperty("CurrentYear")))
			   HomePage.SelectYear(NYCBaseTest.prop.getProperty("CurrentYear"));
	    }



	/* ***************** Test Widget Counts ****************** */
	
	

	@Test
	public void VerifySummaryOfSubContracts() throws SQLException {
		Integer numOfSubContractsDB = NYCDatabaseUtil.getSubContractsCount(year, 'B');
		Integer numOfSubContractsApp = ContractsPage.GetTop5WidgetTotalCount(WidgetOption.SummaryOfSubVendorContracts);
		assertEquals("Number of Contracts in the  Active Expense contract by Industry  widget did not match",numOfSubContractsApp,numOfSubContractsDB);
	}
	@Test
	public void VerifyNumOfContracts() throws SQLException {
		Integer NumberOfContractsDb = NYCDatabaseUtil.getNumberOfContracts(year, 'B');
		Integer NumberOfContractsApp = ContractsPage.GetTop5WidgetTotalCount(WidgetOption.SummaryOfPrimeVendor);
		assertEquals("Number of Contracts in the  Active Expense Contracts by Size widget did not match", NumberOfContractsApp,NumberOfContractsDb);
	}
	/////// amounts and titles

	@Test
    public void VerifyTopNavContractAmount() throws SQLException {
        String TotalContractAmtDB = NYCDatabaseUtil.getContractsTopAmount(year, 'B');
        String TotalContractAmtApp = ContractsPage.GetContractsAmount();
        System.out.println(TotalContractAmtApp);
        assertEquals("Active Expense Contracts Top navigation amount did not match", TotalContractAmtApp, TotalContractAmtDB);
    }

	@Test
    public void VerifyNumofReportedPrimeContractswithSubVendor() throws SQLException {
        Integer TotalContractAmtDB = NYCDatabaseUtil.getReportedPrimeContracts(year, 'B');
        Integer TotalContractAmtApp =  ContractsPage.GetTop5WidgetTotalCount(WidgetOption.SubContractStatus);
       
    	System.out.println(TotalContractAmtApp);
    	 assertEquals("Active Expense Contracts Bottom navigation Count did not match", TotalContractAmtApp, TotalContractAmtDB);

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