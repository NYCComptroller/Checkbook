package pages.home;

import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;

import utility.Driver;

import java.util.ArrayList;
import java.util.List;
import java.util.concurrent.TimeUnit;

public class HomePage {

    public static void GoTo(String url) {
        Driver.GoTo(url);
    }

    public static void SelectYear(String year) {
        String yearRequired = YearType.getCurrentYear(year);
        WebElement yearSelected = Driver.Instance.findElement(By.cssSelector("#year_list_chzn > .chzn-single > span"));
        if (!(yearSelected.getText()).equals(yearRequired)) {
            WebElement dropdownContainer = Driver.Instance.findElement(By.cssSelector("#year_list_chzn > .chzn-single"));
            dropdownContainer.click();
            WebElement dropdown = Driver.Instance.findElement(By.cssSelector("#year_list_chzn > .chzn-drop > .chzn-results"));
            List<WebElement> options = dropdown.findElements(By.tagName("li"));
            WebElement selectedYear = null;
            for (WebElement option : options) {
                String optionYear = option.getText();
                if (optionYear.equals(yearRequired)) {
                    selectedYear = options.get(options.indexOf(option));
                    break;
                }
            }
            selectedYear.click();
        }
    }

    public static void createAlert() {
        Driver.Instance.findElement(
                By.xpath("//*[@id=\"block-block-7\"]/div/div/span[contains(text(),'Create Alert')]"))
                .click();
    }
    
    public static boolean IsAlertCreated(){
    	return Driver.Instance.findElements(By.xpath(
                "//*[@id=\"ui-dialog-title-block-checkbook-advanced-search-checkbook-advanced-search-form\"" +
                "]/span/span[1][contains(text(),'1. Select Criteria')]")).size() > 0;
    }
    
    public static boolean IsAtCheckbookNYC(){
    	return Driver.Instance.getCurrentUrl().contains("checkbooknyc");
    }
    
    public static ArrayList<String> VisualizationTitles() {
		ArrayList<String> titles = new ArrayList<String>();
		List<WebElement> titleContainers = Driver.Instance.findElements(By.cssSelector("#nyc-spending > .top-chart > .inside > .panel-pane"));
		for(int i=0; i < titleContainers.size(); i++){
			selectVisualizationSlider(i);
			WebElement titleClass = titleContainers.get(i).findElement(By.cssSelector(".pane-content .chart-title"));
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
	
	public static void ShowWidgetDetails(){
		WebElement detailsLinkContainer = Driver.Instance.findElement(By.className("bottomContainerToggle"));
		if(detailsLinkContainer.getText().contains("Show Details")){
			detailsLinkContainer.click();
		}
		Driver.Instance.manage().timeouts().implicitlyWait(2, TimeUnit.SECONDS);
	}

	public static Boolean IsTableNotEmpty(String TableTitle) {
		List<WebElement> panelContainers = Driver.Instance.findElements(By.cssSelector(".bottomContainer > .panel-display > .panel-panel > .inside > .panel-pane"));
		for (WebElement panelContainer : panelContainers) {
			WebElement header= panelContainer.findElement(By.tagName("h2"));
			String tex = header.getText();
			if(header.getText().equalsIgnoreCase(TableTitle)){
				List<WebElement> emptyContainer = panelContainer.findElements(By.id("no-records-datatable"));
				if(emptyContainer.size() > 0)
					return false;
				else
					return true;
			}
			else 
				return null;
		}
		return null;
	}
    
}
