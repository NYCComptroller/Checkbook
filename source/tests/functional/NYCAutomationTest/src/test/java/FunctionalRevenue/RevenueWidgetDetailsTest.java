package FunctionalRevenue;

import static org.junit.Assert.assertEquals;

import static org.junit.Assert.assertTrue;

import java.sql.SQLException;
import java.util.concurrent.TimeUnit;

import org.junit.Before;
import org.junit.Test;
import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;

import pages.revenue.RevenuePage.WidgetOption;
import pages.budget.BudgetPage;
import pages.home.HomePage;
import pages.revenue.RevenuePage;
import utilities.NYCBaseTest;
import utilities.NYCDatabaseUtil;
import helpers.Driver;
import helpers.Helper;
import utilities.TestStatusReport;

public class RevenueWidgetDetailsTest extends NYCBaseTest {
	//public class RevenueWidgetDetailsTest extends TestStatusReport {

	@Before
	public void GoToPage() {
		RevenuePage.GoTo();
		if(!(Helper.getCurrentSelectedYear()).equalsIgnoreCase(NYCBaseTest.prop.getProperty("CurrentYear")))
			HomePage.SelectYear(NYCBaseTest.prop.getProperty("CurrentYear"));
		HomePage.ShowWidgetDetails();
	}
	
	/* ***************** Test Widget Transaction Count ****************** */
	@Test
	public void VerifyRevenueDetailsTransactionCount() throws SQLException {
	    RevenuePage.GoToTop5DetailsPage(WidgetOption.Top5Agencies);	
		int NumOfRevenueDetailsCount2016 =  NYCDatabaseUtil.getRevenueDetailsCount(2016,'B');
		int numOfRevenueDetailsCountapp = RevenuePage.GetTransactionCount();
	    System.out.println( RevenuePage.GetTransactionCount()); 
		assertEquals("Number of master contracts widget Details page table count did not match", numOfRevenueDetailsCountapp, NumOfRevenueDetailsCount2016); 
	}
	@Test
	//Driver.Instance.manage().timeouts().implicitlyWait(20, TimeUnit.SECONDS);
	public void VerifyRevenueCategoriesDetailsTransactionCount() throws SQLException {
		RevenuePage.GoToTop5DetailsPage(WidgetOption.Top5RevenueCategories);
		HomePage.ShowWidgetDetails();
		int NumOfRevenueDetailsCount2016 =  NYCDatabaseUtil.getRevenueDetailsCount(2016,'B');
		int numOfRevenueDetailsCountapp = RevenuePage.GetTransactionCount();
		 System.out.println( RevenuePage.GetTransactionCount()); 
		assertEquals("Number of master contracts widget Details page table count did not match", numOfRevenueDetailsCountapp, NumOfRevenueDetailsCount2016); 
	}
	@Test
	public void VerifyRevenueByFundingclassesDetailsTransactionCount() throws SQLException {
		RevenuePage.GoToTop5DetailsPage(WidgetOption.RevenuebyFundingClasses);
		HomePage.ShowWidgetDetails();
		int NumOfRevenueDetailsCount2016 =  NYCDatabaseUtil.getRevenueDetailsCount(2016,'B');
		int numOfRevenueDetailsCountapp = RevenuePage.GetTransactionCount();
		 System.out.println( RevenuePage.GetTransactionCount()); 
		assertEquals("Number of master contracts widget Details page table count did not match", numOfRevenueDetailsCountapp, NumOfRevenueDetailsCount2016); 
	}



	/* ***************** Test Widget Transaction Total Amount ****************** */
	
	 
	@Test
	public void VerifyRevenueTransactionAmount() throws SQLException {
		//Float transactionAmt = 26.3f;
		RevenuePage.GoToTop5DetailsPage(WidgetOption.Top5Agencies);
		//HomePage.ShowWidgetDetails();
	//assertTrue(HomePage.GetTransactionAmount1()== transactionAmt);
		String NumOfRevenueDetailsAmount2016 =  NYCDatabaseUtil.getRevenueDetailsAmount(2016,'B');
		String numOfRevenueDetailsAmountapp = HomePage.GetTransactionAmount1();
		
		System.out.println( RevenuePage.GetTransactionAmount1()); 
	assertEquals("Number ofRevenue widget Details page table count did not match", numOfRevenueDetailsAmountapp, NumOfRevenueDetailsAmount2016); 
	}


}
