package utilities;

import java.io.FileReader;
import java.io.IOException;
import java.text.SimpleDateFormat;
import java.util.Date;
import java.util.Properties;

import org.junit.AfterClass;
import org.junit.BeforeClass;

import pages.HomePage;
import utility.Driver;
import utility.InterfaceExcel;
import utility.InterfaceTextFile;

public class NYCBaseTest {	
	// class fields
	public static Properties prop;

	@BeforeClass
	public static void Init() throws IOException {

		// loads properties from SupportingFiles/conf.properties
		try {
			NYCBaseTest.LoadProperties();
		} catch (IOException e) {
			e.printStackTrace();
		}

		String browserSelection = NYCBaseTest.prop.getProperty("BrowserSelection");
		// launches web browser
		Driver.Initialize(browserSelection);

		// Setup text results text file
		// Allows you to add yyyyMMddHHmmss to file name so each test run has
		// new file
		SimpleDateFormat sdf = new SimpleDateFormat("yyyyMMddHHmmss");
		String addDate = sdf.format(new Date());

		String TextFileName = NYCBaseTest.prop.getProperty("TextFileName");
		String DoTextReporting = NYCBaseTest.prop.getProperty("DoTextReporting");
		boolean boolDoTextReporting = Boolean.parseBoolean(DoTextReporting);
		InterfaceTextFile.setTextReporting(boolDoTextReporting);
		InterfaceTextFile.CreateTextFile(TextFileName + addDate);

		// Setup excel reporting
		// Allows you to add yyyyMMddHHmmss to file name so each test run has
		// new file
		String ExcelFileName = NYCBaseTest.prop.getProperty("ExcelFileName");
		String DoExcelReporting = NYCBaseTest.prop.getProperty("DoExcelReporting");
		boolean boolDoExcelReporting = Boolean.parseBoolean(DoExcelReporting);
		InterfaceExcel.setExcelReporting(boolDoExcelReporting);
		InterfaceExcel.CreateExcelFile(ExcelFileName + addDate);
		
		HomePage.GoTo(NYCBaseTest.prop.getProperty("BaseUrl"));
		HomePage.SelectYear(NYCBaseTest.prop.getProperty("CurrentYear"));
	}
		
		
	@AfterClass
	public static void Stop() {
	    Driver.TearDown();
		System.out.println("DONE");
	}
		
	public static void LoadProperties() throws IOException {
		try (FileReader reader = new FileReader("src/SupportingFiles/conf.properties")) {
			NYCBaseTest.prop = new Properties();
			NYCBaseTest.prop.load(reader);
		} catch (Exception e) {
			e.printStackTrace();
		}
	}

}
