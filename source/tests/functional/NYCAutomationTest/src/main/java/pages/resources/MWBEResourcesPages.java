package pages.resources;

import navigation.PrimaryMenuNavigation;

public class MWBEResourcesPages {
    public enum MWBEResourcesPageOptions {
        agencyChiefContractingOffice, directoryOfCertifiedBusiness, becomeACertifiedMWBEVendor, sellingToTheGovt,
        helpForBusiness, contractingOpportunities
    }

    public static void GoTo(MWBEResourcesPageOptions pageSelection) {
        switch (pageSelection) {
            case agencyChiefContractingOffice:
                PrimaryMenuNavigation.Resources.
                        MWBEResourcesPagesSelector(PrimaryMenuNavigation.agencyChiefContractingOffice);
                break;

            case directoryOfCertifiedBusiness:
                PrimaryMenuNavigation.Resources.
                        MWBEResourcesPagesSelector(PrimaryMenuNavigation.directoryOfCertifiedBusiness);
                break;

            case becomeACertifiedMWBEVendor:
                PrimaryMenuNavigation.Resources.
                        MWBEResourcesPagesSelector(PrimaryMenuNavigation.becomeACertifiedMWBEVendor);
                break;

            case sellingToTheGovt:
                PrimaryMenuNavigation.Resources.
                        MWBEResourcesPagesSelector(PrimaryMenuNavigation.sellingToTheGovt);
                break;

            case helpForBusiness:
                PrimaryMenuNavigation.Resources.
                        MWBEResourcesPagesSelector(PrimaryMenuNavigation.helpForBusiness);
                break;

            case contractingOpportunities:
                PrimaryMenuNavigation.Resources.
                        MWBEResourcesPagesSelector(PrimaryMenuNavigation.contractingOpportunities);
                break;

            default:
                break;
        }
    }
}
