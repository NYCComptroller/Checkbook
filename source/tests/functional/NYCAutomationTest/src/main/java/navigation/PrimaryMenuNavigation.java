package navigation;

import org.openqa.selenium.By;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.interactions.Actions;
import org.openqa.selenium.support.ui.ExpectedConditions;
import org.openqa.selenium.support.ui.WebDriverWait;
import helpers.Driver;

public class PrimaryMenuNavigation {

    // Primary Menu Options
    public static final By home = By.cssSelector(".menu-230");
    public static final By tools = By.xpath("//*[@id=\"nice-menu-1\"]//span[contains(text(),'Tools')]");
    public static final By dataFeeds = By.linkText("Data Feeds");
    public static final By resources = By.linkText("Resources");
    public static final By help = By.xpath("//*[@id=\"nice-menu-1\"]//span[contains(text(),'Help')]");

    // Tools
    public static final By trends = By.xpath("//*[@id=\"nice-menu-1\"]//a[contains(text(),'Trends')]");
    public static final By MWBEAgencySummary = By.xpath("//*[@id=\"nice-menu-1\"]//a[contains(text(),'M/WBE Agency Summary')]");

    // Tools>Trends
    public static final By featuredTrends = By.xpath("//*[@id=\"nice-menu-1\"]//a[contains(text(),'Featured Trends')]");
    public static final By allTrends = By.xpath("//*[@id=\"nice-menu-1\"]//a[contains(text(),'All Trends')]");
    public static final By financial = By.xpath("//*[@id=\"nice-menu-1\"]//span[contains(text(),'Financial')]");
    public static final By revenueCapacity = By.xpath("//*[@id=\"nice-menu-1\"]//span[contains(text(),'Revenue Capacity')]");
    public static final By debtCapacity = By.xpath("//*[@id=\"nice-menu-1\"]//span[contains(text(),'Debt Capacity')]");
    public static final By demographic = By.xpath("//*[@id=\"nice-menu-1\"]//span[contains(text(),'Demographic')]");
    public static final By operational = By.xpath("//*[@id=\"nice-menu-1\"]//span[contains(text(),'Operational')]");

    // Tools>Trends>Financial
    public static final By changesInNetAssets = By.xpath("//*[@id=\"nice-menu-1\"]//a[contains(text(),'Changes in Net Assets')]");
    public static final By fundBalances = By.xpath("//*[@id=\"nice-menu-1\"]//a[contains(text(),'Fund Balances-Governmental Funds')]");
    public static final By changesInFundBalances = By.xpath("//*[@id=\"nice-menu-1\"]//a[contains(text(),'Changes in Fund Balances')]");
    public static final By generalFundRevenues = By.xpath("//*[@id=\"nice-menu-1\"]//a[contains(text(),'General Fund Revenues and Other Financing Sources')]");
    public static final By generalFundExpenditures = By.xpath("//*[@id=\"nice-menu-1\"]//a[contains(text(),'General Fund Expenditures and Other Financing Uses')]");
    public static final By capitalProjectsFundAidRevenues = By.xpath("//*[@id=\"nice-menu-1\"]//a[contains(text(),'Capital Projects Fund Aid Revenues')]");
    public static final By NYCEducationalConstructionFund = By.xpath("//*[@id=\"nice-menu-1\"]//a[contains(text(),'New York City Educational Construction Fund')]");

    // Tools>Trends>RevenueCapacity
    public static final By assessedValueAndEstimatedActualValue = By.xpath("//*[@id=\"nice-menu-1\"]//a[contains(text(),'Assessed Value and Estimated Actual Value of Taxable Property')]");
    public static final By propTaxRates = By.xpath("//*[@id=\"nice-menu-1\"]//a[contains(text(),'Property Tax Rates')]");
    public static final By propTaxLeviesAndCollections = By.xpath("//*[@id=\"nice-menu-1\"]//a[contains(text(),'Property Tax Levies and Collections')]");
    public static final By assessedValuationAndTaxRateByClass = By.xpath("//*[@id=\"nice-menu-1\"]//a[contains(text(),'Assessed Valuation and Tax Rate by Class')]");
    public static final By collectionsCancellations = By.xpath("//*[@id=\"nice-menu-1\"]//a[contains(text(),'Collections, Cancellations, Abatements and Other Discounts as a Percent of Tax Levy')]");
    public static final By uncollectedParkingViolationFines = By.xpath("//*[@id=\"nice-menu-1\"]//a[contains(text(),'Uncollected Parking Violation Fines')]");
    public static final By hudsonYardsInfrastructure = By.xpath("//*[@id=\"nice-menu-1\"]//a[contains(text(),'Hudson Yards Infrastructure Corporation')]");

