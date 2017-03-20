package pages.budget;
import java.util.ArrayList;
import java.util.List;
import java.util.concurrent.TimeUnit;
import org.openqa.selenium.interactions.Actions;
import pages.home.HomePage;

import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;

import navigation.TopNavigation;
import utility.Driver;

public class BudgetPage {
	
		public enum WidgetOption{
			 Top5Agencies, TopAgencies ,Top5ExpenseCategories,TopExpenseCategories,Top5AgenciesbyCommittedExpenseBudget,
			 TopAgenciesbyCommittedExpenseBudget,Top5AgenciesbyPercentDifference,TopAgenciesbyPercentDifference,
			 Top5ExpenseCategoriesbyCommittedExpenseBudget,TopExpenseCategoriesbyCommittedExpenseBudget,
			 Top5ExpenseCategoriesbyPercentDifference,TopExpenseCategoriesbyPercentDifference,
			 Top5Departments,TopDepartments,Top5DepartmentsbyCommittedExpenseBudget,TopDepartmentsbyCommittedExpenseBudget,
			 Top5DepartmentsbyPercentDifference,TopDepartmentsbyPercentDifference,
			 Top5ExpenseBudgetCategories,TopExpenseBudgetCategories,
			 Top5ExpenseBudgetCategoriesbyCommittedExpenseBudget,TopExpenseBudgetCategoriesbyCommittedExpenseBudget
			 }
		public static void GoTo() {
	        TopNavigation.Budget.Select();
	    }
		
		   public static String GetBudgetAmount() {
	            WebElement budgetAmt = Driver.Instance.findElement(By.cssSelector(".top-navigation-left .budget > .expense-container > a"));
	            return budgetAmt.getText().substring((budgetAmt.getText().indexOf("$")));
	        }
		
		public static boolean isAt() {
	    	WebElement topTitleCont = Driver.Instance.findElement(By.cssSelector(".top-navigation-left > table > tbody > tr .budget"));
	    	Boolean budgetSelected = (topTitleCont.getAttribute("class")).contains("active");	    
	        return budgetSelected;
	    }
		public static String GetTop5WidgetTotalCount(WidgetOption option) {
			switch (option) {
			
			case Top5Agencies:
				return HomePage.GetWidgetTotalNumber("Top 5 Agencies");
			case TopAgencies:
				return HomePage.GetWidgetTotalNumber("Top Agencies");
			case Top5AgenciesbyCommittedExpenseBudget:
				return HomePage.GetWidgetTotalNumber("Top 5 Agencies by Committed Expense Budget");
			case TopAgenciesbyCommittedExpenseBudget:
				return HomePage.GetWidgetTotalNumber("Top Agencies by Committed Expense Budget");
			case Top5AgenciesbyPercentDifference:
				return HomePage.GetWidgetTotalNumber("Top 5 Agencies by Percent Difference");
			case TopAgenciesbyPercentDifference:
				return HomePage.GetWidgetTotalNumber("Top Agencies by Percent Difference");
			case Top5ExpenseCategories:	
				return HomePage.GetWidgetTotalNumber("Top 5 Expense Categories");
			case TopExpenseCategories:
				return HomePage.GetWidgetTotalNumber("Top Expense Categories");
			case Top5ExpenseCategoriesbyCommittedExpenseBudget:
				return HomePage.GetWidgetTotalNumber("Top 5 Expense Categories by Committed Expense Budget");
			case TopExpenseCategoriesbyCommittedExpenseBudget:
				return HomePage.GetWidgetTotalNumber("Top Expense Categories by Committed Expense Budget");					
			case Top5ExpenseCategoriesbyPercentDifference:
				return HomePage.GetWidgetTotalNumber("Top 5 Expense Categories by Percent Difference");
			case TopExpenseCategoriesbyPercentDifference:
				return HomePage.GetWidgetTotalNumber("Top Expense Categories by Percent Difference");
			case Top5Departments:	
				return HomePage.GetWidgetTotalNumber("Top 5 Departments");
			case TopDepartments:
				return HomePage.GetWidgetTotalNumber("Top Departments");
			case Top5DepartmentsbyCommittedExpenseBudget:
				return HomePage.GetWidgetTotalNumber("Top 5 Departments by Committed Expense Budget");
			case TopDepartmentsbyCommittedExpenseBudget:
				return HomePage.GetWidgetTotalNumber("Top Departments by Committed Expense Budget");
			case Top5DepartmentsbyPercentDifference:
				return HomePage.GetWidgetTotalNumber("Top 5 Departments by Percent Difference");
			case TopDepartmentsbyPercentDifference:
				return HomePage.GetWidgetTotalNumber("Top Departments by Percent Difference");
			case Top5ExpenseBudgetCategories:	
				return HomePage.GetWidgetTotalNumber("Top 5 Expense Budget Categories");
			case TopExpenseBudgetCategories:
				return HomePage.GetWidgetTotalNumber("Top Expense Budget Categories");
			case Top5ExpenseBudgetCategoriesbyCommittedExpenseBudget:
				return HomePage.GetWidgetTotalNumber("Top 5 Expense Budget Categories by Committed Expense Budget");
			case TopExpenseBudgetCategoriesbyCommittedExpenseBudget:
				return HomePage.GetWidgetTotalNumber("Top Expense Budget Categories by Committed Expense Budget");					
			
				
			default:		
				return null;
			}
		}
		
