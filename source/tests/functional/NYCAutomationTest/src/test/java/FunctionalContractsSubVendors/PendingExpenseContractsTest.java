package FunctionalContractsSubVendors;

import static org.junit.Assert.assertEquals;
import static org.junit.Assert.assertTrue;

import java.sql.SQLException;
import java.util.Arrays;

import org.junit.Before;
import org.junit.Test;

import navigation.TopNavigation.Contracts.ActiveExpenseContracts;
import navigation.TopNavigation.Contracts.PendingExpenseContracts;
import pages.contracts.ActiveExpenseContractsPage;
import pages.contracts.ContractsPage;
import pages.contracts.PendingExpenseContractsPage;
import pages.contracts.PendingRevenueContractsPage;
import pages.contracts.ContractsPage.WidgetOption;
import pages.home.HomePage;
import utilities.NYCBaseTest;
import utilities.NYCDatabaseUtil;
import helpers.Helper;
import utilities.TestStatusReport;
public class PendingExpenseContractsTest  extends TestStatusReport{

	//public class PendingExpenseContractsTest extends NYCBaseTest {
	
	int year =  Integer.parseInt(NYCBaseTest.prop.getProperty("year"));
	@Before
	public void GoToPage() {
			  if (!PendingExpenseContracts.isAt())	{
			   PendingExpenseContractsPage.GoTo();
		  }		
	
	}
	
	/* ***************** Test Widget Counts ****************** */
	@Test
	public void VerifyNumOfMasterAgreementsContracts() throws SQLException {
	 	Integer numOfMasterAgreementContractsDB = NYCDatabaseUtil.getPEContractsMasterCount(year,'B');	 	
        Integer numOfMasterAgreementContractsApp = ContractsPage.GetTop5WidgetTotalCount(WidgetOption.Top5MasterAgreements);
        assertEquals("Number of Master Agreement Contracts in the Pending Expense Contracts did not match", numOfMasterAgreementContractsApp, numOfMasterAgreementContractsDB);
	}
	@Test
	public void VerifyNumOfMasterAgreementModificationsContracts() throws SQLException {
	 	Integer numOfMasterAgreementModificationsContractsDB = NYCDatabaseUtil.getPEContractsMasterModificationsCount(year,'B');	
        Integer numOfMasterAgreementModificationsContractsApp = ContractsPage.GetTop5WidgetTotalCount(WidgetOption.Top5MasterAgreementModifications);
        assertEquals("Number of Contract Amount Modifications Contracts in the Pending Expense Contracts did not match", numOfMasterAgreementModificationsContractsApp, numOfMasterAgreementModificationsContractsDB);
	}
	@Test
	public void VerifyNumOfContracts() throws SQLException {
	 	Integer numOfContractsDB = NYCDatabaseUtil.getPEContractsCount(year,'B');
        Integer numOfContracts = ContractsPage.GetTop5WidgetTotalCount(WidgetOption.Top5Contracts);
        assertEquals("Number of Contracts in the Pending Expense Contracts did not match", numOfContracts, numOfContractsDB);
	}
	@Test
	public void VerifyNumOfContractsAmountModificationsContracts() throws SQLException {
	 	Integer numOfContractsAmountModificationsContractsDB = NYCDatabaseUtil.getPEContractsModificationsCount(year,'B');
        Integer numOfContractsAmountModificationsContractsApp = ContractsPage.GetTop5WidgetTotalCount(WidgetOption.Top5ContractAmountModifications);
        assertEquals("Number of Contracts Amount Modifications Contracts in the Pending Expense Contracts did not match", numOfContractsAmountModificationsContractsApp, numOfContractsAmountModificationsContractsDB);
	}
	@Test
	public void VerifyNumOfPrimeVendorsContracts() throws SQLException {
	 	Integer numOfPrimeVendorsContractsDB = NYCDatabaseUtil.getPEContractsPrimeVendorsCount(year,'B');
        Integer numOfPrimeVendorsContractsApp = ContractsPage.GetTop5WidgetTotalCount(WidgetOption.Top5PrimeVendors);
        assertEquals("Number of Prime Vendors Contracts in the Pending Expense Contracts did not match", numOfPrimeVendorsContractsApp, numOfPrimeVendorsContractsDB);
	}
	@Test
	public void VerifyNumOfAwardMethodsContracts() throws SQLException {
	 	Integer numOfAwardMethodsContractsDB = NYCDatabaseUtil.getPEContractsAwardMethodsCount(year,'B');
        Integer numOfAwardMethodsContractsApp = ContractsPage.GetTop5WidgetTotalCount(WidgetOption.Top5AwardMethods);
        assertEquals("Number of Award Methods Contracts in the Pending Expense Contracts did not match", numOfAwardMethodsContractsApp, numOfAwardMethodsContractsDB);
	}
	@Test
	public void VerifyNumOfAgenciesContracts() throws SQLException {
	 	Integer numOfAgenciesContractsDB = NYCDatabaseUtil.getPEContractsAgenciesCount(year,'B');
        Integer numOfAgenciesContractsApp = ContractsPage.GetTop5WidgetTotalCount(WidgetOption.Top5Agencies);
        assertEquals("Number of Agencies Contracts in the Pending Expense Contracts did not match", numOfAgenciesContractsApp, numOfAgenciesContractsDB);
	}
	@Test
	public void VerifyNumOfContractsByIndustries() throws SQLException {
	 	Integer numOfContractsByIndustriesDB = NYCDatabaseUtil.getPEContractsIndustriesCount(year,'B');
        Integer numOfContractsByIndustriesApp = ContractsPage.GetTop5WidgetTotalCount(WidgetOption.ContractsByIndustries);
        assertEquals("Number of Contracts By Industries in the Pending Expense Contracts did not match", numOfContractsByIndustriesApp, numOfContractsByIndustriesDB);
	}
	@Test
	public void VerifyNumOfContractsBySize() throws SQLException {
	 	Integer numOfContractsBySizeDB = NYCDatabaseUtil.getPEContractsSizeCount(year,'B');
        Integer numOfContractsBySizeApp = ContractsPage.GetTop5WidgetTotalCount(WidgetOption.ContractsBySize);
        assertEquals("Number of Contracts By Size in the Pending Expense Contracts did not match", numOfContractsBySizeApp, numOfContractsBySizeDB);
	}
	
