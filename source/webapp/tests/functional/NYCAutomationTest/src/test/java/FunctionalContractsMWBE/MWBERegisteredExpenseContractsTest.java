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
	public class MWBERegisteredExpenseContractsTest  extends TestStatusReport{
		
		//	public class MWBERegisteredExpenseContractsTest extends NYCBaseTest {
		
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
		Integer numOfMasterAgreementContractsFYyear = NYCDatabaseUtil.getMWBEREContractsMasterCount(year,'B');	 	
		Integer numOfMasterAgreementContracts = ContractsPage.GetTop5WidgetTotalCount(WidgetOption.Top5MasterAgreements);
		assertEquals("Number of Master Agreement Contracts in the Registered Expense Contracts did not match", numOfMasterAgreementContracts, numOfMasterAgreementContractsFYyear);
	}
	@Test
	public void VerifyNumOfMasterAgreementModificationsContracts() throws SQLException {
		Integer numOfMasterAgreementModificationsContractsFYyear = NYCDatabaseUtil.getMWBEREContractsMasterModificationsCount(year,'B');	
		Integer numOfMasterAgreementModificationsContracts = ContractsPage.GetTop5WidgetTotalCount(WidgetOption.Top5MasterAgreementModifications);
		assertEquals("Number of Contract Amount Modifications Contracts in the Registered Expense Contracts did not match", numOfMasterAgreementModificationsContracts, numOfMasterAgreementModificationsContractsFYyear);
	}
	@Test
	public void VerifyNumOfContracts() throws SQLException {
		Integer numOfContractsFYyear = NYCDatabaseUtil.getMWBEREContractsCount(year,'B');	
		Integer numOfContracts = ContractsPage.GetTop5WidgetTotalCount(WidgetOption.Top5Contracts);
		assertEquals("Number of Contracts in the Registered Expense Contracts did not match", numOfContracts, numOfContractsFYyear);
	}
	@Test
	public void VerifyNumOfContractsAmountModificationsContracts() throws SQLException {
		Integer numOfContractsAmountModificationsContractsFYyear = NYCDatabaseUtil.getMWBEREContractsModificationsCount(year,'B');	
		Integer numOfContractsAmountModificationsContracts = ContractsPage.GetTop5WidgetTotalCount(WidgetOption.Top5ContractAmountModifications);
		assertEquals("Number of Contracts Amount Modifications Contracts in the Registered Expense Contracts did not match", numOfContractsAmountModificationsContracts, numOfContractsAmountModificationsContractsFYyear);
	}
	@Test
	public void VerifyNumOfPrimeVendorsContracts() throws SQLException {
		Integer numOfPrimeVendorsContractsFYyear = NYCDatabaseUtil.getMWBEREContractsPrimeVendorsCount(year,'B');	
		Integer numOfPrimeVendorsContracts = ContractsPage.GetTop5WidgetTotalCount(WidgetOption.Top5PrimeVendors);
		assertEquals("Number of Prime Vendors Contracts By Industries in the Registered Expense Contracts did not match", numOfPrimeVendorsContracts, numOfPrimeVendorsContractsFYyear);
	}
	@Test
	public void VerifyNumOfAwardMethodsContracts() throws SQLException {
		Integer numOfAwardMethodsContractsFYyear = NYCDatabaseUtil.getMWBEREContractsAwardMethodsCount(year,'B');	
		Integer numOfAwardMethodsContracts = ContractsPage.GetTop5WidgetTotalCount(WidgetOption.Top5AwardMethods);
		assertEquals("Number of Award Methods Contracts in the Registered Expense Contracts did not match", numOfAwardMethodsContracts, numOfAwardMethodsContractsFYyear);
	}
	@Test
	public void VerifyNumOfAgenciesContracts() throws SQLException {
		Integer numOfAgenciesContractsFYyear = NYCDatabaseUtil.getMWBEREContractsAgenciesCount(year,'B');	
		Integer numOfAgenciesContracts = ContractsPage.GetTop5WidgetTotalCount(WidgetOption.Top5Agencies);
		assertEquals("Number of Agencies Contracts in the Registered Expense Contracts did not match", numOfAgenciesContracts, numOfAgenciesContractsFYyear);
	}
	@Test
	public void VerifyNumOfContractsByIndustries() throws SQLException {
		Integer numOfContractsByIndustriesFYyear = NYCDatabaseUtil.getMWBEREContractsIndustriesCount(year,'B');	
		Integer numOfContractsByIndustries = ContractsPage.GetTop5WidgetTotalCount(WidgetOption.ContractsByIndustries);
		assertEquals("Number of Contracts By Industries in the Registered Expense Contracts did not match", numOfContractsByIndustries, numOfContractsByIndustriesFYyear);
	}
	@Test
	public void VerifyNumOfContractsBySize() throws SQLException {
		Integer numOfContractsBySizeFYyear = NYCDatabaseUtil.getMWBEREContractsSizeCount(year,'B');	
		Integer numOfContractsBySize = ContractsPage.GetTop5WidgetTotalCount(WidgetOption.ContractsBySize);
		assertEquals("Number of Contracts By Size in the Registered Expense Contracts did not match", numOfContractsBySize, numOfContractsBySizeFYyear);
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
	    System.out.println( ContractsPage.MWBEVisualizationTitles()); 
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
