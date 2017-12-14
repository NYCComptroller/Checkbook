package FunctionalContractsSubVendors;

import static org.junit.Assert.assertEquals;
import static org.junit.Assert.assertTrue;

import java.sql.SQLException;
import java.util.Arrays;

import org.junit.Before;
import org.junit.Test;

import navigation.TopNavigation.Contracts.PendingExpenseContracts;
import navigation.TopNavigation.Contracts.PendingRevenueContracts;
import pages.contracts.ContractsPage;
import pages.contracts.PendingExpenseContractsPage;
import pages.contracts.PendingRevenueContractsPage;
import pages.contracts.ContractsPage.WidgetOption;
import pages.home.HomePage;
import utilities.NYCBaseTest;
import utilities.NYCDatabaseUtil;
import helpers.Helper;
import utilities.TestStatusReport;
public class PendingRevenueContractsTest  extends TestStatusReport{
	//public class PendingRevenueContractsTest extends NYCBaseTest {
	int year =  Integer.parseInt(NYCBaseTest.prop.getProperty("year"));
	@Before
    public void GoToPage() {
	   if (!PendingRevenueContracts.isAt())
		   PendingRevenueContractsPage.GoTo();
	   HomePage.ShowWidgetDetails();
    }

	/* ***************** Test Widget Counts ****************** */
	@Test
	public void VerifyNumOfContracts() throws SQLException {
	 	Integer numOfContractsDB = NYCDatabaseUtil.getPRContractsCount(year,'B');
        Integer numOfContractsApp = ContractsPage.GetTop5WidgetTotalCount(WidgetOption.Top5Contracts);
        assertEquals("Number of Contracts in the Pending Revenue Contracts did not match", numOfContractsApp, numOfContractsDB);
	}
	@Test
	public void VerifyNumOfContractAmountModificationsContracts() throws SQLException {
	 	Integer numOfContractsAmountModificationsContractsDB =NYCDatabaseUtil.getPRContractsModificationsCount(year,'B');
        Integer numOfContractsAmountModificationsContractsApp = ContractsPage.GetTop5WidgetTotalCount(WidgetOption.TopContractAmountModifications);
        assertEquals("Number of Contracts Amount Modifications Contracts in the Pending Revenue Contracts did not match", numOfContractsAmountModificationsContractsApp, numOfContractsAmountModificationsContractsDB);
	}
	@Test
	public void VerifyNumOfPrimeVendorsContracts() throws SQLException {
	 	Integer numOfPrimeVendorsContractsDB = NYCDatabaseUtil.getPRContractsPrimeVendorsCount(year,'B');
        Integer numOfPrimeVendorsContractsApp = ContractsPage.GetTop5WidgetTotalCount(WidgetOption.Top5PrimeVendors);
        assertEquals("Number of Prime Vendors Contracts By Industries in the Pending Revenue Contracts did not match", numOfPrimeVendorsContractsApp, numOfPrimeVendorsContractsDB);
	}
	@Test
	public void VerifyNumOfAwardMethodsContracts() throws SQLException {
	 	Integer numOfAwardMethodsContractsDB = NYCDatabaseUtil.getPRContractsAwardMethodsCount(year,'B');
        Integer numOfAwardMethodsContractsApp = ContractsPage.GetTop5WidgetTotalCount(WidgetOption.Top5AwardMethods);
        assertEquals("Number of Award Methods Contracts in the Pending Revenue Contracts did not match", numOfAwardMethodsContractsApp, numOfAwardMethodsContractsDB);
	}
	@Test
	public void VerifyNumOfAgenciesContracts() throws SQLException {
	 	Integer numOfAgenciesContractsDB = NYCDatabaseUtil.getPRContractsAgenciesCount(year,'B');
        Integer numOfAgenciesContractsApp = ContractsPage.GetTop5WidgetTotalCount(WidgetOption.Top5Agencies);
        assertEquals("Number of Agencies Contracts in the Pending Revenue Contracts did not match", numOfAgenciesContractsApp, numOfAgenciesContractsDB);
	}
	@Test
	public void VerifyNumOfContractsByIndustries() throws SQLException {
	 	Integer numOfContractsByIndustriesDB = NYCDatabaseUtil.getPRContractsIndustriesCount(year,'B');
        Integer numOfContractsByIndustriesApp = ContractsPage.GetTop5WidgetTotalCount(WidgetOption.ContractsByIndustries);
        assertEquals("Number of Contracts By Industries in the Pending Revenue Contracts did not match", numOfContractsByIndustriesApp, numOfContractsByIndustriesDB);
	}	
	@Test
	public void VerifyNumOfContractsBySize() throws SQLException {
	 	Integer numOfContractsBySizeDB = NYCDatabaseUtil.getPRContractsSizeCount(year,'B');
        Integer numOfContractsBySizeApp = ContractsPage.GetTop5WidgetTotalCount(WidgetOption.ContractsBySize);
        assertEquals("Number of Contracts By Size in the Pending Revenue Contracts did not match", numOfContractsBySizeApp, numOfContractsBySizeDB);
	}
	
/* ***************** amounts and titles ****************** */
	
	
	@Test
    public void VerifyTopNavPendingRevenueContractAmount() throws SQLException {
        String TotalContractAmtDB = NYCDatabaseUtil.getContractsCurrentFYTopAmount(year, 'B');
        String TotalContractAmtApp = ContractsPage.GetContractsAmount();
        System.out.println(TotalContractAmtApp); 
        assertEquals("Active Revenue Contracts Top navigation amount did not match", TotalContractAmtApp, TotalContractAmtDB);
    }
	
