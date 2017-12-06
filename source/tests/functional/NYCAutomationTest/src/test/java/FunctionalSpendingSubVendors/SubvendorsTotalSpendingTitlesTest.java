package FunctionalSpendingSubVendors;

import static org.junit.Assert.assertEquals;
import static org.junit.Assert.assertTrue;
import java.util.Arrays;
import helpers.Helper;
import java.sql.SQLException;
import org.junit.Before;
import org.junit.Test;

import navigation.SubVendorCategory.SubVendorCategoryOption;
import pages.home.HomePage;
import pages.subvendors.SubVendorsPage;
import utilities.NYCBaseTest;
import utilities.NYCDatabaseUtil;
import utilities.TestStatusReport;

public class SubvendorsTotalSpendingTitlesTest extends TestStatusReport{
	//	public class SubvendorsTotalSpendingTitlesTest extends NYCBaseTest{

	
	int year =  Integer.parseInt(NYCBaseTest.prop.getProperty("year"));
	@Before
    public void GoToPage(){
	   if (!SubVendorsPage.IsAt())
			SubVendorsPage.GoTo("Spending", SubVendorCategoryOption.SubVendorsHome);
	//	MWBEPage.GoTo("Spending", MWBECategoryOption.MWBEHome);	
	   if(!(Helper.getCurrentSelectedYear()).equalsIgnoreCase(NYCBaseTest.prop.getProperty("CurrentYear")))
		   HomePage.SelectYear(NYCBaseTest.prop.getProperty("CurrentYear"));
    }



	@Test
    public void VerifyTopNavSpendingAmount() throws SQLException {
        String TotalSpendingAmtDB = NYCDatabaseUtil.getSubVendorsSpendingAmount(year, 'B');
          // String spendingAmt = SubVendorsPage.GetSpendingSubVendorAmount();
        String spendingAmt = SubVendorsPage.GetSubVendorSpendingAmount();
        assertEquals("Spending Amount did not match", spendingAmt, TotalSpendingAmtDB);
    }
	
	@Test
    public void VerifyBottomNavTotalSpendingAmount() throws SQLException {
        String TotalSpendingAmtDB = NYCDatabaseUtil.getSubVendorsSpendingAmount(year, 'B');
        String spendingAmt = SubVendorsPage.GetBottomNavSpendingAmount();
    	System.out.println(spendingAmt); 
        assertEquals("Spending Amount did not match", spendingAmt, TotalSpendingAmtDB);
            
    }
	
	@Test
    public void VerifyTotalSpendingVisualizationsTitles(){
	    String[] sliderTitles= {"Sub Vendors Total Spending", 
	    						"Top Ten Agencies by Sub Vendors Disbursement Amount", 
	    						"Top Ten Contracts by Sub Vendors Disbursement Amount", 
	    						"Top Ten Prime Vendors by Sub Vendors Disbursement Amount",
	    						"Top Ten Sub Vendors by Disbursement Amount"};  
    	assertTrue(Arrays.equals(sliderTitles, SubVendorsPage.VisualizationTitles().toArray()));
    	System.out.println( SubVendorsPage.VisualizationTitles()); 
    }
	 
	@Test
    public void VerifyTotalSpendingWidgetTitles(){
	   String[] widgetTitles = {"Top 5 Checks",
	    						"Top 5 Agencies",
	    						"Top 5 Sub Vendors",
	    						"Top 5 Prime Vendors",
	    						"Top 5 Sub Contracts"}; 
	    							    						 
		   System.out.println( SubVendorsPage.WidgetTitles()); 		
    	//HomePage.ShowWidgetDetails();
    	assertTrue(Arrays.equals(widgetTitles, SubVendorsPage.WidgetTitles().toArray()));
    	
    /*	try {
    		//assertTrue(Arrays.equals(widgetTitles, SpendingPage.GetAllWidgetText().toArray()));
    	 	//assertTrue(Arrays.equals(widgetTitles, SpendingPage.WidgetTitles().toArray()));
    		//System.out.println( SpendingPage.GetAllWidgetText()); 
 	
    		//System.out.println( SpendingPage.GetAllWidgetText()); 
    		System.out.println( SpendingPage.WidgetTitles()); 
    		 
    	    System.out.println("no errors in widget titles");
    	}  catch (Throwable e) {
            System.out.println("errors in widget titles");
            } 
      */	
    }  
	
	

	
	
}
