package FunctionalRevenue;

import static org.junit.Assert.assertEquals;

import static org.junit.Assert.assertTrue;

import java.sql.SQLException;
import java.util.concurrent.TimeUnit;

import org.junit.After;
import org.junit.Before;
import org.junit.BeforeClass;
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
	int year =  Integer.parseInt(NYCBaseTest.prop.getProperty("year"));
	@Before
	public  void GoToPage() {
		RevenuePage.GoTo();
		if(!(Helper.getCurrentSelectedYear()).equalsIgnoreCase(NYCBaseTest.prop.getProperty("CurrentYear")))
			HomePage.SelectYear(NYCBaseTest.prop.getProperty("CurrentYear"));
		HomePage.ShowWidgetDetails();
	}

	
	
	/* ***************** Test Widget Transaction Count ****************** */
	@Test
	public  void VerifyRevenueDetailsTransactionCount() throws SQLException {
	    RevenuePage.GoToTop5DetailsPage(WidgetOption.Top5Agencies);	
	    HomePage.ShowWidgetDetails();
	   	int NumOfRevenueDetailsCount2016 =  NYCDatabaseUtil.getRevenueDetailsCount(year,'B');
		int numOfRevenueDetailsCountapp = RevenuePage.GetTransactionCount();
	    System.out.println( RevenuePage.GetTransactionCount()); 
		assertEquals("Number of master contracts widget Details page table count did not match", numOfRevenueDetailsCountapp, NumOfRevenueDetailsCount2016); 
		}	
	

	@Test
	public  void VerifyRevenueCategoriesDetailsTransactionCount() throws SQLException {
		RevenuePage.GoToTop5DetailsPage(WidgetOption.Top5RevenueCategories);
		//HomePage.ShowWidgetDetails();
		int NumOfRevenueDetailsCount2016 =  NYCDatabaseUtil.getRevenueDetailsCount(year,'B');
		int numOfRevenueDetailsCountapp = RevenuePage.GetTransactionCount();
		 System.out.println( RevenuePage.GetTransactionCount()); 
		assertEquals("Number of master contracts widget Details page table count did not match", numOfRevenueDetailsCountapp, NumOfRevenueDetailsCount2016); 
	
	}

	@Test
	public  void VerifyRevenueByFundingclassesDetailsTransactionCount() throws SQLException {
		RevenuePage.GoToTop5DetailsPage(WidgetOption.RevenuebyFundingClasses);
		HomePage.ShowWidgetDetails();
		int NumOfRevenueDetailsCount2016 =  NYCDatabaseUtil.getRevenueDetailsCount(year,'B');
		int numOfRevenueDetailsCountapp = RevenuePage.GetTransactionCount();
		 System.out.println( RevenuePage.GetTransactionCount()); 
		assertEquals("Number of master contracts widget Details page table count did not match", numOfRevenueDetailsCountapp, NumOfRevenueDetailsCount2016); 
		}

	
	/*@After
	public void EndProgram()
	{
		Driver.Instance.quit();
	}
	*/
	
	@Test
	public  void VerifyFundingclassesbyCrossYearCollectionsDetailsTransactionCount() throws SQLException {
		RevenuePage.GoToTop5DetailsPage(WidgetOption.RevenuebyFundingClassesbyCrossYearCollections);
		HomePage.ShowWidgetDetails();
		int NumOfRevenueDetailsCount2016 =  NYCDatabaseUtil.getRevenueFundingclassCount(year,'B');
		int numOfRevenueDetailsCountapp = RevenuePage.GetTransactionCount2();
		 System.out.println( RevenuePage.GetTransactionCount2()); 
		assertEquals("Number of master contracts widget Details page table count did not match", numOfRevenueDetailsCountapp, NumOfRevenueDetailsCount2016); 
		}
	@Test
	public  void VerifyRevenueCategoriesbyCrossYearCollectionsDetailsTransactionCount() throws SQLException {
		RevenuePage.GoToTop5DetailsPage(WidgetOption.Top5RevenueCategoriesbyCrossYearCollections);
		HomePage.ShowWidgetDetails();
		int NumOfRevenueDetailsCount2016 =  NYCDatabaseUtil.getRevenueCategoriesCount(year,'B');
		int numOfRevenueDetailsCountapp = RevenuePage.GetTransactionCount3();
		 System.out.println( RevenuePage.GetTransactionCount3()); 
		assertEquals("Number of master contracts widget Details page table count did not match", numOfRevenueDetailsCountapp, NumOfRevenueDetailsCount2016); 
		}
	
	@Test
	public  void VerifyAgenciesbyCrossYearCollectionsDetailsTransactionCount() throws SQLException {
		RevenuePage.GoToTop5DetailsPage(WidgetOption.Top5AgenciesbyCrossYearCollections);
		HomePage.ShowWidgetDetails();
		int NumOfRevenueDetailsCount2016 =  NYCDatabaseUtil.getRevenueAgenciesCount(year,'B');
		int numOfRevenueDetailsCountapp = RevenuePage.GetTransactionCount1();
		 System.out.println( RevenuePage.GetTransactionCount1()); 
		assertEquals("Number of master contracts widget Details page table count did not match", numOfRevenueDetailsCountapp, NumOfRevenueDetailsCount2016); 
		}


	/* ***************** Test Widget Transaction Total Amount ****************** */
	
	/* 
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
	Driver.Instance.quit();
	}

*/
	
	
}
