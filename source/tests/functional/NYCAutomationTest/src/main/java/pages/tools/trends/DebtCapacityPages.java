package pages.tools.trends;

import navigation.PrimaryMenuNavigation;

public class DebtCapacityPages {
    public enum DebtCapacityOptions {
        ratiosOfOutstandingDebt, ratiosOfCityGeneralBondedDebt, legalDebtMarginInfo, pledgedRevenueCoverageNYC
    }

    public static void GoTo(DebtCapacityOptions pageSelection) {
        switch (pageSelection) {
            case ratiosOfOutstandingDebt:
                PrimaryMenuNavigation.Tools.Trends.DebtCapacity.
                        debtCapacitySelection(PrimaryMenuNavigation.ratiosOfOutstandingDebt);
                break;

            case ratiosOfCityGeneralBondedDebt:
                PrimaryMenuNavigation.Tools.Trends.DebtCapacity.
                        debtCapacitySelection(PrimaryMenuNavigation.ratiosOfCityGeneralBondedDebt);
                break;

            case legalDebtMarginInfo:
                PrimaryMenuNavigation.Tools.Trends.DebtCapacity.
                        debtCapacitySelection(PrimaryMenuNavigation.legalDebtMarginInfo);
                break;

            case pledgedRevenueCoverageNYC:
                PrimaryMenuNavigation.Tools.Trends.DebtCapacity.
                        debtCapacitySelection(PrimaryMenuNavigation.pledgedRevenueCoverageNYC);
                break;

            default:
                break;
        }
    }
}
