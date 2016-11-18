package pages.tools.trends;

import navigation.PrimaryMenuNavigation;

public class DemographicPages {
    public enum DemographicPageOptions {
        population, personalIncome, nonagriculturalWageSalaryEmployment,
        personsReceivingPublicAssistance, employmentStatus
    }

    public static void GoTo(DemographicPageOptions pageSelection) {
        switch (pageSelection) {
            case population:
                PrimaryMenuNavigation.Tools.Trends.Demographic.
                        demographicSelection(PrimaryMenuNavigation.population);
                break;

            case personalIncome:
                PrimaryMenuNavigation.Tools.Trends.Demographic.
                        demographicSelection(PrimaryMenuNavigation.personalIncome);
                break;

            case nonagriculturalWageSalaryEmployment:
                PrimaryMenuNavigation.Tools.Trends.Demographic.
                        demographicSelection(PrimaryMenuNavigation.nonagriculturalWageSalaryEmployment);
                break;

            case personsReceivingPublicAssistance:
                PrimaryMenuNavigation.Tools.Trends.Demographic.
                        demographicSelection(PrimaryMenuNavigation.personsReceivingPublicAssistance);
                break;

            case employmentStatus:
                PrimaryMenuNavigation.Tools.Trends.Demographic.
                        demographicSelection(PrimaryMenuNavigation.employmentStatus);
                break;

            default:
                break;
        }
    }
}
