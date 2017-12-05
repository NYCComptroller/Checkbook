package pages.payroll;

import java.util.ArrayList;
import java.util.List;
import java.util.concurrent.TimeUnit;
import org.openqa.selenium.interactions.Actions;
import org.openqa.selenium.support.ui.ExpectedConditions;
import org.openqa.selenium.support.ui.WebDriverWait;

import pages.home.HomePage;

import org.openqa.selenium.By;
import org.openqa.selenium.JavascriptExecutor;
import org.openqa.selenium.WebElement;

import navigation.TopNavigation;
import helpers.Driver;
import helpers.Helper;

public class PayrollPage {

		public enum WidgetOption{
			
			 Top5AgenciesbyPayroll, TopAgenciesbyPayroll ,Top5AgenciesbyOvertime,
			 TopAgenciesbyOvertime,Top5AnnualSalaries,TopAnnualSalaries,
			 Top5TitlesbyNumberofEmployees,TopTitlesbyNumberofEmployees
			 }
				
			public static void GoTo() {
		        TopNavigation.Payroll.Select();
		    }
			   public static String GetPayrollAmount() {
		            WebElement PayrollAmt = Driver.Instance.findElement(By.cssSelector(".top-navigation-left .employees > .expense-container > a"));
		            return PayrollAmt.getText().substring((PayrollAmt.getText().indexOf("$")));
		        }
			
			public static boolean isAt() {
		    	WebElement topTitleCont = Driver.Instance.findElement(By.cssSelector(".top-navigation-left > table > tbody > tr .employees"));
		    	Boolean payrollSelected = (topTitleCont.getAttribute("class")).contains("active");	    
		        return payrollSelected;		    }
		

	    
		public static Integer GetTop5WidgetTotalCount(WidgetOption option) {
			switch (option) {
			
				case Top5AgenciesbyPayroll:
					return HomePage.GetWidgetTotalNumber("Top 5 Agencies by Payroll");
				case TopAgenciesbyPayroll:
					return HomePage.GetWidgetTotalNumber("Top Agencies by Payroll");
				case Top5AgenciesbyOvertime:
					return HomePage.GetWidgetTotalNumber("Top 5 Agencies by Overtime");
				case TopAgenciesbyOvertime:
					return HomePage.GetWidgetTotalNumber("Top Agencies by Overtime");
				case Top5AnnualSalaries:
					return HomePage.GetWidgetTotalNumber("Top 5 Annual Salaries");
				case TopAnnualSalaries:
					return HomePage.GetWidgetTotalNumber("Top Annual Salaries");
				case Top5TitlesbyNumberofEmployees:	
					return HomePage.GetWidgetTotalNumber("Top 5 Titles by Number of Employees");
				case TopTitlesbyNumberofEmployees:
					return HomePage.GetWidgetTotalNumber("Top Titles by Number of Employees");
					
				default:		
					return null;
			}
		}
		
