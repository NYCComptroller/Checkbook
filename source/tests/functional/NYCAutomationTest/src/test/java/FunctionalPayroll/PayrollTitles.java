package FunctionalPayroll;

import org.junit.Before;
import org.junit.Test;

import helpers.Helper;

import java.sql.SQLException;
import java.util.Arrays;

import static org.junit.Assert.assertEquals;
import static org.junit.Assert.assertTrue;

import pages.budget.BudgetPage;
import pages.home.HomePage;
import pages.payroll.PayrollPage;
import pages.revenue.RevenuePage;
import pages.spending.SpendingPage;
import utilities.NYCBaseTest;
import utilities.NYCDatabaseUtil;
import utilities.TestStatusReport;

//public class PayrollTitles extends NYCBaseTest {
	
	public class PayrollTitles extends TestStatusReport {
	
	@Before
	public void GoToPage(){
		PayrollPage.GoTo();

		if(!(Helper.getCurrentSelectedYear()).equalsIgnoreCase(NYCBaseTest.prop.getProperty("CurrentYear")))
			HomePage.SelectYear(NYCBaseTest.prop.getProperty("CurrentYear"));
		HomePage.ShowWidgetDetails();
	}	
	
	// top navigation amount
	
	@Test
    public void VerifyPayrollAmount() throws SQLException {
        String TotalPayrollAmtFY2016 = NYCDatabaseUtil.getPayrollAmount(2016, 'B');
        String payrollAmt = PayrollPage.GetPayrollAmount();
        assertEquals("Payroll Amount did not match", payrollAmt, TotalPayrollAmtFY2016);
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
		assertTrue(Arrays.equals(sliderTitles, PayrollPage.PayrollVisualizationTitles().toArray()));
	}
	
	//Widget titles
	@Test
    public void VerifyPayrollWidgetTitles(){
	    String[] widgetTitles = {"Top 5 Agencies by Payroll",
	    						"Top 5 Agencies by Overtime",
	    						"Top 5 Annual Salaries",
	    					    "Top 5 Titles by"}   ;  
 
    	assertTrue(Arrays.equals(widgetTitles, PayrollPage.WidgetTitles().toArray()));
    	//assertEquals("Budget Title did not match", widgetTitles,  PayrollPage.WidgetTitles().toArray());
    	
     	try {
    		//System.out.println( PayrollPage.GetAllWidgetText()); 
    		System.out.println( PayrollPage.WidgetTitles());     		
  	    System.out.println("no errors in widget titles");
    	}  catch (Throwable e) {
            System.out.println("errors in widget titles");
            } 
	}

}
