package FunctionalContractsSubVendors;

import static org.junit.Assert.assertEquals;

import static org.junit.Assert.assertTrue;

import java.sql.SQLException;
import java.util.Arrays;

import org.junit.Before;
import org.junit.Test;
import org.openqa.selenium.By;
import org.openqa.selenium.support.ui.ExpectedConditions;
import org.openqa.selenium.support.ui.WebDriverWait;

import navigation.SecondarySubVendorTabSelector;
import navigation.SecondaryTabSelector;
import navigation.TopNavigation;
import navigation.SubVendorCategory.SubVendorCategoryOption;
import navigation.TopNavigation.Contracts.ActiveExpenseContracts;
import pages.contracts.ActiveExpenseContractsPage;
import pages.contracts.ContractsPage;
import pages.contracts.ContractsPage.WidgetOption;
import pages.home.HomePage;
import pages.spending.SpendingPage;
import pages.subvendors.RegisteredSubVendorContractsPage;
import pages.subvendors.SubVendorsPage;
import utilities.NYCBaseTest;
import utilities.NYCDatabaseUtil;
import utilities.TestStatusReport;
import helpers.Driver;
import helpers.Helper;

//public class  NewSubVendorContractsbyFiscalYearTest extends NYCBaseTest {
public class NewSubVendorContractsbyFiscalYearTest extends TestStatusReport{
	int year =  Integer.parseInt(NYCBaseTest.prop.getProperty("year"));
	@Before
	 public void GoToPage(){
		   if (!SubVendorsPage.IsAt())
			   
			   SubVendorsPage.GoTo("Contracts", SubVendorCategoryOption.SubVendorsHome);
			  // RegisteredSubVendorContractsPage.GoTo(); 
		  
		   
			   if(!(Helper.getCurrentSelectedYear()).equalsIgnoreCase(NYCBaseTest.prop.getProperty("CurrentYear")))
			   HomePage.SelectYear(NYCBaseTest.prop.getProperty("CurrentYear"));
			//   WebDriverWait wait = new WebDriverWait(Driver.Instance, 30);
			   SubVendorsPage.GoToBottomNavContractslink();
	    }



	/* ***************** Test Widget Counts ****************** */
	@Test
	public void VerifyTop5SubVendorContracts() throws SQLException {
		Integer activeSubVendorContractsNumDB = NYCDatabaseUtil.getTotalRegisteredSubContractsCount(year, 'B');
		Integer numOfsubVendorContractsApp = ContractsPage.GetTop5WidgetTotalCount(WidgetOption.Top5SubContracts);
		assertEquals("Number of Sub Vendor Contracts in the Active Sub Vendor Contracts did not match",numOfsubVendorContractsApp,activeSubVendorContractsNumDB);
	}
	

	@Test
	public void VerifyTop5SubContractsAmountModifications() throws SQLException {
		Integer activeSubVendorContractAmountModificationsNumDB = NYCDatabaseUtil.getTotalRegisteredSubContractModifications(year, 'B');
		Integer numOfSubVendorContractsAmountModificationApp = ContractsPage.GetTop5WidgetTotalCount(WidgetOption.Top5SubContractAmountModifications);
		assertEquals("Number of Sub Contract Modifications Contracts in the Active Sub Vendor Contracts did not match", numOfSubVendorContractsAmountModificationApp,activeSubVendorContractAmountModificationsNumDB);
	}
	@Test
	public void VerifyNumOfSubVendors() throws SQLException {
		Integer numOfSubVendorsDB = NYCDatabaseUtil.getRegisteredSubVendorsCount(year, 'B');
		Integer numOfSubVendorsApp = ContractsPage.GetTop5WidgetTotalCount(WidgetOption.Top5SubVendors);
		assertEquals("Number of Contracts in the Active Expense Contracts did not match",numOfSubVendorsApp, numOfSubVendorsDB);
	}

