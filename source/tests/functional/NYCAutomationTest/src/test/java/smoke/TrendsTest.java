package smoke;

import static org.junit.Assert.assertTrue;

import org.junit.Test;

import navigation.PrimaryMenuNavigation;
import pages.tools.trends.AllTrendsPage;
import utility.Helper;
import utility.TestStatusReport;

public class TrendsTest extends TestStatusReport{
	
	 @Test
    public void goToChangesInNetAssetsThroughAllTrendsPage() {
		 if (!PrimaryMenuNavigation.isAt("Changes in Net Assets"))
			   AllTrendsPage.GoTo(AllTrendsPage.allTrendsOptions.changesInNetAssets);
        assertTrue(PrimaryMenuNavigation.isAt("Changes in Net Assets"));
    }

    @Test
    public void verifyChangesInNetAssets2015() {
    	 if (!PrimaryMenuNavigation.isAt("Changes in Net Assets"))
			   AllTrendsPage.GoTo(AllTrendsPage.allTrendsOptions.changesInNetAssets);
        assertTrue(Helper.stringToInt(AllTrendsPage.changesInNetAssets2015()) >= 5479762);
    }
    
   // @Test
   // public void verifyFeaturedTrendsHover() {
    //    assertEquals(FeaturedTrendsPage.featuredTrends2015orange(), "$78.04B");
  //  } 
	
}
