package pages.tools.trends;

import navigation.PrimaryMenuNavigation;

public class FinancialPages {

    public enum financialPageOptions {
        changesInNetAssets, fundBalances, changesInFundBalances, generalFundRevenues,
        generalFundExpenditures, capitalProjectsFundAidRevenues, NYCEducationalConstructionFund
    }

    public static void GoTo(financialPageOptions pageSelection) {
        switch (pageSelection) {
            case changesInNetAssets:
                PrimaryMenuNavigation.Tools.Trends.Financial
                        .financialPageSelection(PrimaryMenuNavigation.changesInNetAssets);
                break;

            case fundBalances:
                PrimaryMenuNavigation.Tools.Trends.Financial
                        .financialPageSelection(PrimaryMenuNavigation.fundBalances);
                break;

            case changesInFundBalances:
                PrimaryMenuNavigation.Tools.Trends.Financial
                        .financialPageSelection(PrimaryMenuNavigation.changesInFundBalances);
                break;

            case generalFundRevenues:
                PrimaryMenuNavigation.Tools.Trends.Financial
                        .financialPageSelection(PrimaryMenuNavigation.generalFundRevenues);
                break;

            case generalFundExpenditures:
                PrimaryMenuNavigation.Tools.Trends.Financial
                        .financialPageSelection(PrimaryMenuNavigation.generalFundExpenditures);
                break;

            case capitalProjectsFundAidRevenues:
                PrimaryMenuNavigation.Tools.Trends.Financial
                        .financialPageSelection(PrimaryMenuNavigation.capitalProjectsFundAidRevenues);
                break;

            case NYCEducationalConstructionFund:
                PrimaryMenuNavigation.Tools.Trends.Financial
                        .financialPageSelection(PrimaryMenuNavigation.NYCEducationalConstructionFund);
                break;

            default:
                break;
        }

//        PrimaryMenuNavigation.Tools.Trends.Financial.ChangesInNetAssets();
    }
}
