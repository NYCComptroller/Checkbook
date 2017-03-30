package utilities;

import java.io.File;
import java.io.IOException;

import jxl.Sheet;
import jxl.Workbook;
import jxl.read.biff.BiffException;
import jxl.write.Label;
import jxl.write.WritableSheet;
import jxl.write.WritableWorkbook;
import jxl.write.WriteException;

public class InterfaceExcel {
	// controls if text reporting to be done
	private static boolean doExcelReporting;

	public static boolean getDoExcelReporting() {
		return doExcelReporting;
	}

	public static void setExcelReporting(boolean inDoExcelReporting) {
		doExcelReporting = inDoExcelReporting;
	}

	// path for result files - will be used for Excel and text files
	private static String pathResultFile;

	public static String getPathResultFile() {
		return pathResultFile;
	}

	public static void setPathResultFile(String inPath) {
		pathResultFile = inPath;
	}

	// Excel file name
	private static String excelFileName;

	public static String getExcelFileName() {
		return excelFileName;
	}

	public static void setExcelFileName(String inExcelFileName) {
		excelFileName = inExcelFileName;
	}

	public static void CreateExcelFile(String inExcelFileName) {
		// abort if not doing excel reporting
		if (InterfaceExcel.getDoExcelReporting() == false) {
			return;
		}

		// delete file if exists and creates a new file
		// add path to class
		String currentVar = getPathResultFile();

		if (currentVar == null) {
			setPathResultFile(ParseCurrentPath());
		}

		setExcelFileName(inExcelFileName + ".xls");

		// update path variable
		// check if file exist, delete, make new file
		File newFile = new File(getPathResultFile() + getExcelFileName());

		try {
			if (newFile.exists()) {
				newFile.delete();
			}

			// add worksheet 'Test Results'
			WritableWorkbook workbook = Workbook.createWorkbook(newFile);
			WritableSheet sheet = workbook.createSheet("Test Results", 0);

			// add labels: 'Test Name'; 'Test Passed/Failed'; 'Test Results'
			// uses reference column number, row
			Label testName = new Label(0, 0, "Test Name");
			sheet.addCell(testName);
			Label testPassedFailed = new Label(1, 0, "Test Passed/Failed");
			sheet.addCell(testPassedFailed);
			Label testResults = new Label(2, 0, "Test Results");
			sheet.addCell(testResults);

			workbook.write();
			workbook.close();

		} catch (IOException e) {
		} catch (WriteException ex) {
		}
	}

	public static void AddTestResultsToExcelFile(TestMethodResults inResults) {
		// abort if not doing excel reporting
		if (InterfaceExcel.getDoExcelReporting() == false) {
			return;
		}

		// check if file exist, delete, make new file
		File file = new File(getPathResultFile() + getExcelFileName());

		try {
			if (file.exists()) {
				// open workbook to read - assumes only 1 worksheet in workbook
				// "Test Results"
				Workbook workbook = Workbook.getWorkbook(file);
				Sheet sheet = workbook.getSheet(0);

				// gets the number of rows used in worksheet
				int row = sheet.getRows();

				// open workbook to write
				WritableWorkbook workBookWrite = Workbook.createWorkbook(file, workbook);
				WritableSheet sheetW = workBookWrite.getSheet(0);

				// Add test results to next empty row
				// uses reference column number, row
				//
				// first column, A/0 Test Name
				Label testName = new Label(0, row, inResults.getTestName());
				sheetW.addCell(testName);

				// 2nd column, B/1, Test Passed/Failed
				String passed = "Failed";
				if (inResults.getTestPassed()) {
					passed = "Passed";
				}

				Label testPassedFailed = new Label(1, row, passed);
				sheetW.addCell(testPassedFailed);

				// 3rd column, C/2, Test Results
				Label testResults = new Label(2, row, inResults.getTestResults());
				sheetW.addCell(testResults);

				workBookWrite.write();
				workBookWrite.close();

			}
		} catch (IOException | BiffException | WriteException e) {
			// write error stuff
		}
	}

	private static String ParseCurrentPath() {
		// parses file path so results text file will be stored under main
		// package folder
		// for this example, AutoTestProjectABC

		String currentPath = System.getProperty("user.dir") + "\\";
		// C:\Training\ProgrammingIntro\JAVA\FromScratch\Code\FromScratchJava\TestProjectABC
		// get path from hard-drive letter, in this example C to project folder,

		return currentPath;
	}
}
