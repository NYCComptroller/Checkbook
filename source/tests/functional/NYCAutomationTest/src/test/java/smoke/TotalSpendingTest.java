package smoke;

import static org.junit.Assert.assertEquals;
import static org.junit.Assert.assertTrue;

import java.io.IOException;
import java.sql.SQLException;

import org.junit.Before;
import org.junit.Test;

import pages.home.HomePage;
import pages.spending.SpendingPage;
import pages.spending.TotalSpendingPage;
import utilities.NYCBaseTest;
import utility.Helper;
import utility.TestStatusReport;

public class TotalSpendingTest extends NYCBaseTest{
	@Before
    public void GoToPage(){
	   if (!TotalSpendingPage.isAt())
		   TotalSpendingPage.GoTo();
	   if(!(Helper.getCurrentSelectedYear()).equalsIgnoreCase(NYCBaseTest.prop.getProperty("CurrentYear")))
		   HomePage.SelectYear(NYCBaseTest.prop.getProperty("CurrentYear"));
    }
	@Test
    public void VerifySpendingTop5ChecksTitle(){
       String totalSpendingTitle = "Checks Total Spending Transactions";
	   HomePage.ShowWidgetDetails();
	   TotalSpendingPage.GoToTop5ChecksDetailsPage();
	   assertEquals("Payroll Spending Title did not match", totalSpendingTitle, TotalSpendingPage.GetChecksDetailsPageTitle());
    }
	
	@Test
    public void VerifyTotalSpendingTop5ChecksTotalSpendingAmount(){
       Number spendingAmount = 94.93;
	   HomePage.ShowWidgetDetails();
	   TotalSpendingPage.GoToTop5ChecksDetailsPage();
	   assertEquals("Total Spending Top 5 Checks Total Spending Amount did not match.", TotalSpendingPage.GetTotalSpendingAmount(), spendingAmount);  
    }
	
	@Test
    public void VerifyTotalSpendingTop5ChecksTransactionCount(){
	   HomePage.ShowWidgetDetails();
	   TotalSpendingPage.GoToTop5ChecksDetailsPage();
	   assertTrue(TotalSpendingPage.GetChecksTransactionCount() > 900000); 
    }
	
	@Test
	public void VerifyTotalSpendingTop5ChecksTableRowsNotEmpty(){
		HomePage.ShowWidgetDetails();
		assertEquals(true, HomePage.IsTableNotEmpty("Top 5 Checks"));
	}
	
	/*@Test
	public void VerifySpendingTransactionsExport(){
		HomePage.ShowWidgetDetails();
		TotalSpendingPage.GoToTop5ChecksDetailsPage();
		TotalSpendingPage.ExportAllTransactions();	
	}*/
}
