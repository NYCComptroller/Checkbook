package utility;

import java.util.concurrent.TimeUnit;

import org.openqa.selenium.WebDriver;
import org.openqa.selenium.chrome.ChromeDriver;
import org.openqa.selenium.firefox.FirefoxDriver;
import org.openqa.selenium.ie.InternetExplorerDriver;

public class Driver {

	// class fields
	public static WebDriver Instance;
	public static String DriverPath;

	// class accessors/properties
	public WebDriver getDriver() {
		return Driver.Instance;
	}

	// make singleton webdriver management
	public static void Initialize() {
		Instance = new FirefoxDriver();
	}

	public static void Initialize(String BrowswerSelection) {
		if (Driver.DriverPath == null) {
			Driver.GetDriverPath();
		}

		switch (BrowswerSelection.replace(" ", "").toUpperCase()) {
		case "FIREFOX":
			Instance = new FirefoxDriver();
			break;
		case "IE":
			System.setProperty("webdriver.ie.driver", Driver.DriverPath + "IEDriverServer.exe");
			Instance = new InternetExplorerDriver();

			break;
		case "CHROME":
			System.setProperty("webdriver.chrome.driver", Driver.DriverPath + "chromedriver");
			Instance = new ChromeDriver();
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
		String frameworkProjectPath = mainProjectpath + "Framework/src/support/";
		
		Driver.DriverPath = frameworkProjectPath;
	}
}
