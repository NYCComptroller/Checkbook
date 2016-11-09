package utility;

import java.io.BufferedWriter;
import java.io.File;
import java.io.FileWriter;
import java.io.IOException;
import java.io.PrintWriter;
import java.text.SimpleDateFormat;
import java.util.Date;

public class InterfaceTextFile {
	// controls if text reporting to be done
	private static boolean doTextReporting;

	public static boolean getDoTextReporting() {
		return doTextReporting;
	}

	public static void setTextReporting(boolean inDoTextReporting) {
		doTextReporting = inDoTextReporting;
	}

	// path for result files - will be used for Excel and text files
	private static String pathResultFile;

	public static String getPathResultFile() {
		return pathResultFile;
	}

	public static void setPathResultFile(String inPath) {
		pathResultFile = inPath;
	}

	// text file name
	private static String textFileName;

	public static String getTextFileName() {
		return textFileName;
	}

	public static void setTextFileName(String inTextFileName) {
		textFileName = inTextFileName;
	}

	public static void CreateTextFile(String inTextFileName) {
		// abort if not doing text reporting
		if (InterfaceTextFile.getDoTextReporting() == false) {
			return;
		}

		// delete file if exists and creates a new file
		// add path to class
		String currentVar = getPathResultFile();

		if (currentVar == null) {
			setPathResultFile(ParseCurrentPath());
		}

		setTextFileName(inTextFileName + ".txt");

		// update path variable
		// check if file exist, delete, make new file
		File newFile = new File(getPathResultFile() + getTextFileName());

		try {
			if (newFile.exists()) {
				newFile.delete();
			}
			newFile.createNewFile();
			PrintWriter outputStream = new PrintWriter(newFile);
			SimpleDateFormat sdf = new SimpleDateFormat("MM/dd/yyyy HH:mm:ss");
			outputStream.println("File created on " + sdf.format(new Date()) + "\n" + "\n");
			outputStream.close();
		} catch (IOException ioEx) {
			// write error stuff
		}
	}

	public static void AddTestResultsToTextFile(TestMethodResults inResults) {
		// abort if not doing text reporting
		if (InterfaceTextFile.getDoTextReporting() == false) {
			return;
		}

		// check if file exist, delete, make new file
		File newFile = new File(getPathResultFile() + getTextFileName());

		try {
			if (newFile.exists()) {
				FileWriter writer = new FileWriter(newFile, true);
				BufferedWriter buffer = new BufferedWriter(writer);
				PrintWriter fileWriter = new PrintWriter(buffer);
				fileWriter.print("Test name: " + inResults.getTestName() + "\n" + "Test passed/failed: "
						+ inResults.getTestPassed() + "\n" + "Test results: " + inResults.getTestResults());
				fileWriter.close();
			}
		} catch (IOException ioEx) {
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
