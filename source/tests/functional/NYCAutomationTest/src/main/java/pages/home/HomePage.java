package pages.home;

import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.ui.ExpectedConditions;
import org.openqa.selenium.support.ui.WebDriverWait;

import utility.Driver;
import utility.Helper;

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
			String headerText = header.getText();
			String subTitle = header.getText().substring(0, header.getText().indexOf("Number")-1);
			if(subTitle.equalsIgnoreCase(TableTitle)){
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
	
	/*public static String GetWidgetTotalNumber(String WidgetTitle) {
		List<WebElement> panelContainers = Driver.Instance.findElements(By.cssSelector(".bottomContainer > .panel-display > .panel-panel > .inside > .panel-pane"));
		for (WebElement panelContainer : panelContainers) {
			WebElement header= panelContainer.findElement(By.tagName("h2"));
	//		String headerText = header.getText();
			String subTitle = header.getText().substring(0, header.getText().indexOf("Number")-1);
			if(subTitle.equalsIgnoreCase(WidgetTitle)){
				WebElement countContainer = panelContainer.findElement(By.className("contentCount"));
				String numAgencyText = countContainer.getText();
		        String numAgency = numAgencyText.substring(numAgencyText.indexOf(":") + 1).trim();
		        return numAgency;
			}
		}
		return null;
		}*/
	
	public static String GetWidgetTotalNumber(String WidgetTitle) {
		List<WebElement> panelContainers = Driver.Instance.findElements(By.cssSelector(".bottomContainer > .panel-display > .panel-panel .panel-pane"));
		for (WebElement panelContainer : panelContainers) {
			WebElement header= panelContainer.findElement(By.tagName("h2"));
	//		String headerText = header.getText();
			String subTitle = header.getText().substring(0, header.getText().indexOf("Number")-1);
			if(subTitle.equalsIgnoreCase(WidgetTitle)){
				WebElement countContainer = panelContainer.findElement(By.className("contentCount"));
				String numAgencyText = countContainer.getText();
		        String numAgency = numAgencyText.substring(numAgencyText.indexOf(":") + 1).trim();
		        return numAgency;
			}
		}
		return null;
	}
	
	
	
	
	
	
	public static WebElement GetWidgetDetailsContainer(String WidgetTitle) {
		List<WebElement> panelContainers = Driver.Instance.findElements(By.cssSelector(".bottomContainer > .panel-display > .panel-panel > .inside > .panel-pane"));
		for (WebElement panelContainer : panelContainers) {
			WebElement header= panelContainer.findElement(By.tagName("h2"));
//			String headerText = header.getText();
			String subTitle = header.getText().substring(0, header.getText().indexOf("Number")-1);
			if(subTitle.equalsIgnoreCase(WidgetTitle)){
		        return panelContainer;
			}
		}
		return null;
	}
	
	public static boolean IsAtTop5DetailsPage(String WidgetTitle) {
		if(Driver.Instance.findElements(By.className("contract-title")).size() > 0){
			return ((Driver.Instance.findElement(By.className("contract-title"))).getText()).equalsIgnoreCase(WidgetTitle);
		}else
			return false;
	}
	
	public static Float GetTransactionAmount() {
		WebDriverWait wait = new WebDriverWait(Driver.Instance, 10);
		wait.until(ExpectedConditions.visibilityOfElementLocated(By.className("transactions-total-amount")));
		String count = (Driver.Instance.findElement(By.className("transactions-total-amount"))).getText();	
		System.out.println(Helper.billionStringToFloat(count));
		return Helper.billionStringToFloat(count);
	}
    
}
