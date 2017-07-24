package pages.tools.trends;

import navigation.PrimaryMenuNavigation;

public class RevenueCapacityPages {
    public enum RevCapPageOptions {
        assessedValueAndEstimatedActualValue, propTaxRates, propTaxLeviesAndCollections,
        assessedValuationAndTaxRateByClass, collectionsCancellations, uncollectedParkingViolationFines,
        hudsonYardsInfrastructure
    }

    public static void GoTo(RevCapPageOptions pageSelection) {
        switch (pageSelection) {
            case assessedValueAndEstimatedActualValue:
                PrimaryMenuNavigation.Tools.Trends.RevenueCapacity.
                        revenueCapacitySelection(PrimaryMenuNavigation.assessedValueAndEstimatedActualValue);
                break;

            case propTaxRates:
                PrimaryMenuNavigation.Tools.Trends.RevenueCapacity.
                        revenueCapacitySelection(PrimaryMenuNavigation.propTaxRates);
                break;

            case propTaxLeviesAndCollections:
                PrimaryMenuNavigation.Tools.Trends.RevenueCapacity.
                        revenueCapacitySelection(PrimaryMenuNavigation.propTaxLeviesAndCollections);
                break;

            case assessedValuationAndTaxRateByClass:
                PrimaryMenuNavigation.Tools.Trends.RevenueCapacity.
                        revenueCapacitySelection(PrimaryMenuNavigation.assessedValuationAndTaxRateByClass);
                break;

            case collectionsCancellations:
                PrimaryMenuNavigation.Tools.Trends.RevenueCapacity.
                        revenueCapacitySelection(PrimaryMenuNavigation.collectionsCancellations);
                break;

            case uncollectedParkingViolationFines:
                PrimaryMenuNavigation.Tools.Trends.RevenueCapacity.
                        revenueCapacitySelection(PrimaryMenuNavigation.uncollectedParkingViolationFines);
                break;

            case hudsonYardsInfrastructure:
                PrimaryMenuNavigation.Tools.Trends.RevenueCapacity.
                        revenueCapacitySelection(PrimaryMenuNavigation.hudsonYardsInfrastructure);
                break;

            default:
                break;
        }
    }
}
