package pages.tools.trends;

import navigation.PrimaryMenuNavigation;

public class OperationalPages {
    public enum OperationalPageOptions {
        numberOfFullTimeCityEmployees, capitalAssetsStats
    }

    public static void GoTo(OperationalPageOptions pageSelection) {
        switch (pageSelection) {
            case numberOfFullTimeCityEmployees:
                PrimaryMenuNavigation.Tools.Trends.Operational.
                        operationalSelection(PrimaryMenuNavigation.numberOfFullTimeCityEmployees);
                break;

            case capitalAssetsStats:
                PrimaryMenuNavigation.Tools.Trends.Operational.
                        operationalSelection(PrimaryMenuNavigation.capitalAssetsStats);
                break;

            default:
                break;
        }
    }
}
