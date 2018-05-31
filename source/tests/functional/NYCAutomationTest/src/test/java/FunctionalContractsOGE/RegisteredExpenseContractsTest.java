package FunctionalContractsOGE;

import static org.junit.Assert.assertEquals;
import static org.junit.Assert.assertTrue;

import java.sql.SQLException;
import java.util.Arrays;

import org.junit.Before;
import org.junit.Test;

import navigation.TopNavigation.Contracts.RegisteredExpenseContracts;
import pages.contracts.ContractsPage;
import pages.contracts.RegisteredExpenseContractsPage;
import pages.contracts.ContractsPage.WidgetOption;
import pages.home.HomePage;
import utilities.NYCBaseTest;
import utilities.NYCDatabaseUtil;
import helpers.Helper;
	import utilities.TestStatusReport;
	public class RegisteredExpenseContractsTest  extends TestStatusReport{
		
		//public class RegisteredExpenseContractsTest extends NYCBaseTest {
		
		int year =  Integer.parseInt(NYCBaseTest.prop.getProperty("year"));
	@Before
	public void GoToPage(){
		if (!RegisteredExpenseContracts.isAt())
			RegisteredExpenseContractsPage.GoTo();
		if(!(Helper.getCurrentSelectedYear()).equalsIgnoreCase(NYCBaseTest.prop.getProperty("CurrentYear")))
			HomePage.SelectYear(NYCBaseTest.prop.getProperty("CurrentYear"));
		HomePage.ShowWidgetDetails();
	}

	/* ***************** Test Widget Counts ****************** */
	@Test
	public void VerifyNumOfMasterAgreementsContracts() throws SQLException {
		Integer numOfMasterAgreementContractsFY2016 = NYCDatabaseUtil.getREContractsMasterCount(2016,'B');	 	
		Integer numOfMasterAgreementContracts = ContractsPage.GetTop5WidgetTotalCount(WidgetOption.Top5MasterAgreements);
		assertEquals("Number of Master Agreement Contracts in the Registered Expense Contracts did not match", numOfMasterAgreementContracts, numOfMasterAgreementContractsFY2016);
	}
	@Test
	public void VerifyNumOfMasterAgreementModificationsContracts() throws SQLException {
		Integer numOfMasterAgreementModificationsContractsFY2016 = NYCDatabaseUtil.getREContractsMasterModificationsCount(2016,'B');	
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
		Integer numOfContractsAmountModificationsContractsFY2016 = NYCDatabaseUtil.getREContractsModificationsCount(2016,'B');	
		Integer numOfContractsAmountModificationsContracts = ContractsPage.GetTop5WidgetTotalCount(WidgetOption.Top5ContractAmountModifications);
		assertEquals("Number of Contracts Amount Modifications Contracts in the Registered Expense Contracts did not match", numOfContractsAmountModificationsContracts, numOfContractsAmountModificationsContractsFY2016);
	}
	@Test
	public void VerifyNumOfPrimeVendorsContracts() throws SQLException {
		Integer numOfPrimeVendorsContractsFY2016 = NYCDatabaseUtil.getREContractsPrimeVendorsCount(2016,'B');	
		Integer numOfPrimeVendorsContracts = ContractsPage.GetTop5WidgetTotalCount(WidgetOption.Top5PrimeVendors);
		assertEquals("Number of Prime Vendors Contracts By Industries in the Registered Expense Contracts did not match", numOfPrimeVendorsContracts, numOfPrimeVendorsContractsFY2016);
	}
	@Test
	public void VerifyNumOfAwardMethodsContracts() throws SQLException {
		Integer numOfAwardMethodsContractsFY2016 = NYCDatabaseUtil.getREContractsAwardMethodsCount(2016,'B');	
		Integer numOfAwardMethodsContracts = ContractsPage.GetTop5WidgetTotalCount(WidgetOption.Top5AwardMethods);
		assertEquals("Number of Award Methods Contracts in the Registered Expense Contracts did not match", numOfAwardMethodsContracts, numOfAwardMethodsContractsFY2016);
	}
	@Test
	public void VerifyNumOfAgenciesContracts() throws SQLException {
		Integer numOfAgenciesContractsFY2016 = NYCDatabaseUtil.getREContractsAgenciesCount(2016,'B');	
		Integer numOfAgenciesContracts = ContractsPage.GetTop5WidgetTotalCount(WidgetOption.Top5Agencies);
		assertEquals("Number of Agencies Contracts in the Registered Expense Contracts did not match", numOfAgenciesContracts, numOfAgenciesContractsFY2016);
	}
	@Test
	public void VerifyNumOfContractsByIndustries() throws SQLException {
		Integer numOfContractsByIndustriesFY2016 = NYCDatabaseUtil.getREContractsIndustriesCount(2016,'B');	
		Integer numOfContractsByIndustries = ContractsPage.GetTop5WidgetTotalCount(WidgetOption.ContractsByIndustries);
		assertEquals("Number of Contracts By Industries in the Registered Expense Contracts did not match", numOfContractsByIndustries, numOfContractsByIndustriesFY2016);
	}
	@Test
	public void VerifyNumOfContractsBySize() throws SQLException {
		Integer numOfContractsBySizeFY2016 = NYCDatabaseUtil.getREContractsSizeCount(2016,'B');	
		Integer numOfContractsBySize = ContractsPage.GetTop5WidgetTotalCount(WidgetOption.ContractsBySize);
		assertEquals("Number of Contracts By Size in the Registered Expense Contracts did not match", numOfContractsBySize, numOfContractsBySizeFY2016);
	}

	
	/* ***************** amounts and titles ****************** */
	
	
/////// amounts and titles
	
	@Test
  public void VerifyTopNavContractAmount() throws SQLException {
      String TotalContractAmtDB = NYCDatabaseUtil.getContractsTopAmount(year, 'B');
      String TotalContractAmtApp = ContractsPage.GetContractsAmount();
      System.out.println(TotalContractAmtApp); 
      assertEquals("Registered Expense Contracts Top navigation amount did not match", TotalContractAmtApp, TotalContractAmtDB);
  }
	
	@Test
  public void VerifyBottomNavRegisteredExpenseAmount() throws SQLException {
      String TotalContractAmtDB = NYCDatabaseUtil.getREContractsAmount(year, 'B');
      String TotalContractAmtApp = ContractsPage.GetBottomNavContractAmount();
  	System.out.println(TotalContractAmtApp); 
  	 assertEquals("Registered Expense Contracts Bottom navigation Amount did not match", TotalContractAmtApp, TotalContractAmtDB);
      
   
  }
	
	@Test
  public void VerifyBottomNavRegisteredExpenseCount() throws SQLException {
		Integer TotalContractCountDB = NYCDatabaseUtil.getContractsRECount(year, 'B');
		Integer TotalContractCountApp = ContractsPage.GetBottomNavContractCount();
  	System.out.println(TotalContractCountApp); 
  	 assertEquals("Registered Expense Contracts Bottom navigation count did not match", TotalContractCountApp, TotalContractCountDB);
	}
	
	@Test
  public void VerifyRegisteredExpenseContractsVisualizationsTitles(){
	    String[] sliderTitles= {"Spending by Registered Expense Contracts", 
	    						"Top Ten Agencies by Registered Expense Contracts", 
	    						"Top Ten Registered Expense Contracts by Current Amount", 
	    						"Top Ten Prime Vendors by Registered Expense Contracts"};
	    System.out.println( ContractsPage.VisualizationTitles()); 
  	assertTrue(Arrays.equals(sliderTitles, ContractsPage.VisualizationTitles().toArray()));
  	System.out.println( ContractsPage.VisualizationTitles()); 
  }
	 
	@Test
  public void VerifyRegisteredExpenseContractsWidgetTitles(){
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