	@Test
	public void VerifyNumOfPrimeVendorsContracts() throws SQLException {
		Integer NumOfPrimeVendorsContractsDB = NYCDatabaseUtil. getTotalRegisteredPrimeVendorCount(year, 'B');
		Integer numOfPrimeVendorsContractsApp = ContractsPage.GetTop5WidgetTotalCount(WidgetOption.Top5PrimeVendors);
		assertEquals("Number of Prime vendors in the Active Expense Contracts did not match",numOfPrimeVendorsContractsApp,NumOfPrimeVendorsContractsDB);
	}
	@Test
	public void VerifyNumOfAwardMethodsContracts() throws SQLException {
		Integer NumOfAwardMethodsContractsDB = NYCDatabaseUtil.getTotalRegisteredSubVendorAwardMethodsCount(year, 'B');
		Integer numOfAwardMethodsContractsApp = ContractsPage.GetTop5WidgetTotalCount(WidgetOption.Top5AwardMethods);
		assertEquals("Number of Award methods in the Active Expense Contracts did not match",numOfAwardMethodsContractsApp,NumOfAwardMethodsContractsDB);
	}
	@Test
	public void VerifyNumOfAgenciesContracts() throws SQLException {
		Integer NumOfAgenciesContractsDB = NYCDatabaseUtil.getTotalRegisteredSubVendorContractsAgenciesCount(year, 'B');
		Integer numOfAgenciesContractsApp = ContractsPage.GetTop5WidgetTotalCount(WidgetOption.Top5Agencies);
		assertEquals("Number of Agencies in the Active Expense Contracts did not match",numOfAgenciesContractsApp,NumOfAgenciesContractsDB);
	}
	@Test
	public void VerifyNumOfContractsByIndustries() throws SQLException {
		Integer activeExpenseContractsNumOfContractsByIndustriesDB = NYCDatabaseUtil.getRegisteredSubVendorContractsByIndustriesCount(year, 'B');
		Integer numOfContractsByIndustries = ContractsPage.GetTop5WidgetTotalCount(WidgetOption.ContractsByIndustries);
		assertEquals("Number of Contracts in the  Active Expense contract by Industry  widget did not match",numOfContractsByIndustries,activeExpenseContractsNumOfContractsByIndustriesDB);
	}
	@Test
	public void VerifyNumOfContractsBySize() throws SQLException {
		Integer activeExpenseContractsNumOfContractsBySizeDB = NYCDatabaseUtil.getTotalRegisteredSubVendorContractsSizeCount(year, 'B');
		Integer numOfContractsBySize = ContractsPage.GetTop5WidgetTotalCount(WidgetOption.ContractsBySize);
		assertEquals("Number of Contracts in the  Active Expense Contracts by Size widget did not match",numOfContractsBySize,activeExpenseContractsNumOfContractsBySizeDB);
	}
	/////// amounts and titles

	@Test
    public void VerifyTopNavContractAmount() throws SQLException {
        String TotalContractAmtDB = NYCDatabaseUtil.getContractsTopAmount(year, 'B');
        String TotalContractAmtApp = ContractsPage.GetContractsAmount();
        System.out.println(TotalContractAmtApp);
        assertEquals("Active Expense Contracts Top navigation amount did not match", TotalContractAmtApp, TotalContractAmtDB);
    }

	@Test
    public void VerifyCountForRegisteredSubVendorContracts() throws SQLException {
        Integer TotalContractAmtDB = NYCDatabaseUtil.getTotalRegisteredSubVendorContracts(year, 'B');
        Integer TotalContractAmtApp = ContractsPage.GetActiveSubVendorContractAmount();
    	System.out.println(TotalContractAmtApp);
    	 assertEquals("Active Expense Contracts Bottom navigation Count did not match", TotalContractAmtApp, TotalContractAmtDB);

    }

	@Test
    public void VerifyContractsVisualizationsTitles(){
	    String[] sliderTitles= {"Sub Vendors Spending by New Sub Vendor Contracts by Fiscal Year",
	    						"Top Ten Agencies by New Sub Vendor Contracts by Fiscal Year",
	    						"Top Ten Active New Sub Vendor Contracts by Current Amount",
	    						"Top Ten Prime Vendors by New Sub Vendor Contracts by Fiscal Year",
	    						"Top Ten Sub Vendors by New Sub Vendor Contracts by Fiscal Year"
	    						};
	 //  System.out.println( ContractsPage.VisualizationTitles());
    	assertTrue(Arrays.equals(sliderTitles, ContractsPage.SubVendorVisualizationTitles().toArray()));
    	System.out.println( ContractsPage.SubVendorVisualizationTitles());
    }

	@Test
    public void VerifySubVendorContractsSpendingWidgetTitles(){
	   String[] widgetTitles = {"Top 5 Sub Contracts",
	    						"Top 5 Sub Contract Amount Modifications",
	    						"Top 5 Sub Vendors",
	    						"Top 5 Prime Vendors",
	    						"Top 5 Award Methods",
	    						"Top 5 Agencies",
	    						"Contracts by Industries",
	    						"Contracts by Size"
	    						};

		   System.out.println( ContractsPage.WidgetTitles());

    	assertTrue(Arrays.equals(widgetTitles, ContractsPage.WidgetTitles().toArray()));

     }

}