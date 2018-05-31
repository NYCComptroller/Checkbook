package FunctionalContractsMWBE;

import static org.junit.Assert.assertEquals;

import static org.junit.Assert.assertTrue;

import java.sql.SQLException;
import java.util.Arrays;

import org.junit.Before;
import org.junit.Test;

import navigation.MWBECategory.MWBECategoryOption;
import navigation.TopNavigation.Contracts.ActiveExpenseContracts;
import pages.contracts.ActiveExpenseContractsPage;
import pages.contracts.ContractsPage;
import pages.contracts.ContractsPage.WidgetOption;
import pages.home.HomePage;
import pages.mwbe.MWBEPage;
import pages.spending.SpendingPage;
import utilities.NYCBaseTest;
import utilities.NYCDatabaseUtil;
import utilities.TestStatusReport;
import helpers.Helper;

public class MWBEActiveExpenseContractsTest extends NYCBaseTest {
	//public class MWBEActiveExpenseContractsTest extends TestStatusReport{
	int year =  Integer.parseInt(NYCBaseTest.prop.getProperty("year"));
	@Before
	/*public void GoToPage() {
		ContractsPage.GoTo();
		if (!ActiveExpenseContracts.isAt()) {
			ActiveExpenseContractsPage.GoTo();
		}
		if (!(Helper.getCurrentSelectedYear())
				.equalsIgnoreCase(NYCBaseTest.prop.getProperty("CurrentYear")))
			HomePage.SelectYear(NYCBaseTest.prop.getProperty("CurrentYear"));
		HomePage.ShowWidgetDetails();
	}*/
	
	public void GoToPage(){
		if(!MWBEPage.IsAt()){
			MWBEPage.GoTo("Contracts", MWBECategoryOption.MWBEHome);
			
		}
		if(!(Helper.getCurrentSelectedYear()).equalsIgnoreCase(NYCBaseTest.prop.getProperty("CurrentYear")))
			   HomePage.SelectYear(NYCBaseTest.prop.getProperty("CurrentYear"));
	}

	/* ***************** Test Widget Counts ****************** */
	@Test
	public void VerifyNumOfMasterAgreementContracts() throws SQLException {
		Integer activeExpenseContractsNumOfMasterAgreementContractsDB = NYCDatabaseUtil.getMWBEAEMasterContractsCount(year, 'B');
		Integer numOfMasterAgreementContractsApp = ContractsPage.GetTop5WidgetTotalCount(WidgetOption.Top5MasterAgreements);
		assertEquals("Number of Master Agreement Contracts in the Active Expense Contracts did not match",numOfMasterAgreementContractsApp,activeExpenseContractsNumOfMasterAgreementContractsDB);
	}
	@Test
	public void VerifyNumOfMasterAgreementModificationsContracts() throws SQLException {
		Integer activeExpenseContractsNumOfMasterAgreementModificationsContractsDB = NYCDatabaseUtil.getMWBEAEMasterContractsModificationCount(year, 'B');
		Integer numOfMasterAgreementContractsApp = ContractsPage.GetTop5WidgetTotalCount(WidgetOption.Top5MasterAgreementModifications);
		assertEquals("Number of Master Agreement Modifications Contracts in the Active Expense Contracts did not match",numOfMasterAgreementContractsApp,activeExpenseContractsNumOfMasterAgreementModificationsContractsDB);
	}
	@Test
	public void VerifyNumOfContracts() throws SQLException {
		Integer activeExpenseContractsNumOfContractsDB = NYCDatabaseUtil.getMWBEAEContractsCount(year, 'B');
		Integer numOfContractsApp = ContractsPage.GetTop5WidgetTotalCount(WidgetOption.Top5Contracts);
		assertEquals("Number of Contracts in the Active Expense Contracts did not match",numOfContractsApp, activeExpenseContractsNumOfContractsDB);
	}
	@Test
	public void VerifyNumOfContractAmountModificationsContracts() throws SQLException {
		Integer activeExpenseContractsNumOfContractAmountModificationsContractsDB = NYCDatabaseUtil.getMWBEAEContractsModificationCount(year, 'B');
		Integer numOfContractAmountModificationsContractsApp = ContractsPage.GetTop5WidgetTotalCount(WidgetOption.Top5ContractAmountModifications);
		assertEquals("Number of Contract modifications in the Active Expense Contracts did not match",numOfContractAmountModificationsContractsApp,activeExpenseContractsNumOfContractAmountModificationsContractsDB);
	}
	@Test
	public void VerifyNumOfPrimeVendorsContracts() throws SQLException {
		Integer activeExpenseContractsNumOfPrimeVendorsContractsDB = NYCDatabaseUtil.getMWBEAEContractsPrimeVendorsCount(year, 'B');
		Integer numOfPrimeVendorsContractsApp = ContractsPage.GetTop5WidgetTotalCount(WidgetOption.Top5PrimeVendors);
		assertEquals("Number of Prime vendors in the Active Expense Contracts did not match",numOfPrimeVendorsContractsApp,activeExpenseContractsNumOfPrimeVendorsContractsDB);
	}
	@Test
	public void VerifyNumOfAwardMethodsContracts() throws SQLException {
		Integer activeExpenseContractsNumOfAwardMethodsContractsDB = NYCDatabaseUtil.getMWBEAEContractsAwardMethodsCount(year, 'B');
		Integer numOfAwardMethodsContractsApp = ContractsPage.GetTop5WidgetTotalCount(WidgetOption.Top5AwardMethods);
		assertEquals("Number of Awardmethods in the Active Expense Contracts did not match",numOfAwardMethodsContractsApp,activeExpenseContractsNumOfAwardMethodsContractsDB);
	}
	@Test
	public void VerifyNumOfAgenciesContracts() throws SQLException {
		Integer activeExpenseContractsNumOfAgenciesContractsDB = NYCDatabaseUtil.getMWBEAEContractsAgenciesCount(year, 'B');
		Integer numOfAgenciesContractsApp = ContractsPage.GetTop5WidgetTotalCount(WidgetOption.Top5Agencies);
		assertEquals("Number of Agencies in the Active Expense Contracts did not match",numOfAgenciesContractsApp,activeExpenseContractsNumOfAgenciesContractsDB);
	}
	@Test
	public void VerifyNumOfContractsByIndustries() throws SQLException {
		Integer activeExpenseContractsNumOfContractsByIndustriesDB = NYCDatabaseUtil.getMWBEAEContractsIndustriesCount(year, 'B');
		Integer numOfContractsByIndustries = ContractsPage.GetTop5WidgetTotalCount(WidgetOption.ContractsByIndustries);
		assertEquals("Number of Contracts in the  Active Expense contract by Industry  widget did not match",numOfContractsByIndustries,activeExpenseContractsNumOfContractsByIndustriesDB);
	}
	@Test
	public void VerifyNumOfContractsBySize() throws SQLException {
		Integer activeExpenseContractsNumOfContractsBySizeDB = NYCDatabaseUtil.getMWBEAEContractsSizeCount(year, 'B');
		Integer numOfContractsBySize = ContractsPage.GetTop5WidgetTotalCount(WidgetOption.ContractsBySize);
		assertEquals("Number of Contracts in the  Active Expense Contracts by Size widget did not match",numOfContractsBySize,activeExpenseContractsNumOfContractsBySizeDB);
	}
	/////// amounts and titles
	
