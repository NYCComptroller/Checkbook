package FunctionalContractsOGE;

import static org.junit.Assert.assertEquals;
import static org.junit.Assert.assertTrue;

import java.sql.SQLException;
import java.util.Arrays;

import org.junit.Before;
import org.junit.Test;

import navigation.SecondaryMenuNavigation.OtherGovernmentEntities;
import navigation.TopNavigation.Contracts.RegisteredExpenseContracts;
import pages.contracts.ContractsPage;
import pages.contracts.RegisteredExpenseContractsPage;
import pages.contracts.ContractsPage.WidgetOption;
import pages.home.HomePage;
import utilities.OGENYCBaseTest;
import utilities.OGENYCDatabaseUtil;
import helpers.Helper;
	import utilities.TestStatusReport;
	public class OGERegisteredExpenseContractsTest  extends TestStatusReport{
		
		//public class OGERegisteredExpenseContractsTest extends OGENYCBaseTest {
		
		int year =  Integer.parseInt(OGENYCBaseTest.prop.getProperty("year"));
	@Before
	public void GoToPage(){
		if(!OtherGovernmentEntities.IsAt())
			OtherGovernmentEntities.GoTo();
		
		ContractsPage.GoTo();
		if (!RegisteredExpenseContracts.isAt())
			RegisteredExpenseContractsPage.GoTo();
		
		if(!(Helper.getCurrentSelectedYear()).equalsIgnoreCase(OGENYCBaseTest.prop.getProperty("CurrentYear")))
			HomePage.SelectYear(OGENYCBaseTest.prop.getProperty("CurrentYear"));
		HomePage.ShowWidgetDetails();
	}

	/* ***************** Test Widget Counts ****************** */
	@Test
	public void VerifyNumOfMasterAgreementsContracts() throws SQLException {
		Integer numOfMasterAgreementContractsFYyear = OGENYCDatabaseUtil.getREContractsMasterCount(year,'B');	 	
		Integer numOfMasterAgreementContracts = ContractsPage.GetTop5WidgetTotalCount(WidgetOption.Top5MasterAgreements);
		assertEquals("Number of Master Agreement Contracts in the Registered Expense Contracts did not match", numOfMasterAgreementContracts, numOfMasterAgreementContractsFYyear);
	}
	@Test
	public void VerifyNumOfMasterAgreementModificationsContracts() throws SQLException {
		Integer numOfMasterAgreementModificationsContractsFYyear = OGENYCDatabaseUtil.getREContractsMasterModificationsCount(year,'B');	
		Integer numOfMasterAgreementModificationsContracts = ContractsPage.GetTop5WidgetTotalCount(WidgetOption.TopMasterAgreementModifications);
		assertEquals("Number of Contract Amount Modifications Contracts in the Registered Expense Contracts did not match", numOfMasterAgreementModificationsContracts, numOfMasterAgreementModificationsContractsFYyear);
	}
	@Test
	public void VerifyNumOfContracts() throws SQLException {
		Integer numOfContractsFYyear = OGENYCDatabaseUtil.getREContractsCount(year,'B');	
		Integer numOfContracts = ContractsPage.GetTop5WidgetTotalCount(WidgetOption.Top5Contracts);
		assertEquals("Number of Contracts in the Registered Expense Contracts did not match", numOfContracts, numOfContractsFYyear);
	}
	@Test
	public void VerifyNumOfContractsAmountModificationsContracts() throws SQLException {
		Integer numOfContractsAmountModificationsContractsFYyear = OGENYCDatabaseUtil.getREContractsModificationsCount(year,'B');	
		Integer numOfContractsAmountModificationsContracts = ContractsPage.GetTop5WidgetTotalCount(WidgetOption.TopContractAmountModifications);
		assertEquals("Number of Contracts Amount Modifications Contracts in the Registered Expense Contracts did not match", numOfContractsAmountModificationsContracts, numOfContractsAmountModificationsContractsFYyear);
	}
	@Test
	public void VerifyNumOfPrimeVendorsContracts() throws SQLException {
		Integer numOfPrimeVendorsContractsFYyear = OGENYCDatabaseUtil.getREContractsPrimeVendorsCount(year,'B');	
		Integer numOfPrimeVendorsContracts = ContractsPage.GetTop5WidgetTotalCount(WidgetOption.Top5PrimeVendors);
		assertEquals("Number of Prime Vendors Contracts By Industries in the Registered Expense Contracts did not match", numOfPrimeVendorsContracts, numOfPrimeVendorsContractsFYyear);
	}
	@Test
	public void VerifyNumOfAwardMethodsContracts() throws SQLException {
		Integer numOfAwardMethodsContractsFYyear = OGENYCDatabaseUtil.getREContractsAwardMethodsCount(year,'B');	
		Integer numOfAwardMethodsContracts = ContractsPage.GetTop5WidgetTotalCount(WidgetOption.TopAwardMethods);
		assertEquals("Number of Award Methods Contracts in the Registered Expense Contracts did not match", numOfAwardMethodsContracts, numOfAwardMethodsContractsFYyear);
	}
	@Test
	public void VerifyNumOfDepartmentsContracts() throws SQLException {
		Integer numOfAgenciesContractsFYyear = OGENYCDatabaseUtil.getREContractsDepartmentsCount(year,'B');	
		Integer numOfAgenciesContracts = ContractsPage.GetTop5WidgetTotalCount(WidgetOption.TopDepartments);
		assertEquals("Number of Agencies Contracts in the Registered Expense Contracts did not match", numOfAgenciesContracts, numOfAgenciesContractsFYyear);
	}
	@Test
	public void VerifyNumOfContractsByIndustries() throws SQLException {
		Integer numOfContractsByIndustriesFYyear = OGENYCDatabaseUtil.getREContractsIndustriesCount(year,'B');	
		Integer numOfContractsByIndustries = ContractsPage.GetTop5WidgetTotalCount(WidgetOption.ContractsByIndustries);
		assertEquals("Number of Contracts By Industries in the Registered Expense Contracts did not match", numOfContractsByIndustries, numOfContractsByIndustriesFYyear);
	}
	@Test
	public void VerifyNumOfContractsBySize() throws SQLException {
		Integer numOfContractsBySizeFYyear = OGENYCDatabaseUtil.getREContractsSizeCount(year,'B');	
		Integer numOfContractsBySize = ContractsPage.GetTop5WidgetTotalCount(WidgetOption.ContractsBySize);
		assertEquals("Number of Contracts By Size in the Registered Expense Contracts did not match", numOfContractsBySize, numOfContractsBySizeFYyear);
	}

	
	/* ***************** amounts and titles ****************** */
	
	
/////// amounts and titles
	
	@Test
  public void VerifyTopNavContractAmount() throws SQLException {
      String TotalContractAmtDB = OGENYCDatabaseUtil.getContractsTopAmount(year, 'B');
      String TotalContractAmtApp = ContractsPage.GetContractsAmount();
      System.out.println(TotalContractAmtApp); 
      assertEquals("Registered Expense Contracts Top navigation amount did not match", TotalContractAmtApp, TotalContractAmtDB);
  }
	
	@Test
  public void VerifyBottomNavRegisteredExpenseAmount() throws SQLException {
      String TotalContractAmtDB = OGENYCDatabaseUtil.getREContractsAmount(year, 'B');
      String TotalContractAmtApp = ContractsPage.GetBottomNavContractAmount();
  	System.out.println(TotalContractAmtApp); 
  	 assertEquals("Registered Expense Contracts Bottom navigation Amount did not match", TotalContractAmtApp, TotalContractAmtDB);
      
   
  }
	
	@Test
  public void VerifyBottomNavRegisteredExpenseCount() throws SQLException {
		Integer TotalContractCountDB = OGENYCDatabaseUtil.getContractsRECount(year, 'B');
		Integer TotalContractCountApp = ContractsPage.GetBottomNavContractCount();
  	System.out.println(TotalContractCountApp); 
  	 assertEquals("Registered Expense Contracts Bottom navigation count did not match", TotalContractCountApp, TotalContractCountDB);
	}
	
	@Test
  public void VerifyRegisteredExpenseContractsVisualizationsTitles(){
	    String[] sliderTitles= {"Spending by Registered Expense Contracts", 
	    						"Top Ten Registered Expense Contracts by Current Amount", 
	    						"Top Ten Prime Vendors by Registered Expense Contracts"};
	    System.out.println( ContractsPage.VisualizationTitles()); 
  	assertTrue(Arrays.equals(sliderTitles, ContractsPage.VisualizationTitles().toArray()));
  	System.out.println( ContractsPage.VisualizationTitles()); 
  }
	 
	@Test
  public void VerifyRegisteredExpenseContractsWidgetTitles(){
	   String[] widgetTitles = {"Top Master Agreements",
	    						"Top Master Agreement Modifications",
	    						"Top 5 Contracts",
	    						"Top Contract Amount Modifications",
	    						"Top 5 Prime Vendors",
	    						"Top Award Methods",
	    						"Top Departments",
	    						"Contracts by Industries",
	    						"Contracts by Size"
	    						};	    						
	    							    						 
		   System.out.println( ContractsPage.WidgetTitles()); 		
  
  	assertTrue(Arrays.equals(widgetTitles, ContractsPage.WidgetTitles().toArray()));
  	
   }  
	
}
