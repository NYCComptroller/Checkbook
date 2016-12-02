package smoke;

import static org.junit.Assert.assertTrue;

import org.junit.Test;

import pages.search.SearchPage;

public class AdvancedSearchTest {
	@Test
    public void verifyActiveExpenseContractsTransactionsCount() {
        SearchPage.AdvancedSearch.GoTo();
        assertTrue(SearchPage.AdvancedSearch.activeExpenseContractsTransactionsCount() > 33000);
    }
}
