package smoke;

import navigation.PrimaryMenuNavigation;
import org.junit.Before;
import org.junit.Test;
import org.openqa.selenium.By;
import pages.*;
import pages.help.HelpPages;
import pages.resources.CheckbookResourcesPage;
import pages.resources.MWBEResourcesPages;
import pages.tools.MWBE_AgencySummaryPage;
import pages.tools.trends.*;
import utilities.NYCBaseTest;
import utilities.NYCDatabaseUtil;
import utility.Driver;

import java.io.IOException;
import java.sql.SQLException;

import static org.junit.Assert.assertEquals;
import static org.junit.Assert.assertTrue;


public class SmokeTest extends NYCBaseTest {

    @Before // Return home after every test if necessary
    public void GoHome() throws SQLException, IOException, ClassNotFoundException {
       // if (!SpendingPage.isAt()) 
    	Driver.Instance.get("http://checkbooknyc.com/spending_landing/yeartype/B/year/118");
    }

    private void GoFY2016() {
        HomePage.GoTo(NYCBaseTest.prop.getProperty("BaseUrl"));
        HomePage.SelectYear(NYCBaseTest.prop.getProperty("CurrentYear"));
    }

    @Test
    public void VerifySpendingAmount() throws SQLException {
        GoFY2016();
        String TotalSpendingAmtFY2016 = NYCDatabaseUtil.getSpendingAmount(2016, 'B');

        SpendingPage.GoTo();
        String spendingAmt = SpendingPage.GetSpendingAmount();

        assertEquals("Spending Amount did not match", spendingAmt, TotalSpendingAmtFY2016);

    }

    @Test
    public void VerifyNumOfAgenciesPayrollSpending() {
        GoFY2016();
        String PayrollSpendingNumOfAgenciesFY2016 = "130";

        PayrollSpendingPage.GoTo();
        String numberOfAgencies = PayrollSpendingPage.GetTotalNumOfAgencies();

        assertEquals("Number of Agencies in Payroll Spending did not match", numberOfAgencies, PayrollSpendingNumOfAgenciesFY2016);
    }

    @Test
    public void goToPayrollSpending() {

        PayrollSpendingPage.GoTo();
        assertTrue(PayrollSpendingPage.isAt());
    }

    // Helper class
    private static boolean isPresent(By by) {
        return Driver.Instance.findElements(by).size() > 0;
    }

    @Test
    public void bannerExists() {
        assertTrue(isPresent(By.id("logo")));
    }

    // MENU NAVIGATION SMOKE TESTS

    @Test
    public void primaryHomeExists() {
        assertTrue(isPresent(PrimaryMenuNavigation.home));
    }

    @Test
    public void primaryToolsExists() {
        assertTrue(isPresent(PrimaryMenuNavigation.tools));
    }

    @Test
    public void primaryDataFeedsExists() {
        assertTrue(isPresent(PrimaryMenuNavigation.dataFeeds));
    }

    @Test
    public void primaryResourcesExists() {
        assertTrue(isPresent(PrimaryMenuNavigation.resources));
    }

    @Test
    public void primaryHelpExists() {
        assertTrue(isPresent(PrimaryMenuNavigation.help));
    }

    // Can navigate to home
    @Test
    public void primaryHomeLinkWorks() {
        PrimaryMenuNavigation.select(PrimaryMenuNavigation.home);
        assertTrue(SpendingPage.isAt());
    }

    // PrimaryMenuNavigation.ToolsNavigation Menu Tests
    @Test
    public void trendsExists() {
        assertTrue(isPresent(PrimaryMenuNavigation.trends));
    }

    @Test
    public void MWBEAgencySummaryExists() {
        assertTrue(isPresent(PrimaryMenuNavigation.MWBEAgencySummary));
    }

    // Tools > Trends
    @Test
    public void featureTrendsExists() {
        assertTrue(isPresent(PrimaryMenuNavigation.featuredTrends));
    }

    @Test
    public void allTrendsExists() {
        assertTrue(isPresent(PrimaryMenuNavigation.allTrends));
    }

    @Test
    public void financialExists() {
        assertTrue(isPresent(PrimaryMenuNavigation.financial));
    }

    @Test
    public void revenueCapacityExists() {
        assertTrue(isPresent(PrimaryMenuNavigation.revenueCapacity));
    }

    @Test
    public void debtCapacityExists() {
        assertTrue(isPresent(PrimaryMenuNavigation.debtCapacity));
    }

    @Test
    public void demographicExists() {
        AllTrendsPage.GoTo();
        assertTrue(isPresent(PrimaryMenuNavigation.demographic));
    }

    @Test
    public void operationalExists() {
        assertTrue(isPresent(PrimaryMenuNavigation.operational));
    }


