package FunctionalSpendingMWBE;

import static org.junit.Assert.assertEquals;
import static org.junit.Assert.assertTrue;
import helpers.Helper;
import navigation.MWBECategory.MWBECategoryOption;

import java.sql.SQLException;
import java.util.Arrays;

import org.junit.Before;
import org.junit.Test;

import pages.budget.BudgetPage;
import pages.budget.BudgetPage.WidgetOption;
import pages.home.HomePage;
import pages.mwbe.MWBEPage;
import pages.spending.SpendingPage;
import utilities.NYCBaseTest;
import utilities.NYCDatabaseUtil;
import utilities.TestStatusReport;

//public class TotalSpendingTitlesTest extends TestStatusReport{
	public class TotalSpendingTitlesTest extends NYCBaseTest{
	int year =  Integer.parseInt(NYCBaseTest.prop.getProperty("year"));
	@Before
	public void GoToPage(){
		if(!MWBEPage.IsAt()){
			MWBEPage.GoTo("Spending", MWBECategoryOption.MWBEHome);		
		}
		if(!(Helper.getCurrentSelectedYear()).equalsIgnoreCase(NYCBaseTest.prop.getProperty("CurrentYear")))
			   HomePage.SelectYear(NYCBaseTest.prop.getProperty("CurrentYear"));
	}


	@Test
    public void VerifyTopNavSpendingAmount() throws SQLException {
        String TotalSpendingAmtDB = NYCDatabaseUtil.getSpendingAmount(2016, 'B');
        String spendingAmt = SpendingPage.GetSpendingAmount();
        assertEquals("Spending Amount did not match", spendingAmt, TotalSpendingAmtDB);
    }
	
	@Test
    public void VerifyBottomNavTotalSpendingAmount() throws SQLException {
        String TotalSpendingAmtDB = NYCDatabaseUtil.getSpendingAmount(year, 'B');
        String spendingAmt = SpendingPage.GetBottomNavSpendingAmount();
    	System.out.println(spendingAmt); 
        assertEquals("Spending Amount did not match", spendingAmt, TotalSpendingAmtDB);
            
    }
	
	@Test
    public void VerifyTotalSpendingVisualizationsTitles(){
	    String[] sliderTitles= {"Total Spending", 
	    						"Top Ten Agencies by Disbursement Amount", 
	    						"Top Ten Contracts by Disbursement Amount", 
	    						"Top Ten Prime Vendors by Disbursement Amount"};  
    	assertTrue(Arrays.equals(sliderTitles, SpendingPage.VisualizationTitles().toArray()));
    	System.out.println( SpendingPage.VisualizationTitles()); 
    }
	 
	@Test
    public void VerifyTotalSpendingWidgetTitles(){
	   String[] widgetTitles = {"Top 5 Checks",
	    						"Top 5 Agencies",
	    						"Top Expense Categories",
	    						"Top 5 Prime Vendors",
	    						"Top 5 Contracts",
	    						"Top 5 Agencies"}; 
	    							    						 
		   System.out.println( SpendingPage.WidgetTitles()); 		
    	//HomePage.ShowWidgetDetails();
    	assertTrue(Arrays.equals(widgetTitles, SpendingPage.WidgetTitles().toArray()));
    	
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