    // Tools>Trends>DebtCapacity
    public static final By ratiosOfOutstandingDebt = By.xpath("//*[@id=\"nice-menu-1\"]//a[contains(text(),'Ratios of Outstanding Debt by Type')]");
    public static final By ratiosOfCityGeneralBondedDebt = By.xpath("//*[@id=\"nice-menu-1\"]//a[contains(text(),'Ratios of City General Bonded Debt Payable')]");
    public static final By legalDebtMarginInfo = By.xpath("//*[@id=\"nice-menu-1\"]//a[contains(text(),'Legal Debt Margin Information')]");
    public static final By pledgedRevenueCoverageNYC = By.xpath("//*[@id=\"nice-menu-1\"]//a[contains(text(),'Pledged-Revenue Coverage NYC Transitional Finance Authority')]");

    // Tools>Trends>Demographic
    public static final By population = By.xpath("//*[@id=\"nice-menu-1\"]//a[contains(text(),'Population')]");
    public static final By personalIncome = By.xpath("//*[@id=\"nice-menu-1\"]//a[contains(text(),'Personal Income')]");
    public static final By nonagriculturalWageSalaryEmployment = By.xpath("//*[@id=\"nice-menu-1\"]//a[contains(text(),'Nonagricultural Wage Salary Employment')]");
    public static final By personsReceivingPublicAssistance = By.xpath("//*[@id=\"nice-menu-1\"]//a[contains(text(),'Persons Receiving Public Assistance')]");
    public static final By employmentStatus = By.xpath("//*[@id=\"nice-menu-1\"]//a[contains(text(),'Employment Status of the Resident Population')]");

    // Tools>Trends>Operational
    public static final By numberOfFullTimeCityEmployees = By.xpath("//*[@id=\"nice-menu-1\"]//a[contains(text(),'Number of Full Time City Employees')]");
    public static final By capitalAssetsStats = By.xpath("//*[@id=\"nice-menu-1\"]//a[contains(text(),'Capital Assets Statistics by Function/Program')]");

    // Resources
    private static final By MWBEResources = By.xpath("//*[@id=\"nice-menu-1\"]//span[contains(text(),'MWBE Resources')]");

    // Resources>MWBE Resources
    public static final By agencyChiefContractingOffice = By.xpath("//*[@id=\"nice-menu-1\"]//a[contains(text(),'Agency Chief Contracting Officer (ACCO)')]");
    public static final By directoryOfCertifiedBusiness = By.xpath("//*[@id=\"nice-menu-1\"]//a[contains(text(),'Directory of Certified Businesses')]");
    public static final By becomeACertifiedMWBEVendor = By.xpath("//*[@id=\"nice-menu-1\"]//a[contains(text(),'Become a Certified M/WBE Vendor')]");
    public static final By sellingToTheGovt = By.xpath("//*[@id=\"nice-menu-1\"]//a[contains(text(),'Selling to the Government')]");
    public static final By helpForBusiness = By.xpath("//*[@id=\"nice-menu-1\"]//a[contains(text(),'Help for Business')]");
    public static final By contractingOpportunities = By.xpath("//*[@id=\"nice-menu-1\"]//a[contains(text(),'Contracting Opportunities')]");

    // Help
    private static final By siteNavigationAndGlossary = By.xpath("//*[@id=\"nice-menu-1\"]//a[contains(text(),'Site Navigation & Glossary')]");
    private static final By instructionalVideos = By.xpath("//*[@id=\"nice-menu-1\"]//a[contains(text(),'Instructional Videos')]");
    private static final By FAQ = By.xpath("//*[@id=\"nice-menu-1\"]//a[contains(text(),'FAQ')]");
    private static final By askAQuestion = By.xpath("//*[@id=\"nice-menu-1\"]//a[contains(text(),'Ask a Question')]");
    private static final By reportAProblem = By.xpath("//*[@id=\"nice-menu-1\"]//a[contains(text(),'Report a Problem')]");
    private static final By shareAnIdea = By.xpath("//*[@id=\"nice-menu-1\"]//a[contains(text(),'Share an Idea')]");


    public static void select(By by) {
        Driver.Instance.findElement(by).click();
    }

