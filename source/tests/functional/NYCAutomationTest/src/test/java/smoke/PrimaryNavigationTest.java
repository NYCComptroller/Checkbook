package smoke;

import static org.junit.Assert.assertTrue;

import org.junit.Before;
import org.junit.Test;

import navigation.PrimaryMenuNavigation;
import pages.datafeeds.DataFeedsPage;
import pages.help.HelpPages;
import pages.home.HomePage;
import pages.resources.CheckbookResourcesPage;
import pages.resources.MWBEResourcesPages;
import pages.tools.MWBE_AgencySummaryPage;
import pages.tools.trends.AllTrendsPage;
import pages.tools.trends.DebtCapacityPages;
import pages.tools.trends.DemographicPages;
import pages.tools.trends.FeaturedTrendsPage;
import pages.tools.trends.FinancialPages;
import pages.tools.trends.OperationalPages;
import pages.tools.trends.RevenueCapacityPages;
import utilities.NYCBaseTest;
import utility.Helper;
import utility.TestStatusReport;

public class PrimaryNavigationTest extends TestStatusReport{
	@Before
	public void GoToPage(){
		if(!HomePage.IsAtCheckbookNYC())
			HomePage.GoTo(NYCBaseTest.prop.getProperty("BaseUrl"));
	}
	
	/*************************** Primary Navigation Main Level *****************************/ 
    @Test
    public void primaryHomeExists() {
        assertTrue(Helper.isPresent(PrimaryMenuNavigation.home));
    }

    @Test
    public void primaryToolsExists() {
        assertTrue(Helper.isPresent(PrimaryMenuNavigation.tools));
    }

    @Test
    public void primaryDataFeedsExists() {
        assertTrue(Helper.isPresent(PrimaryMenuNavigation.dataFeeds));
    }

    @Test
    public void primaryResourcesExists() {
        assertTrue(Helper.isPresent(PrimaryMenuNavigation.resources));
    }

    @Test
    public void primaryHelpExists() {
        assertTrue(Helper.isPresent(PrimaryMenuNavigation.help));
    }    
    
    /********************************* Data Feeds ***********************************/ 
    @Test
    public void canGoToDataFeeds() {
        DataFeedsPage.GoTo();
        assertTrue(DataFeedsPage.isAt());
    }

    
    /******************************* PrimaryMenuNavigation > Tools ***********************/
    @Test
    public void trendsExists() {
        assertTrue(Helper.isPresent(PrimaryMenuNavigation.trends));
    }

    @Test
    public void MWBEAgencySummaryExists() {
        assertTrue(Helper.isPresent(PrimaryMenuNavigation.MWBEAgencySummary));
    }
    
    /**************************** PrimaryMenuNavigation > Tools > Trends **************************/
    @Test
    public void featureTrendsExists() {
        assertTrue(Helper.isPresent(PrimaryMenuNavigation.featuredTrends));
    }

    @Test
    public void allTrendsExists() {
        assertTrue(Helper.isPresent(PrimaryMenuNavigation.allTrends));
    }

    @Test
    public void financialExists() {
        assertTrue(Helper.isPresent(PrimaryMenuNavigation.financial));
    }

    @Test
    public void revenueCapacityExists() {
        assertTrue(Helper.isPresent(PrimaryMenuNavigation.revenueCapacity));
    }

    @Test
    public void debtCapacityExists() {
        assertTrue(Helper.isPresent(PrimaryMenuNavigation.debtCapacity));
    }

    @Test
    public void demographicExists() {
        AllTrendsPage.GoTo();
        assertTrue(Helper.isPresent(PrimaryMenuNavigation.demographic));
    }

    @Test
    public void operationalExists() {
        assertTrue(Helper.isPresent(PrimaryMenuNavigation.operational));
    }
    
    /************** PrimaryMenuNavigation > Tools > MWBE Agency Summary **********************/
    @Test
    public void canGoToMWBEAgencySummary() {
        MWBE_AgencySummaryPage.GoTo();
        assertTrue(MWBE_AgencySummaryPage.isAt());
    }
        
    /********************* PrimaryMenuNavigation > Tools > Trends ****************************/
    @Test
    public void canGoToFeaturedTrends() {
        FeaturedTrendsPage.GoTo();
        assertTrue(FeaturedTrendsPage.isAt());
    }
    
    @Test
    public void canGoToAllTrends() {
        AllTrendsPage.GoTo();
        assertTrue(AllTrendsPage.isAt());
    }
    
    /********************* PrimaryMenuNavigation > Tools > Trends > All Trends ****************************/
    @Test
    public void goToAllTrends() {
        PrimaryMenuNavigation.Tools.Trends.AllTrends();
        assertTrue(PrimaryMenuNavigation.isAt("All Trends"));
    }
    
    /************************************* PrimaryMenuNavigation > Tools > Trends > Financial  *********************************/
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
    
    /********************************** PrimaryMenuNavigation > Tools > Trends > DebtCapacity *****************************/

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
    
   /************************** PrimaryMenuNavigation > Tools > Trends > Demographic **************************/

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
    
    /*********************** PrimaryMenuNavigation > Tools > Trends > Operational ***************************/

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
    
    /********************** PrimaryMenuNavigation > Tools > Trends > RevenueCapacity *****************************/

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
    
    
    /******************************* PrimaryMenuNavigation > Resources > MWBE Resources ************************************/

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
        assertTrue(PrimaryMenuNavigation.isAtResource("Contracting Opportunities - MOCS"));
    }
    
    
    /**************************** PrimaryMenuNavigation > Resources > Checkbook Resources********************************/

    @Test
    public void canGoToCheckbookResources() {
        CheckbookResourcesPage.GoTo();
        assertTrue(CheckbookResourcesPage.isAt());
    }
 

    /***************************** PrimaryMenuNavigation > Help ******************************/
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
    
}
