package pages.mwbe;

import java.util.ArrayList;

import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;

import navigation.MWBECategory.MWBECategoryOption;
import navigation.MWBECategory;
import navigation.TopNavigation;
import pages.home.HomePage;
import helpers.Driver;

public class MWBEPage {

	public static boolean IsAt() {
		WebElement spendingCont = Driver.Instance.findElement(By.cssSelector(".top-navigation-left > table > tbody > tr .spending"));
    	Boolean spendingSelected = (spendingCont.getAttribute("class")).contains("active");	
    	WebElement contractsCont = Driver.Instance.findElement(By.cssSelector(".top-navigation-left > table > tbody > tr .contracts"));
    	Boolean contractsSelected = (contractsCont.getAttribute("class")).contains("active");	
        WebElement mwbeCont = Driver.Instance.findElement(By.cssSelector(".top-navigation-right .mwbe"));
        Boolean mwbeSelected = (mwbeCont.getAttribute("class")).contains("active");	   
        return (spendingSelected || contractsSelected) && mwbeSelected;
	}

	public static void GoTo(String domain, MWBECategoryOption category) {
		if(domain.equals("Spending"))
			TopNavigation.Spending.Select();
		MWBECategory.select(category);		
	}

	public static String GetMWBEAmount() {
		WebElement mwbeAmt = Driver.Instance.findElement(By.cssSelector(".top-navigation-right .mwbe .top-navigation-amount"));
		return mwbeAmt.getText().substring((mwbeAmt.getText().indexOf("$")));
	}

	public static ArrayList<String> VisualizationTitles() {
		
		return HomePage.VisualizationTitles();
	}

}
