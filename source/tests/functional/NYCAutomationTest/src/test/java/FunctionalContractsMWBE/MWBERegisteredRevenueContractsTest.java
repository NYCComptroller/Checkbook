package FunctionalContractsMWBE;

import static org.junit.Assert.assertEquals;
import static org.junit.Assert.assertTrue;

import java.sql.SQLException;
import java.util.Arrays;

import org.junit.Before;
import org.junit.Test;

import navigation.MWBECategory.MWBECategoryOption;
import navigation.TopNavigation.Contracts.RegisteredRevenueContracts;
import pages.contracts.ContractsPage;
import pages.contracts.RegisteredRevenueContractsPage;
import pages.contracts.ContractsPage.WidgetOption;
import pages.home.HomePage;
import pages.mwbe.MWBEPage;
import utilities.NYCBaseTest;
import utilities.NYCDatabaseUtil;
import helpers.Helper;
import utilities.TestStatusReport;
//public class MWBERegisteredRevenueContractsTest  extends TestStatusReport{
public class MWBERegisteredRevenueContractsTest extends NYCBaseTest {
	int year =  Integer.parseInt(NYCBaseTest.prop.getProperty("year"));
	@Before
	public void GoToPage(){
		if(!MWBEPage.IsAt()){
			MWBEPage.GoTo("Contracts", MWBECategoryOption.MWBEHome);
			navigation.TopNavigation.Contracts.RegisteredRevenueContracts.Select();	
		}
		if(!(Helper.getCurrentSelectedYear()).equalsIgnoreCase(NYCBaseTest.prop.getProperty("CurrentYear")))
			   HomePage.SelectYear(NYCBaseTest.prop.getProperty("CurrentYear"));
	}
	/* ***************** Test Widget Counts ****************** */
	@Test
	public void VerifyNumOfContracts() throws SQLException {
	 	Integer numOfContractsFY2016 = NYCDatabaseUtil.getMWBERRContractsCount(2016,'B');	
        Integer numOfContracts = ContractsPage.GetTop5WidgetTotalCount(WidgetOption.TopContracts);
        assertEquals("Number of Contracts in the Registered Revenue Contracts did not match", numOfContracts, numOfContractsFY2016);
	}
	@Test
	public void VerifyNumOfContractsAmountModificationsContracts() throws SQLException {
	 	Integer numOfContractsAmountModificationsContractsFY2016 = NYCDatabaseUtil.getMWBERRContractsModificationsCount(2016,'B');	
        Integer numOfContractsAmountModificationsContracts = ContractsPage.GetTop5WidgetTotalCount(WidgetOption.TopContractAmountModifications);
        assertEquals("Number of Contracts Amount Modifications Contracts in the Registered Revenue Contracts did not match", numOfContractsAmountModificationsContracts, numOfContractsAmountModificationsContractsFY2016);
	}
	@Test
	public void VerifyNumOfPrimeVendorsContracts() throws SQLException {
	 	Integer numOfPrimeVendorsContractsFY2016 = NYCDatabaseUtil.getMWBERRContractsPrimeVendorsCount(2016,'B');
        Integer numOfPrimeVendorsContracts = ContractsPage.GetTop5WidgetTotalCount(WidgetOption.TopPrimeVendors);
        assertEquals("Number of Prime Vendors Contracts By Industries in the Registered Revenue Contracts did not match", numOfPrimeVendorsContracts, numOfPrimeVendorsContractsFY2016);
	}
	@Test
	public void VerifyNumOfAwardMethodsContracts() throws SQLException {
	 	Integer numOfAwardMethodsContractsFY2016 = NYCDatabaseUtil.getMWBERRContractsAwardMethodsCount(2016,'B');
        Integer numOfAwardMethodsContracts = ContractsPage.GetTop5WidgetTotalCount(WidgetOption.TopAwardMethods);
        assertEquals("Number of Award Methods Contracts in the Registered Revenue Contracts did not match", numOfAwardMethodsContracts, numOfAwardMethodsContractsFY2016);
	}
	@Test
	public void VerifyNumOfAgenciesContracts() throws SQLException {
	 	Integer numOfAgenciesContractsFY2016 = NYCDatabaseUtil.getMWBERRContractsAgenciesCount(2016,'B');
        Integer numOfAgenciesContracts = ContractsPage.GetTop5WidgetTotalCount(WidgetOption.TopAgencies);
        assertEquals("Number of Agencies Contracts in the Registered Revenue Contracts did not match", numOfAgenciesContracts, numOfAgenciesContractsFY2016);
	}
	@Test
	public void VerifyNumOfContractsByIndustries() throws SQLException {
	 	Integer numOfContractsByIndustriesFY2016 = NYCDatabaseUtil.getMWBERRContractsIndustriesCount(2016,'B');
        Integer numOfContractsByIndustries = ContractsPage.GetTop5WidgetTotalCount(WidgetOption.ContractsByIndustries);
        assertEquals("Number of Contracts By Industries in the Registered Revenue Contracts did not match", numOfContractsByIndustries, numOfContractsByIndustriesFY2016);
	}	
	@Test
	public void VerifyNumOfContractsBySize() throws SQLException {
	 	Integer numOfContractsBySizeFY2016 = NYCDatabaseUtil.getMWBERRContractsSizeCount(2016,'B');
        Integer numOfContractsBySize = ContractsPage.GetTop5WidgetTotalCount(WidgetOption.ContractsBySize);
        assertEquals("Number of Contracts By Size in the Registered Revenue Contracts did not match", numOfContractsBySize, numOfContractsBySizeFY2016);
	}
	/// titles
	@Test
    public void VerifyTopNavRegisteredRevenueContractAmount() throws SQLException {
        String TotalContractAmtDB = NYCDatabaseUtil.getMWBEContractsTopAmount(year, 'B');
        String TotalContractAmtApp = ContractsPage.GetMWBEContractsAmount();
        System.out.println(TotalContractAmtApp); 
        assertEquals("Registered Revenue Contracts Top navigation amount did not match", TotalContractAmtApp, TotalContractAmtDB);
    }
	
