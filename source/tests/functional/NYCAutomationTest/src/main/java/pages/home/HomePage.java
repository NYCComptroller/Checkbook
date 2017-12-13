package pages.home;

import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.ui.ExpectedConditions;
import org.openqa.selenium.support.ui.WebDriverWait;

import helpers.Driver;
import helpers.Helper;

import java.util.ArrayList;
import java.util.List;
import java.util.concurrent.TimeUnit;

public class HomePage {

    public static void GoTo(String url) {
        Driver.GoTo(url);
    }

  /*  public static void SelectYear(String year) {
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
*/
    
    public static void SelectYear(String year) {
        String yearRequired = YearType.getCurrentYear(year);
        WebElement yearSelected = Driver.Instance.findElement(By.cssSelector("#year_list_chosen > .chosen-single > span"));
        if (!(yearSelected.getText()).equals(yearRequired)) {
            WebElement dropdownContainer = Driver.Instance.findElement(By.cssSelector("#year_list_chosen > .chosen-single"));
            dropdownContainer.click();
            WebElement dropdown = Driver.Instance.findElement(By.cssSelector("#year_list_chosen > .chosen-drop > .chosen-results"));
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
		Driver.Instance.manage().timeouts().implicitlyWait(10, TimeUnit.SECONDS);
	}

	public static Boolean IsTableNotEmpty(String TableTitle) {
		List<WebElement> panelContainers = Driver.Instance.findElements(By.cssSelector(".bottomContainer > .panel-display > .panel-panel > .inside > .panel-pane"));
		for (WebElement panelContainer : panelContainers) {
			WebElement header= panelContainer.findElement(By.tagName("h2"));
		//	String headerText = header.getText();
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
	
	public static Integer GetWidgetTotalNumber(String WidgetTitle) {
		String strTotalNumber = GetWidgetTotalNumberText(WidgetTitle);
		return Helper.stringToInt(strTotalNumber);
	}
	
	public static String GetWidgetTotalNumberText(String WidgetTitle) {
	List<WebElement> panelContainers = Driver.Instance.findElements(By.cssSelector(".bottomContainer > .panel-display > .panel-panel > .inside > .panel-pane"));
//		List<WebElement> panelContainers = Driver.Instance.findElements(By.cssSelector(".bottomContainer > .panel-display > .panel-panel > div > .panel-pane"));
		for (WebElement panelContainer : panelContainers) {
			WebElement header= panelContainer.findElement(By.tagName("h2"));
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
	
	
	public static Integer GetWidgetTotalNumber1(String WidgetTitle) {
		String strTotalNumber = GetWidgetTotalNumberText1(WidgetTitle);
		return Helper.stringToInt(strTotalNumber);
	}
		
		public static String GetWidgetTotalNumberText1(String WidgetTitle) {
			//List<WebElement> panelContainers = Driver.Instance.findElements(By.cssSelector(".bottomContainer > .panel-display > .panel-panel > .inside > .panel-pane"));
			List<WebElement> panelContainers = Driver.Instance.findElements(By.cssSelector(".bottomContainer > .panel-display > .panel-panel > div > .panel-pane"));
				for (WebElement panelContainer : panelContainers) {
					WebElement header= panelContainer.findElement(By.tagName("h2"));
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
			
	
	/*public static String GetWidgetTotalNumberText2(String WidgetTitle) {
		List<WebElement> panelContainers = Driver.Instance.findElements(By.cssSelector(".bottomContainer > .panel-display > .panel-panel > div > .panel-pane"));
		for (WebElement panelContainer : panelContainers) {
			WebElement header= panelContainer.findElement(By.tagName("h2"));
			String subTitle = header.getText().substring(0, header.getText().indexOf("Number")-1);
			if(subTitle.equalsIgnoreCase(WidgetTitle)) {
				WebElement countContainer = panelContainer.findElement(By.className("contentCount"));
				String numTotalText = countContainer.getText();
		        numTotalText = numTotalText.substring(numTotalText.indexOf(":") + 1).trim();
		        return numTotalText;
			}
		}
      return "150";	
      }
*/
	
	public static WebElement GetWidgetDetailsContainer(String WidgetTitle) {
		List<WebElement> panelContainers = Driver.Instance.findElements(By.cssSelector(".bottomContainer > .panel-display > .panel-panel > .inside > .panel-pane"));
		for (WebElement panelContainer : panelContainers) {
			WebElement header= panelContainer.findElement(By.tagName("h2"));
          //	String headerText = header.getText();
			String subTitle = header.getText().substring(0, header.getText().indexOf("Number")-1);
			if(subTitle.equalsIgnoreCase(WidgetTitle)){
		        return panelContainer;
			}
		}
		return null;
	}
	
	public static WebElement GetWidgetDetailsContainer1(String WidgetTitle) {
		List<WebElement> panelContainers = Driver.Instance.findElements(By.cssSelector(".bottomContainer > .panel-display > .panel-panel > div > .panel-pane"));
		for (WebElement panelContainer : panelContainers) {
			WebElement header= panelContainer.findElement(By.tagName("h2"));
          //	String headerText = header.getText();
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
	
	public static String DetailsPagetitle() {
		   WebDriverWait wait = new WebDriverWait(Driver.Instance, 30);
			wait.until(ExpectedConditions.visibilityOfElementLocated(By.className("contract-title")));
			String title = (Driver.Instance.findElement(By.className("contract-title"))).getText();	
			System.out.println(title);
			return title;
	}
	
	public static Float GetTransactionAmount() {
		WebDriverWait wait = new WebDriverWait(Driver.Instance, 30);
		wait.until(ExpectedConditions.visibilityOfElementLocated(By.className("transactions-total-amount")));
		String count = (Driver.Instance.findElement(By.className("transactions-total-amount"))).getText();	
		System.out.println(Helper.billionStringToFloat(count));
		return Helper.billionStringToFloat(count);
	}
	  public static String GetSpendingAmount() {
          WebElement spendingAmt = Driver.Instance.findElement(By.cssSelector(".top-navigation-left .spending > .expense-container > a"));
          return spendingAmt.getText().substring((spendingAmt.getText().indexOf("$")));
      }
	public static String GetTransactionAmount1() {
		WebDriverWait wait = new WebDriverWait(Driver.Instance, 30);
		wait.until(ExpectedConditions.visibilityOfElementLocated(By.className("total-spending-amount")));
		String amount = (Driver.Instance.findElement(By.className("total-spending-amount"))).getText();	
		System.out.println(amount);
		//return amount.substring(0,8).replaceAll("\\s", "");
		return  amount.substring(0,7).replaceAll("\\s", "");
		//return Helper.billionStringToFloat(count);
	}
	public static String GetTransactionAmount2() {
		WebDriverWait wait = new WebDriverWait(Driver.Instance, 30);
		wait.until(ExpectedConditions.visibilityOfElementLocated(By.className("total-spending-amount")));
		String amount = (Driver.Instance.findElement(By.className("total-spending-amount"))).getText();	
		//System.out.println(Helper.billionStringToFloat(count));
		return amount.substring(0,8);
		//return Helper.billionStringToFloat(count);
	}
	public static String GetTransactionAmount3() {
		WebDriverWait wait = new WebDriverWait(Driver.Instance, 30);
		wait.until(ExpectedConditions.visibilityOfElementLocated(By.className("total-spending-amount")));
		String amount = (Driver.Instance.findElement(By.className("total-spending-amount"))).getText();	
		//System.out.println(Helper.billionStringToFloat(count));
		return amount.substring(0,6);
		//return Helper.billionStringToFloat(count);
	}
    
	
    public static ArrayList<String> BudgetVisualizationTitles() {
		ArrayList<String> titles = new ArrayList<String>();
		List<WebElement> titleContainers = Driver.Instance.findElements(By.cssSelector("#nyc-budget > .top-chart > .inside > .panel-pane"));
		for(int i=0; i < titleContainers.size(); i++){
			selectBudgetVisualizationSlider(i);
			WebElement titleClass = titleContainers.get(i).findElement(By.cssSelector(".pane-content .chart-title"));
			if(titleClass.isDisplayed()){
				String title = titleClass.getText();
				titles.add(title);
			}
		}	
		return titles;
	}
	
	public static void selectBudgetVisualizationSlider(int sliderPosition){
		List<WebElement> sliderContainer = Driver.Instance.findElements(By.cssSelector("#nyc-budget > .top-chart > .slider-pager > a"));
		sliderContainer.get(sliderPosition).click();
		Driver.Instance.manage().timeouts().implicitlyWait(2, TimeUnit.SECONDS);
	}
	
    public static ArrayList<String> RevenueVisualizationTitles() {
		ArrayList<String> titles = new ArrayList<String>();
		List<WebElement> titleContainers = Driver.Instance.findElements(By.cssSelector("#nyc-revenue > .top-chart > .inside > .panel-pane"));
		for(int i=0; i < titleContainers.size(); i++){
			selectRevenueVisualizationSlider(i);
			WebElement titleClass = titleContainers.get(i).findElement(By.cssSelector(".pane-content .chart-title"));
			if(titleClass.isDisplayed()){
				String title = titleClass.getText();
				titles.add(title);
			}
		}	
		return titles;
	}
	
	public static void selectRevenueVisualizationSlider(int sliderPosition){
		List<WebElement> sliderContainer = Driver.Instance.findElements(By.cssSelector("#nyc-revenue > .top-chart > .slider-pager > a"));
		sliderContainer.get(sliderPosition).click();
		Driver.Instance.manage().timeouts().implicitlyWait(2, TimeUnit.SECONDS);
	}
}