	/////// amounts and titles
	
	@Test
    public void VerifyTopNavPendingExpenseContractAmount() throws SQLException {
        String TotalContractAmtDB = NYCDatabaseUtil.getContractsCurrentFYTopAmount(year, 'B');
        String TotalContractAmtApp = ContractsPage.GetContractsAmount();
        System.out.println(TotalContractAmtApp); 
        assertEquals("Pending Expense Contracts Top navigation amount did not match", TotalContractAmtApp, TotalContractAmtDB);
    }
	
	@Test
    public void VerifyBottomNavPendingExpenseAmount() throws SQLException {
        String TotalContractAmtDB = NYCDatabaseUtil.getPEContractsAmount(year, 'B');
        String TotalContractAmtApp = ContractsPage.GetBottomNavContractAmount();
    	System.out.println(TotalContractAmtApp); 
    	 assertEquals("Pending Expense Contracts Bottom navigation Amount did not match", TotalContractAmtApp, TotalContractAmtDB);
        
     
    }
	
	@Test
    public void VerifyBottomNavPendingExpenseCount() throws SQLException {
		Integer TotalContractCountDB = NYCDatabaseUtil.getContractsBottomnNavPECount(year, 'B');
		Integer TotalContractCountApp = ContractsPage.GetBottomNavContractCount();
    	System.out.println(TotalContractCountApp); 
    	 assertEquals("Pending Expense Contracts Bottom navigation count did not match", TotalContractCountApp, TotalContractCountDB);
	}
	
	@Test
    public void VerifyPendingExpenseContractsVisualizationsTitles(){
	    String[] sliderTitles= {"Top Ten Pending Expense Contracts by Current Amount", 
	    						"Top Ten Agencies by Pending Expense Contracts",	    						
	    						"Top Ten Prime Vendors by Pending Expense Contracts"};
	    System.out.println( ContractsPage.VisualizationTitles2()); 
    	assertTrue(Arrays.equals(sliderTitles, ContractsPage.VisualizationTitles2().toArray()));
    	System.out.println( ContractsPage.VisualizationTitles2()); 
    }
	 
	@Test
    public void VerifyPendingExpenseContractsSpendingWidgetTitles(){
	   String[] widgetTitles = {"Top 5 Master Agreements",
	    						"Top 5 Master Agreement Modifications",
	    						"Top 5 Contracts",
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
