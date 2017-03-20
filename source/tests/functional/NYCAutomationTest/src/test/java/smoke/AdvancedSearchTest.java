package smoke;

import static org.junit.Assert.assertEquals;
import static org.junit.Assert.assertTrue;

import org.junit.Test;


import pages.search.SearchPage;
import utilities.NYCBaseTest;
import utilities.NYCDatabaseUtil;
import utility.Helper;
import utility.TestStatusReport;

public class AdvancedSearchTest extends TestStatusReport{
	
//public class AdvancedSearchTest extends NYCBaseTest{
	@Test
    public void verifyActiveExpenseContractsTransactionsCount() {
        SearchPage.AdvancedSearch.GoTo();
      // assertTrue(SearchPage.AdvancedSearch.activeExpenseContractsTransactionsCount() > 33000);
        int NumOfActiveExpenseCount2016 =  33001;
       int numOfActivExpADVapp = SearchPage.AdvancedSearch.activeExpenseContractsTransactionsCount();
       assertEquals("Number of Advanced searchactive expense Contracts  did not match", numOfActivExpADVapp, NumOfActiveExpenseCount2016);
	}
	
}