		public static void GoToTop5DetailsPage(WidgetOption option) {
			WebElement detailsContainer = null;
			switch (option) {
				case Top5AgenciesbyPayroll:	
					if(!HomePage.IsAtTop5DetailsPage("Top 5 Agencies by Payroll"))
						detailsContainer = HomePage.GetWidgetDetailsContainer("Top 5 Agencies by Payroll");
					break;
				case TopAgenciesbyPayroll:
					if(!HomePage.IsAtTop5DetailsPage("Top Agencies by Payroll"))
						detailsContainer = HomePage.GetWidgetDetailsContainer("Top Agencies by Payroll");
					break;
				case Top5AgenciesbyOvertime:
					if(!HomePage.IsAtTop5DetailsPage("Top 5 Agencies by Overtime"))
						detailsContainer = HomePage.GetWidgetDetailsContainer("Top 5 Agencies by Overtime");
					break;
				case TopAgenciesbyOvertime:
					if(!HomePage.IsAtTop5DetailsPage("Top Agencies by Overtime"))
						detailsContainer = HomePage.GetWidgetDetailsContainer("Top Agencies by Overtime");
					break;
				case Top5AnnualSalaries:
					if(!HomePage.IsAtTop5DetailsPage("Top 5 Annual Salaries"))
						detailsContainer = HomePage.GetWidgetDetailsContainer("Top 5 Annual Salaries");
					break;
				case TopAnnualSalaries:
					if(!HomePage.IsAtTop5DetailsPage("Top Annual Salaries"))
						detailsContainer = HomePage.GetWidgetDetailsContainer("Top Annual Salaries");
					break;
				case Top5TitlesbyNumberofEmployees:
					if(!HomePage.IsAtTop5DetailsPage("Top 5 Titles by Number of Employees"))
						detailsContainer = HomePage.GetWidgetDetailsContainer("Top 5 Titles by Number of Employees");
					break;
				case TopTitlesbyNumberofEmployees:
					if(!HomePage.IsAtTop5DetailsPage("Top Titles by Number of Employees"))
						detailsContainer = HomePage.GetWidgetDetailsContainer("Top Titles by Number of Employees");
					break;
				
			
				default:
					break;
			}
			WebElement detailsAnchor = detailsContainer.findElement(By.partialLinkText("Details"));
			((JavascriptExecutor) Driver.Instance).executeScript("arguments[0].scrollIntoView(true);", detailsAnchor);
			detailsAnchor.click();	
			Driver.Instance.manage().timeouts().implicitlyWait(20, TimeUnit.SECONDS);
		}
		
	
///widget visualization titles   

public static ArrayList<String> PayrollVisualizationTitles() {
		ArrayList<String> titles = new ArrayList<String>();
		List<WebElement> titleContainers = Driver.Instance.findElements(By.cssSelector("#nyc-payroll > .top-chart > .inside > .panel-pane"));
		for(int i=0; i < titleContainers.size(); i++){
			selectPayrollVisualizationSlider(i);
			WebElement titleClass = titleContainers.get(i).findElement(By.cssSelector(".pane-content .chart-title"));
			if(titleClass.isDisplayed()){
				String title = titleClass.getText();
				titles.add(title);
			}
		}	
		return titles;
	}

public static void selectPayrollVisualizationSlider(int sliderPosition){
	List<WebElement> sliderContainer = Driver.Instance.findElements(By.cssSelector("#nyc-payroll> .top-chart > .slider-pager > a"));
	sliderContainer.get(sliderPosition).click();
	Driver.Instance.manage().timeouts().implicitlyWait(2, TimeUnit.SECONDS);
}

//////widget  titles   
public static ArrayList<String> WidgetTitles() {
	ArrayList<String> titles = new ArrayList<String>();
	List<WebElement> titleContainers = Driver.Instance.findElements(By.className("tableHeader"));
	for (WebElement titleContainer : titleContainers) {
		WebElement titleHeaderContainer = titleContainer.findElement(By.cssSelector("h2"));
		//String subTitle = titleHeaderContainer.getText().substring(0, titleHeaderContainer.getText().indexOf("Number")-1);
		//titles.add(titleHeaderContainer.getText());
		titles.add(titleHeaderContainer.getText().substring(0, titleHeaderContainer.getText().indexOf("Number")-1));
	}	
	return titles;
}

public static String GetWidgetText() {
	List<WebElement> panelContainers = Driver.Instance.findElements(By.cssSelector(".bottomContainer > .panel-display > .panel-panel > .inside > .panel-pane"));
	//List<WebElement> panelContainers = Driver.Instance.findElements(By.cssSelector(".bottomContainer > .panel-display > .panel-panel > div > .panel-pane"));
		for (WebElement panelContainer : panelContainers) {
			WebElement header= panelContainer.findElement(By.tagName("h2"));
			String subTitle = header.getText().substring(0, header.getText().indexOf("Number")-1);
			return subTitle;
			}
		return null;
	}

///Transaction count

public static Integer GetTransactionCount() {
	WebDriverWait wait = new WebDriverWait(Driver.Instance, 10);
	wait.until(ExpectedConditions.visibilityOfElementLocated(By.id("table_317_info")));
	String count = (Driver.Instance.findElement(By.id("table_317_info"))).getText();
	return Helper.GetTotalEntries(count, 9);
}
	public static Integer GetTransactionCount1() {
		WebDriverWait wait = new WebDriverWait(Driver.Instance, 10);
		wait.until(ExpectedConditions.visibilityOfElementLocated(By.id("table_886_info")));
		String count = (Driver.Instance.findElement(By.id("table_886_info"))).getText();
		return Helper.GetTotalEntries(count, 5);
}

}


