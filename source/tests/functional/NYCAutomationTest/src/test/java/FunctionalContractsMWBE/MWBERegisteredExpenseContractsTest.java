package FunctionalContractsMWBE;

import static org.junit.Assert.assertEquals;
import static org.junit.Assert.assertTrue;

import java.sql.SQLException;
import java.util.Arrays;

import org.junit.Before;
import org.junit.Test;

import navigation.MWBECategory.MWBECategoryOption;
import navigation.TopNavigation.Contracts.RegisteredExpenseContracts;
import pages.contracts.ContractsPage;
import pages.contracts.RegisteredExpenseContractsPage;
import pages.contracts.ContractsPage.WidgetOption;
import pages.home.HomePage;
import pages.mwbe.MWBEPage;
import utilities.NYCBaseTest;
import utilities.NYCDatabaseUtil;
import helpers.Helper;
	import utilities.TestStatusReport;
	//public class MWBERegisteredExpenseContractsTest  extends TestStatusReport{
		
		public class MWBERegisteredExpenseContractsTest extends NYCBaseTest {
		
		int year =  Integer.parseInt(NYCBaseTest.prop.getProperty("year"));
	
	@Before
	public void GoToPage(){
		if(!MWBEPage.IsAt()){
			MWBEPage.GoTo("Contracts", MWBECategoryOption.MWBEHome);
			navigation.TopNavigation.Contracts.RegisteredExpenseContracts.Select();	
		}
		if(!(Helper.getCurrentSelectedYear()).equalsIgnoreCase(NYCBaseTest.prop.getProperty("CurrentYear")))
			   HomePage.SelectYear(NYCBaseTest.prop.getProperty("CurrentYear"));
	}

	/* ***************** Test Widget Counts ****************** */
	@Test
	public void VerifyNumOfMasterAgreementsContracts() throws SQLException {
		Integer numOfMasterAgreementContractsFY2016 = NYCDatabaseUtil.getMWBEREContractsMasterCount(2016,'B');	 	
		Integer numOfMasterAgreementContracts = ContractsPage.GetTop5WidgetTotalCount(WidgetOption.Top5MasterAgreements);
		assertEquals("Number of Master Agreement Contracts in the Registered Expense Contracts did not match", numOfMasterAgreementContracts, numOfMasterAgreementContractsFY2016);
	}
	@Test
	public void VerifyNumOfMasterAgreementModificationsContracts() throws SQLException {
		Integer numOfMasterAgreementModificationsContractsFY2016 = NYCDatabaseUtil.getMWBEREContractsMasterModificationsCount(2016,'B');	
		Integer numOfMasterAgreementModificationsContracts = ContractsPage.GetTop5WidgetTotalCount(WidgetOption.Top5MasterAgreementModifications);
		assertEquals("Number of Contract Amount Modifications Contracts in the Registered Expense Contracts did not match", numOfMasterAgreementModificationsContracts, numOfMasterAgreementModificationsContractsFY2016);
	}
	@Test
	public void VerifyNumOfContracts() throws SQLException {
		Integer numOfContractsFY2016 = NYCDatabaseUtil.getREContractsCount(2016,'B');	
		Integer numOfContracts = ContractsPage.GetTop5WidgetTotalCount(WidgetOption.Top5Contracts);
		assertEquals("Number of Contracts in the Registered Expense Contracts did not match", numOfContracts, numOfContractsFY2016);
	}
	@Test
	public void VerifyNumOfContractsAmountModificationsContracts() throws SQLException {
		Integer numOfContractsAmountModificationsContractsFY2016 = NYCDatabaseUtil.getMWBEREContractsModificationsCount(2016,'B');	
		Integer numOfContractsAmountModificationsContracts = ContractsPage.GetTop5WidgetTotalCount(WidgetOption.Top5ContractAmountModifications);
		assertEquals("Number of Contracts Amount Modifications Contracts in the Registered Expense Contracts did not match", numOfContractsAmountModificationsContracts, numOfContractsAmountModificationsContractsFY2016);
	}
	@Test
	public void VerifyNumOfPrimeVendorsContracts() throws SQLException {
		Integer numOfPrimeVendorsContractsFY2016 = NYCDatabaseUtil.getMWBEREContractsPrimeVendorsCount(2016,'B');	
		Integer numOfPrimeVendorsContracts = ContractsPage.GetTop5WidgetTotalCount(WidgetOption.Top5PrimeVendors);
		assertEquals("Number of Prime Vendors Contracts By Industries in the Registered Expense Contracts did not match", numOfPrimeVendorsContracts, numOfPrimeVendorsContractsFY2016);
	}
	@Test
	public void VerifyNumOfAwardMethodsContracts() throws SQLException {
		Integer numOfAwardMethodsContractsFY2016 = NYCDatabaseUtil.getMWBEREContractsAwardMethodsCount(2016,'B');	
		Integer numOfAwardMethodsContracts = ContractsPage.GetTop5WidgetTotalCount(WidgetOption.Top5AwardMethods);
		assertEquals("Number of Award Methods Contracts in the Registered Expense Contracts did not match", numOfAwardMethodsContracts, numOfAwardMethodsContractsFY2016);
	}
	@Test
	public void VerifyNumOfAgenciesContracts() throws SQLException {
		Integer numOfAgenciesContractsFY2016 = NYCDatabaseUtil.getMWBEREContractsAgenciesCount(2016,'B');	
		Integer numOfAgenciesContracts = ContractsPage.GetTop5WidgetTotalCount(WidgetOption.Top5Agencies);
		assertEquals("Number of Agencies Contracts in the Registered Expense Contracts did not match", numOfAgenciesContracts, numOfAgenciesContractsFY2016);
	}
	@Test
	public void VerifyNumOfContractsByIndustries() throws SQLException {
		Integer numOfContractsByIndustriesFY2016 = NYCDatabaseUtil.getMWBEREContractsIndustriesCount(2016,'B');	
		Integer numOfContractsByIndustries = ContractsPage.GetTop5WidgetTotalCount(WidgetOption.ContractsByIndustries);
		assertEquals("Number of Contracts By Industries in the Registered Expense Contracts did not match", numOfContractsByIndustries, numOfContractsByIndustriesFY2016);
	}
	@Test
	public void VerifyNumOfContractsBySize() throws SQLException {
		Integer numOfContractsBySizeFY2016 = NYCDatabaseUtil.getMWBEREContractsSizeCount(2016,'B');	
		Integer numOfContractsBySize = ContractsPage.GetTop5WidgetTotalCount(WidgetOption.ContractsBySize);
		assertEquals("Number of Contracts By Size in the Registered Expense Contracts did not match", numOfContractsBySize, numOfContractsBySizeFY2016);
	}

	
	/* ***************** amounts and titles ****************** */
	
	
/////// amounts and titles
	
	@Test
  public void VerifyTopNavContractAmount() throws SQLException {
      String TotalContractAmtDB = NYCDatabaseUtil.getMWBEContractsTopAmount(year, 'B');
      String TotalContractAmtApp = ContractsPage.GetMWBEContractsAmount();
      System.out.println(TotalContractAmtApp); 
      assertEquals("Registered Expense Contracts Top navigation amount did not match", TotalContractAmtApp, TotalContractAmtDB);
  }
	
	@Test
  public void VerifyBottomNavRegisteredExpenseAmount() throws SQLException {
      String TotalContractAmtDB = NYCDatabaseUtil.getMWBEREContractsAmount(year, 'B');
      String TotalContractAmtApp = ContractsPage.GetBottomNavContractAmount();
  	System.out.println(TotalContractAmtApp); 
  	 assertEquals("Registered Expense Contracts Bottom navigation Amount did not match", TotalContractAmtApp, TotalContractAmtDB);
      
   
  }
	
	@Test
  public void VerifyBottomNavRegisteredExpenseCount() throws SQLException {
		Integer TotalContractCountDB = NYCDatabaseUtil.getMWBEContractsRECount(year, 'B');
		Integer TotalContractCountApp = ContractsPage.GetBottomNavContractCount();
  	System.out.println(TotalContractCountApp); 
  	 assertEquals("Registered Expense Contracts Bottom navigation count did not match", TotalContractCountApp, TotalContractCountDB);
	}
	
	@Test
  public void VerifyRegisteredExpenseContractsVisualizationsTitles(){
	    String[] sliderTitles= {"M/WBE Spending by Registered Expense Contracts", 
	    						"Top Ten Agencies by M/WBE Registered Expense Contracts", 
	    						"Top Ten M/WBE Registered Expense Contracts by Current Amount", 
	    						"Top Ten M/WBE Prime Vendors by Registered Expense Contracts",
	    						"Top Ten M/WBE Sub Vendors by Registered Expense Contracts"};
	    System.out.println( ContractsPage.VisualizationTitles()); 
  	assertTrue(Arrays.equals(sliderTitles, ContractsPage.MWBEVisualizationTitles().toArray()));
  	System.out.println( ContractsPage.VisualizationTitles()); 
  }
	 
	@Test
  public void VerifyRegisteredExpenseContractsWidgetTitles(){
	   String[] widgetTitles = {"Top 5 Master Agreements",
	    						"Top Master Agreement Modifications",
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
