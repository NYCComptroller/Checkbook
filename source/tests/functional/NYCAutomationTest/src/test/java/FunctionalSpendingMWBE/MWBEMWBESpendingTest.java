package FunctionalSpendingMWBE;

import static org.junit.Assert.assertEquals;
import static org.junit.Assert.assertTrue;
import helpers.Helper;

import java.util.Arrays;

import org.junit.Before;
import org.junit.Test;

import navigation.MWBECategory;
import navigation.MWBECategory.MWBECategoryOption;
import pages.home.HomePage;
import pages.mwbe.MWBEPage;
import utilities.NYCBaseTest;
import utilities.TestStatusReport;

//public class MWBESpendingTest extends TestStatusReport{
	public class MWBEMWBESpendingTest extends NYCBaseTest{
	@Before
	public void GoToPage(){
		if(!MWBEPage.IsAt()){
			MWBEPage.GoTo("Spending", MWBECategoryOption.MWBEHome);		
		}
		if(!(Helper.getCurrentSelectedYear()).equalsIgnoreCase(NYCBaseTest.prop.getProperty("CurrentYear")))
			   HomePage.SelectYear(NYCBaseTest.prop.getProperty("CurrentYear"));
	}
	
	@Test
	public void VerifyTotalMWBEAmountForAsianAmerican(){
		String TotalMWBEAmtFY = "$481.1M";
		MWBECategory.select(MWBECategoryOption.AsianAmerican);
		String totalMWBEAmt = MWBEPage.GetMWBEAmount();
		assertEquals("MWBE Amount did not match",TotalMWBEAmtFY, totalMWBEAmt);
	}
	
	@Test
    public void VerifyMWBEVisualizationsTitlesForAsianAmerican(){
		MWBECategory.select(MWBECategoryOption.AsianAmerican);
	    String[] sliderTitles= {"Asian American Prime Spending", 
	    						"Asian American Total Prime Spending Share", 
	    						"Asian American Top Ten Agencies Spending", 
	    						"Asian American Top Ten Prime Vendors Spending",
	    						"Asian American Top Ten Contracts Spending",
	    						"Asian American Top Ten Sub Vendors Spending"};  
    	assertTrue(Arrays.equals(sliderTitles, MWBEPage.VisualizationTitles().toArray()));
    }

}
