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

//public class RevenueWidgetDetailsTest extends NYCBaseTest {
		public class RevenueWidgetDetailsTest extends TestStatusReport {
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
	public  void VerifyRevenueAgenciesDetailsTransactionCount() throws SQLException {
	    RevenuePage.GoToTop5DetailsPage(WidgetOption.Top5Agencies);	
	    HomePage.ShowWidgetDetails();
	   	int NumOfRevenueDetailsCountDB =  NYCDatabaseUtil.getRevenueDetailsCount(year,'B');
		int numOfRevenueDetailsCountapp = RevenuePage.GetTransactionCount();
	    System.out.println( RevenuePage.GetTransactionCount()); 
		assertEquals("Revenue domain Agencies widget Details page table count did not match with DB", numOfRevenueDetailsCountapp, NumOfRevenueDetailsCountDB); 
		}	
	

	@Test
	public  void VerifyRevenueCategoriesDetailsTransactionCount() throws SQLException {
		RevenuePage.GoToTop5DetailsPage(WidgetOption.Top5RevenueCategories);
		//HomePage.ShowWidgetDetails();
		int NumOfRevenueDetailsCountDB =  NYCDatabaseUtil.getRevenueDetailsCount(year,'B');
		int numOfRevenueDetailsCountapp = RevenuePage.GetTransactionCount();
		 System.out.println( RevenuePage.GetTransactionCount()); 
		assertEquals("Revenue Domain RevenueCategories widget Details page table count did not match with DB", numOfRevenueDetailsCountapp, NumOfRevenueDetailsCountDB); 
	
	}

	@Test
	public  void VerifyRevenueByFundingclassesDetailsTransactionCount() throws SQLException {
		RevenuePage.GoToTop5DetailsPage(WidgetOption.RevenuebyFundingClasses);
		HomePage.ShowWidgetDetails();
		int NumOfRevenueDetailsCountDB =  NYCDatabaseUtil.getRevenueDetailsCount(year,'B');
		int numOfRevenueDetailsCountapp = RevenuePage.GetTransactionCount();
		 System.out.println( RevenuePage.GetTransactionCount()); 
		assertEquals("Revenue Domain RevenueByFundingclasses widget Details page table count did not match with DB", numOfRevenueDetailsCountapp, NumOfRevenueDetailsCountDB); 
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
		int NumOfRevenueDetailsCountDB =  NYCDatabaseUtil.getRevenueFundingClassesCrossYearColectionsDetailsCount(year,'B');
		int numOfRevenueDetailsCountapp = RevenuePage.GetTransactionCount2();
		 System.out.println( RevenuePage.GetTransactionCount2()); 
		assertEquals("Revenue Domain FundingclassesbyCrossYearCollections widget Details page table count did not match with DB", numOfRevenueDetailsCountapp, NumOfRevenueDetailsCountDB); 
		}
	@Test
	public  void VerifyRevenueCategoriesbyCrossYearCollectionsDetailsTransactionCount() throws SQLException {
		RevenuePage.GoToTop5DetailsPage(WidgetOption.Top5RevenueCategoriesbyCrossYearCollections);
		HomePage.ShowWidgetDetails();
		int NumOfRevenueDetailsCountDB =  NYCDatabaseUtil.getRevenueCategoriesCrossYearColectionsDetailsCount(year,'B');
		int numOfRevenueDetailsCountapp = RevenuePage.GetTransactionCount3();
		 System.out.println( RevenuePage.GetTransactionCount3()); 
		assertEquals("Revenue Domain RevenueCategoriesbyCrossYearCollections widget Details page table count did not match with DB", numOfRevenueDetailsCountapp, NumOfRevenueDetailsCountDB); 
		}
	
	@Test
	public  void VerifyAgenciesbyCrossYearCollectionsDetailsTransactionCount() throws SQLException {
		RevenuePage.GoToTop5DetailsPage(WidgetOption.Top5AgenciesbyCrossYearCollections);
		HomePage.ShowWidgetDetails();
		int NumOfRevenueDetailsCountDB =  NYCDatabaseUtil.getRevenueAgenciesCrossYearColectionsDetailsCount(year,'B');
		int numOfRevenueDetailsCountapp = RevenuePage.GetTransactionCount1();
		 System.out.println( RevenuePage.GetTransactionCount1()); 
		assertEquals("Revenue Domain AgenciesbyCrossYearCollections widget Details page table count did not match with DB", numOfRevenueDetailsCountapp, NumOfRevenueDetailsCountDB); 
		}


	/* ***************** Test Widget Transaction Total Amount ****************** */
	
	/* 
	@Test
	public void VerifyRevenueTransactionAmount() throws SQLException {
		//Float transactionAmt = 26.3f;
		RevenuePage.GoToTop5DetailsPage(WidgetOption.Top5Agencies);
		//HomePage.ShowWidgetDetails();
	//assertTrue(HomePage.GetTransactionAmount1()== transactionAmt);
		String NumOfRevenueDetailsAmountDB =  NYCDatabaseUtil.getRevenueDetailsAmount(DB,'B');
		String numOfRevenueDetailsAmountapp = HomePage.GetTransactionAmount1();
		
		System.out.println( RevenuePage.GetTransactionAmount1()); 
	assertEquals("Number ofRevenue widget Details page table count did not match", numOfRevenueDetailsAmountapp, NumOfRevenueDetailsAmountDB); 
	Driver.Instance.quit();
	}

*/
	
	
}
