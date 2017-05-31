package FunctionalPayroll;

import org.junit.Before;
import org.junit.Test;

import helpers.Helper;

import java.util.Arrays;

import static org.junit.Assert.assertTrue;
import pages.home.HomePage;
import pages.payroll.PayrollPage;
import utilities.NYCBaseTest;
import utilities.TestStatusReport;

public class PayrollTitles extends TestStatusReport {
	@Before
    public void GoToPage(){
		PayrollPage.GoTo();
	 
	   if(!(Helper.getCurrentSelectedYear()).equalsIgnoreCase(NYCBaseTest.prop.getProperty("CurrentYear")))
		   HomePage.SelectYear(NYCBaseTest.prop.getProperty("CurrentYear"));
	   HomePage.ShowWidgetDetails();
    }	
	

//Visualization titles
	
	@Test
    public void VerifyPayrollVisualizationsTitles(){
		//MWBECategory.select(MWBECategoryOption.AsianAmerican);
	    String[] sliderTitles= {"Gross Pay by Month", 
	    						"Overtime Payments by Month", 
	    						"Top Ten Agencies by Gross YTD", 
	    						"Top Ten Agencies by Total Overtime",
	    					};  
    	assertTrue(Arrays.equals(sliderTitles, PayrollPage.VisualizationTitles().toArray()));
	}

}
