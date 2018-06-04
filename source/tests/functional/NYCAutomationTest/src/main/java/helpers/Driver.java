package helpers;

import org.openqa.selenium.Alert;
import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.ui.ExpectedConditions;
import org.openqa.selenium.support.ui.WebDriverWait;
import java.util.concurrent.TimeUnit;

import org.openqa.selenium.WebDriver;
import org.openqa.selenium.chrome.ChromeDriver;
import org.openqa.selenium.firefox.FirefoxDriver;
import org.openqa.selenium.ie.InternetExplorerDriver;
import org.openqa.selenium.phantomjs.PhantomJSDriver;
import org.openqa.selenium.remote.server.handler.SendKeys;
import org.openqa.selenium.security.UserAndPassword;

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
       Driver.Initialize(BrowswerSelection, null);
    }
    
    public static void Initialize(String BrowswerSelection, String Platform) {
    	if (Driver.DriverPath == null) {
			Driver.GetDriverPath();
		}
    	String driver;

        switch (BrowswerSelection.replace(" ", "").toUpperCase()) {
        
            case "FIREFOX":
            	Platform = Platform != null ? Platform.replace(" ", "").toUpperCase() : "";
            	switch (Platform.replace(" ", "").toUpperCase()) {
	                case "MAC":
	              	  driver = "geckodriver-mac";
	                    break;
	                case "LINUX":
	              	  driver = "geckodriver-linux-64";
	                    break;
	                default:
	              	  driver = "geckodriver.exe";
	                    break;
	            }
                System.setProperty("webdriver.gecko.driver", Driver.DriverPath + driver);
                Instance = new FirefoxDriver();
                break;

            case "IE":
                System.setProperty("webdriver.ie.driver", Driver.DriverPath + "IEDriverServer.exe");
                Instance = new InternetExplorerDriver();
                break;

            case "CHROME":
            	Platform = Platform != null ? Platform.replace(" ", "").toUpperCase() : "";
            	switch (Platform.replace(" ", "").toUpperCase()) {
	                case "MAC":
	              	  driver = "chromedriver-mac";
	                    break;
	                case "LINUX":
	              	  driver = "chromedriver-linux-64";
	                    break;
	                default:
	              	  driver = "chromedriver.exe";
	                    break;
	            }
                System.setProperty("webdriver.chrome.driver", Driver.DriverPath + driver );
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
    
   /* public void login(String username, String password){
        WebDriver driver = getDriver();
        String URL = "http:// + username + ":" + password + "@" + "link";
        driver.get(URL);
        driver.manage().window().maximize();
    }
    */
}
