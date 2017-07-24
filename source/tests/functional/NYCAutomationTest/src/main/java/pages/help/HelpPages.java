package pages.help;

import navigation.PrimaryMenuNavigation;

public class HelpPages {
    public enum HelpOptions {
        siteNavigationAndGlossary, instructionalVideos, FAQ, askAQuestion,
        reportAProblem, shareAnIdea
    }

    public static void GoTo(HelpOptions pageSelection) {
        switch (pageSelection) {
            case siteNavigationAndGlossary:
                PrimaryMenuNavigation.Help.SiteNavigationAndGlossary();
                break;

            case instructionalVideos:
                PrimaryMenuNavigation.Help.InstructionalVideos();
                break;

            case FAQ:
                PrimaryMenuNavigation.Help.FAQ();
                break;

            case askAQuestion:
                PrimaryMenuNavigation.Help.AskAQuestion();
                break;

            case reportAProblem:
                PrimaryMenuNavigation.Help.ReportAProblem();
                break;

            case shareAnIdea:
                PrimaryMenuNavigation.Help.ShareAnIdea();
                break;

            default:
                break;
        }
    }
}
