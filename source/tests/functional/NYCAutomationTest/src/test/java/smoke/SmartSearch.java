package smoke;

import static org.junit.Assert.assertTrue;

import java.io.IOException;
import java.sql.SQLException;

import org.junit.Before;
import org.junit.Test;

import pages.search.SearchPage;
import utilities.NYCBaseTest;
import utility.TestStatusReport;

public class SmartSearch extends TestStatusReport{
	@Before 
    public void GoToPage(){
	   if (!SearchPage.isAt())
		   SearchPage.GoToSmartSearch();
    }
	@Test
    public void canGoToSmartSearch() {
        assertTrue(SearchPage.isAt());
    }

    @Test
    public void verifySearchEntriesTotalGreaterThan80M() { 
        assertTrue(SearchPage.getTotalSearchEntries() > 80000000);
    }

    @Test
    public void verifyTypeOfDataTotals() {
        SearchPage.openTypeOfData();

        assertTrue(SearchPage.intArrayElementsAllGreaterThan(SearchPage.typeOfDataTotals(), 10000));
    }
      
}