	@Test
    public void VerifyBottomNavRegisteredRevenueAmount() throws SQLException {
        String TotalContractAmtDB = NYCDatabaseUtil.getMWBERRContractsAmount(year, 'B');
        String TotalContractAmtApp = ContractsPage.GetBottomNavContractAmount();
    	System.out.println(TotalContractAmtApp); 
    	 assertEquals("Registered Revenue Contracts Bottom navigation Amount did not match", TotalContractAmtApp, TotalContractAmtDB);
      
    }
	@Test
    public void VerifyBottomNavRegisteredRevenueCount() throws SQLException {
		Integer TotalContractCountDB = NYCDatabaseUtil.getMWBEContractsRRCount(year, 'B');
		Integer TotalContractCountApp = ContractsPage.GetBottomNavContractCount();
    	System.out.println(TotalContractCountApp); 
    	 assertEquals("Registered Revenue Contracts Bottom navigation count did not match", TotalContractCountApp, TotalContractCountDB);
	}
 	 
    
	
	@Test
    public void VerifyRegisteredRevenueContractsVisualizationsTitles(){
	    String[] sliderTitles= {"Top Ten Agencies by M/WBE Registered Revenue Contracts",
	    		             "Top Ten M/WBE Registered Revenue Contracts by Current Amount",	    							    						 
	    						"Top Ten M/WBE Prime Vendors by Registered Revenue Contracts"};
	    System.out.println( ContractsPage.VisualizationTitles()); 
    	assertTrue(Arrays.equals(sliderTitles, ContractsPage.MWBEVisualizationTitles().toArray()));
    	System.out.println( ContractsPage.VisualizationTitles()); 
    }
	 
	@Test
    public void VerifyRegisteredRevenueContractsWidgetTitles(){
	   String[] widgetTitles = {"Top Contracts",
	    						"Top Contract Amount Modifications",
	    						"Top Prime Vendors",
	    						"Top Award Methods",
	    						"Top Agencies",
	    						"Contracts by Industries",
	    						"Contracts by Size"
	    						};	    						
	    							    						 
		   System.out.println( ContractsPage.WidgetTitles()); 		
    
    	assertTrue(Arrays.equals(widgetTitles, ContractsPage.WidgetTitles().toArray()));
    	
     }  
}
