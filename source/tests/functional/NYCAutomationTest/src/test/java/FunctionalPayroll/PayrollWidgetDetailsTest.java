package FunctionalPayroll;

import static org.junit.Assert.assertEquals;
import static org.junit.Assert.assertTrue;

import java.sql.SQLException;

import org.junit.Before;
import org.junit.Test;

import pages.payroll.PayrollPage;
import pages.payroll.PayrollPage.WidgetOption;
import pages.contracts.ActiveRevenueContractsPage;
import pages.contracts.ContractsPage;
import pages.home.HomePage;
import utilities.NYCBaseTest;
import utilities.NYCDatabaseUtil;
import utility.Helper;
import utility.TestStatusReport;

//public class PayrollWidgetDetailsTest extends NYCBaseTest{
	public class PayrollWidgetDetailsTest   extends TestStatusReport{

	@Before
    public void GoToPage(){
		PayrollPage.GoTo();
	 
	   if(!(Helper.getCurrentSelectedYear()).equalsIgnoreCase(NYCBaseTest.prop.getProperty("CurrentYear")))
		   HomePage.SelectYear(NYCBaseTest.prop.getProperty("CurrentYear"));
	   HomePage.ShowWidgetDetails();
    }
	
	
	/* ***************** Test Widget Transaction Count ****************** */
	@Test
    public void VerifyTop5AgenciesbyPayrollTransactionCount() throws SQLException{
		PayrollPage.GoToTop5DetailsPage(WidgetOption.Top5AgenciesbyPayroll);
		HomePage.ShowWidgetDetails();
		int NumOfPayrollDetailsCount2016 =  NYCDatabaseUtil.getPayrollDetailsCount(2016,'B');
		 int numOfPayrollDetailsCountapp = PayrollPage.GetTransactionCount();
	   //assertTrue(PayrollPage.GetTransactionCount() == 600296); 
		 assertEquals("Number of Payroll salaried employees did not match", numOfPayrollDetailsCountapp, NumOfPayrollDetailsCount2016); 
    }
	@Test
   /*//with out DB
    *  public void VerifyTop5AgenciesbyOvertimeTransactionCount(){
		PayrollPage.GoToTop5DetailsPage(WidgetOption.Top5AgenciesbyOvertime);
		//HomePage.ShowWidgetDetails();
		 //assertTrue(PayrollPage.GetTransactionCount() == 600296); 
	   assertTrue(PayrollPage.GetTransactionCount() >= 200000); 
	}*/
	
    public void VerifyTop5AgenciesbyOvertimeTransactionCount() throws SQLException{
		PayrollPage.GoToTop5DetailsPage(WidgetOption.Top5AgenciesbyOvertime);
		HomePage.ShowWidgetDetails();
		int NumOfPayrollDetailsCount2016 =  NYCDatabaseUtil.getPayrollDetailsCount(2016,'B');
		 int numOfPayrollDetailsCountapp = PayrollPage.GetTransactionCount();
	   //assertTrue(PayrollPage.GetTransactionCount() == 600296); 
		 assertEquals("Number of Payroll salaried employees did not match", numOfPayrollDetailsCountapp, NumOfPayrollDetailsCount2016); 
    }
	
	@Test
		public void VerifyNumOfPayrollAnnualSalariesTransactioncount() throws SQLException {
			PayrollPage.GoToTop5DetailsPage(WidgetOption.Top5AnnualSalaries);
			HomePage.ShowWidgetDetails();
        int NumOfPayrollDetailsCount2016 =  NYCDatabaseUtil.getPayrollDetailsCount(2016,'B');
		 int numOfPayrollDetailsCountapp = PayrollPage.GetTransactionCount();
		 assertEquals("Number of Payroll salaried employees did not match", numOfPayrollDetailsCountapp, NumOfPayrollDetailsCount2016); 
		}
	/* issue with widgettitle function for titile widget	
	@Test
		public void VerifyNumOfPayrollTitlesbyNumberofEmployeesTransactioncount() throws SQLException {
			PayrollPage.GoToTop5DetailsPage(WidgetOption.Top5TitlesbyNumberofEmployees);
			HomePage.ShowWidgetDetails();
			 int NumOfPayrollDetailsCount2016 =  NYCDatabaseUtil.getPayrollTitleDetailsCount(2016,'B');
			 int numOfPayrollDetailsCountapp = PayrollPage.GetTransactionCount1();
			 assertEquals("Number of Payroll salaried employees did not match", numOfPayrollDetailsCountapp, NumOfPayrollDetailsCount2016); 
		}
	  
	   */
	}
	