	@Test
    public void VerifyTopNavContractAmount() throws SQLException {
        String TotalContractAmtDB = NYCDatabaseUtil.getMWBEContractsTopAmount(year, 'B');
        String TotalContractAmtApp = ContractsPage.GetMWBEContractsAmount();
        System.out.println(TotalContractAmtApp); 
        assertEquals("Active Expense Contracts Top navigation amount did not match", TotalContractAmtApp, TotalContractAmtDB);
    }
	
	@Test
    public void VerifyBottomNavActiveExpenseAmount() throws SQLException {
        String TotalContractAmtDB = NYCDatabaseUtil.getMWBEAEContractsAmount(year, 'B');
        String TotalContractAmtApp = ContractsPage.GetBottomNavContractAmount();
    	System.out.println(TotalContractAmtApp); 
    	 assertEquals("Active Expense Contracts Bottom navigation Amount did not match", TotalContractAmtApp, TotalContractAmtDB);
           
    }
	
	@Test
    public void VerifyBottomNavActiveExpenseCount() throws SQLException {
		Integer TotalContractCountDB = NYCDatabaseUtil.getMWBEContractsAECount(year, 'B');
		Integer TotalContractCountApp = ContractsPage.GetBottomNavContractCount();
    	System.out.println(TotalContractCountApp); 
    	 assertEquals("Active Expense Contracts Bottom navigation count did not match", TotalContractCountApp, TotalContractCountDB);
	}
	
	@Test
    public void VerifyActiveExpenseContractsVisualizationsTitles(){
	    String[] sliderTitles= {"M/WBE Spending by Active Expense Contracts", 
	    						"Top Ten Agencies by M/WBE Active Expense Contracts", 
	    						"Top Ten M/WBE Active Expense Contracts by Current Amount", 
	    						"Top Ten M/WBE Prime Vendors by Active Expense Contracts",
	    						"Top Ten M/WBE Sub Vendors by Active Expense Contracts"};
	    System.out.println( ContractsPage.MWBEVisualizationTitles()); 
    	assertTrue(Arrays.equals(sliderTitles, ContractsPage.MWBEVisualizationTitles().toArray()));
    	//System.out.println( ContractsPage.MWBEVisualizationTitles()); 
    }
	 
	@Test
    public void VerifyActiveExpenseContractsSpendingWidgetTitles(){
	   String[] widgetTitles = {"Top 5 Master Agreements",
	    						"Top 5 Master Agreement Modifications",
	    						"Top 5 Contracts",
	    						"Top 5 Contract Amount Modifications",
	    						"Top 5 Prime Vendors",
	    						"Top 5 Sub Vendors",
	    						"Top 5 Award Methods",
	    						"Top 5 Agencies",
	    						"Contracts by Industries",
	    						"Contracts by Size"
	    						};	    						
	    							    						 
		   System.out.println( ContractsPage.WidgetTitles()); 		
    
    	assertTrue(Arrays.equals(widgetTitles, ContractsPage.WidgetTitles().toArray()));
    	
     }  
	
}
