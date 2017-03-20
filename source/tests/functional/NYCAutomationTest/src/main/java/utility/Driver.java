package utility;

import org.openqa.selenium.WebDriver;
import org.openqa.selenium.chrome.ChromeDriver;
import org.openqa.selenium.firefox.FirefoxDriver;
import org.openqa.selenium.ie.InternetExplorerDriver;
import org.openqa.selenium.phantomjs.PhantomJSDriver;

import java.util.concurrent.TimeUnit;

public class Driver {

    // class fields
    public static WebDriver Instance;
    private static String DriverPath;

    // class accessors/properties
    public WebDriver getDriver() {
        return Driver.Instance;
    }

    // make singleton webdriver management
    public static void Initialize() {
        System.out.println("hello");
        Instance = new FirefoxDriver();
    }

    public static void Initialize(String BrowswerSelection) {
        if (Driver.DriverPath == null) {
			Driver.GetDriverPath();
		}

        switch (BrowswerSelection.replace(" ", "").toUpperCase()) {
            case "FIREFOX":
                Instance = new ChromeDriver();
                break;

            case "IE":
                System.setProperty("webdriver.ie.driver", Driver.DriverPath + "IEDriverServer.exe");
                Instance = new InternetExplorerDriver();
                break;

            case "CHROME":
                System.setProperty("webdriver.chrome.driver", Driver.DriverPath + "chromedriver.exe");
                Instance = new ChromeDriver();
                break;

            case "HEADLESS":
                Instance = new PhantomJSDriver();
                break;

            default:
                break;
        }
    }

    public static void TearDown() {
        Instance.quit();
    }

    public static void GoTo(String URL) {
        Instance.get(URL);
        Instance.manage().timeouts().implicitlyWait(2, TimeUnit.SECONDS);
    }
    private static void GetDriverPath() {
        String mainProjectpath;
        mainProjectpath = System.getProperty("user.dir");
        String frameworkProjectPath = mainProjectpath + "/src/main/resources/support/";

        Driver.DriverPath = frameworkProjectPath;
    }
}
