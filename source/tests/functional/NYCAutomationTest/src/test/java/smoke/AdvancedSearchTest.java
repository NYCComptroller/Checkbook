package smoke;

import static org.junit.Assert.assertTrue;

import org.junit.Test;

import pages.search.SearchPage;
import utility.TestStatusReport;

public class AdvancedSearchTest extends TestStatusReport{
	@Test
    public void verifyActiveExpenseContractsTransactionsCount() {
        SearchPage.AdvancedSearch.GoTo();
        assertTrue(SearchPage.AdvancedSearch.activeExpenseContractsTransactionsCount() > 33000);
    }
}