		public static void GoToTop5DetailsPage(WidgetOption option) {
			WebElement detailsContainer = null;
			switch (option) {
				case Top5Agencies:	
					if(!HomePage.IsAtTop5DetailsPage("Top 5 Agencies"))
						detailsContainer = HomePage.GetWidgetDetailsContainer("Top 5 Agencies");
					break;
				case TopAgencies:
					if(!HomePage.IsAtTop5DetailsPage("Top Agencies"))
						detailsContainer = HomePage.GetWidgetDetailsContainer("Top Agencies");
					break;
				case Top5AgenciesbyCommittedExpenseBudget:
					if(!HomePage.IsAtTop5DetailsPage("Top 5 Agencies by Committed Expense Budget"))
						detailsContainer = HomePage.GetWidgetDetailsContainer("Top 5 Agencies by Committed Expense Budget");
					break;
				case TopAgenciesbyCommittedExpenseBudget:
					if(!HomePage.IsAtTop5DetailsPage("Top Agencies by Committed Expense Budget"))
						detailsContainer = HomePage.GetWidgetDetailsContainer("Top Agencies by Committed Expense Budget");
					break;
				case Top5AgenciesbyPercentDifference:
					if(!HomePage.IsAtTop5DetailsPage("Top 5 Agencies by Percent Difference"))
						detailsContainer = HomePage.GetWidgetDetailsContainer("Top 5 Agencies by Percent Difference");
					break;
				case TopAgenciesbyPercentDifference:
					if(!HomePage.IsAtTop5DetailsPage("Top Agencies by Percent Difference"))
						detailsContainer = HomePage.GetWidgetDetailsContainer("Top Agencies by Percent Difference");
					break;
				case Top5ExpenseCategories:
					if(!HomePage.IsAtTop5DetailsPage("Top 5 Expense Categories"))
						detailsContainer = HomePage.GetWidgetDetailsContainer("Top 5 Expense Categories");
					break;
				case TopExpenseCategories:
					if(!HomePage.IsAtTop5DetailsPage("Top Expense Categories"))
						detailsContainer = HomePage.GetWidgetDetailsContainer("Top Expense Categories");
					break;
				case Top5ExpenseCategoriesbyCommittedExpenseBudget:
					if(!HomePage.IsAtTop5DetailsPage("Top 5 Expense Categories by Committed Expense Budget"))
						detailsContainer = HomePage.GetWidgetDetailsContainer("Top 5 Expense Categories by Committed Expense Budget");
					break;
				case TopExpenseCategoriesbyCommittedExpenseBudget:
					if(!HomePage.IsAtTop5DetailsPage("Top Expense Categories by Committed Expense Budget"))
						detailsContainer = HomePage.GetWidgetDetailsContainer("Top Expense Categories by Committed Expense Budget");
					break;
				case Top5ExpenseCategoriesbyPercentDifference:
					if(!HomePage.IsAtTop5DetailsPage("Top 5 Expense Categories by Percent Difference"))
						detailsContainer = HomePage.GetWidgetDetailsContainer("Top 5 Expense Categories by Percent Difference");
					break;
				case TopExpenseCategoriesbyPercentDifference:
					if(!HomePage.IsAtTop5DetailsPage("Top Expense Categories by Percent Difference"))
						detailsContainer = HomePage.GetWidgetDetailsContainer("Top Expense Categories by Percent Difference");
					break;
				case Top5ExpenseBudgetCategories:
					if(!HomePage.IsAtTop5DetailsPage("Top 5 Expense Budget Categories"))
						detailsContainer = HomePage.GetWidgetDetailsContainer("Top 5 Expense Budget Categories");
					break;
				case TopExpenseBudgetCategories:
					if(!HomePage.IsAtTop5DetailsPage("Top Expense Budget Categories"))
						detailsContainer = HomePage.GetWidgetDetailsContainer("Top Expense Budget Categories");
					break;
				case Top5ExpenseBudgetCategoriesbyCommittedExpenseBudget:
					if(!HomePage.IsAtTop5DetailsPage("Top 5 Expense Budget Categories by Committed Expense Budget"))
						detailsContainer = HomePage.GetWidgetDetailsContainer("Top 5 Expense Budget Categories by Committed Expense Budget");
					break;
				case TopExpenseBudgetCategoriesbyCommittedExpenseBudget:
					if(!HomePage.IsAtTop5DetailsPage("Top Expense Budget Categories by Committed Expense Budget"))
						detailsContainer = HomePage.GetWidgetDetailsContainer("Top Expense Budget Categories by Committed Expense Budget");
					break;
			
				default:
					break;
			}
			WebElement detailsAnchor = detailsContainer.findElement(By.partialLinkText("Details"));
			detailsAnchor.click();	
			Driver.Instance.manage().timeouts().implicitlyWait(2, TimeUnit.SECONDS);
		}
		
	
///widget visualization titles   
public static ArrayList<String> VisualizationTitles() {
	
	return HomePage.VisualizationTitles();
}

//////widget  titles   
public static ArrayList<String> WidgetTitles() {
	ArrayList<String> titles = new ArrayList<String>();
	List<WebElement> titleContainers = Driver.Instance.findElements(By.className("tableHeader"));
	for (WebElement titleContainer : titleContainers) {
		WebElement titleHeaderContainer = titleContainer.findElement(By.cssSelector("h2"));
		titles.add(titleHeaderContainer.getText());
	}	
	return titles;
}
}

	

