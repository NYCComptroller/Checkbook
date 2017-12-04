package FunctionalContracts;

import static org.junit.Assert.assertEquals;
import static org.junit.Assert.assertTrue;

import java.sql.SQLException;
import java.util.Arrays;

import org.junit.Before;
import org.junit.Test;

import navigation.TopNavigation.Contracts.ActiveRevenueContracts;
import pages.contracts.ActiveRevenueContractsPage;
import pages.contracts.ContractsPage;
import pages.contracts.ContractsPage.WidgetOption;
import pages.home.HomePage;
import utilities.NYCBaseTest;
import utilities.NYCDatabaseUtil;
import utilities.TestStatusReport;
import helpers.Helper;

//public class ActiveRevenueContractsTest extends NYCBaseTest {
	public class ActiveRevenueContractsTest extends TestStatusReport{
		int year =  Integer.parseInt(NYCBaseTest.prop.getProperty("year"));
	@Before
	public void GoToPage(){
		if (!ActiveRevenueContracts.isAt()){
			ActiveRevenueContractsPage.GoTo();
		}
		if(!(Helper.getCurrentSelectedYear()).equalsIgnoreCase(NYCBaseTest.prop.getProperty("CurrentYear")))
			HomePage.SelectYear(NYCBaseTest.prop.getProperty("CurrentYear"));
		HomePage.ShowWidgetDetails();
	}
	
	/* ***************** Test Widget Counts ****************** */
	@Test
	public void VerifyNumOfContracts() throws SQLException {
	 	Integer numOfContractsDB = NYCDatabaseUtil.getARContractsCount(year,'B');	 	
        Integer numOfContractsApp = ContractsPage.GetTop5WidgetTotalCount(WidgetOption.Top5Contracts);
        assertEquals("Number of Contracts in the Active Revenue Contracts did not match", numOfContractsApp, numOfContractsDB);
	}
	@Test
	public void VerifyNumOfContractAmountModificationsContracts() throws SQLException {
	 	Integer numOfContractAmountModificationsContractsDB = NYCDatabaseUtil.getARContractsModificationsCount(year,'B');	
        Integer numOfContractAmountModificationsContractsApp = ContractsPage.GetTop5WidgetTotalCount(WidgetOption.Top5ContractAmountModifications);
        assertEquals("Number of Contract Amount Modifications Contracts in the Active Revenue Contracts did not match", numOfContractAmountModificationsContractsApp, numOfContractAmountModificationsContractsDB);
	}
	@Test
	public void VerifyNumOfPrimeVendorsContracts() throws SQLException {
	 	Integer numOfPrimeVendorsContractsDB = NYCDatabaseUtil.getARContractsPrimeVendorsCount(year,'B');	
        Integer numOfPrimeVendorsContractsApp = ContractsPage.GetTop5WidgetTotalCount(WidgetOption.Top5PrimeVendors);
        assertEquals("Number of Prime Vendors Contracts in the Active Revenue Contracts did not match", numOfPrimeVendorsContractsApp, numOfPrimeVendorsContractsDB);
	}
	@Test
	public void VerifyNumOfAwardMethodsContracts() throws SQLException {
	 	Integer numOfAwardMethodsContractsDB = NYCDatabaseUtil.getARContractsAwardMethodsCount(year,'B');	
        Integer numOfAwardMethodsContractsApp = ContractsPage.GetTop5WidgetTotalCount(WidgetOption.Top5AwardMethods);
        assertEquals("Number of Award Methods Contracts in the Active Revenue Contracts did not match", numOfAwardMethodsContractsApp, numOfAwardMethodsContractsDB);
	}
	@Test
	public void VerifyNumOfAgenciesContracts() throws SQLException {
	 	Integer numOfAgenciesContractsDB = NYCDatabaseUtil.getARContractsAgenciesCount(year,'B');	
        Integer numOfAgenciesContractsApp = ContractsPage.GetTop5WidgetTotalCount(WidgetOption.Top5Agencies);
        assertEquals("Number of Agencies Contracts in the Active Revenue Contracts did not match", numOfAgenciesContractsApp, numOfAgenciesContractsDB);
	}
	@Test
	public void VerifyNumOfContractsByIndustries() throws SQLException {
	 	Integer numOfContractsByIndustriesDB = NYCDatabaseUtil.getARContractsIndustriesCount(year,'B');	
        Integer numOfContractsByIndustriesApp = ContractsPage.GetTop5WidgetTotalCount(WidgetOption.ContractsByIndustries);
        assertEquals("Number of Contracts By Industries in the Active Revenue Contracts did not match", numOfContractsByIndustriesApp, numOfContractsByIndustriesDB);
	}
	@Test
	public void VerifyNumOfContractsBySize() throws SQLException {
	 	Integer numOfContractsBySizeDB = NYCDatabaseUtil.getARContractsSizeCount(year,'B');	
        Integer numOfContractsBySizeApp = ContractsPage.GetTop5WidgetTotalCount(WidgetOption.ContractsBySize);
        assertEquals("Number of Contracts By Size in the Agencies Revenue did not match", numOfContractsBySizeApp, numOfContractsBySizeDB);
	}
	
	
	/* ***************** amounts and titles ****************** */
	
	
	@Test
    public void VerifyTopNavContractAmount() throws SQLException {
        String TotalContractAmtDB = NYCDatabaseUtil.getContractsTopAmount(year, 'B');
        String TotalContractAmtApp = ContractsPage.GetContractsAmount();
        System.out.println(TotalContractAmtApp); 
        assertEquals("Active Revenue Contracts Top navigation amount did not match", TotalContractAmtApp, TotalContractAmtDB);
    }
	
	@Test
    public void VerifyBottomNavActiveRevenueAmount() throws SQLException {
        String TotalContractAmtDB = NYCDatabaseUtil.getARContractsAmount(year, 'B');
        String TotalContractAmtApp = ContractsPage.GetBottomNavContractAmount();
    	System.out.println(TotalContractAmtApp); 
    	 assertEquals("Active Revenue Contracts Bottom navigation Amount did not match", TotalContractAmtApp, TotalContractAmtDB);
      
    }
	@Test
    public void VerifyBottomNavActiveRevenueCount() throws SQLException {
		Integer TotalContractCountDB = NYCDatabaseUtil.getContractsARCount(year, 'B');
		Integer TotalContractCountApp = ContractsPage.GetBottomNavContractCount();
    	System.out.println(TotalContractCountApp); 
    	 assertEquals("Active Revenue Contracts Bottom navigation count did not match", TotalContractCountApp, TotalContractCountDB);
	}
 	 
    
	
	@Test
    public void VerifyActiveRevenueContractsVisualizationsTitles(){
	    String[] sliderTitles= {"Top Ten Active Revenue Contracts by Current Amount", 
	    		                 "Top Ten Agencies by Active Revenue Contracts", 	    						
	    						"Top Ten Prime Vendors by Active Revenue Contracts"};
	    System.out.println( ContractsPage.VisualizationTitles()); 
    	assertTrue(Arrays.equals(sliderTitles, ContractsPage.VisualizationTitles().toArray()));
    	System.out.println( ContractsPage.VisualizationTitles()); 
    }
	 
	@Test
    public void VerifyActiveRevenueContractsSpendingWidgetTitles(){
	   String[] widgetTitles = {"Top 5 Contracts",
	    						"Top 5 Contract Amount Modifications",
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
