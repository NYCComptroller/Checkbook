package utilities;

import helpers.Driver;

import java.io.BufferedWriter;
import java.io.File;
import java.io.FileReader;
import java.io.FileWriter;
import java.io.IOException;
import java.sql.SQLException;
import java.text.DateFormat;
import java.text.SimpleDateFormat;
import java.util.Date;
import java.util.Properties;
import java.util.logging.Level;
import java.util.logging.Logger;

import org.junit.AfterClass;
import org.junit.BeforeClass;
import org.junit.Rule;
import org.junit.rules.TestRule;
import org.junit.rules.TestWatcher;
import org.junit.runner.Description;
import org.junit.runners.model.Statement;

import pages.home.HomePage;

public class NYCBaseTest{
    // class fields
    public static Properties prop;
    public static File report;
	public static BufferedWriter writer;

    @BeforeClass
    public static void Init() throws IOException, SQLException, ClassNotFoundException {
    	
        // loads properties from SupportingFiles/conf.properties
        try {
            NYCBaseTest.LoadProperties();
        } catch (IOException e) {
            e.printStackTrace();
        }

        //connect to data base
        NYCDatabaseUtil.connectToDatabase();
     

        String browserSelection = NYCBaseTest.prop.getProperty("BrowserSelection");
        String platform = NYCBaseTest.prop.getProperty("Platform");

        // Disables needless css warnings
        Logger log = Logger.getLogger("com.gargoylesoftware");
        log.setLevel(Level.OFF);

        // launches web browser
        Driver.Initialize(browserSelection, platform);

        SimpleDateFormat sdf = new SimpleDateFormat("yyyyMMddHHmmss");
        String addDate = sdf.format(new Date());

        // Setup excel reporting
        // Allows you to add yyyyMMddHHmmss to file name so each test run has
        // new file
        String ExcelFileName = NYCBaseTest.prop.getProperty("ExcelFileName");
        String DoExcelReporting = NYCBaseTest.prop.getProperty("DoExcelReporting");
        boolean boolDoExcelReporting = Boolean.parseBoolean(DoExcelReporting);
        InterfaceExcel.setExcelReporting(boolDoExcelReporting);
        InterfaceExcel.CreateExcelFile(ExcelFileName + addDate);
        
        
        String reportFile = "reportFile.html";
		DateFormat dateFormat = new SimpleDateFormat("dd-MMM-yyyy HH:mm:ss");
		Date date = new Date();
		report = new File(reportFile);
		writer = new BufferedWriter(new FileWriter(report, true));
		writer.write("<html><body>");
		writer.write("<h1>Test Execution Summary - " + dateFormat.format(date)+ "</h1>");

        System.out.println("init");
        HomePage.GoTo(NYCBaseTest.prop.getProperty("BaseUrl"));       
       
    }

    @AfterClass
    public static void Stop() throws SQLException, IOException {
    	 writer.write("</body></html>");
 		writer.close();
 		//Desktop.getDesktop().browse(report.toURI());
    	
        //NYCDatabaseUtil.closeDatabase();
        Driver.TearDown();
        System.out.println("DONE");
        
       
    }

    private static void LoadProperties() throws IOException {
        try (FileReader reader = new FileReader("src/test/resources/conf.properties")) {
            NYCBaseTest.prop = new Properties();
            NYCBaseTest.prop.load(reader);
        } catch (Exception e) {
            e.printStackTrace();
        }
    }

}