    @Test
    public void canGoToMWBEAgencySummary() {
        MWBE_AgencySummaryPage.GoTo();
        assertTrue(MWBE_AgencySummaryPage.isAt());
    }

    // Tools>Trends
    @Test
    public void canGoToFeaturedTrends() {
        FeaturedTrendsPage.GoTo();
        assertTrue(FeaturedTrendsPage.isAt());
    }

//    @Test
//    public void verifyFeaturedTrendsHover() {
//        assertEquals(FeaturedTrendsPage.featuredTrends2015orange(), "$78.04B");
//    }

    @Test
    public void canGoToAllTrends() {
        AllTrendsPage.GoTo();
        assertTrue(AllTrendsPage.isAt());
    }

    @Test
    public void canGoToChangesInNetAssetsPage() {
        FinancialPages.GoTo(FinancialPages.financialPageOptions.changesInNetAssets);
        assertTrue(PrimaryMenuNavigation.isAt("Changes in Net Assets"));
    }

    @Test
    public void canGoToFundBalancesPage() {
        FinancialPages.GoTo(FinancialPages.financialPageOptions.fundBalances);
        assertTrue(PrimaryMenuNavigation.isAt("Fund Balances-Governmental Funds"));
    }

    @Test
    public void canGoToChangesInFundBalancesPage() {
        FinancialPages.GoTo(FinancialPages.financialPageOptions.changesInFundBalances);
        assertTrue(PrimaryMenuNavigation.isAt("Changes in Fund Balances"));
    }

    @Test
    public void canGoToGeneralFundRevenuesPage() {
        FinancialPages.GoTo(FinancialPages.financialPageOptions.generalFundRevenues);
        assertTrue(PrimaryMenuNavigation.isAt("General Fund Revenues and Other Financing Sources"));
    }

    @Test
    public void canGoToGeneralFundExpendituresPage() {
        FinancialPages.GoTo(FinancialPages.financialPageOptions.generalFundExpenditures);
        assertTrue(PrimaryMenuNavigation.isAt("General Fund Expenditures and Other Financing Uses"));
    }

    @Test
    public void canGoToCapitalProjectsFundAidRevenuesPage() {
        FinancialPages.GoTo(FinancialPages.financialPageOptions.capitalProjectsFundAidRevenues);
        assertTrue(PrimaryMenuNavigation.isAt("Capital Projects Fund Aid Revenues"));
    }

    @Test
    public void canGoToNYCEducationalConstructionFundPage() {
        FinancialPages.GoTo(FinancialPages.financialPageOptions.NYCEducationalConstructionFund);
        assertTrue(PrimaryMenuNavigation.isAtSpecial("New York City Educational Construction Fund*"));
    }

    // Tools>Trends>RevenueCapacity

    @Test
    public void canGoToAssessedValueAndEstimatedActualValuePage() {
        RevenueCapacityPages.GoTo(RevenueCapacityPages.RevCapPageOptions.assessedValueAndEstimatedActualValue);
        assertTrue(PrimaryMenuNavigation.isAt("Assessed Value and Estimated Actual Value of Taxable Property"));
    }

    @Test
    public void canGoToPropTaxRatesPage() {
        RevenueCapacityPages.GoTo(RevenueCapacityPages.RevCapPageOptions.propTaxRates);
        assertTrue(PrimaryMenuNavigation.isAt("Property Tax Rates"));
    }

    @Test
    public void canGoToPropTaxLeviesAndCollectionsPage() {
        RevenueCapacityPages.GoTo(RevenueCapacityPages.RevCapPageOptions.propTaxLeviesAndCollections);
        assertTrue(PrimaryMenuNavigation.isAt("Property Tax Levies and Collections"));
    }

    @Test
    public void canGoToAssessedValuationAndTaxRateByClassPage() {
        RevenueCapacityPages.GoTo(RevenueCapacityPages.RevCapPageOptions.assessedValuationAndTaxRateByClass);
        assertTrue(PrimaryMenuNavigation.isAt("Assessed Valuation and Tax Rate by Class"));
    }

    @Test
    public void canGoToCollectionsCancellationsPage() {
        RevenueCapacityPages.GoTo(RevenueCapacityPages.RevCapPageOptions.collectionsCancellations);
        assertTrue(PrimaryMenuNavigation.isAt("Collections, Cancellations, Abatements and Other Discounts as a Percent of Tax Levy"));
    }

    @Test
    public void canGoToUncollectedParkingViolationFinesPage() {
        RevenueCapacityPages.GoTo(RevenueCapacityPages.RevCapPageOptions.uncollectedParkingViolationFines);
        assertTrue(PrimaryMenuNavigation.isAt("Uncollected Parking Violation Fines"));
    }

