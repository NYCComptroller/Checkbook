package FunctionalRevenue;

import static org.junit.Assert.assertEquals;

import static org.junit.Assert.assertTrue;

import java.sql.SQLException;

import org.junit.Before;
import org.junit.Test;
import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;

import pages.revenue.RevenuePage.WidgetOption;
import pages.spending.SpendingPage;
import pages.budget.BudgetPage;
import pages.home.HomePage;
import pages.revenue.RevenuePage;
import utilities.NYCBaseTest;
import utilities.NYCDatabaseUtil;
import helpers.Driver;
import helpers.Helper;
import utilities.TestStatusReport;

//public class RevenueWidgetDetailsPageTitlesTest extends NYCBaseTest {
	public class RevenueWidgetDetailsPageTitlesTest extends TestStatusReport {
//	int year =  Integer.parseInt(NYCBaseTest.prop.getProperty("year"));
	@Before
	public void GoToPage() {
		RevenuePage.GoTo();
		if(!(Helper.getCurrentSelectedYear()).equalsIgnoreCase(NYCBaseTest.prop.getProperty("CurrentYear")))
			HomePage.SelectYear(NYCBaseTest.prop.getProperty("CurrentYear"));
		HomePage.ShowWidgetDetails();
	}
	
	

	/* ***************** Test Widget Transaction Total Amount ****************** */
	

	@Test
	public void VerifyRevenueAgenciesTransactionTitle() throws SQLException {
			RevenuePage.GoToTop5DetailsPage(WidgetOption.Top5Agencies);
		HomePage.ShowWidgetDetails();
			String RevenueAgenciesTitle =  "Agencies Revenue Transactions";
		String RevenueAgenciesTitleApp = HomePage.DetailsPagetitle();
	assertEquals("Revenue Agencies Widget details page title did not match", RevenueAgenciesTitle, RevenueAgenciesTitleApp); 
	}


	@Test
	public void VerifyRevenueCategoriesTransactionTitle() throws SQLException {
			RevenuePage.GoToTop5DetailsPage(WidgetOption.Top5RevenueCategories);
		HomePage.ShowWidgetDetails();
		String RevenueCategoriesTitle =  "Revenue Categories Revenue Transactions";
		String RevenueCategoriesTitleApp = HomePage.DetailsPagetitle();
	assertEquals("RevenueCategories Widget details page title did not match", RevenueCategoriesTitle, RevenueCategoriesTitleApp); 
	}
	
	@Test
	public void VerifyRevenuebyFundingclassesTransactionTitle() throws SQLException {
		RevenuePage.GoToTop5DetailsPage(WidgetOption.RevenuebyFundingClasses);
		HomePage.ShowWidgetDetails();
		String RevenueFundingclassesTitle =  "Revenue by Funding Classes Revenue Transactions";
		String RevenueFundingclassesTitleApp = HomePage.DetailsPagetitle();
	assertEquals("RevenueCategories Widget details page title did not match", RevenueFundingclassesTitle, RevenueFundingclassesTitleApp); 
	}
	
	@Test
	public void VerifyRevenueAgenciesCrossYearCollectionsTransactionTitle() throws SQLException {
		RevenuePage.GoToTop5DetailsPage(WidgetOption.Top5AgenciesbyCrossYearCollections);
		HomePage.ShowWidgetDetails();
		String RevenueAgenciesCrossYearCollectionsTitle =  "Agencies by Cross Year Collections Transactions";
		String RevenueAgenciesCrossYearCollectionsTitleApp = HomePage.DetailsPagetitle();
	assertEquals("Revenue AgenciesCrossYearCollectionsWidget details page title did not match", RevenueAgenciesCrossYearCollectionsTitle, RevenueAgenciesCrossYearCollectionsTitleApp); 
	}

	@Test
	public void VerifyRevenueCategoriesCrossYearCollectionsTransactionTitle() throws SQLException {
		//Float transactionAmt = 26.3f;
		RevenuePage.GoToTop5DetailsPage(WidgetOption.Top5RevenueCategoriesbyCrossYearCollections);
		HomePage.ShowWidgetDetails();
		String RevenueCategoriesCrossYearCollectionsTitle =  "Revenue Categories by Cross Year Collections Transactions";
		String RevenueCategoriesCrossYearCollectionsTitleApp = HomePage.DetailsPagetitle();
	assertEquals("Revenue AgenciesCrossYearCollectionsWidget details page title did not match", RevenueCategoriesCrossYearCollectionsTitle, RevenueCategoriesCrossYearCollectionsTitleApp); 
	}
	
	@Test
	public void VerifyRevenueFundingclassesCrossYearCollectionsTransactionTitle() throws SQLException {
		//String transactionAmt = "-$47.08M";
		RevenuePage.GoToTop5DetailsPage(WidgetOption.RevenuebyFundingClassesbyCrossYearCollections);
		HomePage.ShowWidgetDetails();
		String RevenueFundingclassesCrossYearCollectionsTitle =  "Revenue by Funding Classes by Cross Year Collections Transactions";
		String RevenueFundingclassesCrossYearCollectionsTitleApp = HomePage.DetailsPagetitle();
	assertEquals("Revenue AgenciesCrossYearCollectionsWidget details page title did not match", RevenueFundingclassesCrossYearCollectionsTitle, RevenueFundingclassesCrossYearCollectionsTitleApp); 
	}

	
}
