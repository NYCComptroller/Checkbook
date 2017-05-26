package functional;

import static org.junit.Assert.assertEquals;

import java.sql.SQLException;

import org.junit.Before;
import org.junit.Test;

import navigation.TopNavigation.Contracts.RegisteredExpenseContracts;
import pages.contracts.ContractsPage;
import pages.contracts.RegisteredExpenseContractsPage;
import pages.contracts.ContractsPage.WidgetOption;
import pages.home.HomePage;
import utilities.NYCBaseTest;
import utilities.NYCDatabaseUtil;
import utility.Helper;

public class RegisteredExpenseContractsTest extends NYCBaseTest{
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
	public void VerifyNumOfMasterAgreementsContracts()throws SQLException{
	 	int numOfMasterAgreementContractsFY2016 = NYCDatabaseUtil.getREContractsMasterCount(2016,'B');	 	
        int numOfMasterAgreementContracts = Helper.stringToInt(ContractsPage.GetTop5WidgetTotalCount(WidgetOption.Top5MasterAgreements));
        assertEquals("Number of Master Agreement Contracts in the Registered Expense Contracts did not match", numOfMasterAgreementContracts, numOfMasterAgreementContractsFY2016);
	}
	
	@Test
	public void VerifyNumOfMasterAgreementModificationsContracts()throws SQLException{
	 	int numOfMasterAgreementModificationsContractsFY2016 = NYCDatabaseUtil.getREContractsMasterModificationsCount(2016,'B');	
        int numOfMasterAgreementModificationsContracts = Helper.stringToInt(ContractsPage.GetTop5WidgetTotalCount(WidgetOption.Top5MasterAgreementModifications));
        assertEquals("Number of Contract Amount Modifications Contracts in the Registered Expense Contracts did not match", numOfMasterAgreementModificationsContracts, numOfMasterAgreementModificationsContractsFY2016);
	}
	
	@Test
	public void VerifyNumOfContracts()throws SQLException{
	 	int numOfContractsFY2016 = NYCDatabaseUtil.getREContractsCount(2016,'B');	
        int numOfContracts = Helper.stringToInt(ContractsPage.GetTop5WidgetTotalCount(WidgetOption.Top5Contracts));
        assertEquals("Number of Contracts in the Registered Expense Contracts did not match", numOfContracts, numOfContractsFY2016);
	}
	
	@Test
	public void VerifyNumOfContractsAmountModificationsContracts()throws SQLException{
	 	int numOfContractsAmountModificationsContractsFY2016 = NYCDatabaseUtil.getREContractsModificationsCount(2016,'B');	
        int numOfContractsAmountModificationsContracts = Helper.stringToInt(ContractsPage.GetTop5WidgetTotalCount(WidgetOption.Top5ContractAmountModifications));
        assertEquals("Number of Contracts Amount Modifications Contracts in the Registered Expense Contracts did not match", numOfContractsAmountModificationsContracts, numOfContractsAmountModificationsContractsFY2016);
	}
		
	@Test
	public void VerifyNumOfPrimeVendorsContracts()throws SQLException{
	 	int numOfPrimeVendorsContractsFY2016 = NYCDatabaseUtil.getREContractsPrimeVendorsCount(2016,'B');	
        int numOfPrimeVendorsContracts = Helper.stringToInt(ContractsPage.GetTop5WidgetTotalCount(WidgetOption.Top5PrimeVendors));
        assertEquals("Number of Prime Vendors Contracts By Industries in the Registered Expense Contracts did not match", numOfPrimeVendorsContracts, numOfPrimeVendorsContractsFY2016);
	}
	
	@Test
	public void VerifyNumOfAwardMethodsContracts()throws SQLException{
	 	int numOfAwardMethodsContractsFY2016 = NYCDatabaseUtil.getREContractsAwardMethodsCount(2016,'B');	
        int numOfAwardMethodsContracts = Helper.stringToInt(ContractsPage.GetTop5WidgetTotalCount(WidgetOption.Top5AwardMethods));
        assertEquals("Number of Award Methods Contracts in the Registered Expense Contracts did not match", numOfAwardMethodsContracts, numOfAwardMethodsContractsFY2016);
	}
	
	@Test
	public void VerifyNumOfAgenciesContracts()throws SQLException{
	 	int numOfAgenciesContractsFY2016 = NYCDatabaseUtil.getREContractsAgenciesCount(2016,'B');	
        int numOfAgenciesContracts = Helper.stringToInt(ContractsPage.GetTop5WidgetTotalCount(WidgetOption.Top5Agencies));
        assertEquals("Number of Agencies Contracts in the Registered Expense Contracts did not match", numOfAgenciesContracts, numOfAgenciesContractsFY2016);
	}
	
	@Test
	public void VerifyNumOfContractsByIndustries()throws SQLException{
	 	int numOfContractsByIndustriesFY2016 = NYCDatabaseUtil.getREContractsIndustriesCount(2016,'B');	
        int numOfContractsByIndustries = Helper.stringToInt(ContractsPage.GetTop5WidgetTotalCount(WidgetOption.ContractsByIndustries));
        assertEquals("Number of Contracts By Industries in the Registered Expense Contracts did not match", numOfContractsByIndustries, numOfContractsByIndustriesFY2016);
	}
	
	@Test
	public void VerifyNumOfContractsBySize()throws SQLException{
	 	int numOfContractsBySizeFY2016 = NYCDatabaseUtil.getREContractsSizeCount(2016,'B');	
        int numOfContractsBySize = Helper.stringToInt(ContractsPage.GetTop5WidgetTotalCount(WidgetOption.ContractsBySize));
        assertEquals("Number of Contracts By Size in the Registered Expense Contracts did not match", numOfContractsBySize, numOfContractsBySizeFY2016);
	}
	
}