    private static void dropdownSelector(By optionOne, By optionTwo) {
        WebDriver driver = Driver.Instance;
        WebDriverWait wait = new WebDriverWait(driver, 60);
        WebElement link = driver.findElement(optionOne);
        link.click();

        wait.until(ExpectedConditions.visibilityOfElementLocated(optionTwo));

        link.findElement(optionTwo).click();
    }

    private static void dropdownSelector(By optionOne, By optionTwo, By optionThree) {
        WebDriver driver = Driver.Instance;
        WebDriverWait wait = new WebDriverWait(driver, 60);
        WebElement link = driver.findElement(optionOne);

        // Hover and hold
        Actions action = new Actions(driver);
        action.moveToElement(driver.findElement(optionOne)).perform();

        wait.until(ExpectedConditions.visibilityOfElementLocated(optionTwo));

        action.moveToElement(driver.findElement(optionTwo)).perform();

        wait.until(ExpectedConditions.visibilityOfElementLocated(optionThree));

        link.findElement(optionThree).click();
    }

    private static void dropdownSelector(By optionOne, By optionTwo, By optionThree, By optionFour) {
        WebDriver driver = Driver.Instance;
        WebDriverWait wait = new WebDriverWait(driver, 60);
        WebElement link = driver.findElement(optionOne);
        link.click();

        wait.until(ExpectedConditions.visibilityOfElementLocated(optionTwo));

        // Hover and hold
        Actions action = new Actions(driver);
        action.moveToElement(driver.findElement(optionTwo)).perform();

        wait.until(ExpectedConditions.visibilityOfElementLocated(optionThree));

        action.moveToElement(driver.findElement(optionThree)).perform();

        wait.until(ExpectedConditions.visibilityOfElementLocated(optionFour));

        link.findElement(optionFour).click();
    }

    public static boolean isAt(String pageTitle) {
        WebElement h2title = Driver.Instance.findElement(By.xpath("//*[@id=\"page-title\"]"));

        return h2title.getText().equals(pageTitle);
    }

    public static boolean isAtResource(String pageTitle) {
        return Driver.Instance.getTitle().equals(pageTitle);
    }

    public static boolean isAtSpecial(String pageTitle) {
        WebElement h2title = Driver.Instance.findElement(By.id("page-titleSpecial"));
        return h2title.getText().equals(pageTitle);
    }

    public static class Tools {
        public static class Trends {
            public static void FeaturedTrends() {
                dropdownSelector(tools, trends);
            }

            public static void AllTrends() {
                dropdownSelector(tools, trends, allTrends);
            }

            public static class Financial {
                public static void financialPageSelection(By optionFour) {
                    dropdownSelector(tools, trends, financial, optionFour);
                }
            }

            public static class RevenueCapacity {
                public static void revenueCapacitySelection(By optionFour) {
                    dropdownSelector(tools, trends, revenueCapacity, optionFour);
                }
            }

            public static class DebtCapacity {
                public static void debtCapacitySelection(By optionFour) {
                    dropdownSelector(tools, trends, debtCapacity, optionFour);
                }
            }

            public static class Demographic {
                public static void demographicSelection(By optionFour) {
                    dropdownSelector(tools, trends, demographic, optionFour);
                }
            }

            public static class Operational {
                public static void operationalSelection(By optionFour) {
                    dropdownSelector(tools, trends, operational, optionFour);
                }
            }
        }

        public static void MWBEAgencySummary() {
            dropdownSelector(tools, MWBEAgencySummary);
        }
    }

    public static void DataFeeds() {
        select(dataFeeds);
    }

    public static class Resources {
        public static void CheckbookResources() {
            select(resources);
        }

        public static void MWBEResourcesPagesSelector(By optionThree) {
            dropdownSelector(resources, MWBEResources, optionThree);
        }
    }

    public static class Help {
        public static void SiteNavigationAndGlossary() {
            dropdownSelector(help, siteNavigationAndGlossary);
        }

        public static void InstructionalVideos() {
            dropdownSelector(help, instructionalVideos);
        }

        public static void FAQ() {
            dropdownSelector(help, FAQ);
        }

        public static void AskAQuestion() {
            dropdownSelector(help, askAQuestion);
        }

        public static void ReportAProblem() {
            dropdownSelector(help, reportAProblem);
        }

        public static void ShareAnIdea() {
            dropdownSelector(help, shareAnIdea);
        }
    }
}