    @Test
    public void canGoToHudsonYardsInfrastructurePage() {
        RevenueCapacityPages.GoTo(RevenueCapacityPages.RevCapPageOptions.hudsonYardsInfrastructure);
        assertTrue(PrimaryMenuNavigation.isAtSpecial("Hudson Yards Infrastructure Corporation"));
    }


    // Tools>Trends>DebtCapacity

    @Test
    public void canGoToRatiosOfOutstandingDebt() {
        DebtCapacityPages.GoTo(DebtCapacityPages.DebtCapacityOptions.ratiosOfOutstandingDebt);
        assertTrue(PrimaryMenuNavigation.isAt("Ratios of Outstanding Debt by Type"));
    }

    @Test
    public void canGoToRatiosOfCityGeneralBondedDebt() {
        DebtCapacityPages.GoTo(DebtCapacityPages.DebtCapacityOptions.ratiosOfCityGeneralBondedDebt);
        assertTrue(PrimaryMenuNavigation.isAt("Ratios of City General Bonded Debt Payable"));
    }

    @Test
    public void canGoTLegalDebtMarginInfo() {
        DebtCapacityPages.GoTo(DebtCapacityPages.DebtCapacityOptions.legalDebtMarginInfo);
        assertTrue(PrimaryMenuNavigation.isAt("Legal Debt Margin Information"));
    }

    @Test
    public void canGoToPledgedRevenueCoverageNYC() {
        DebtCapacityPages.GoTo(DebtCapacityPages.DebtCapacityOptions.pledgedRevenueCoverageNYC);
        assertTrue(PrimaryMenuNavigation.isAt("Pledged-Revenue Coverage NYC Transitional Finance Authority"));
    }


    // Tools>Trends>Demographic

    @Test
    public void canGoToPopulation() {
        DemographicPages.GoTo(DemographicPages.DemographicPageOptions.population);
        assertTrue(PrimaryMenuNavigation.isAt("Population"));
    }

    @Test
    public void canGoToPersonalIncome() {
        DemographicPages.GoTo(DemographicPages.DemographicPageOptions.personalIncome);
        assertTrue(PrimaryMenuNavigation.isAt("Personal Income"));
    }

    @Test
    public void canGoToNonAgriculturalWageSalaryEmployment() {
        DemographicPages.GoTo(DemographicPages.DemographicPageOptions.nonagriculturalWageSalaryEmployment);
        assertTrue(PrimaryMenuNavigation.isAt("Nonagricultural Wage Salary Employment"));
    }

    @Test
    public void canGoToPersonsReceivingPublicAssistance() {
        DemographicPages.GoTo(DemographicPages.DemographicPageOptions.personsReceivingPublicAssistance);
        assertTrue(PrimaryMenuNavigation.isAt("Persons Receiving Public Assistance"));
    }

    @Test
    public void canGoToEmploymentStatus() {
        DemographicPages.GoTo(DemographicPages.DemographicPageOptions.employmentStatus);
        assertTrue(PrimaryMenuNavigation.isAt("Employment Status of the Resident Population"));
    }

    // Tools>Trends>Operational

    @Test
    public void canGoToNumberOfFullTimeCityEmployees() {
        OperationalPages.GoTo(OperationalPages.OperationalPageOptions.numberOfFullTimeCityEmployees);
        assertTrue(PrimaryMenuNavigation.isAt("Number of Full Time City Employees"));
    }

    @Test
    public void canGoToCapitalAssetsStats() {
        OperationalPages.GoTo(OperationalPages.OperationalPageOptions.capitalAssetsStats);
        assertTrue(PrimaryMenuNavigation.isAt("Capital Assets Statistics by Function/Program"));
    }

    // Data Feeds

    @Test
    public void canGoToDataFeeds() {
        DataFeedsPage.GoTo();
        assertTrue(DataFeedsPage.isAt());
    }

    // Resources

    @Test
    public void canGoToCheckbookResources() {
        CheckbookResourcesPage.GoTo();
        assertTrue(CheckbookResourcesPage.isAt());
    }

    // Resources>MWBE Resources

    @Test
    public void canGoToAgencyChiefContractingOffice() {
        MWBEResourcesPages.GoTo(MWBEResourcesPages.MWBEResourcesPageOptions.agencyChiefContractingOffice);
        assertTrue(PrimaryMenuNavigation.isAtResource("Sell to NYC - Selling to NYC - Agency Contact List"));
    }

    @Test
    public void canGoToDirectorOfCertifiedBusiness() {
        MWBEResourcesPages.GoTo(MWBEResourcesPages.MWBEResourcesPageOptions.directoryOfCertifiedBusiness);
        assertTrue(PrimaryMenuNavigation.isAtResource("M/WBE Online Directory"));
    }

