package pages.spending;

import navigation.TopNavigation;

import java.util.ArrayList;
import java.util.List;
import java.util.concurrent.TimeUnit;

import org.omg.CORBA.TIMEOUT;
import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;
import utility.Driver;
import utility.Helper;

public class SpendingPage {
	public static void GoTo() {
        TopNavigation.Spending.Select();
    }

    public static String GetSpendingAmount() {
        WebElement spendingAmt = Driver.Instance.findElement(By.cssSelector(".top-navigation-left .spending > .expense-container > a"));
        return spendingAmt.getText().substring((spendingAmt.getText().indexOf("$")));
    }

    public static boolean isAt() {
    	WebElement topTitleCont = Driver.Instance.findElement(By.cssSelector(".top-navigation-left > table > tbody > tr .spending"));
    	Boolean spendingSelected = (topTitleCont.getAttribute("class")).contains("active");	
        //WebElement h2title = Driver.Instance.findElement(By.xpath("//*[@id=\"node-widget-21\"]/div[1]/h2"));
        //Boolean totalSpendingSelected = h2title.getText().equals("Total Spending");    
        return spendingSelected;
    }

	public static ArrayList<String> VisualizationTitles() {
		ArrayList<String> titles = new ArrayList<String>();
		List<WebElement> titleContainers = Driver.Instance.findElements(By.cssSelector("#nyc-spending > .top-chart > .inside > .panel-pane"));
		for(int i=0; i < titleContainers.size(); i++){
			selectVisualizationSlider(i);
			WebElement titleClass = titleContainers.get(i).findElement(By.cssSelector(".pane-content > .node-widget > .content > .chart-title"));
			if(titleClass.isDisplayed()){
				String title = titleClass.getText();
				titles.add(title);
			}
		}	
		return titles;
	}
	
	public static void selectVisualizationSlider(int sliderPosition){
		List<WebElement> sliderContainer = Driver.Instance.findElements(By.cssSelector("#nyc-spending > .top-chart > .slider-pager > a"));
		sliderContainer.get(sliderPosition).click();
		Driver.Instance.manage().timeouts().implicitlyWait(2, TimeUnit.SECONDS);
	}

	public static ArrayList<String> WidgetTitles() {
		ArrayList<String> titles = new ArrayList<String>();
		List<WebElement> titleContainers = Driver.Instance.findElements(By.className("tableHeader"));
		for (WebElement titleContainer : titleContainers) {
			WebElement titleHeaderContainer = titleContainer.findElement(By.cssSelector("h2"));
			titles.add(titleHeaderContainer.getText());
		}	
		return titles;
	}
	
	public static void ShowWidgetDetails(){
		WebElement detailsLinkContainer = Driver.Instance.findElement(By.className("bottomContainerToggle"));
		if(detailsLinkContainer.getText().contains("Show Details")){
			detailsLinkContainer.click();
		}
		Driver.Instance.manage().timeouts().implicitlyWait(2, TimeUnit.SECONDS);
	}

}
