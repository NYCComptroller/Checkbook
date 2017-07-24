package pages.home;

class YearType {

	static String getCurrentYear(String year) {
		switch (year) {
		case "FY2017":
			return "FY 2017 (Jul 1, 2016 - Jun 30, 2017)";
		case "FY2016":
			return "FY 2016 (Jul 1, 2015 - Jun 30, 2016)";
		case "FY2015":
			return "FY 2015 (Jul 1, 2014 - Jun 30, 2015)";
		case "FY2014":
			return "FY 2014 (Jul 1, 2013 - Jun 30, 2014)";
		case "FY2013":
			return "FY 2013 (Jul 1, 2012 - Jun 30, 2013)";
		case "FY2012":
			return "FY 2012 (Jul 1, 2011 - Jun 30, 2012)";
		case "FY2011":
			return "FY 2011 (Jul 1, 2010 - Jun 30, 2011)";
		case "CY2016":
			return "CY 2016 (Jan 1, 2016 - Dec 31, 2016)";
		case "CY2015":
			return "CY 2015 (Jan 1, 2015 - Dec 31, 2015)";
		case "CY2014":
			return "CY 2014 (Jan 1, 2014 - Dec 31, 2014)";
		case "CY2013":
			return "CY 2013 (Jan 1, 2013 - Dec 31, 2013)";
		case "CY2012":
			return "CY 2012 (Jan 1, 2012 - Dec 31, 2012)";
		case "CY2011":
			return "CY 2011 (Jan 1, 2011 - Dec 31, 2011)";
		case "CY2010":
			return "CY 2010 (Jan 1, 2010 - Dec 31, 2010)";
		default:
			return "FY 2017 (Jul 1, 2016 - Jun 30, 2017)";
		}
		
	}
	
	/*public enum CurrentYear{
		FY2017, FY2016, FY2015, FY2014, FY2013, FY2012, FY2011, CY2010, CY2011, CY2012, CY2013, CY2014, CY2015, CY2016
	}*/
}
