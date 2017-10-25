package FunctionalRevenue;

import static org.junit.Assert.assertEquals;

import static org.junit.Assert.assertTrue;

import java.sql.SQLException;

import org.junit.Before;
import org.junit.Test;
import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;

import pages.revenue.RevenuePage.WidgetOption;
import pages.home.HomePage;
import pages.revenue.RevenuePage;
import utilities.NYCBaseTest;
import utilities.NYCDatabaseUtil;
import helpers.Driver;
import helpers.Helper;
import utilities.TestStatusReport;

public class RevenueWidgetDetailsAmountTest extends NYCBaseTest {
	//public class RevenueWidgetDetailsAmountTest extends TestStatusReport {

	@Before
	public void GoToPage() {
		RevenuePage.GoTo();
		if(!(Helper.getCurrentSelectedYear()).equalsIgnoreCase(NYCBaseTest.prop.getProperty("CurrentYear")))
			HomePage.SelectYear(NYCBaseTest.prop.getProperty("CurrentYear"));
		HomePage.ShowWidgetDetails();
	}
	
	

	/* ***************** Test Widget Transaction Total Amount ****************** */
	
	 
	@Test
	public void VerifyRevenueTransactionAmount() throws SQLException {
		//Float transactionAmt = 26.3f;
		RevenuePage.GoToTop5DetailsPage(WidgetOption.Top5Agencies);
		HomePage.ShowWidgetDetails();
	//assertTrue(HomePage.GetTransactionAmount1()== transactionAmt);
		String NumOfRevenueDetailsAmount2016 =  NYCDatabaseUtil.getRevenueDetailsAmount(2016,'B');
		String numOfRevenueDetailsAmountapp = RevenuePage.GetTransactionAmount1();
		System.out.println( RevenuePage.GetTransactionAmount1()); 
	assertEquals("Number ofRevenue widget Details page table count did not match", numOfRevenueDetailsAmountapp, NumOfRevenueDetailsAmount2016); 
	}


	@Test
	public void VerifyRevenueCategoriesTransactionAmount() throws SQLException {
		//Float transactionAmt = 26.3f;
		RevenuePage.GoToTop5DetailsPage(WidgetOption.Top5RevenueCategories);
		HomePage.ShowWidgetDetails();
	//assertTrue(HomePage.GetTransactionAmount1()== transactionAmt);
		String NumOfRevenueDetailsAmount2016 =  NYCDatabaseUtil.getRevenueDetailsAmount(2016,'B');
		String numOfRevenueDetailsAmountapp = RevenuePage.GetTransactionAmount1();
		System.out.println( RevenuePage.GetTransactionAmount1()); 
	assertEquals("Number ofRevenue widget Details page table count did not match", numOfRevenueDetailsAmountapp, NumOfRevenueDetailsAmount2016); 
	}
	
	@Test
	public void VerifyRevenueFundingclassesTransactionAmount() throws SQLException {
		//Float transactionAmt = 26.3f;
		RevenuePage.GoToTop5DetailsPage(WidgetOption.RevenuebyFundingClasses);
		HomePage.ShowWidgetDetails();
	//assertTrue(HomePage.GetTransactionAmount1()== transactionAmt);
		String NumOfRevenueDetailsAmount2016 =  NYCDatabaseUtil.getRevenueDetailsAmount(2016,'B');
		String numOfRevenueDetailsAmountapp = RevenuePage.GetTransactionAmount1();
		System.out.println( RevenuePage.GetTransactionAmount1()); 
	assertEquals("Number ofRevenue widget Details page table count did not match", numOfRevenueDetailsAmountapp, NumOfRevenueDetailsAmount2016); 
	}
	
	@Test
	public void VerifyAgenciesCrossYearCollectionsTransactionAmount() throws SQLException {
		//Float transactionAmt = 26.3f;
		RevenuePage.GoToTop5DetailsPage(WidgetOption.Top5AgenciesbyCrossYearCollections);
		HomePage.ShowWidgetDetails();
	//assertTrue(HomePage.GetTransactionAmount1()== transactionAmt);
		String NumOfRevenueDetailsAmount2016 =  NYCDatabaseUtil.getRevenueDetailsAmount(2016,'B');
		String numOfRevenueDetailsAmountapp = RevenuePage.GetTransactionAmount1();
		System.out.println( RevenuePage.GetTransactionAmount1()); 
	assertEquals("Number ofRevenue widget Details page table count did not match", numOfRevenueDetailsAmountapp, NumOfRevenueDetailsAmount2016); 
	}

	@Test
	public void VerifyRevenueCategoriesCrossYearCollectionsTransactionAmount() throws SQLException {
		//Float transactionAmt = 26.3f;
		RevenuePage.GoToTop5DetailsPage(WidgetOption.Top5RevenueCategoriesbyCrossYearCollections);
		HomePage.ShowWidgetDetails();
	//assertTrue(HomePage.GetTransactionAmount1()== transactionAmt);
		String NumOfRevenueDetailsAmount2016 =  NYCDatabaseUtil.getRevenueDetailsAmount(2016,'B');
		String numOfRevenueDetailsAmountapp = RevenuePage.GetTransactionAmount1();
		System.out.println( RevenuePage.GetTransactionAmount1()); 
	assertEquals("Number ofRevenue widget Details page table count did not match", numOfRevenueDetailsAmountapp, NumOfRevenueDetailsAmount2016); 
	}
	
	@Test
	public void VerifyRevenueFundingclassesCrossYearCollectionsTransactionAmount() throws SQLException {
		//Float transactionAmt = 26.3f;
		RevenuePage.GoToTop5DetailsPage(WidgetOption.RevenuebyFundingClassesbyCrossYearCollections);
		HomePage.ShowWidgetDetails();
	//assertTrue(HomePage.GetTransactionAmount1()== transactionAmt);
		String NumOfRevenueDetailsAmount2016 =  NYCDatabaseUtil.getRevenueDetailsAmount(2016,'B');
		String numOfRevenueDetailsAmountapp = RevenuePage.GetTransactionAmount1();
		System.out.println( RevenuePage.GetTransactionAmount1()); 
	assertEquals("Number ofRevenue widget Details page table count did not match", numOfRevenueDetailsAmountapp, NumOfRevenueDetailsAmount2016);
}
}