	@Test
    public void VerifyBottomNavPendingRevenueAmount() throws SQLException {
        String TotalContractAmtDB = NYCDatabaseUtil.getPRContractsAmount(year, 'B');
        String TotalContractAmtApp = ContractsPage.GetBottomNavContractAmount();
    	System.out.println(TotalContractAmtApp); 
    	 assertEquals("Pending Revenue Contracts Bottom navigation Amount did not match", TotalContractAmtApp, TotalContractAmtDB);
      
    }
	@Test
    public void VerifyBottomNavActiveRevenueCount() throws SQLException {
		Integer TotalContractCountDB = NYCDatabaseUtil.getContractsPRCount(year, 'B');
		Integer TotalContractCountApp = ContractsPage.GetBottomNavContractCount();
    	System.out.println(TotalContractCountApp); 
    	 assertEquals("Pending Revenue Contracts Bottom navigation count did not match", TotalContractCountApp, TotalContractCountDB);
	}
 	 
    
	
	@Test
    public void VerifyPendingRevenueContractsVisualizationsTitles(){
	    String[] sliderTitles= {"Top Ten Pending Revenue Contracts by Current Amount", 
	    		                "Top Ten Agencies by Pending Revenue Contracts", 	    						
	    						"Top Ten Prime Vendors by Pending Revenue Contracts"};
	    System.out.println( ContractsPage.VisualizationTitles()); 
    	assertTrue(Arrays.equals(sliderTitles, ContractsPage.VisualizationTitles().toArray()));
    	System.out.println( ContractsPage.VisualizationTitles()); 
    }
	 
	@Test
    public void VerifyPendingRevenueContractsWidgetTitles(){
	   String[] widgetTitles = {"Top 5 Contracts",
	    						"Top Contract Amount Modifications",
	    						"Top 5 Prime Vendors",
	    						"Top 5 Award Methods",
	    						"Top Agencies",
	    						"Contracts by Industries",
	    						"Contracts by Size"
	    						};	    						
	    							    						 
		   System.out.println( ContractsPage.WidgetTitles()); 		
    
    	assertTrue(Arrays.equals(widgetTitles, ContractsPage.WidgetTitles().toArray()));
    	
     }  
}