    @Test
    public void canGoToBecomeACertifiedMWBEVendor() {
        MWBEResourcesPages.GoTo(MWBEResourcesPages.MWBEResourcesPageOptions.becomeACertifiedMWBEVendor);
        assertTrue(PrimaryMenuNavigation.isAtResource("NYC Business Solutions - Summary of Services - Certification"));
    }

    @Test
    public void canGoToSellingToTheGovt() {
        MWBEResourcesPages.GoTo(MWBEResourcesPages.MWBEResourcesPageOptions.sellingToTheGovt);
        assertTrue(PrimaryMenuNavigation.isAtResource("SBS - Selling to Government"));
    }

    @Test
    public void canGoToHelpForBusiness() {
        MWBEResourcesPages.GoTo(MWBEResourcesPages.MWBEResourcesPageOptions.helpForBusiness);
        assertTrue(PrimaryMenuNavigation.isAtResource("SBS - Help for Businesses"));
    }

    @Test
    public void canGoToContractingOpportunities() {
        MWBEResourcesPages.GoTo(MWBEResourcesPages.MWBEResourcesPageOptions.contractingOpportunities);
        assertTrue(PrimaryMenuNavigation.isAtResource("Contracting Opportunities"));
    }

    @Test
    public void goToAllTrends() {
        PrimaryMenuNavigation.Tools.Trends.AllTrends();
        assertTrue(PrimaryMenuNavigation.isAt("All Trends"));
    }

    // Help
    @Test
    public void canGoToSiteNavAndGlossary() {
        HelpPages.GoTo(HelpPages.HelpOptions.siteNavigationAndGlossary);
        assertTrue(PrimaryMenuNavigation.isAt("Site Navigation & Glossary"));
    }

    @Test
    public void canGoToInstructionalVideos() {
        HelpPages.GoTo(HelpPages.HelpOptions.instructionalVideos);
        assertTrue(PrimaryMenuNavigation.isAt("Instructional Videos"));
    }

    @Test
    public void canGoToFAQ() {
        HelpPages.GoTo(HelpPages.HelpOptions.FAQ);
        assertTrue(PrimaryMenuNavigation.isAtResource(
                "Recently active topics in New York City Comptroller about Checkbook 2.0"));
    }

    @Test
    public void canGoToAskAQuestion() {
        HelpPages.GoTo(HelpPages.HelpOptions.askAQuestion);
        assertTrue(PrimaryMenuNavigation.isAt("Ask a Question"));
    }

    @Test
    public void canGoToReportAProblem() {
        HelpPages.GoTo(HelpPages.HelpOptions.reportAProblem);
        assertTrue(PrimaryMenuNavigation.isAt("Report a Problem"));
    }

    @Test
    public void canGoToShareAnIdea() {
        HelpPages.GoTo(HelpPages.HelpOptions.shareAnIdea);
        assertTrue(PrimaryMenuNavigation.isAt("Share an Idea"));
    }

    @Test
    public void goToChangesInNetAssetsThroughAllTrendsPage() {
        AllTrendsPage.GoTo(AllTrendsPage.allTrendsOptions.changesInNetAssets);
        assertTrue(PrimaryMenuNavigation.isAt("Changes in Net Assets"));
    }

    @Test
    public void verifyChangesInNetAssets2015() {
        AllTrendsPage.GoTo(AllTrendsPage.allTrendsOptions.changesInNetAssets);
        assertTrue(SearchPage.stringToInt(AllTrendsPage.changesInNetAssets2015()) >= 5479762);
    }

    @Test
    public void dataFeedsToThankYouPage() {
        DataFeedsPage.submitDataFeedsForm();
        assertTrue(PrimaryMenuNavigation.isAt("Thank You"));
    }

    @Test
    public void verifyCreateAlert() {
        HomePage.createAlert();
        assertTrue(Driver.Instance.findElements(By.xpath(
                "//*[@id=\"ui-dialog-title-block-checkbook-advanced-search-checkbook-advanced-search-form\"" +
                        "]/span/span[1][contains(text(),'1. Select Criteria')]")).size() > 0);
    }

    @Test
    public void canGoToSmartSearch() {
        SearchPage.smartSearch();
        assertTrue(SearchPage.isAt());
    }

    @Test
    public void verifySearchEntriesTotalGreaterThan80M() {
        SearchPage.smartSearch();
        assertTrue(SearchPage.getTotalSearchEntries() > 80000000);
    }

    @Test
    public void verifyTypeOfDataTotals() {
        SearchPage.smartSearch();
        SearchPage.openTypeOfData();

        assertTrue(SearchPage.intArrayElementsAllGreaterThan(
                SearchPage.typeOfDataTotals(), 10000));
    }

    @Test
    public void verifyActiveExpenseContractsTransactionsCount() {
        SearchPage.AdvancedSearch.GoTo();
        assertTrue(SearchPage.AdvancedSearch.activeExpenseContractsTransactionsCount() > 33000);
    }
}
