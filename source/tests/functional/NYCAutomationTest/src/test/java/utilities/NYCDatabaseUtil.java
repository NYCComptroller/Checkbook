package utilities;

/*
    Porting NYC_TestcaseUtil.php to Java.
    Consists of all the SQL statements we may need.
 */

import java.math.BigDecimal;
import java.sql.Connection;
import java.sql.DriverManager;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.sql.Statement;
import java.util.ArrayList;
import java.util.Calendar;
import java.util.Map;
import java.util.Properties;
import java.util.TreeMap;

public class NYCDatabaseUtil {

    private static Connection con = null;
    private static Statement stmt = null;
    private static ResultSet rs = null;
    private static String query = null;
    private static String query2 = null;

    // TODO: t.le: move to NYCBaseTest?
    // Establishes connection
    static void connectToDatabase() throws SQLException, ClassNotFoundException {
        String URL = NYCBaseTest.prop.getProperty("DBConnectionURL");
        Properties props = new Properties();
        props.setProperty("user", NYCBaseTest.prop.getProperty("DBUser"));
        props.setProperty("password", NYCBaseTest.prop.getProperty("DBPass"));
        props.setProperty("ssl", "false");
        Class.forName("org.postgresql.Driver");
        con = DriverManager.getConnection(URL, props);
    }

    // Closes connection
    static void closeDatabase() throws SQLException {
        con.close();
    }

    // Returns an array of all the years from 2009 till current
    public static Map<String, ArrayList<Integer>> getYears() throws SQLException {
        Calendar now = Calendar.getInstance();
        Map<String, ArrayList<Integer>> years = new TreeMap<>();
        ArrayList<Integer> yearValues = new ArrayList<>();
        ArrayList<Integer> yearID = new ArrayList<>();

        // Query to get years
        query = "SELECT year_value, year_id FROM ref_year "
                + "WHERE year_value > 2009 AND year_value <= " + now.get(Calendar.YEAR)
                + "ORDER BY year_value";

        try { // Runs the query
            stmt = con.createStatement();
            rs = stmt.executeQuery(query);

            // Loops through and adds query results into arrayLists
            while (rs.next()) {
                yearValues.add(rs.getInt("year_value"));
                yearID.add(rs.getInt("year_id"));
            }
        } catch (SQLException e) {
            e.printStackTrace();
        } finally {
            if (stmt != null) stmt.close();
        }

        // Add both arrayLists into map
        years.put("YEAR ID", yearID);
        years.put("YEAR VALUE", yearValues);

        return years;
    }

    private static ResultSet amountQueryHelper(char yearTypeVal) throws SQLException {
        try {
            stmt = con.createStatement();

            if (Character.toUpperCase(yearTypeVal) == 'B') {
                rs = stmt.executeQuery(query);
            } else {
                rs = stmt.executeQuery(query2);
            }
        } catch (SQLException e) {
            e.printStackTrace();
        }
        return rs;
    }

    // TODO: t.le: are we receiving only one integer, or an int[]?
    public static int getContractAmount(int year, char yearTypeVal) throws SQLException {
        query = "SELECT SUM(maximum_contract_amount) sumContractAmount "
                + "FROM agreement_snapshot WHERE document_code_id IN (5,1,2,7) "
                + "AND registered_year = " + year + " AND original_version_flag = 'Y'";

        // Selects from different table
        query2 = "SELECT SUM(maximum_contract_amount) sumContractAmount "
                + "FROM agreement_snapshot_cy WHERE document_code_id IN (5,1,2,7) "
                + "AND registered_year = " + year + " AND original_version_flag = 'Y'";


        rs = amountQueryHelper(yearTypeVal);
        return rs.getInt("sumContractAmount");
    }


    private static String formatNumber(BigDecimal num) {
        String formattedNum = null;
        String moneyChar = null;
        int count = 0;

        while (num.compareTo(new BigDecimal(999)) >= 0) {
            num = num.divide(new BigDecimal(1000));
            count++;
        }

        switch (count) {
            case 1:
                moneyChar = "K";
                break;
            case 2:
                moneyChar = "M";
                break;
            case 3:
                moneyChar = "B";
                break;
            case 4:
                moneyChar = "T";
                break;
            case 5:
                moneyChar = "Q";
                break;
            default:
                moneyChar = "";
                break;
        }

        formattedNum = num.setScale(1, BigDecimal.ROUND_HALF_UP).toString();

        return "$" + formattedNum + moneyChar;
    }
    
    private static String formatNumber2(BigDecimal num) {
        String formattedNum = null;
        String moneyChar = null;
        int count = 0;

        while (num.compareTo(new BigDecimal(999)) >= 0) {
            num = num.divide(new BigDecimal(1000));
            count++;
        }

        switch (count) {
            case 1:
                moneyChar = "K";
                break;
            case 2:
                moneyChar = "M";
                break;
            case 3:
                moneyChar = "B";
                break;
            case 4:
                moneyChar = "T";
                break;
            case 5:
                moneyChar = "Q";
                break;
            default:
                moneyChar = "";
                break;
        }

        formattedNum = num.setScale(2, BigDecimal.ROUND_HALF_UP).toString();

        return "$" + formattedNum + moneyChar;
    }
  //
    
    //Payroll Spending  widget Details count

    public static int getPayrollSpendingDetailsCount(int year,char yearTypeVal) throws SQLException {
      query = "select(  (select count(*)   from disbursement_line_item_Details  where   spending_category_id = 2 and  fiscal_year= "+ year +" )  ) aCount "; 
     		 
    rs = amountQueryHelper(yearTypeVal);
    int count = 0;
    while (rs.next()) {
        count = rs.getInt("aCount");
    }

    return count;

    }
    
    //Capital Spending  widget Details count

    public static int getCapitalSpendingDetailsCount(int year,char yearTypeVal) throws SQLException {
      query = "select(  (select count(*)   from disbursement_line_item_Details  where   spending_category_id = 3 and  fiscal_year= "+ year +" ) ) aCount";
    rs = amountQueryHelper(yearTypeVal);
    int count = 0;
    while (rs.next()) {
        count = rs.getInt("aCount");
    }

    return count;

    }
    
    public static int getCapitalSpendingContractsDetailsCount(int year,char yearTypeVal) throws SQLException {
	    query = "select(  (select count(*)   from disbursement_line_item_Details  where spending_category_id = 3 and fiscal_year= "+ year +" and contract_document_code in ( 'CT1', 'CTA1', 'POD', 'POC', 'PCC1', 'DO1','MA1','MMA1')))  aCount"; 
   		 
	 rs = amountQueryHelper(yearTypeVal);
	  int count = 0;
	  while (rs.next()) {
	      count = rs.getInt("aCount");
	  }

	  return count;
	  
	} 
    
    //MWBECapital Spending  widget Details count

    public static int getMWBECapitalSpendingDetailsCount(int year,char yearTypeVal) throws SQLException {
      query = "select(  (select count(*)   from disbursement_line_item_Details  where   spending_category_id = 3  and   minority_type_id in (2,3,4,5,9)  and fiscal_year= "+ year +" ) ) aCount";
    rs = amountQueryHelper(yearTypeVal);
    int count = 0;
    while (rs.next()) {
        count = rs.getInt("aCount");
    }

    return count;

    }
    
    public static int geMWBECapitalSpendingContractsDetailsCount(int year,char yearTypeVal) throws SQLException {
	    query = "select(  (select count(*)   from disbursement_line_item_Details  where spending_category_id = 3 and   minority_type_id in (2,3,4,5,9) and fiscal_year= "+ year +" and contract_document_code in ( 'CT1', 'CTA1', 'POD', 'POC', 'PCC1', 'DO1','MA1','MMA1')))  aCount"; 
   		 
	 rs = amountQueryHelper(yearTypeVal);
	  int count = 0;
	  while (rs.next()) {
	      count = rs.getInt("aCount");
	  }

	  return count;
	  
	} 
  //Contract Spending  widget Details count

  public static int getContractSpendingDetailsCount(int year,char yearTypeVal) throws SQLException {
    query = "select(  (select count(*)   from disbursement_line_item_Details  where   spending_category_id = 1 and  fiscal_year= "+ year +" ) + " 
  +"  (select count(*) from subcontract_spending_Details where fiscal_year = "+ year +" )  ) aCount";
   		 
  rs = amountQueryHelper(yearTypeVal);
  int count = 0;
  while (rs.next()) {
      count = rs.getInt("aCount");
  }

  return count;

  } 
  
  public static int getContractSpendingContractsDetailsCount(int year,char yearTypeVal) throws SQLException {
	   // query = "select(  (select count(*)   from disbursement_line_item_Details  where spending_category_id = 1 and fiscal_year= "+ year +" and contract_document_code in ( 'CT1', 'CTA1', 'POD', 'POC', 'PCC1', 'DO1','MA1','MMA1')))  aCount"; 
 		 query = " select(  (select count(*)   from disbursement_line_item_Details  where spending_category_id = 1"
+" and fiscal_year= "+ year +" and contract_document_code in ( 'CT1', 'CTA1', 'POD', 'POC', 'PCC1', 'DO1','MA1','MMA1')) +"
+" (select count(*) from subContract_spending_details  where fiscal_year ="+ year +" and spending_category_id = 1 "
  +"and contract_document_code in ( 'CT1', 'CTA1', 'POD', 'POC', 'PCC1', 'DO1','MA1','MMA1')))  acount";
	 rs = amountQueryHelper(yearTypeVal);
	  int count = 0;
	  while (rs.next()) {
	      count = rs.getInt("aCount");
	  }

	  return count;
	  
	} 
  ////MWBE Contract Spending  widget Details count
  public static int getMWBEContractSpendingDetailsCount(int year,char yearTypeVal) throws SQLException {
	    query = "select count(*) aCount from disbursement_line_item_Details  where   spending_category_id = 1"
	    		+ " and minority_type_id in (2,3,4,5,9)"
	    		+ "and   fiscal_year = " + year;
	   		 
	  rs = amountQueryHelper(yearTypeVal);
	  int count = 0;
	  while (rs.next()) {
	      count = rs.getInt("aCount");
	  }

	  return count;

	  } 
	  
	  public static int getMWBEContractSpendingContractsDetailsCount(int year,char yearTypeVal) throws SQLException {
		   // query = "select(  (select count(*)   from disbursement_line_item_Details  where spending_category_id = 1 and fiscal_year= "+ year +" and contract_document_code in ( 'CT1', 'CTA1', 'POD', 'POC', 'PCC1', 'DO1','MA1','MMA1')))  aCount"; 
	 		 query = "   select count(*) aCount  from disbursement_line_item_Details  where spending_category_id = 1"
	                +"  and minority_type_id in (2,3,4,5,9) and contract_document_code in ( 'CT1', 'CTA1', 'POD', 'POC', 'PCC1', 'DO1','MA1','MMA1') "
	                + "and   fiscal_year = " + year;
		 rs = amountQueryHelper(yearTypeVal);
		  int count = 0;
		  while (rs.next()) {
		      count = rs.getInt("aCount");
		  }

		  return count;
		  
		} 
	  
	  public static int getMWBEContractSpendingSubVendorsDetailsCount(int year,char yearTypeVal) throws SQLException {
		    query = "select count(*) aCount from subcontract_spending_Details  where   spending_category_id = 1"
		    		+ " and minority_type_id in (2,3,4,5,9)"
		    		+ "and   fiscal_year = " + year;
		   		 
		  rs = amountQueryHelper(yearTypeVal);
		  int count = 0;
		  while (rs.next()) {
		      count = rs.getInt("aCount");
		  }

		  return count;

		  } 
  
  //Trust agency Spending  widget Details count

  public static int getTrustAgencySpendingDetailsCount(int year,char yearTypeVal) throws SQLException {
    query = " select count(*) aCount  from disbursement_line_item_Details  where   spending_category_id = 5 "
    		+ "and   fiscal_year = " + year;

  rs = amountQueryHelper(yearTypeVal);
  int count = 0;
  while (rs.next()) {
      count = rs.getInt("aCount");
  }

  return count;

  } 
  public static int getTrustAgencySpendingContractsDetailsCount(int year,char yearTypeVal) throws SQLException {
	    query = "select count(*) aCount from disbursement_line_item_Details  where spending_category_id = 5 and  contract_document_code in ( 'CT1', 'CTA1', 'POD', 'POC', 'PCC1', 'DO1','MA1','MMA1')" 
	    		+ "and   fiscal_year = " + year;
	 rs = amountQueryHelper(yearTypeVal);
	  int count = 0;
	  while (rs.next()) {
	      count = rs.getInt("aCount");
	  }

	  return count;
	  
	} 
  
  //MWBE Trust agency Spending  widget Details count

  public static int getMWBETrustAgencySpendingDetailsCount(int year,char yearTypeVal) throws SQLException {
    query = "select count(*) aCount  from disbursement_line_item_Details  where   spending_category_id = 5  and minority_type_id in (2,3,4,5,9) "
    + "and   fiscal_year = " + year;
  rs = amountQueryHelper(yearTypeVal);
  int count = 0;
  while (rs.next()) {
      count = rs.getInt("aCount");
  }

  return count;

  } 
  public static int getMWBETrustAgencySpendingContractsDetailsCount(int year,char yearTypeVal) throws SQLException {
	    query = "select count(*)  aCount  from disbursement_line_item_Details  where spending_category_id = 5 and  minority_type_id in (2,3,4,5,9)  and contract_document_code in ( 'CT1', 'CTA1', 'POD', 'POC', 'PCC1', 'DO1','MA1','MMA1')"
	      + "and   fiscal_year = " + year;
	 rs = amountQueryHelper(yearTypeVal);
	  int count = 0;
	  while (rs.next()) {
	      count = rs.getInt("aCount");
	  }

	  return count;
	  
	} 
  
  //Other Spending  widget Details count

  public static int getOtherSpendingDetailsCount(int year,char yearTypeVal) throws SQLException {
    query = "select count(*) aCount  from disbursement_line_item_Details  where   spending_category_id = 4 "
    		  + "and   fiscal_year = " + year;
   		 
  rs = amountQueryHelper(yearTypeVal);
  int count = 0;
  while (rs.next()) {
      count = rs.getInt("aCount");
  }

  return count;

  } 
  
  //MWBE Other Spending  widget Details count

  public static int getMWBEOtherSpendingDetailsCount(int year,char yearTypeVal) throws SQLException {
    query = "select count(*)  aCount  from disbursement_line_item_Details  where   spending_category_id = 4 and  minority_type_id in (2,3,4,5,9)" 
    		 + "and   fiscal_year = " + year;
  rs = amountQueryHelper(yearTypeVal);
  int count = 0;
  while (rs.next()) {
      count = rs.getInt("aCount");
  }

  return count;

  } 
  
  //Spending details amounts
  
  public static String getTotalSpendingMWBEDetailsAmount(int year, char yearTypeVal) throws SQLException {
      query = "SELECT SUM(check_amount) sumSpendingAmt "
              + "FROM disbursement_line_item_details"
              + " WHERE  minority_type_id in (2,3,4,5,9) and  fiscal_year = " + year;
      rs = amountQueryHelper(yearTypeVal);

      BigDecimal totalSpendingAmount = new BigDecimal(0);

      while (rs.next()) {
          totalSpendingAmount = rs.getBigDecimal("sumSpendingAmt");
      }
      return formatNumber2(totalSpendingAmount);
      // .divide(new BigDecimal(1000000000)).setScale(1, BigDecimal.ROUND_HALF_UP);
  }
  
  public static String getTotalSpendingMWBESubVendorsDetailsAmount(int year, char yearTypeVal) throws SQLException {
      query = "SELECT SUM(check_amount) sumSpendingAmt "
              + "FROM subcontract_spending_details"
              + " WHERE  minority_type_id in (2,3,4,5,9) and  fiscal_year = " + year;
      rs = amountQueryHelper(yearTypeVal);

      BigDecimal totalSpendingAmount = new BigDecimal(0);

      while (rs.next()) {
          totalSpendingAmount = rs.getBigDecimal("sumSpendingAmt");
      }
      return formatNumber2(totalSpendingAmount);
      // .divide(new BigDecimal(1000000000)).setScale(1, BigDecimal.ROUND_HALF_UP);
  }
  
	public static String getPayrollSpendingMWBEDetailsAmount(int year, char yearTypeVal) throws SQLException {
		// TODO Auto-generated method stub
		 query = "SELECT SUM(check_amount) sumSpendingAmt "
	                + "FROM disbursement_line_item_details "
	                + " WHERE spending_category_id='2' and  minority_type_id in (2,3,4,5,9) and   fiscal_year = " + year;

	         rs = amountQueryHelper(yearTypeVal);

	        BigDecimal totalSpendingAmount = new BigDecimal(0);

	        while (rs.next()) {
	            totalSpendingAmount = rs.getBigDecimal("sumSpendingAmt");
	        }
	        return formatNumber2(totalSpendingAmount);
	        // .divide(new BigDecimal(1000000000)).setScale(1, BigDecimal.ROUND_HALF_UP);
	    }
	
	public static String getContractsSpendingMWBEDetailsAmount(int year, char yearTypeVal) throws SQLException {
		// TODO Auto-generated method stub
		 query = "SELECT SUM(check_amount) sumSpendingAmt "
	                + "FROM disbursement_line_item_details "
	                + " WHERE spending_category_id='1' and  minority_type_id in (2,3,4,5,9) and   fiscal_year = " + year;

	          rs = amountQueryHelper(yearTypeVal);

	        BigDecimal totalSpendingAmount = new BigDecimal(0);

	        while (rs.next()) {
	            totalSpendingAmount = rs.getBigDecimal("sumSpendingAmt");
	        }
	        return formatNumber2(totalSpendingAmount);
	        // .divide(new BigDecimal(1000000000)).setScale(1, BigDecimal.ROUND_HALF_UP);
	    }
	
	
	public static String getContractsSpendingMWBESubVendorsDetailsAmount(int year, char yearTypeVal) throws SQLException {
		// TODO Auto-generated method stub
		 query = "SELECT SUM(check_amount) sumSpendingAmt "
	                + "FROM subcontract_spending_details "
	                + " WHERE spending_category_id='1' and  minority_type_id in (2,3,4,5,9) and   fiscal_year = " + year;

	          rs = amountQueryHelper(yearTypeVal);

	        BigDecimal totalSpendingAmount = new BigDecimal(0);

	        while (rs.next()) {
	            totalSpendingAmount = rs.getBigDecimal("sumSpendingAmt");
	        }
	        return formatNumber2(totalSpendingAmount);
	        // .divide(new BigDecimal(1000000000)).setScale(1, BigDecimal.ROUND_HALF_UP);
	    }


	
	
	public static String getCapitalContractsSpendingMWBEDetailsAmount(int year, char yearTypeVal) throws SQLException {
		// TODO Auto-generated method stub
		 query = "SELECT SUM(check_amount) sumSpendingAmt "
	                + "FROM disbursement_line_item_details "
	                + " WHERE spending_category_id='3' and  minority_type_id in (2,3,4,5,9) and   fiscal_year = " + year;

	          rs = amountQueryHelper(yearTypeVal);

	        BigDecimal totalSpendingAmount = new BigDecimal(0);

	        while (rs.next()) {
	            totalSpendingAmount = rs.getBigDecimal("sumSpendingAmt");
	        }
	        return formatNumber2(totalSpendingAmount);
	        // .divide(new BigDecimal(1000000000)).setScale(1, BigDecimal.ROUND_HALF_UP);
	    }
	
	public static String getTrustAgencySpendingMWBEDetailsAmount(int year, char yearTypeVal) throws SQLException {
		// TODO Auto-generated method stub
		 query = "SELECT SUM(check_amount) sumSpendingAmt "
	                + "FROM disbursement_line_item_details "
	                + " WHERE spending_category_id='5' and  minority_type_id in (2,3,4,5,9) and  fiscal_year = " + year;

	          rs = amountQueryHelper(yearTypeVal);

	        BigDecimal totalSpendingAmount = new BigDecimal(0);

	        while (rs.next()) {
	            totalSpendingAmount = rs.getBigDecimal("sumSpendingAmt");
	        }
	        return formatNumber2(totalSpendingAmount);
	        // .divide(new BigDecimal(1000000000)).setScale(1, BigDecimal.ROUND_HALF_UP);
	    }
	
	public static String getOtherSpendingMWBEDetailsAmount(int year, char yearTypeVal) throws SQLException {
		// TODO Auto-generated method stub
		 query = "SELECT SUM(check_amount) sumSpendingAmt "
	                + "FROM disbursement_line_item_details "
	                + " WHERE spending_category_id='4' and  minority_type_id in (2,3,4,5,9) and  fiscal_year = " + year;

	          rs = amountQueryHelper(yearTypeVal);

	        BigDecimal totalSpendingAmount = new BigDecimal(0);

	        while (rs.next()) {
	            totalSpendingAmount = rs.getBigDecimal("sumSpendingAmt");
	        }
	        return formatNumber2(totalSpendingAmount);
	        // .divide(new BigDecimal(1000000000)).setScale(1, BigDecimal.ROUND_HALF_UP);
	    }
	
	//Spending Widget Contracts widget details amounts
	
  public static String getTotalSpendingMWBEContractsDetailsAmount(int year, char yearTypeVal) throws SQLException {
      query = "SELECT SUM(check_amount) sumSpendingAmt "
              + "FROM disbursement_line_item_details"
              + " WHERE   contract_document_code in( 'CT1', 'CTA1', 'POD', 'POC', 'PCC1', 'DO1','MA1','MMA1') and  minority_type_id in (2,3,4,5,9) and  fiscal_year = " + year;
      rs = amountQueryHelper(yearTypeVal);

      BigDecimal totalSpendingAmount = new BigDecimal(0);

      while (rs.next()) {
          totalSpendingAmount = rs.getBigDecimal("sumSpendingAmt");
      }
      return formatNumber2(totalSpendingAmount);
      // .divide(new BigDecimal(1000000000)).setScale(1, BigDecimal.ROUND_HALF_UP);
  }
  
	
	
	public static String getContractsSpendingMWBEContractsDetailsAmount(int year, char yearTypeVal) throws SQLException {
		// TODO Auto-generated method stub
		 query = "SELECT SUM(check_amount) sumSpendingAmt "
	                + "FROM disbursement_line_item_details "
	                + " WHERE spending_category_id='1'  and contract_document_code in( 'CT1', 'CTA1', 'POD', 'POC', 'PCC1', 'DO1','MA1','MMA1') and  minority_type_id in (2,3,4,5,9) and   fiscal_year = " + year;

	          rs = amountQueryHelper(yearTypeVal);

	        BigDecimal totalSpendingAmount = new BigDecimal(0);

	        while (rs.next()) {
	            totalSpendingAmount = rs.getBigDecimal("sumSpendingAmt");
	        }
	        return formatNumber2(totalSpendingAmount);
	        // .divide(new BigDecimal(1000000000)).setScale(1, BigDecimal.ROUND_HALF_UP);
	    }

	
	
	public static String getCapitalContractsSpendingMWBEContractsDetailsAmount(int year, char yearTypeVal) throws SQLException {
		// TODO Auto-generated method stub
		 query = "SELECT SUM(check_amount) sumSpendingAmt "
	                + "FROM disbursement_line_item_details "
	                + " WHERE spending_category_id='3' and  contract_document_code in( 'CT1', 'CTA1', 'POD', 'POC', 'PCC1', 'DO1','MA1','MMA1') and  minority_type_id in (2,3,4,5,9) and   fiscal_year = " + year;

	          rs = amountQueryHelper(yearTypeVal);

	        BigDecimal totalSpendingAmount = new BigDecimal(0);

	        while (rs.next()) {
	            totalSpendingAmount = rs.getBigDecimal("sumSpendingAmt");
	        }
	        return formatNumber2(totalSpendingAmount);
	        // .divide(new BigDecimal(1000000000)).setScale(1, BigDecimal.ROUND_HALF_UP);
	    }
	
	public static String getTrustAgencySpendingMWBEContractsDetailsAmount(int year, char yearTypeVal) throws SQLException {
		// TODO Auto-generated method stub
		 query = "SELECT SUM(check_amount) sumSpendingAmt "
	                + "FROM disbursement_line_item_details "
	                + " WHERE spending_category_id='5' and contract_document_code in( 'CT1', 'CTA1', 'POD', 'POC', 'PCC1', 'DO1','MA1','MMA1') and  minority_type_id in (2,3,4,5,9) and   fiscal_year = " + year;

	          rs = amountQueryHelper(yearTypeVal);

	        BigDecimal totalSpendingAmount = new BigDecimal(0);

	        while (rs.next()) {
	            totalSpendingAmount = rs.getBigDecimal("sumSpendingAmt");
	        }
	        return formatNumber2(totalSpendingAmount);
	        // .divide(new BigDecimal(1000000000)).setScale(1, BigDecimal.ROUND_HALF_UP);
	    }
	

  

  // Spending Amount
    public static String getSpendingAmount(int year, char yearTypeVal) throws SQLException {
        query = "SELECT SUM(check_amount) sumSpendingAmt "
                + "FROM disbursement_line_item_details"
                + " WHERE fiscal_year = " + year;

   
        rs = amountQueryHelper(yearTypeVal);

        BigDecimal totalSpendingAmount = new BigDecimal(0);

        while (rs.next()) {
            totalSpendingAmount = rs.getBigDecimal("sumSpendingAmt");
        }
        return formatNumber(totalSpendingAmount);
        // .divide(new BigDecimal(1000000000)).setScale(1, BigDecimal.ROUND_HALF_UP);
    }
    
    public static String getCapitalSpendingAmount(int year, char yearTypeVal) throws SQLException {
        query = "SELECT SUM(check_amount) sumSpendingAmt "
                + "FROM disbursement_line_item_details"
                + " WHERE  spending_category_id='3' and fiscal_year = " + year;


        rs = amountQueryHelper(yearTypeVal);

        BigDecimal totalSpendingAmount = new BigDecimal(0);

        while (rs.next()) {
            totalSpendingAmount = rs.getBigDecimal("sumSpendingAmt");
        }
        return formatNumber(totalSpendingAmount);
        // .divide(new BigDecimal(1000000000)).setScale(1, BigDecimal.ROUND_HALF_UP);
    }
    
    
    public static String getPayrollSpendingAmount(int year, char yearTypeVal) throws SQLException {
        query = "SELECT SUM(check_amount) sumSpendingAmt "
                + "FROM disbursement_line_item_details"
                + " WHERE  spending_category_id='2' and fiscal_year = " + year;


        rs = amountQueryHelper(yearTypeVal);

        BigDecimal totalSpendingAmount = new BigDecimal(0);

        while (rs.next()) {
            totalSpendingAmount = rs.getBigDecimal("sumSpendingAmt");
        }
        return formatNumber(totalSpendingAmount);
        // .divide(new BigDecimal(1000000000)).setScale(1, BigDecimal.ROUND_HALF_UP);
    }
    public static String getContractSpendingAmount(int year, char yearTypeVal) throws SQLException {
        query = "SELECT SUM(check_amount) sumSpendingAmt "
                + "FROM disbursement_line_item_details"
                + " WHERE  spending_category_id='1' and fiscal_year = " + year;


        rs = amountQueryHelper(yearTypeVal);

        BigDecimal totalSpendingAmount = new BigDecimal(0);

        while (rs.next()) {
            totalSpendingAmount = rs.getBigDecimal("sumSpendingAmt");
        }
        return formatNumber(totalSpendingAmount);
        // .divide(new BigDecimal(1000000000)).setScale(1, BigDecimal.ROUND_HALF_UP);
    }
    
    public static String getOtherSpendingAmount(int year, char yearTypeVal) throws SQLException {
        query = "SELECT SUM(check_amount) sumSpendingAmt "
                + "FROM disbursement_line_item_details"
                + " WHERE  spending_category_id='4' and fiscal_year = " + year;


        rs = amountQueryHelper(yearTypeVal);

        BigDecimal totalSpendingAmount = new BigDecimal(0);

        while (rs.next()) {
            totalSpendingAmount = rs.getBigDecimal("sumSpendingAmt");
        }
        return formatNumber(totalSpendingAmount);
        // .divide(new BigDecimal(1000000000)).setScale(1, BigDecimal.ROUND_HALF_UP);
    }
    
    public static String getTrustAgencySpendingAmount(int year, char yearTypeVal) throws SQLException {
        query = "SELECT SUM(check_amount) sumSpendingAmt "
                + "FROM disbursement_line_item_details"
                + " WHERE  spending_category_id='5' and fiscal_year = " + year;


        rs = amountQueryHelper(yearTypeVal);

        BigDecimal totalSpendingAmount = new BigDecimal(0);

        while (rs.next()) {
            totalSpendingAmount = rs.getBigDecimal("sumSpendingAmt");
        }
        return formatNumber(totalSpendingAmount);
        // .divide(new BigDecimal(1000000000)).setScale(1, BigDecimal.ROUND_HALF_UP);
    }
    
    
    
    //Spending details amounts
    
    public static String getTotalSpendingDetailsAmount(int year, char yearTypeVal) throws SQLException {
        query = "SELECT SUM(check_amount) sumSpendingAmt "
                + "FROM disbursement_line_item_details"
                + " WHERE fiscal_year = " + year;
        rs = amountQueryHelper(yearTypeVal);

        BigDecimal totalSpendingAmount = new BigDecimal(0);

        while (rs.next()) {
            totalSpendingAmount = rs.getBigDecimal("sumSpendingAmt");
        }
        return formatNumber2(totalSpendingAmount);
        // .divide(new BigDecimal(1000000000)).setScale(1, BigDecimal.ROUND_HALF_UP);
    }
    
	public static String getPayrollSpendingDetailsAmount(int year, char yearTypeVal) throws SQLException {
		// TODO Auto-generated method stub
		 query = "SELECT SUM(check_amount) sumSpendingAmt "
	                + "FROM disbursement_line_item_details "
	                + " WHERE spending_category_id='2' and  fiscal_year = " + year;

	         rs = amountQueryHelper(yearTypeVal);

	        BigDecimal totalSpendingAmount = new BigDecimal(0);

	        while (rs.next()) {
	            totalSpendingAmount = rs.getBigDecimal("sumSpendingAmt");
	        }
	        return formatNumber2(totalSpendingAmount);
	        // .divide(new BigDecimal(1000000000)).setScale(1, BigDecimal.ROUND_HALF_UP);
	    }
	
	public static String getContractsSpendingDetailsAmount(int year, char yearTypeVal) throws SQLException {
		// TODO Auto-generated method stub
		 query = "SELECT SUM(check_amount) sumSpendingAmt "
	                + "FROM disbursement_line_item_details "
	                + " WHERE spending_category_id='1' and  fiscal_year = " + year;

	          rs = amountQueryHelper(yearTypeVal);

	        BigDecimal totalSpendingAmount = new BigDecimal(0);

	        while (rs.next()) {
	            totalSpendingAmount = rs.getBigDecimal("sumSpendingAmt");
	        }
	        return formatNumber2(totalSpendingAmount);
	        // .divide(new BigDecimal(1000000000)).setScale(1, BigDecimal.ROUND_HALF_UP);
	    }

	
	
	public static String getCapitalContractsSpendingDetailsAmount(int year, char yearTypeVal) throws SQLException {
		// TODO Auto-generated method stub
		 query = "SELECT SUM(check_amount) sumSpendingAmt "
	                + "FROM disbursement_line_item_details "
	                + " WHERE spending_category_id='3' and   fiscal_year = " + year;

	          rs = amountQueryHelper(yearTypeVal);

	        BigDecimal totalSpendingAmount = new BigDecimal(0);

	        while (rs.next()) {
	            totalSpendingAmount = rs.getBigDecimal("sumSpendingAmt");
	        }
	        return formatNumber2(totalSpendingAmount);
	        // .divide(new BigDecimal(1000000000)).setScale(1, BigDecimal.ROUND_HALF_UP);
	    }
	
	public static String getTrustAgencySpendingDetailsAmount(int year, char yearTypeVal) throws SQLException {
		// TODO Auto-generated method stub
		 query = "SELECT SUM(check_amount) sumSpendingAmt "
	                + "FROM disbursement_line_item_details "
	                + " WHERE spending_category_id='5' and  fiscal_year = " + year;

	          rs = amountQueryHelper(yearTypeVal);

	        BigDecimal totalSpendingAmount = new BigDecimal(0);

	        while (rs.next()) {
	            totalSpendingAmount = rs.getBigDecimal("sumSpendingAmt");
	        }
	        return formatNumber2(totalSpendingAmount);
	        // .divide(new BigDecimal(1000000000)).setScale(1, BigDecimal.ROUND_HALF_UP);
	    }
	
	public static String getOtherSpendingDetailsAmount(int year, char yearTypeVal) throws SQLException {
		// TODO Auto-generated method stub
		 query = "SELECT SUM(check_amount) sumSpendingAmt "
	                + "FROM disbursement_line_item_details "
	                + " WHERE spending_category_id='4' and  fiscal_year = " + year;

	          rs = amountQueryHelper(yearTypeVal);

	        BigDecimal totalSpendingAmount = new BigDecimal(0);

	        while (rs.next()) {
	            totalSpendingAmount = rs.getBigDecimal("sumSpendingAmt");
	        }
	        return formatNumber2(totalSpendingAmount);
	        // .divide(new BigDecimal(1000000000)).setScale(1, BigDecimal.ROUND_HALF_UP);
	    }
	
	//Spending Widget Contracts widget details amounts
	
    public static String getTotalSpendingContractsDetailsAmount(int year, char yearTypeVal) throws SQLException {
        query = "SELECT SUM(check_amount) sumSpendingAmt "
                + "FROM disbursement_line_item_details"
                + " WHERE   contract_document_code in( 'CT1', 'CTA1', 'POD', 'POC', 'PCC1', 'DO1','MA1','MMA1') and fiscal_year = " + year;
        rs = amountQueryHelper(yearTypeVal);

        BigDecimal totalSpendingAmount = new BigDecimal(0);

        while (rs.next()) {
            totalSpendingAmount = rs.getBigDecimal("sumSpendingAmt");
        }
        return formatNumber2(totalSpendingAmount);
        // .divide(new BigDecimal(1000000000)).setScale(1, BigDecimal.ROUND_HALF_UP);
    }
    
	
	
	public static String getContractsSpendingContractsDetailsAmount(int year, char yearTypeVal) throws SQLException {
		// TODO Auto-generated method stub
		 query = "SELECT SUM(check_amount) sumSpendingAmt "
	                + "FROM disbursement_line_item_details "
	                + " WHERE spending_category_id='1'  and contract_document_code in( 'CT1', 'CTA1', 'POD', 'POC', 'PCC1', 'DO1','MA1','MMA1') and  fiscal_year = " + year;

	          rs = amountQueryHelper(yearTypeVal);

	        BigDecimal totalSpendingAmount = new BigDecimal(0);

	        while (rs.next()) {
	            totalSpendingAmount = rs.getBigDecimal("sumSpendingAmt");
	        }
	        return formatNumber2(totalSpendingAmount);
	        // .divide(new BigDecimal(1000000000)).setScale(1, BigDecimal.ROUND_HALF_UP);
	    }

	
	
	public static String getCapitalContractsSpendingContractsDetailsAmount(int year, char yearTypeVal) throws SQLException {
		// TODO Auto-generated method stub
		 query = "SELECT SUM(check_amount) sumSpendingAmt "
	                + "FROM disbursement_line_item_details "
	                + " WHERE spending_category_id='3' and  contract_document_code in( 'CT1', 'CTA1', 'POD', 'POC', 'PCC1', 'DO1','MA1','MMA1') and  fiscal_year = " + year;

	          rs = amountQueryHelper(yearTypeVal);

	        BigDecimal totalSpendingAmount = new BigDecimal(0);

	        while (rs.next()) {
	            totalSpendingAmount = rs.getBigDecimal("sumSpendingAmt");
	        }
	        return formatNumber2(totalSpendingAmount);
	        // .divide(new BigDecimal(1000000000)).setScale(1, BigDecimal.ROUND_HALF_UP);
	    }
	
	public static String getTrustAgencySpendingContractsDetailsAmount(int year, char yearTypeVal) throws SQLException {
		// TODO Auto-generated method stub
		 query = "SELECT SUM(check_amount) sumSpendingAmt "
	                + "FROM disbursement_line_item_details "
	                + " WHERE spending_category_id='5' and contract_document_code in( 'CT1', 'CTA1', 'POD', 'POC', 'PCC1', 'DO1','MA1','MMA1') and   fiscal_year = " + year;

	          rs = amountQueryHelper(yearTypeVal);

	        BigDecimal totalSpendingAmount = new BigDecimal(0);

	        while (rs.next()) {
	            totalSpendingAmount = rs.getBigDecimal("sumSpendingAmt");
	        }
	        return formatNumber2(totalSpendingAmount);
	        // .divide(new BigDecimal(1000000000)).setScale(1, BigDecimal.ROUND_HALF_UP);
	    }
	

	
	// Citywide Total Spending widget counts
    
        
        public static int getTotalSpendingAgenciesCount(int year,char yearTypeVal) throws SQLException {
            query = "SELECT COUNT(distinct agency_id) aCount from disbursement_line_item_details where fiscal_year =" + year ;
          
           rs = amountQueryHelper(yearTypeVal);
           int count = 0;
           while (rs.next()) {
               count = rs.getInt("aCount");
           }
           return count;   
           }        
           
        public static int getTotalSpendingChecksCount(int year,char yearTypeVal) throws SQLException {
            query = "SELECT COUNT(*) aCount from disbursement_line_item_details where fiscal_year =" + year ;
           // query2 = "SELECT COUNT(*) aCount from disbursement_line_item_details where fiscal_year =" + year ;  

           rs = amountQueryHelper(yearTypeVal);
           int count = 0;
           while (rs.next()) {
               count = rs.getInt("aCount");
           }        
           return count;           
        }        
        
        public static int getTotalSpendingPrimeVendorsCount(int year,char yearTypeVal) throws SQLException {
            query = "SELECT COUNT(distinct vendor_id) aCount from disbursement_line_item_details where fiscal_year =" + year ;
  
           rs = amountQueryHelper(yearTypeVal);
           int count = 0;
           while (rs.next()) {
               count = rs.getInt("aCount");
           }
         return count;           
        }        

		public static int getTotalSpendingExpCategoriesCount(int year, char yearTypeVal)  throws SQLException {
			
		       //query = "SELECT COUNT(distinct expenditure_object_id) aCount from disbursement_line_item_details where fiscal_year =" + year ;
	           query = "SELECT COUNT(distinct expenditure_object_code) aCount from disbursement_line_item_details where fiscal_year =" + year ;
	           rs = amountQueryHelper(yearTypeVal);
	           int count = 0;
	           while (rs.next()) {
	               count = rs.getInt("aCount");
	           }
	          return count;	           
	       		}  		
        
public static int getTotalSpendingContractsCount(int year,char yearTypeVal) throws SQLException {
	query =   " SELECT count( distinct ( COALESCE(master_contract_number,contract_number)  )) aCount FROM disbursement_line_item_details" 
	         +    " WHERE  contract_document_code in ( 'CT1', 'CTA1', 'POD', 'POC', 'PCC1', 'DO1','MA1','MMA1')   and fiscal_year = "+ year;
    rs = amountQueryHelper(yearTypeVal);
   int count = 0;
   while (rs.next()) {
       count = rs.getInt("aCount");
   }
   return count;
}




//Payroll Spending widget counts

public static int getPayrollSpendingAgenciesCount(int year,char yearTypeVal) throws SQLException {
    query = "SELECT COUNT(distinct agency_id) aCount from disbursement_line_item_details where spending_category_id='2'  and fiscal_year =" + year ;
  
   rs = amountQueryHelper(yearTypeVal);
   int count = 0;
   while (rs.next()) {
       count = rs.getInt("aCount");
   }
   return count;   
   }   

public static int getPayrollSpendingExpCategoriesCount(int year, char yearTypeVal)  throws SQLException {
   // query = "SELECT COUNT(distinct expenditure_object_id) aCount from disbursement_line_item_details where spending_category_id='2' and fiscal_year =" + year ;
    query = "SELECT COUNT(distinct expenditure_object_code) aCount from disbursement_line_item_details where spending_category_id='2' and fiscal_year =" + year ;
  rs = amountQueryHelper(yearTypeVal);
  int count = 0;
  while (rs.next()) {
      count = rs.getInt("aCount");
  }
 return count;	           
		}  	
   
       
//Capital Spending widget counts

public static int getCapitalSpendingChecksCount(int year,char yearTypeVal) throws SQLException {
    query = "SELECT COUNT(*) aCount from disbursement_line_item_details where spending_category_id='3' and fiscal_year =" + year ;
   // query2 = "SELECT COUNT(*) aCount from disbursement_line_item_details where fiscal_year =" + year ;  

   rs = amountQueryHelper(yearTypeVal);
   int count = 0;
   while (rs.next()) {
       count = rs.getInt("aCount");
   }        
   return count; 
}
   public static int getCapitalSpendingAgenciesCount(int year,char yearTypeVal) throws SQLException {
	    query = "SELECT COUNT(distinct agency_id) aCount from disbursement_line_item_details where spending_category_id='3'  and fiscal_year =" + year ;
	  
	   rs = amountQueryHelper(yearTypeVal);
	   int count = 0;
	   while (rs.next()) {
	       count = rs.getInt("aCount");
	   }
	   return count;   
	   }   


public static int getCapitalSpendingPrimeVendorsCount(int year,char yearTypeVal) throws SQLException {
    query = "SELECT COUNT(distinct vendor_id) aCount from disbursement_line_item_details where spending_category_id='3' and fiscal_year =" + year ;
   // query2 = "SELECT COUNT(*) aCount from disbursement_line_item_details where fiscal_year =" + year ;  
   rs = amountQueryHelper(yearTypeVal);
   int count = 0;
   while (rs.next()) {
       count = rs.getInt("aCount");
   }
 return count;           
}        

public static int getCapitalSpendingExpCategoriesCount(int year, char yearTypeVal)  throws SQLException {
	   // query = "SELECT COUNT(distinct expenditure_object_id) aCount from disbursement_line_item_details where spending_category_id='3' and fiscal_year =" + year ;
     query = "SELECT COUNT(distinct expenditure_object_code) aCount from disbursement_line_item_details where spending_category_id='3' and fiscal_year =" + year ;
       rs = amountQueryHelper(yearTypeVal);
       int count = 0;
       while (rs.next()) {
           count = rs.getInt("aCount");
       }
      return count;	           
   		}  		

public static int getCapitalSpendingContractsCount(int year,char yearTypeVal) throws SQLException {
	query =   " SELECT count( distinct ( COALESCE(master_contract_number,contract_number)  )) aCount FROM disbursement_line_item_details" 
	         +    " WHERE  contract_document_code in ( 'CT1', 'CTA1', 'POD', 'POC', 'PCC1', 'DO1','MA1','MMA1') and  spending_category_id='3'  and fiscal_year = "+ year;
rs = amountQueryHelper(yearTypeVal);
int count = 0;
while (rs.next()) {
count = rs.getInt("aCount");
}
return count;
}




//Contract Spending widget counts

public static int getContractSpendingChecksCount(int year,char yearTypeVal) throws SQLException {
query = "SELECT COUNT(*) aCount from disbursement_line_item_details where spending_category_id='1' and fiscal_year =" + year ;
// query2 = "SELECT COUNT(*) aCount from disbursement_line_item_details where fiscal_year =" + year ;  

rs = amountQueryHelper(yearTypeVal);
int count = 0;
while (rs.next()) {
count = rs.getInt("aCount");
}        
return count;           
} 
public static int getContractSpendingPrimeVendorsCount(int year,char yearTypeVal) throws SQLException {
query = "SELECT COUNT(distinct vendor_id) aCount from disbursement_line_item_details where spending_category_id='1' and fiscal_year =" + year ;
// query2 = "SELECT COUNT(*) aCount from disbursement_line_item_details where fiscal_year =" + year ;  
rs = amountQueryHelper(yearTypeVal);
int count = 0;
while (rs.next()) {
count = rs.getInt("aCount");
}
return count;           
}   

public static int getContractSpendingAgenciesCount(int year,char yearTypeVal) throws SQLException {
    query = "SELECT COUNT(distinct agency_id) aCount from disbursement_line_item_details where spending_category_id='1'  and fiscal_year =" + year ;
  
   rs = amountQueryHelper(yearTypeVal);
   int count = 0;
   while (rs.next()) {
       count = rs.getInt("aCount");
   }
   return count;   
   } 

   public static int getContractSpendingExpCategoriesCount(int year, char yearTypeVal)  throws SQLException {
 //query = "SELECT COUNT(distinct expenditure_object_id) aCount from disbursement_line_item_details where spending_category_id='1' and fiscal_year =" + year ;
 query=  "SELECT COUNT(distinct expenditure_object_code) aCount from disbursement_line_item_details where spending_category_id='1' and fiscal_year =" + year ; 
rs = amountQueryHelper(yearTypeVal);
int count = 0;
while (rs.next()) {
    count = rs.getInt("aCount");
}
return count;	           
	}  		

    public static int getContractSpendingContractsCount(int year,char yearTypeVal) throws SQLException {
query =   " SELECT count( distinct ( COALESCE(master_contract_number,contract_number)  )) aCount FROM disbursement_line_item_details" 
         +    " WHERE  contract_document_code in ( 'CT1', 'CTA1', 'POD', 'POC', 'PCC1', 'DO1','MA1','MMA1') and  spending_category_id='1'  and fiscal_year = "+ year;
rs = amountQueryHelper(yearTypeVal);
int count = 0;
while (rs.next()) {
count = rs.getInt("aCount");
}
return count;
}

//TrustAgency Spending widget counts

public static int getTrustAgencySpendingChecksCount(int year,char yearTypeVal) throws SQLException {
query = "SELECT COUNT(*) aCount from disbursement_line_item_details where spending_category_id='5' and fiscal_year =" + year ;
//query2 = "SELECT COUNT(*) aCount from disbursement_line_item_details where fiscal_year =" + year ;  

rs = amountQueryHelper(yearTypeVal);
int count = 0;
while (rs.next()) {
count = rs.getInt("aCount");
}        
return count;           
} 
public static int getTrustAgencySpendingPrimeVendorsCount(int year,char yearTypeVal) throws SQLException {
query = "SELECT COUNT(distinct vendor_id) aCount from disbursement_line_item_details where spending_category_id='5' and fiscal_year =" + year ;
//query2 = "SELECT COUNT(*) aCount from disbursement_line_item_details where fiscal_year =" + year ;  
rs = amountQueryHelper(yearTypeVal);
int count = 0;
while (rs.next()) {
count = rs.getInt("aCount");
}
return count;           
}   

public static int getTrustAgencySpendingAgenciesCount(int year,char yearTypeVal) throws SQLException {
  query = "SELECT COUNT(distinct agency_id) aCount from disbursement_line_item_details where spending_category_id='5'  and fiscal_year =" + year ;

 rs = amountQueryHelper(yearTypeVal);
 int count = 0;
 while (rs.next()) {
     count = rs.getInt("aCount");
 }
 return count;   
 } 

public static int getTrustAgencySpendingExpCategoriesCount(int year, char yearTypeVal)  throws SQLException {
//query = "SELECT COUNT(distinct expenditure_object_id) aCount from disbursement_line_item_details where spending_category_id='5' and fiscal_year =" + year ;
query = "SELECT COUNT(distinct expenditure_object_code) aCount from disbursement_line_item_details where spending_category_id='5' and fiscal_year =" + year ; 
rs = amountQueryHelper(yearTypeVal);
int count = 0;
while (rs.next()) {
  count = rs.getInt("aCount");
}
return count;	           
	}  		

public static int getTrustAgencySpendingContractsCount(int year,char yearTypeVal) throws SQLException {
//query =   "SELECT count(DISTINCT document_id) as aCount FROM"
	//	+ " aggregateon_mwbe_spending_contract WHERE type_of_year = 'B' AND spending_category_id='5' and year_id = "+ year;

query =   " SELECT count( distinct ( COALESCE(master_contract_number,contract_number)  )) aCount FROM disbursement_line_item_details" 
        +    " WHERE  contract_document_code in ( 'CT1', 'CTA1', 'POD', 'POC', 'PCC1', 'DO1','MA1','MMA1') and  spending_category_id='5'  and fiscal_year = "+ year;

rs = amountQueryHelper(yearTypeVal);
int count = 0;
while (rs.next()) {
count = rs.getInt("aCount");
}
return count;
}

//Other Spending widget counts

public static int getOtherSpendingChecksCount(int year,char yearTypeVal) throws SQLException {
query = "SELECT COUNT(*) aCount from disbursement_line_item_details where spending_category_id='4' and fiscal_year =" + year ;
//query2 = "SELECT COUNT(*) aCount from disbursement_line_item_details where fiscal_year =" + year ;  

rs = amountQueryHelper(yearTypeVal);
int count = 0;
while (rs.next()) {
count = rs.getInt("aCount");
}        
return count;           
} 
public static int getOtherSpendingPrimeVendorsCount(int year,char yearTypeVal) throws SQLException {
query = "SELECT COUNT(distinct vendor_id) aCount from disbursement_line_item_details where spending_category_id='4' and fiscal_year =" + year ;
//query2 = "SELECT COUNT(*) aCount from disbursement_line_item_details where fiscal_year =" + year ;  
rs = amountQueryHelper(yearTypeVal);
int count = 0;
while (rs.next()) {
count = rs.getInt("aCount");
}
return count;           
}   

public static int getOtherSpendingAgenciesCount(int year,char yearTypeVal) throws SQLException {
  query = "SELECT COUNT(distinct agency_id) aCount from disbursement_line_item_details where spending_category_id='4'  and fiscal_year =" + year ;

 rs = amountQueryHelper(yearTypeVal);
 int count = 0;
 while (rs.next()) {
     count = rs.getInt("aCount");
 }
 return count;   
 } 

public static int getOtherSpendingExpCategoriesCount(int year, char yearTypeVal)  throws SQLException {
//query = "SELECT COUNT(distinct expenditure_object_id) aCount from disbursement_line_item_details where spending_category_id='4' and fiscal_year =" + year ;
query = "SELECT COUNT(distinct expenditure_object_code) aCount from disbursement_line_item_details where spending_category_id='4' and fiscal_year =" + year ; 
rs = amountQueryHelper(yearTypeVal);
int count = 0;
while (rs.next()) {
  count = rs.getInt("aCount");
}
return count;	           
	}  		


//Spending  widget Details count

public static int getTotalSpendingDetailsCount(int year,char yearTypeVal) throws SQLException {
     query = "select(  (select count(*)   from disbursement_line_item_Details  where fiscal_year= "+ year +" ) + " 
   +"  (select count(*) from subcontract_spending_Details where fiscal_year = "+ year +" )  ) aCount";
    		 
  rs = amountQueryHelper(yearTypeVal);
   int count = 0;
   while (rs.next()) {
       count = rs.getInt("aCount");
   }
   return count;   
}    

public static int getTotalSpendingContractsDetailsCount(int year,char yearTypeVal) throws SQLException {
    query = "select(  (select count(*)   from disbursement_line_item_Details  where fiscal_year= "+ year +" and contract_document_code in ( 'CT1', 'CTA1', 'POD', 'POC', 'PCC1', 'DO1','MA1','MMA1')) + " 
  +"  (select count(*) from subcontract_spending_Details where fiscal_year = "+ year +" and contract_document_code in ( 'CT1', 'CTA1', 'POD', 'POC', 'PCC1', 'DO1','MA1','MMA1'))  ) aCount";
   		 
 rs = amountQueryHelper(yearTypeVal);
  int count = 0;
  while (rs.next()) {
      count = rs.getInt("aCount");
  }
  return count;  
}  

//MWBE 
// Total Spending widget counts

public static int getTotalSpendingMWBEAgenciesCount(int year,char yearTypeVal) throws SQLException {
    query = "SELECT COUNT(distinct agency_id) aCount from disbursement_line_item_details where minority_type_id in (2,3,4,5,9) and  fiscal_year =" + year ;
  
   rs = amountQueryHelper(yearTypeVal);
   int count = 0;
   while (rs.next()) {
       count = rs.getInt("aCount");
   }
   return count;   
   }        
   
public static int getTotalSpendingMWBEChecksCount(int year,char yearTypeVal) throws SQLException {
    query = "SELECT COUNT(*) aCount from disbursement_line_item_details where minority_type_id in (2,3,4,5,9)  and  fiscal_year =" + year ;
   // query2 = "SELECT COUNT(*) aCount from disbursement_line_item_details where fiscal_year =" + year ;  

   rs = amountQueryHelper(yearTypeVal);
   int count = 0;
   while (rs.next()) {
       count = rs.getInt("aCount");
   }        
   return count;           
}        

public static int getTotalSpendingMWBEPrimeVendorsCount(int year,char yearTypeVal) throws SQLException {
    query = "SELECT COUNT(distinct vendor_id) aCount from disbursement_line_item_details where minority_type_id in (2,3,4,5,9)  and  fiscal_year =" + year ;

   rs = amountQueryHelper(yearTypeVal);
   int count = 0;
   while (rs.next()) {
       count = rs.getInt("aCount");
   }
 return count;           
}        

public static int getTotalSpendingMWBEExpCategoriesCount(int year, char yearTypeVal)  throws SQLException {
	
     //  query = "SELECT COUNT(distinct expenditure_object_id) aCount from disbursement_line_item_details where minority_type_id in (2,3,4,5,9)  and  fiscal_year =" + year ;
        query = "SELECT COUNT(distinct expenditure_object_code) aCount from disbursement_line_item_details where fiscal_year =" + year ;
       rs = amountQueryHelper(yearTypeVal);
       int count = 0;
       while (rs.next()) {
           count = rs.getInt("aCount");
       }
      return count;	           
   		}  		

public static int getTotalSpendingMWBEContractsCount(int year,char yearTypeVal) throws SQLException {
query =   " SELECT count( distinct ( COALESCE(master_contract_number,contract_number)  )) aCount FROM disbursement_line_item_details" 
     +    " WHERE  contract_document_code in ( 'CT1', 'CTA1', 'POD', 'POC', 'PCC1', 'DO1','MA1','MMA1')   and  minority_type_id in (2,3,4,5,9)  and fiscal_year = "+ year;
rs = amountQueryHelper(yearTypeVal);
int count = 0;
while (rs.next()) {
count = rs.getInt("aCount");
}
return count;
}

public static int getTotalSpendingMWBEIndustriesCount(int year,char yearTypeVal) throws SQLException {
query =   " SELECT count( distinct  industry_type_id ) aCount FROM disbursement_line_item_details" 
     +    " WHERE   minority_type_id in (2,3,4,5,9)  and fiscal_year = "+ year;
rs = amountQueryHelper(yearTypeVal);
int count = 0;
while (rs.next()) {
count = rs.getInt("aCount");
}
return count;
}

public static int getTotalSpendingMWBESubVendorsCount(int year,char yearTypeVal) throws SQLException {
query =   " SELECT count( distinct  vendor_id ) aCount FROM subcontract_spending_Details" 
     +    " WHERE   minority_type_id in (2,3,4,5,9)  and fiscal_year = "+ year;
rs = amountQueryHelper(yearTypeVal);
int count = 0;
while (rs.next()) {
count = rs.getInt("aCount");
}
return count;
}




//Payroll Spending widget counts

public static int getPayrollSpendingMWBEAgenciesCount(int year,char yearTypeVal) throws SQLException {
query = "SELECT COUNT(distinct agency_id) aCount from disbursement_line_item_details where spending_category_id='2'   and minority_type_id in (2,3,4,5,9)  and fiscal_year =" + year ;

rs = amountQueryHelper(yearTypeVal);
int count = 0;
while (rs.next()) {
count = rs.getInt("aCount");
}
return count;   
}   

public static int getPayrollSpendingMWBEExpCategoriesCount(int year, char yearTypeVal)  throws SQLException {
// query = "SELECT COUNT(distinct expenditure_object_id) aCount from disbursement_line_item_details where spending_category_id='2' and fiscal_year =" + year ;
query = "SELECT COUNT(distinct expenditure_object_code) aCount from disbursement_line_item_details where spending_category_id='2' and minority_type_id in (2,3,4,5,9)  and  fiscal_year =" + year ;
rs = amountQueryHelper(yearTypeVal);
int count = 0;
while (rs.next()) {
count = rs.getInt("aCount");
}
return count;	           
}  	


//MWBE Capital Spending widget counts

public static int getCapitalSpendingMWBEChecksCount(int year,char yearTypeVal) throws SQLException {
query = "SELECT COUNT(*) aCount from disbursement_line_item_details where spending_category_id='3' and  minority_type_id in (2,3,4,5,9)  and fiscal_year =" + year ;
// query2 = "SELECT COUNT(*) aCount from disbursement_line_item_details where fiscal_year =" + year ;  

rs = amountQueryHelper(yearTypeVal);
int count = 0;
while (rs.next()) {
count = rs.getInt("aCount");
}        
return count; 
}
public static int getCapitalSpendingMWBEAgenciesCount(int year,char yearTypeVal) throws SQLException {
query = "SELECT COUNT(distinct agency_id) aCount from disbursement_line_item_details where spending_category_id='3'  and  minority_type_id in (2,3,4,5,9)  and fiscal_year =" + year ;

rs = amountQueryHelper(yearTypeVal);
int count = 0;
while (rs.next()) {
   count = rs.getInt("aCount");
}
return count;   
}   


public static int getCapitalSpendingMWBEPrimeVendorsCount(int year,char yearTypeVal) throws SQLException {
query = "SELECT COUNT(distinct vendor_id) aCount from disbursement_line_item_details where spending_category_id='3' and minority_type_id in (2,3,4,5,9)  and  fiscal_year =" + year ;
// query2 = "SELECT COUNT(*) aCount from disbursement_line_item_details where fiscal_year =" + year ;  
rs = amountQueryHelper(yearTypeVal);
int count = 0;
while (rs.next()) {
count = rs.getInt("aCount");
}
return count;           
}        

public static int getCapitalSpendingMWBEExpCategoriesCount(int year, char yearTypeVal)  throws SQLException {
query = "SELECT COUNT(distinct expenditure_object_code) aCount from disbursement_line_item_details where spending_category_id='3' and minority_type_id in (2,3,4,5,9)  and  fiscal_year =" + year ;
// query = "SELECT COUNT(distinct expenditure_object_code) aCount from disbursement_line_item_details where spending_category_id='3' and fiscal_year =" + year ;
rs = amountQueryHelper(yearTypeVal);
int count = 0;
while (rs.next()) {
   count = rs.getInt("aCount");
}
return count;	           
	}  		

public static int getCapitalSpendingMWBEContractsCount(int year,char yearTypeVal) throws SQLException {
query =   " SELECT count( distinct ( COALESCE(master_contract_number,contract_number)  )) aCount FROM disbursement_line_item_details" 
     +    " WHERE  contract_document_code in ( 'CT1', 'CTA1', 'POD', 'POC', 'PCC1', 'DO1','MA1','MMA1') and  spending_category_id='3'  and minority_type_id in (2,3,4,5,9)  and  fiscal_year = "+ year;
rs = amountQueryHelper(yearTypeVal);
int count = 0;
while (rs.next()) {
count = rs.getInt("aCount");
}
return count;
}

//MWBE Contract Spending widget counts

public static int getContractSpendingMWBEChecksCount(int year,char yearTypeVal) throws SQLException {
query = "SELECT COUNT(*) aCount from disbursement_line_item_details where spending_category_id='1' and  minority_type_id in (2,3,4,5,9)  and fiscal_year =" + year ;
//query2 = "SELECT COUNT(*) aCount from disbursement_line_item_details where fiscal_year =" + year ;  

rs = amountQueryHelper(yearTypeVal);
int count = 0;
while (rs.next()) {
count = rs.getInt("aCount");
}        
return count;           
} 
public static int getContractSpendingMWBEPrimeVendorsCount(int year,char yearTypeVal) throws SQLException {
query = "SELECT COUNT(distinct vendor_id) aCount from disbursement_line_item_details where spending_category_id='1' and minority_type_id in (2,3,4,5,9)  and  fiscal_year =" + year ;
//query2 = "SELECT COUNT(*) aCount from disbursement_line_item_details where fiscal_year =" + year ;  
rs = amountQueryHelper(yearTypeVal);
int count = 0;
while (rs.next()) {
count = rs.getInt("aCount");
}
return count;           
}   

public static int getContractSpendingMWBEAgenciesCount(int year,char yearTypeVal) throws SQLException {
query = "SELECT COUNT(distinct agency_id) aCount from disbursement_line_item_details where spending_category_id='1'  and minority_type_id in (2,3,4,5,9)  and  fiscal_year =" + year ;

rs = amountQueryHelper(yearTypeVal);
int count = 0;
while (rs.next()) {
count = rs.getInt("aCount");
}
return count;   
} 

public static int getContractSpendingMWBEExpCategoriesCount(int year, char yearTypeVal)  throws SQLException {
query = "SELECT COUNT(distinct expenditure_object_code) aCount from disbursement_line_item_details where spending_category_id='1' and  minority_type_id in (2,3,4,5,9)  and fiscal_year =" + year ;
//query=  "SELECT COUNT(distinct expenditure_object_code) aCount from disbursement_line_item_details where spending_category_id='1' and fiscal_year =" + year ; 
rs = amountQueryHelper(yearTypeVal);
int count = 0;
while (rs.next()) {
count = rs.getInt("aCount");
}
return count;	           
}  		

public static int getContractSpendingMWBEContractsCount(int year,char yearTypeVal) throws SQLException {
query =   " SELECT count( distinct ( COALESCE(master_contract_number,contract_number)  )) aCount FROM disbursement_line_item_details" 
 +    " WHERE  contract_document_code in ( 'CT1', 'CTA1', 'POD', 'POC', 'PCC1', 'DO1','MA1','MMA1') and  spending_category_id='1'  and minority_type_id in (2,3,4,5,9)  and  fiscal_year = "+ year;
rs = amountQueryHelper(yearTypeVal);
int count = 0;
while (rs.next()) {
count = rs.getInt("aCount");
}
return count;
}

// MWBE TrustAgency Spending widget counts

public static int getTrustAgencySpendingMWBEChecksCount(int year,char yearTypeVal) throws SQLException {
query = "SELECT COUNT(*) aCount from disbursement_line_item_details where spending_category_id='5' and  minority_type_id in (2,3,4,5,9)  and fiscal_year =" + year ;
//query2 = "SELECT COUNT(*) aCount from disbursement_line_item_details where fiscal_year =" + year ;  

rs = amountQueryHelper(yearTypeVal);
int count = 0;
while (rs.next()) {
count = rs.getInt("aCount");
}        
return count;           
} 
public static int getTrustAgencySpendingMWBEPrimeVendorsCount(int year,char yearTypeVal) throws SQLException {
query = "SELECT COUNT(distinct vendor_id) aCount from disbursement_line_item_details where spending_category_id='5' and  minority_type_id in (2,3,4,5,9)  and fiscal_year =" + year ;
//query2 = "SELECT COUNT(*) aCount from disbursement_line_item_details where fiscal_year =" + year ;  
rs = amountQueryHelper(yearTypeVal);
int count = 0;
while (rs.next()) {
count = rs.getInt("aCount");
}
return count;           
}   

public static int getTrustAgencySpendingMWBEAgenciesCount(int year,char yearTypeVal) throws SQLException {
query = "SELECT COUNT(distinct agency_id) aCount from disbursement_line_item_details where spending_category_id='5'  and minority_type_id in (2,3,4,5,9)  and  fiscal_year =" + year ;

rs = amountQueryHelper(yearTypeVal);
int count = 0;
while (rs.next()) {
count = rs.getInt("aCount");
}
return count;   
} 

public static int getTrustAgencySpendingMWBEExpCategoriesCount(int year, char yearTypeVal)  throws SQLException {
query = "SELECT COUNT(distinct expenditure_object_id) aCount from disbursement_line_item_details where spending_category_id='5' and minority_type_id in (2,3,4,5,9)  and  fiscal_year =" + year ;
//query = "SELECT COUNT(distinct expenditure_object_code) aCount from disbursement_line_item_details where spending_category_id='5' and fiscal_year =" + year ; 
rs = amountQueryHelper(yearTypeVal);
int count = 0;
while (rs.next()) {
count = rs.getInt("aCount");
}
return count;	           
}  		

public static int getTrustAgencySpendingMWBEContractsCount(int year,char yearTypeVal) throws SQLException {
//query =   "SELECT count(DISTINCT document_id) as aCount FROM"
//	+ " aggregateon_mwbe_spending_contract WHERE type_of_year = 'B' AND spending_category_id='5' and year_id = "+ year;

query =   " SELECT count( distinct ( COALESCE(master_contract_number,contract_number)  )) aCount FROM disbursement_line_item_details" 
+    " WHERE  contract_document_code in ( 'CT1', 'CTA1', 'POD', 'POC', 'PCC1', 'DO1','MA1','MMA1') and  spending_category_id='5'  and minority_type_id in (2,3,4,5,9)  and  fiscal_year = "+ year;

rs = amountQueryHelper(yearTypeVal);
int count = 0;
while (rs.next()) {
count = rs.getInt("aCount");
}
return count;
}

//MWBE Other Spending widget counts

public static int getOtherSpendingMWBEChecksCount(int year,char yearTypeVal) throws SQLException {
query = "SELECT COUNT(*) aCount from disbursement_line_item_details where spending_category_id='4' and minority_type_id in (2,3,4,5,9)  and fiscal_year =" + year ;
//query2 = "SELECT COUNT(*) aCount from disbursement_line_item_details where fiscal_year =" + year ;  

rs = amountQueryHelper(yearTypeVal);
int count = 0;
while (rs.next()) {
count = rs.getInt("aCount");
}        
return count;           
} 
public static int getOtherSpendingMWBEPrimeVendorsCount(int year,char yearTypeVal) throws SQLException {
query = "SELECT COUNT(distinct vendor_id) aCount from disbursement_line_item_details where spending_category_id='4' and minority_type_id in (2,3,4,5,9)  and  fiscal_year =" + year ;
//query2 = "SELECT COUNT(*) aCount from disbursement_line_item_details where fiscal_year =" + year ;  
rs = amountQueryHelper(yearTypeVal);
int count = 0;
while (rs.next()) {
count = rs.getInt("aCount");
}
return count;           
}   

public static int getOtherSpendingMWBEAgenciesCount(int year,char yearTypeVal) throws SQLException {
query = "SELECT COUNT(distinct agency_id) aCount from disbursement_line_item_details where spending_category_id='4'  and  minority_type_id in (2,3,4,5,9)  and fiscal_year =" + year ;

rs = amountQueryHelper(yearTypeVal);
int count = 0;
while (rs.next()) {
count = rs.getInt("aCount");
}
return count;   
} 

public static int getOtherSpendingMWBEExpCategoriesCount(int year, char yearTypeVal)  throws SQLException {
query = "SELECT COUNT(distinct expenditure_object_id) aCount from disbursement_line_item_details where spending_category_id='4' and minority_type_id in (2,3,4,5,9)  and  fiscal_year =" + year ;
//query = "SELECT COUNT(distinct expenditure_object_code) aCount from disbursement_line_item_details where spending_category_id='4' and fiscal_year =" + year ; 
rs = amountQueryHelper(yearTypeVal);
int count = 0;
while (rs.next()) {
count = rs.getInt("aCount");
}
return count;	           
}  		


//Spending  widget Details count

public static int getTotalSpendingMWBEDetailsCount(int year,char yearTypeVal) throws SQLException {
//query = "select(  (select count(*)   from disbursement_line_item_Details  where minority_type_id in (2,3,4,5,9)  and  fiscal_year= "+ year +" ) + " 
//+"  (select count(*) from subcontract_spending_Details where minority_type_id in (2,3,4,5,9)  and  fiscal_year = "+ year +" )  ) aCount";
	query =  "select count(*) aCount  from disbursement_line_item_Details  where minority_type_id in (2,3,4,5,9)  and  fiscal_year= " + year ; 
rs = amountQueryHelper(yearTypeVal); 
int count = 0;
while (rs.next()) {
count = rs.getInt("aCount");
}
return count;   
}    

public static int getTotalSpendingMWBEContractsDetailsCount(int year,char yearTypeVal) throws SQLException {
//query = "select(  (select count(*)   from disbursement_line_item_Details  where  minority_type_id in (2,3,4,5,9)  and fiscal_year= "+ year +" and contract_document_code in ( 'CT1', 'CTA1', 'POD', 'POC', 'PCC1', 'DO1','MA1','MMA1')) + " 
//+"  (select count(*) from subcontract_spending_Details where  minority_type_id in (2,3,4,5,9)  and fiscal_year = "+ year +" and contract_document_code in ( 'CT1', 'CTA1', 'POD', 'POC', 'PCC1', 'DO1','MA1','MMA1'))  ) aCount";
	query = "select count(*) aCount  from disbursement_line_item_Details  where  minority_type_id in (2,3,4,5,9)  and contract_document_code in ( 'CT1', 'CTA1', 'POD', 'POC', 'PCC1', 'DO1','MA1','MMA1','PO1')   and  fiscal_year =" + year; 
rs = amountQueryHelper(yearTypeVal);
int count = 0;
while (rs.next()) {
count = rs.getInt("aCount");
}
return count;  
}  

public static int getTotalSpendingMWBESubVendorsDetailsCount(int year,char yearTypeVal) throws SQLException {
	//query = "select(  (select count(*)   from disbursement_line_item_Details  where minority_type_id in (2,3,4,5,9)  and  fiscal_year= "+ year +" ) + " 
	//+"  (select count(*) from subcontract_spending_Details where minority_type_id in (2,3,4,5,9)  and  fiscal_year = "+ year +" )  ) aCount";
		query =  "select count(*) aCount  from subcontract_spending_Details  where minority_type_id in (2,3,4,5,9)  and  fiscal_year= " + year ; 
	rs = amountQueryHelper(yearTypeVal); 
	int count = 0;
	while (rs.next()) {
	count = rs.getInt("aCount");
	}
	return count;   
	}   

//Sub Vendors Spending


// Total Spending widget counts

public static int getSubVendorsTotalSpendingAgenciesCount(int year,char yearTypeVal) throws SQLException {
    query = "SELECT COUNT(distinct agency_id) aCount from subcontract_spending_details where fiscal_year =" + year ;
  
   rs = amountQueryHelper(yearTypeVal);
   int count = 0;
   while (rs.next()) {
       count = rs.getInt("aCount");
   }
   return count;   
   }        
   
public static int getSubVendorsTotalSpendingChecksCount(int year,char yearTypeVal) throws SQLException {
    query = "SELECT COUNT(*) aCount from subcontract_spending_details where fiscal_year =" + year ;
   // query2 = "SELECT COUNT(*) aCount from disbursement_line_item_details where fiscal_year =" + year ;  

   rs = amountQueryHelper(yearTypeVal);
   int count = 0;
   while (rs.next()) {
       count = rs.getInt("aCount");
   }        
   return count;           
}        

public static int getSubVendorsTotalSpendingPrimeVendorsCount(int year,char yearTypeVal) throws SQLException {
    query = "SELECT COUNT(distinct prime_vendor_id) aCount from subcontract_spending_details where fiscal_year =" + year ;

   rs = amountQueryHelper(yearTypeVal);
   int count = 0;
   while (rs.next()) {
       count = rs.getInt("aCount");
   }
 return count;           
}        

public static int getSubVendorsTotalSpendingSubVendorsCount(int year, char yearTypeVal)  throws SQLException {
	
       query = "SELECT COUNT(distinct vendor_id) aCount from subcontract_spending_details where fiscal_year =" + year ;
       // query = "SELECT COUNT(distinct expenditure_object_code) aCount from disbursement_line_item_details where fiscal_year =" + year ;
       rs = amountQueryHelper(yearTypeVal);
       int count = 0;
       while (rs.next()) {
           count = rs.getInt("aCount");
       }
      return count;	           
   		}  		

public static int getSubVendorsTotalSpendingSubContractsCount(int year,char yearTypeVal) throws SQLException {
query =   " SELECT count(*) aCount from (select  distinct reference_document_number,contract_number ,Sub_contract_id FROM subcontract_spending_details" 
     +    " WHERE  contract_document_code in ( 'CT1', 'CTA1', 'POD', 'POC', 'PCC1', 'DO1','MA1','MMA1')   and fiscal_year = "+ year +") a";
rs = amountQueryHelper(yearTypeVal);
int count = 0;
while (rs.next()) {
count = rs.getInt("aCount");
}
return count;
}

public static int getSubVendorsTotalSpendingSubContractsdetailsCount(int year,char yearTypeVal) throws SQLException {
query =   " select count(*) aCount from subcontract_spending_details where "
		+ " contract_document_code in ( 'CT1', 'CTA1', 'POD', 'POC', 'PCC1', 'DO1','MA1','MMA1')"
		+ "fiscal_year = "+ year ;
rs = amountQueryHelper(yearTypeVal);
int count = 0;
while (rs.next()) {
count = rs.getInt("aCount");
}
return count;
}

// Spending Amount
public static String getSubVendorsSpendingAmount(int year, char yearTypeVal) throws SQLException {
    query = "SELECT SUM(check_amount) sumSpendingAmt "
            + "FROM subcontract_spending_details"
            + " WHERE fiscal_year = " + year;


    rs = amountQueryHelper(yearTypeVal);

    BigDecimal totalSpendingAmount = new BigDecimal(0);

    while (rs.next()) {
        totalSpendingAmount = rs.getBigDecimal("sumSpendingAmt");
    }
    return formatNumber(totalSpendingAmount);
    // .divide(new BigDecimal(1000000000)).setScale(1, BigDecimal.ROUND_HALF_UP);
}

public static String getSubVendorsTotalSpendingDetailsAmount(int year, char yearTypeVal) throws SQLException {
    query = "SELECT SUM(check_amount) sumSpendingAmt "
            + "FROM subcontract_spending_details"
            + " WHERE fiscal_year = " + year;


    rs = amountQueryHelper(yearTypeVal);

    BigDecimal totalSpendingAmount = new BigDecimal(0);

    while (rs.next()) {
        totalSpendingAmount = rs.getBigDecimal("sumSpendingAmt");
    }
    return formatNumber2(totalSpendingAmount);
    // .divide(new BigDecimal(1000000000)).setScale(1, BigDecimal.ROUND_HALF_UP);
}


//MWBE Spending Amount
public static String getSpendingMWBEAmount(int year, char yearTypeVal) throws SQLException {
    query = "SELECT SUM(check_amount) sumSpendingAmt "
            + "FROM disbursement_line_item_details"
            + " WHERE minority_type_id in (2,3,4,5,9)  and  fiscal_year = " + year;


    rs = amountQueryHelper(yearTypeVal);

    BigDecimal totalSpendingAmount = new BigDecimal(0);

    while (rs.next()) {
        totalSpendingAmount = rs.getBigDecimal("sumSpendingAmt");
    }
    return formatNumber(totalSpendingAmount);
    // .divide(new BigDecimal(1000000000)).setScale(1, BigDecimal.ROUND_HALF_UP);
}

public static String getCapitalSpendingMWBEAmount(int year, char yearTypeVal) throws SQLException {
    query = "SELECT SUM(check_amount) sumSpendingAmt "
            + "FROM disbursement_line_item_details"
            + " WHERE  spending_category_id='3'  and minority_type_id in (2,3,4,5,9)  and  fiscal_year = " + year;


    rs = amountQueryHelper(yearTypeVal);

    BigDecimal totalSpendingAmount = new BigDecimal(0);

    while (rs.next()) {
        totalSpendingAmount = rs.getBigDecimal("sumSpendingAmt");
    }
    return formatNumber(totalSpendingAmount);
    // .divide(new BigDecimal(1000000000)).setScale(1, BigDecimal.ROUND_HALF_UP);
}

public static String getPayrollSpendingMWBEAmount(int year, char yearTypeVal) throws SQLException {
    query = "SELECT SUM(check_amount) sumSpendingAmt "
            + "FROM disbursement_line_item_details"
            + " WHERE  spending_category_id='2' and minority_type_id in (2,3,4,5,9)  and fiscal_year = " + year;


    rs = amountQueryHelper(yearTypeVal);

    BigDecimal totalSpendingAmount = new BigDecimal(0);

    while (rs.next()) {
        totalSpendingAmount = rs.getBigDecimal("sumSpendingAmt");
    }
    return formatNumber(totalSpendingAmount);
    // .divide(new BigDecimal(1000000000)).setScale(1, BigDecimal.ROUND_HALF_UP);
}
public static String getContractSpendingMWBEAmount(int year, char yearTypeVal) throws SQLException {
    query = "SELECT SUM(check_amount) sumSpendingAmt "
            + "FROM disbursement_line_item_details"
            + " WHERE  spending_category_id='1' and minority_type_id in (2,3,4,5,9)  and fiscal_year = " + year;


    rs = amountQueryHelper(yearTypeVal);

    BigDecimal totalSpendingAmount = new BigDecimal(0);

    while (rs.next()) {
        totalSpendingAmount = rs.getBigDecimal("sumSpendingAmt");
    }
    return formatNumber(totalSpendingAmount);
    // .divide(new BigDecimal(1000000000)).setScale(1, BigDecimal.ROUND_HALF_UP);
}

public static String getOtherSpendingMWBEAmount(int year, char yearTypeVal) throws SQLException {
    query = "SELECT SUM(check_amount) sumSpendingAmt "
            + "FROM disbursement_line_item_details"
            + " WHERE  spending_category_id='4' and minority_type_id in (2,3,4,5,9)  and fiscal_year = " + year;


    rs = amountQueryHelper(yearTypeVal);

    BigDecimal totalSpendingAmount = new BigDecimal(0);

    while (rs.next()) {
        totalSpendingAmount = rs.getBigDecimal("sumSpendingAmt");
    }
    return formatNumber(totalSpendingAmount);
    // .divide(new BigDecimal(1000000000)).setScale(1, BigDecimal.ROUND_HALF_UP);
}

public static String getTrustAgencySpendingMWBEAmount(int year, char yearTypeVal) throws SQLException {
    query = "SELECT SUM(check_amount) sumSpendingAmt "
            + "FROM disbursement_line_item_details"
            + " WHERE  spending_category_id='5' and minority_type_id in (2,3,4,5,9)  and fiscal_year = " + year;


    rs = amountQueryHelper(yearTypeVal);

    BigDecimal totalSpendingAmount = new BigDecimal(0);

    while (rs.next()) {
        totalSpendingAmount = rs.getBigDecimal("sumSpendingAmt");
    }
    return formatNumber(totalSpendingAmount);
    // .divide(new BigDecimal(1000000000)).setScale(1, BigDecimal.ROUND_HALF_UP);
}



// Sub Vendors Spending Amount
public static String getSubVendorsContractsSpendingDetailsAmount(int year, char yearTypeVal) throws SQLException {
    query = "SELECT SUM(check_amount) sumSpendingAmt "
            + "FROM subcontract_spending_details"
            + " WHERE fiscal_year = " + year;


    rs = amountQueryHelper(yearTypeVal);

    BigDecimal totalSpendingAmount = new BigDecimal(0);

    while (rs.next()) {
        totalSpendingAmount = rs.getBigDecimal("sumSpendingAmt");
    }
    return formatNumber2(totalSpendingAmount);
    // .divide(new BigDecimal(1000000000)).setScale(1, BigDecimal.ROUND_HALF_UP);
}
//Sub vendors Spending details
public static int getSubVendorsTotalSpendingDetailsCount(int year,char yearTypeVal) throws SQLException {
    query = " select count(*) aCount from subcontract_spending_Details where fiscal_year = " + year;
   		 
 rs = amountQueryHelper(yearTypeVal);
  int count = 0;
  while (rs.next()) {
      count = rs.getInt("aCount");
  }
  return count;   
}    

public static int getSubVendorsTotalSpendingSubContractsDetailsCount(int year,char yearTypeVal) throws SQLException {
   query = " select count(*) aCount from subcontract_spending_Details where contract_document_code in ( 'CT1', 'CTA1', 'POD', 'POC', 'PCC1', 'DO1','MA1','MMA1') and fiscal_year = " + year;
  		 
rs = amountQueryHelper(yearTypeVal);
 int count = 0;
 while (rs.next()) {
     count = rs.getInt("aCount");
 }
 return count;  
}


//Revenue Widgets

public static String getRevenueAmount(int year, char yearTypeVal) throws SQLException {
    query = "SELECT SUM(posting_amount) sumRevenueAmt "
            + "FROM revenue_details"
            + " WHERE budget_fiscal_year = " + year;
    rs = amountQueryHelper(yearTypeVal);
    BigDecimal totalRevenueAmount = new BigDecimal(0);
    while (rs.next()) {
        totalRevenueAmount = rs.getBigDecimal("sumRevenueAmt");
    }
    return formatNumber(totalRevenueAmount);
}

public static String getRevenuecrossYearCollectionsDetailsAmount(int year, char yearTypeVal) throws SQLException {
    query = "SELECT SUM(posting_amount) sumRevenueAmt "
            + "FROM revenue_details where fiscal_year = '2018' and "
            + "  budget_fiscal_year = " + year;

    rs = amountQueryHelper(yearTypeVal);

    BigDecimal totalRevenueAmount = new BigDecimal(0);

    while (rs.next()) {
        totalRevenueAmount = rs.getBigDecimal("sumRevenueAmt");
    }
    return formatNumber2(totalRevenueAmount);
}
public static String getRevenuecrossYearCollectionsDetailsAmount1(int year, char yearTypeVal) throws SQLException {
    query = "SELECT SUM(posting_amount) sumRevenueAmt "
            + "FROM revenue_details where fiscal_year = "+ year +" and "
            + "  budget_fiscal_year = " + year;


    rs = amountQueryHelper(yearTypeVal);

    BigDecimal totalRevenueAmount = new BigDecimal(0);

    while (rs.next()) {
        totalRevenueAmount = rs.getBigDecimal("sumRevenueAmt");
    }

    return formatNumber2(totalRevenueAmount);
   

}

public static String getRevenueDetailsAmount(int year, char yearTypeVal) throws SQLException {
	 query = "SELECT SUM(posting_amount) sumRevenueAmt "
             + "FROM revenue_details"
             + " WHERE budget_fiscal_year = " + year;


    rs = amountQueryHelper(yearTypeVal);

    BigDecimal totalRevenueAmount = new BigDecimal(0);

    while (rs.next()) {
        totalRevenueAmount = rs.getBigDecimal("sumRevenueAmt");
    }
    return formatNumber2(totalRevenueAmount);
}


public static int getRevenueAgenciesCount(int year, char yearTypeVal) throws SQLException {
    query = "SELECT COUNT(Distinct(agency_id)) aCount " +
            "FROM  revenue_budget where budget_fiscal_year= " + year ;

    rs = amountQueryHelper(yearTypeVal);
    int count = 0;
    while (rs.next()) {
        count = rs.getInt("aCount");
    }
 
    return count;
    
}
    
    public static int getRevenueCategoriesCount(int year, char yearTypeVal) throws SQLException {
        query = "SELECT COUNT(Distinct(revenue_category_id)) aCount " +
                "FROM  revenue_budget where budget_fiscal_year= " + year ;

        rs = amountQueryHelper(yearTypeVal);
        int count = 0;
        while (rs.next()) {
            count = rs.getInt("aCount");
        }
     
        return count;
}
    
    public static int getRevenueFundingclassCount(int year, char yearTypeVal) throws SQLException {
        query = "SELECT COUNT(Distinct(funding_class_code)) aCount " +
                "FROM  revenue_budget where budget_fiscal_year= " + year ;

        rs = amountQueryHelper(yearTypeVal);
        int count = 0;
        while (rs.next()) {
            count = rs.getInt("aCount");
        }
     
        return count;
}
    
    
    public static int getRevenueDetailsCount(int year, char yearTypeVal) throws SQLException {
        query = "SELECT COUNT(*) aCount " +
                "FROM  revenue_details where budget_fiscal_year= " + year ;

        rs = amountQueryHelper(yearTypeVal);
        int count = 0;
        while (rs.next()) {
            count = rs.getInt("aCount");
        }
     
        return count;
        
    }
    public static int getRevenueAgenciesCrossYearColectionsDetailsCount(int year, char yearTypeVal) throws SQLException {
        query = "SELECT count(distinct agency_id) aCount " +
                "FROM  revenue_details where fiscal_year= "+ year +" and " + 
               "  budget_fiscal_year = " + year;

        rs = amountQueryHelper(yearTypeVal);
        int count = 0;
        while (rs.next()) {
            count = rs.getInt("aCount");
        }
     
        return count;
        
    }
    public static int getRevenueFundingClassesCrossYearColectionsDetailsCount(int year, char yearTypeVal) throws SQLException {
        query = "SELECT count(distinct funding_class_code) aCount " +
                "FROM  revenue_details where fiscal_year= "+ year +" and " + 
               "  budget_fiscal_year = " + year;

        rs = amountQueryHelper(yearTypeVal);
        int count = 0;
        while (rs.next()) {
            count = rs.getInt("aCount");
        }
     
        return count;
        
    }
    public static int getRevenueCategoriesCrossYearColectionsDetailsCount(int year, char yearTypeVal) throws SQLException {
        query = "SELECT count(distinct revenue_category_id) aCount " +
                "FROM  revenue_details where fiscal_year= "+ year +" and " + 
               "  budget_fiscal_year = " + year;
        
    

        rs = amountQueryHelper(yearTypeVal);
        int count = 0;
        while (rs.next()) {
            count = rs.getInt("aCount");
        }
     
        return count;
        
    }
    
 


// Budget Widgets



public static int getBudgetAgenciesCount(int year, char yearTypeVal) throws SQLException {
    query = "SELECT COUNT(Distinct(agency_id)) aCount " +
            "FROM  budget where budget_fiscal_year= " + year ;

    rs = amountQueryHelper(yearTypeVal);
    int count = 0;
    while (rs.next()) {
        count = rs.getInt("aCount");
    }
 
    return count;
    
}
    
    public static int getBudgetExpenseCategoriesCount(int year, char yearTypeVal) throws SQLException {
        query = "SELECT COUNT(Distinct(object_class_id)) aCount " +
                "FROM  budget where budget_fiscal_year= " + year ;

        rs = amountQueryHelper(yearTypeVal);
        int count = 0;
        while (rs.next()) {
            count = rs.getInt("aCount");
        }
     
        return count;
}
    
    public static int getBudgetExpenseBudgetCategoriesCount(int year, char yearTypeVal) throws SQLException {
        query = "SELECT COUNT(Distinct(budget_code)) aCount " +
                "FROM  budget where budget_fiscal_year= " + year ;

        rs = amountQueryHelper(yearTypeVal);
        int count = 0;
        while (rs.next()) {
            count = rs.getInt("aCount");
        }
     
        return count;
}
    
    
    public static String getBudgetAmount(int year, char yearTypeVal) throws SQLException {
        query = "SELECT SUM(current_budget_amount) sumBudgetAmt "
                + "FROM budget"
                + " WHERE budget_fiscal_year = " + year;


        rs = amountQueryHelper(yearTypeVal);

        BigDecimal totalBudgetAmount = new BigDecimal(0);

        while (rs.next()) {
            totalBudgetAmount = rs.getBigDecimal("sumBudgetAmt");
        }
        return formatNumber(totalBudgetAmount);
        // .divide(new BigDecimal(1000000000)).setScale(1, BigDecimal.ROUND_HALF_UP);
    }
    
    public static String getBudgetDetailsAmount(int year, char yearTypeVal) throws SQLException {
        query = "SELECT SUM(pre_encumbered_amount + encumbered_amount+ accrued_expense_amount + cash_expense_amount + post_closing_adjustment_amount) sumBudgetAmt "
                + "FROM budget"
                + " WHERE budget_fiscal_year = " + year;


        rs = amountQueryHelper(yearTypeVal);

        BigDecimal totalBudgetAmount = new BigDecimal(0);

        while (rs.next()) {
            totalBudgetAmount = rs.getBigDecimal("sumBudgetAmt");
        }
        return formatNumber2(totalBudgetAmount);
        // .divide(new BigDecimal(1000000000)).setScale(1, BigDecimal.ROUND_HALF_UP);
    }
      
    
    
        public static int getBudgetDetailsCount(int year, char yearTypeVal) throws SQLException {
            query = "SELECT COUNT(*)  aCount " +
                    "FROM  budget where budget_fiscal_year= " + year ;

            rs = amountQueryHelper(yearTypeVal);
            int count = 0;
            while (rs.next()) {
                count = rs.getInt("aCount");
            }
         
            return count;
            
        }
    
    
 ///Payroll widget sqls
    
    public static int getPayrollAgenciesCount(int year, char yearTypeVal) throws SQLException {
        query = "SELECT COUNT(Distinct(agency_id)) aCount " +
                "FROM  payroll where fiscal_year= " + year ;

        rs = amountQueryHelper(yearTypeVal);
        int count = 0;
        while (rs.next()) {
            count = rs.getInt("aCount");
        }
        return count;
}
       
        public static int getPayrollSalCount(int year, char yearTypeVal) throws SQLException 
        {
            query = "SELECT COUNT(Distinct employee_number) aCount from ("
            		+ "SELECT latest_emp.employee_number,latest_emp.pay_date,latest_emp.fiscal_year, emp.amount_basis_id FROM "
            		+"Payroll emp JOIN ( SELECT max(pay_date) as pay_date, employee_number,fiscal_year FROM payroll where fiscal_year = "+year+" GROUP BY employee_number,fiscal_year )"
            		+"latest_emp ON latest_emp.pay_date = emp.pay_date AND latest_emp.employee_number = emp.employee_number AND latest_emp.fiscal_year = emp.fiscal_year and emp.amount_basis_id =1 ) a";

        	
           /* query = "SELECT COUNT(Distinct(employee_number)) aCount " +
                    "FROM  payroll where fiscal_year= " + year ;*/
            rs = amountQueryHelper(yearTypeVal);
            int count = 0;
            while (rs.next()) {
                count = rs.getInt("aCount");
            }
            return count;
}
        public static String getPayrollAmount(int year, char yearTypeVal) throws SQLException {
            query = "SELECT sum(gross_pay)  sumPayrollAmt "
                    + "FROM Payroll"
                    + " WHERE fiscal_year = " + year;


            rs = amountQueryHelper(yearTypeVal);

            BigDecimal totalPayrollAmount = new BigDecimal(0);

            while (rs.next()) {
                totalPayrollAmount = rs.getBigDecimal("sumPayrollAmt");
            }
            return formatNumber(totalPayrollAmount);
            // .divide(new BigDecimal(1000000000)).setScale(1, BigDecimal.ROUND_HALF_UP);
        }
        
        public static int getPayrollDetailsCount(int year, char yearTypeVal) throws SQLException {
            //query = "SELECT COUNT(*) aCount " +
                    //"FROM  payroll where fiscal_year= " + year ;            

            query = 	"select count(*) aCount from ("
        	+"SELECT employee_id , agency_id,civil_service_title, sum(base_pay)as base_pay, sum(gross_pay),"  
        	+" max(annual_salary) as annual_salary, sum(overtime_pay)as overtime_pay, sum(other_payments)as OT," 
        	+"(CASE WHEN amount_basis_id=1 THEN 'Salaried' ELSE 'Non-Salaried' END) as type_of_employment "
        	+"FROM payroll WHERE fiscal_year = "+year+" and amount_basis_id in (1,2,3)	GROUP BY 1,2,3,9 ) a";

            rs = amountQueryHelper(yearTypeVal);
            int count = 0;
            while (rs.next()) {
                count = rs.getInt("aCount");
            }
            return count;
        }   
            public static int getPayrollTitleDetailsCount(int year, char yearTypeVal) throws SQLException {
                //query = "SELECT COUNT(*) aCount " +
                        //"FROM  payroll where fiscal_year= " + year ;            

                query = 	"select count(*) aCount from ("
            	+"SELECT civil_service_title,count(distinct employee_ID),sum(gross_pay) as gross_pay_ytd ,sum(base_pay) as base_pay_ytd,"  
            	+"sum(other_payments) as other_payments_ytd ,sum(overtime_pay) as overtime_pay_ytd "
            	+"FROM payroll WHERE fiscal_year = "+year+" and amount_basis_id in (1) group by 1 ) a";

                rs = amountQueryHelper(yearTypeVal);
                int count = 0;
                while (rs.next()) {
                    count = rs.getInt("aCount");
                }
                return count;
    }
            
         
            
              
   //top navigation amounts contracts
            
            public static String getContractsTopAmount(int year, char yearTypeVal) throws SQLException {
                query = "SELECT SUM(maximum_contract_amount) aeSum " +
                        "FROM agreement_snapshot WHERE document_code_id IN (1,2,5,7)" +
                        "AND (registered_year =" + year + ")"+
                        "AND(" + year + " BETWEEN starting_year AND ending_year)";
                rs = amountQueryHelper(yearTypeVal);

                BigDecimal totalContractAmount = new BigDecimal(0);

                while (rs.next()) {
                    totalContractAmount = rs.getBigDecimal("AESum");
                }
                return formatNumber(totalContractAmount);
                // .divide(new BigDecimal(1000000000)).setScale(1, BigDecimal.ROUND_HALF_UP);
            } 
            
            public static String getContractsCurrentFYTopAmount(int year, char yearTypeVal) throws SQLException {
                query = "SELECT SUM(maximum_contract_amount) aeSum " +
                        "FROM agreement_snapshot WHERE document_code_id IN (1,2,5,7)" +
                        "AND (registered_year = " + year + ")"+
                        "AND('2018' BETWEEN starting_year AND ending_year)";
                rs = amountQueryHelper(yearTypeVal);

                BigDecimal totalContractAmount = new BigDecimal(0);

                while (rs.next()) {
                    totalContractAmount = rs.getBigDecimal("AESum");
                }
                return formatNumber(totalContractAmount);
                // .divide(new BigDecimal(1000000000)).setScale(1, BigDecimal.ROUND_HALF_UP);
            }  
            
//MWBE top navigation amounts contracts
            
            public static String getMWBEContractsTopAmount(int year, char yearTypeVal) throws SQLException {
                query = "SELECT SUM(maximum_contract_amount) aeSum " +
                        "FROM agreement_snapshot WHERE document_code_id IN (1,2,5,7)" +
                        "and minority_type_id in (2,3,4,5,9)"+
                        "AND (registered_year =" + year + ")"+
                        "AND(" + year + " BETWEEN starting_year AND ending_year)";
                rs = amountQueryHelper(yearTypeVal);

                BigDecimal totalContractAmount = new BigDecimal(0);

                while (rs.next()) {
                    totalContractAmount = rs.getBigDecimal("AESum");
                }
                return formatNumber(totalContractAmount);
                // .divide(new BigDecimal(1000000000)).setScale(1, BigDecimal.ROUND_HALF_UP);
            } 
            
            public static String getMWBEContractsCurrentFYTopAmount(int year, char yearTypeVal) throws SQLException {
                query = "SELECT SUM(maximum_contract_amount) aeSum " +
                        "FROM agreement_snapshot WHERE document_code_id IN (1,2,5,7)" +
                        "and minority_type_id in (2,3,4,5,9)"+
                        "AND (registered_year = 2018)"+
                        "AND( 2018 BETWEEN starting_year AND ending_year)";
                rs = amountQueryHelper(yearTypeVal);

                BigDecimal totalContractAmount = new BigDecimal(0);

                while (rs.next()) {
                    totalContractAmount = rs.getBigDecimal("AESum");
                }
                return formatNumber(totalContractAmount);
                // .divide(new BigDecimal(1000000000)).setScale(1, BigDecimal.ROUND_HALF_UP);
            }  
            
            
            // Active expense contracts details amounts
            public static String getAEContractsMasterContractsDetailsAmount(int year, char yearTypeVal) throws SQLException {
                query = "SELECT SUM(maximum_contract_amount) aeSum " +
                        "FROM agreement_snapshot WHERE document_code_id IN (5,6)" +
                        "AND(" + year + " BETWEEN effective_begin_year AND effective_end_year) " +
                        "AND(" + year + " BETWEEN starting_year AND ending_year)";
                rs = amountQueryHelper(yearTypeVal);

                BigDecimal totalContractAmount = new BigDecimal(0);

                while (rs.next()) {
                    totalContractAmount = rs.getBigDecimal("AESum");
                }
                return formatNumber2(totalContractAmount);
                // .divide(new BigDecimal(1000000000)).setScale(1, BigDecimal.ROUND_HALF_UP);
            }  
            
            public static String getAEContractsMasterModificationDetailsAmount(int year, char yearTypeVal) throws SQLException {
                query = "SELECT SUM(maximum_contract_amount) aeSum " +
                        "FROM agreement_snapshot WHERE document_code_id IN (5,6)" +
                        "AND(" + year + " BETWEEN effective_begin_year AND effective_end_year) " +
                        "AND(" + year + " BETWEEN starting_year AND ending_year)" +
                        "and maximum_contract_amount <> original_contract_amount";
                rs = amountQueryHelper(yearTypeVal);

                BigDecimal totalContractAmount = new BigDecimal(0);

                while (rs.next()) {
                    totalContractAmount = rs.getBigDecimal("AESum");
                }
                return formatNumber2(totalContractAmount);
                // .divide(new BigDecimal(1000000000)).setScale(1, BigDecimal.ROUND_HALF_UP);
            }  
            
            public static String getAEContractsDetailsAmount(int year, char yearTypeVal) throws SQLException {
                query = "SELECT SUM(maximum_contract_amount) aeSum " +
                        "FROM agreement_snapshot WHERE document_code_id IN (1,2)" +
                        "AND(" + year + " BETWEEN effective_begin_year AND effective_end_year) " +
                        "AND(" + year + " BETWEEN starting_year AND ending_year)";
                rs = amountQueryHelper(yearTypeVal);

                BigDecimal totalContractAmount = new BigDecimal(0);

                while (rs.next()) {
                    totalContractAmount = rs.getBigDecimal("AESum");
                }
                return formatNumber2(totalContractAmount);
                // .divide(new BigDecimal(1000000000)).setScale(1, BigDecimal.ROUND_HALF_UP);
            }
            public static String getAEContractsModificationDetailsAmount(int year, char yearTypeVal) throws SQLException {
                query = "SELECT SUM(maximum_contract_amount) aeSum " +
                        "FROM agreement_snapshot WHERE document_code_id IN (1,2)" +
                        "AND(" + year + " BETWEEN effective_begin_year AND effective_end_year) " +
                        "AND(" + year + " BETWEEN starting_year AND ending_year)" +
                         "and maximum_contract_amount <> original_contract_amount";
                rs = amountQueryHelper(yearTypeVal);

                BigDecimal totalContractAmount = new BigDecimal(0);

                while (rs.next()) {
                    totalContractAmount = rs.getBigDecimal("AESum");
                }
                return formatNumber2(totalContractAmount);
                // .divide(new BigDecimal(1000000000)).setScale(1, BigDecimal.ROUND_HALF_UP);
            }
            
            public static String getAEContractsAllDetailsAmount(int year, char yearTypeVal) throws SQLException {
                query = "SELECT SUM(maximum_contract_amount) aeSum " +
                        "FROM agreement_snapshot WHERE document_code_id IN (1,2,5)" +
                        "AND(" + year + " BETWEEN effective_begin_year AND effective_end_year) " +
                        "AND(" + year + " BETWEEN starting_year AND ending_year)";
                rs = amountQueryHelper(yearTypeVal);

                BigDecimal totalContractAmount = new BigDecimal(0);

                while (rs.next()) {
                    totalContractAmount = rs.getBigDecimal("AESum");
                }
                return formatNumber2(totalContractAmount);
                // .divide(new BigDecimal(1000000000)).setScale(1, BigDecimal.ROUND_HALF_UP);
            }


         // MWBE Active expense contracts details amounts
            public static String getMWBEAEContractsMasterContractsDetailsAmount(int year, char yearTypeVal) throws SQLException {
                query = "SELECT SUM(maximum_contract_amount) aeSum " +
                        "FROM agreement_snapshot WHERE document_code_id IN (5,6)  and minority_type_id in (2,3,4,5,9) " +
                        "AND(" + year + " BETWEEN effective_begin_year AND effective_end_year) " +
                        "AND(" + year + " BETWEEN starting_year AND ending_year)";
                rs = amountQueryHelper(yearTypeVal);

                BigDecimal totalContractAmount = new BigDecimal(0);

                while (rs.next()) {
                    totalContractAmount = rs.getBigDecimal("AESum");
                }
                return formatNumber2(totalContractAmount);
                // .divide(new BigDecimal(1000000000)).setScale(1, BigDecimal.ROUND_HALF_UP);
            }

            public static String getMWBEAEContractsMasterModificationDetailsAmount(int year, char yearTypeVal) throws SQLException {
                query = "SELECT SUM(maximum_contract_amount) aeSum " +
                        "FROM agreement_snapshot WHERE document_code_id IN (5,6)  and minority_type_id in (2,3,4,5,9) " +
                        "AND(" + year + " BETWEEN effective_begin_year AND effective_end_year) " +
                        "AND(" + year + " BETWEEN starting_year AND ending_year)" +
                        "and maximum_contract_amount <> original_contract_amount";
                rs = amountQueryHelper(yearTypeVal);

                BigDecimal totalContractAmount = new BigDecimal(0);

                while (rs.next()) {
                    totalContractAmount = rs.getBigDecimal("AESum");
                }
                return formatNumber2(totalContractAmount);
                // .divide(new BigDecimal(1000000000)).setScale(1, BigDecimal.ROUND_HALF_UP);
            }

            public static String getMWBEAEContractsDetailsAmount(int year, char yearTypeVal) throws SQLException {
                query = "SELECT SUM(maximum_contract_amount) aeSum " +
                        "FROM agreement_snapshot WHERE document_code_id IN (1,2)  and minority_type_id in (2,3,4,5,9) " +
                        "AND(" + year + " BETWEEN effective_begin_year AND effective_end_year) " +
                        "AND(" + year + " BETWEEN starting_year AND ending_year)";
                rs = amountQueryHelper(yearTypeVal);

                BigDecimal totalContractAmount = new BigDecimal(0);

                while (rs.next()) {
                    totalContractAmount = rs.getBigDecimal("AESum");
                }
                return formatNumber2(totalContractAmount);
                // .divide(new BigDecimal(1000000000)).setScale(1, BigDecimal.ROUND_HALF_UP);
            }
            public static String getMWBEAEContractsModificationDetailsAmount(int year, char yearTypeVal) throws SQLException {
                query = "SELECT SUM(maximum_contract_amount) aeSum " +
                        "FROM agreement_snapshot WHERE document_code_id IN (1,2)  and minority_type_id in (2,3,4,5,9) " +
                        "AND(" + year + " BETWEEN effective_begin_year AND effective_end_year) " +
                        "AND(" + year + " BETWEEN starting_year AND ending_year)" +
                         "and maximum_contract_amount <> original_contract_amount";
                rs = amountQueryHelper(yearTypeVal);

                BigDecimal totalContractAmount = new BigDecimal(0);

                while (rs.next()) {
                    totalContractAmount = rs.getBigDecimal("AESum");
                }
                return formatNumber2(totalContractAmount);
                // .divide(new BigDecimal(1000000000)).setScale(1, BigDecimal.ROUND_HALF_UP);
            }

            public static String getMWBEAEContractsAllDetailsAmount(int year, char yearTypeVal) throws SQLException {
                query = "SELECT SUM(maximum_contract_amount) aeSum " +
                        "FROM agreement_snapshot WHERE document_code_id IN (1,2,5)  and minority_type_id in (2,3,4,5,9) " +
                        "AND(" + year + " BETWEEN effective_begin_year AND effective_end_year) " +
                        "AND(" + year + " BETWEEN starting_year AND ending_year)";
                rs = amountQueryHelper(yearTypeVal);

                BigDecimal totalContractAmount = new BigDecimal(0);

                while (rs.next()) {
                    totalContractAmount = rs.getBigDecimal("AESum");
                }
                return formatNumber2(totalContractAmount);
                // .divide(new BigDecimal(1000000000)).setScale(1, BigDecimal.ROUND_HALF_UP);
            }  
            

            //Registered Expense Contracts Details amounts
            

            public static String getREContractsMasterContractsDetailsAmount(int year, char yearTypeVal) throws SQLException {
                query = "SELECT SUM(maximum_contract_amount) aeSum " +
                        "FROM agreement_snapshot WHERE document_code_id IN (5,6)" +
                        "AND (registered_year =" + year + ")"+
                        "AND(" + year + " BETWEEN starting_year AND ending_year)";
                rs = amountQueryHelper(yearTypeVal);

                BigDecimal totalContractAmount = new BigDecimal(0);

                while (rs.next()) {
                    totalContractAmount = rs.getBigDecimal("AESum");
                }
                return formatNumber2(totalContractAmount);
                // .divide(new BigDecimal(1000000000)).setScale(1, BigDecimal.ROUND_HALF_UP);
            }  
            
            public static String getREContractsMasterModificationDetailsAmount(int year, char yearTypeVal) throws SQLException {
                query = "SELECT SUM(maximum_contract_amount) aeSum " +
                        "FROM agreement_snapshot WHERE document_code_id IN (5,6)" +
                        "AND (registered_year =" + year + ")"+
                        "AND(" + year + " BETWEEN starting_year AND ending_year)" +
                        "and maximum_contract_amount <> original_contract_amount";
                rs = amountQueryHelper(yearTypeVal);

                BigDecimal totalContractAmount = new BigDecimal(0);

                while (rs.next()) {
                    totalContractAmount = rs.getBigDecimal("AESum");
                }
                return formatNumber2(totalContractAmount);
                // .divide(new BigDecimal(1000000000)).setScale(1, BigDecimal.ROUND_HALF_UP);
            }  
            
            public static String getREContractsDetailsAmount(int year, char yearTypeVal) throws SQLException {
                query = "SELECT SUM(maximum_contract_amount) aeSum " +
                        "FROM agreement_snapshot WHERE document_code_id IN (1,2)" +
                        "AND (registered_year =" + year + ")"+
                        "AND(" + year + " BETWEEN starting_year AND ending_year)";
                rs = amountQueryHelper(yearTypeVal);

                BigDecimal totalContractAmount = new BigDecimal(0);

                while (rs.next()) {
                    totalContractAmount = rs.getBigDecimal("AESum");
                }
                return formatNumber2(totalContractAmount);
                // .divide(new BigDecimal(1000000000)).setScale(1, BigDecimal.ROUND_HALF_UP);
            }
            public static String getREContractsModificationDetailsAmount(int year, char yearTypeVal) throws SQLException {
                query = "SELECT SUM(maximum_contract_amount) AESum " +
                        "FROM agreement_snapshot WHERE document_code_id IN (1,2)" +
                        "AND (registered_year =" + year + ")"+
                        "AND(" + year + " BETWEEN starting_year AND ending_year)" +
                         "and maximum_contract_amount <> original_contract_amount";
                rs = amountQueryHelper(yearTypeVal);

                BigDecimal totalContractAmount = new BigDecimal(0);

                while (rs.next()) {
                    totalContractAmount = rs.getBigDecimal("AESum");
                }
                return formatNumber2(totalContractAmount);
                // .divide(new BigDecimal(1000000000)).setScale(1, BigDecimal.ROUND_HALF_UP);
            }
            
            public static String getREContractsAllDetailsAmount(int year, char yearTypeVal) throws SQLException {
                query = "SELECT SUM(maximum_contract_amount) aeSum " +
                        "FROM agreement_snapshot WHERE document_code_id IN (1,2,5)" +
                        "AND (registered_year =" + year + ")"+
                        "AND(" + year + " BETWEEN starting_year AND ending_year)";
                rs = amountQueryHelper(yearTypeVal);

                BigDecimal totalContractAmount = new BigDecimal(0);

                while (rs.next()) {
                    totalContractAmount = rs.getBigDecimal("AESum");
                }
                return formatNumber2(totalContractAmount);
                // .divide(new BigDecimal(1000000000)).setScale(1, BigDecimal.ROUND_HALF_UP);
            }

//MWBE Registered Expense Contracts Details amounts


            public static String getMWBEREContractsMasterContractsDetailsAmount(int year, char yearTypeVal) throws SQLException {
                query = "SELECT SUM(maximum_contract_amount) aeSum " +
                        "FROM agreement_snapshot WHERE document_code_id IN (5,6) and minority_type_id in (2,3,4,5,9)" +
                        "AND (registered_year =" + year + ")"+
                        "AND(" + year + " BETWEEN starting_year AND ending_year)";
                rs = amountQueryHelper(yearTypeVal);

                BigDecimal totalContractAmount = new BigDecimal(0);

                while (rs.next()) {
                    totalContractAmount = rs.getBigDecimal("AESum");
                }
                return formatNumber2(totalContractAmount);
                // .divide(new BigDecimal(1000000000)).setScale(1, BigDecimal.ROUND_HALF_UP);
            }

            public static String getMWBEREContractsMasterModificationDetailsAmount(int year, char yearTypeVal) throws SQLException {
                query = "SELECT SUM(maximum_contract_amount) aeSum " +
                        "FROM agreement_snapshot WHERE document_code_id IN (5,6)  and minority_type_id in (2,3,4,5,9)" +
                        "AND (registered_year =" + year + ")"+
                        "AND(" + year + " BETWEEN starting_year AND ending_year)" +
                        "and maximum_contract_amount <> original_contract_amount";
                rs = amountQueryHelper(yearTypeVal);

                BigDecimal totalContractAmount = new BigDecimal(0);

                while (rs.next()) {
                    totalContractAmount = rs.getBigDecimal("AESum");
                }
                return formatNumber2(totalContractAmount);
                // .divide(new BigDecimal(1000000000)).setScale(1, BigDecimal.ROUND_HALF_UP);
            }

            public static String getMWBEREContractsDetailsAmount(int year, char yearTypeVal) throws SQLException {
                query = "SELECT SUM(maximum_contract_amount) aeSum " +
                        "FROM agreement_snapshot WHERE document_code_id IN (1,2) and minority_type_id in (2,3,4,5,9)" +
                        "AND (registered_year =" + year + ")"+
                        "AND(" + year + " BETWEEN starting_year AND ending_year)";
                rs = amountQueryHelper(yearTypeVal);

                BigDecimal totalContractAmount = new BigDecimal(0);

                while (rs.next()) {
                    totalContractAmount = rs.getBigDecimal("AESum");
                }
                return formatNumber2(totalContractAmount);
                // .divide(new BigDecimal(1000000000)).setScale(1, BigDecimal.ROUND_HALF_UP);
            }
            public static String getMWBEREContractsModificationDetailsAmount(int year, char yearTypeVal) throws SQLException {
                query = "SELECT SUM(maximum_contract_amount) AESum " +
                        "FROM agreement_snapshot WHERE document_code_id IN (1,2) and minority_type_id in (2,3,4,5,9)" +
                        "AND (registered_year =" + year + ")"+
                        "AND(" + year + " BETWEEN starting_year AND ending_year)" +
                         "and maximum_contract_amount <> original_contract_amount";
                rs = amountQueryHelper(yearTypeVal);

                BigDecimal totalContractAmount = new BigDecimal(0);

                while (rs.next()) {
                    totalContractAmount = rs.getBigDecimal("AESum");
                }
                return formatNumber2(totalContractAmount);
                // .divide(new BigDecimal(1000000000)).setScale(1, BigDecimal.ROUND_HALF_UP);
            }

            public static String getMWBEREContractsAllDetailsAmount(int year, char yearTypeVal) throws SQLException {
                query = "SELECT SUM(maximum_contract_amount) aeSum " +
                        "FROM agreement_snapshot WHERE document_code_id IN (1,2,5) and minority_type_id in (2,3,4,5,9)" +
                        "AND (registered_year =" + year + ")"+
                        "AND(" + year + " BETWEEN starting_year AND ending_year)";
                rs = amountQueryHelper(yearTypeVal);

                BigDecimal totalContractAmount = new BigDecimal(0);

                while (rs.next()) {
                    totalContractAmount = rs.getBigDecimal("AESum");
                }
                return formatNumber2(totalContractAmount);
                // .divide(new BigDecimal(1000000000)).setScale(1, BigDecimal.ROUND_HALF_UP);
            }  
            
            
            
            // Active REvenue contracts details amounts  
            public static String getARContractsDetailsAmount(int year, char yearTypeVal) throws SQLException {
                query = "SELECT SUM(maximum_contract_amount) ARSum " +
                        "FROM agreement_snapshot WHERE document_code_id IN (7)" +
                        "AND(" + year + " BETWEEN effective_begin_year AND effective_end_year) " +
                        "AND(" + year + " BETWEEN starting_year AND ending_year)";
                rs = amountQueryHelper(yearTypeVal);

                BigDecimal totalContractAmount = new BigDecimal(0);

                while (rs.next()) {
                    totalContractAmount = rs.getBigDecimal("ARSum");
                }
                return formatNumber2(totalContractAmount);
                // .divide(new BigDecimal(1000000000)).setScale(1, BigDecimal.ROUND_HALF_UP);
            } 
            
            public static String getARContractsModificationDetailsAmount(int year, char yearTypeVal) throws SQLException {
                
                query = "SELECT SUM(maximum_contract_amount) ARSum " +
                        "FROM agreement_snapshot WHERE document_code_id IN (7)" +
                        "AND(" + year + " BETWEEN effective_begin_year AND effective_end_year) " +
                        "AND(" + year + " BETWEEN starting_year AND ending_year)" +
                         "and maximum_contract_amount <> original_contract_amount";
                rs = amountQueryHelper(yearTypeVal);

                BigDecimal totalContractAmount = new BigDecimal(0);

                while (rs.next()) {
                    totalContractAmount = rs.getBigDecimal("ARSum");
                }
               return formatNumber2(totalContractAmount);
               // return totalContractAmount.toString();
               
            }
            
            // MWBE Active REvenue contracts details amounts
            public static String getMWBEARContractsDetailsAmount(int year, char yearTypeVal) throws SQLException {
                query = "SELECT SUM(maximum_contract_amount) ARSum " +
                        "FROM agreement_snapshot WHERE document_code_id IN (7) and minority_type_id in (2,3,4,5,9)" +
                        "AND(" + year + " BETWEEN effective_begin_year AND effective_end_year) " +
                        "AND(" + year + " BETWEEN starting_year AND ending_year)";
                rs = amountQueryHelper(yearTypeVal);

                BigDecimal totalContractAmount = new BigDecimal(0);

                while (rs.next()) {
                    totalContractAmount = rs.getBigDecimal("ARSum");
                }
                return formatNumber2(totalContractAmount);
                // .divide(new BigDecimal(1000000000)).setScale(1, BigDecimal.ROUND_HALF_UP);
            }

            public static String getMWBEARContractsModificationDetailsAmount(int year, char yearTypeVal) throws SQLException {

                query = "SELECT SUM(maximum_contract_amount) ARSum " +
                        "FROM agreement_snapshot WHERE document_code_id IN (7) and minority_type_id in (2,3,4,5,9)" +
                        "AND(" + year + " BETWEEN effective_begin_year AND effective_end_year) " +
                        "AND(" + year + " BETWEEN starting_year AND ending_year)" +
                         "and maximum_contract_amount <> original_contract_amount";
                rs = amountQueryHelper(yearTypeVal);

                BigDecimal totalContractAmount = new BigDecimal(0);

                while (rs.next()) {
                    totalContractAmount = rs.getBigDecimal("ARSum");
                }
               return formatNumber2(totalContractAmount);
               // return totalContractAmount.toString();

            }



            // Registered Revenue  contracts details amounts  
            public static String getRRContractsDetailsAmount(int year, char yearTypeVal) throws SQLException {
                query = "SELECT SUM(maximum_contract_amount) ARSum " +
                        "FROM agreement_snapshot WHERE document_code_id IN (7)" +
                        "AND (registered_year =" + year + ")"+
                        "AND(" + year + " BETWEEN starting_year AND ending_year)";
                rs = amountQueryHelper(yearTypeVal);

                BigDecimal totalContractAmount = new BigDecimal(0);

                while (rs.next()) {
                    totalContractAmount = rs.getBigDecimal("ARSum");
                }
                return formatNumber2(totalContractAmount);
                // .divide(new BigDecimal(1000000000)).setScale(1, BigDecimal.ROUND_HALF_UP);
            } 
            
            public static String getRRContractsModificationDetailsAmount(int year, char yearTypeVal) throws SQLException {
                       
                query = "SELECT SUM(maximum_contract_amount) ARSum " +
                        "FROM agreement_snapshot WHERE document_code_id IN (7)" +
                        "AND (registered_year =" + year + ")"+
                        "AND(" + year + " BETWEEN starting_year AND ending_year)" +
                         "and maximum_contract_amount <> original_contract_amount";
                rs = amountQueryHelper(yearTypeVal);

                BigDecimal totalContractAmount = new BigDecimal(0);

                while (rs.next()) {
                    totalContractAmount = rs.getBigDecimal("ARSum");
                }
               return formatNumber2(totalContractAmount);
               // return totalContractAmount.toString();

            }

         // MWBE Registered Revenue  contracts details amounts
            public static String getMWBERRContractsDetailsAmount(int year, char yearTypeVal) throws SQLException {
                query = "SELECT SUM(maximum_contract_amount) ARSum " +
                        "FROM agreement_snapshot WHERE document_code_id IN (7)  and minority_type_id in (2,3,4,5,9)" +
                        "AND (registered_year =" + year + ")"+
                        "AND(" + year + " BETWEEN starting_year AND ending_year)";
                rs = amountQueryHelper(yearTypeVal);

                BigDecimal totalContractAmount = new BigDecimal(0);

                while (rs.next()) {
                    totalContractAmount = rs.getBigDecimal("ARSum");
                }
                return formatNumber2(totalContractAmount);
                // .divide(new BigDecimal(1000000000)).setScale(1, BigDecimal.ROUND_HALF_UP);
            }

            public static String getMWBERRContractsModificationDetailsAmount(int year, char yearTypeVal) throws SQLException {

                query = "SELECT SUM(maximum_contract_amount) ARSum " +
                        "FROM agreement_snapshot WHERE document_code_id IN (7)  and minority_type_id in (2,3,4,5,9)" +
                        "AND (registered_year =" + year + ")"+
                        "AND(" + year + " BETWEEN starting_year AND ending_year)" +
                         "and maximum_contract_amount <> original_contract_amount";
                rs = amountQueryHelper(yearTypeVal);

                BigDecimal totalContractAmount = new BigDecimal(0);

                while (rs.next()) {
                    totalContractAmount = rs.getBigDecimal("ARSum");
                }
               return formatNumber2(totalContractAmount);
               // return totalContractAmount.toString();
               
            }  
            
            

    //Active Expense contracts widget counts
            
            public static String getAEContractsAmount(int year, char yearTypeVal) throws SQLException {
                query = "SELECT SUM(maximum_contract_amount) AESum " +
                        "FROM agreement_snapshot WHERE document_code_id IN (1, 2, 5)" +
                        "AND(" + year + " BETWEEN effective_begin_year AND effective_end_year) " +
                        "AND(" + year + " BETWEEN starting_year AND ending_year)";

                rs = amountQueryHelper(yearTypeVal);

                BigDecimal totalContractAmount = new BigDecimal(0);

                while (rs.next()) {
                    totalContractAmount = rs.getBigDecimal("AESum");
                }
                return formatNumber(totalContractAmount);
                // .divide(new BigDecimal(1000000000)).setScale(1, BigDecimal.ROUND_HALF_UP);
            } 
            

            public static int getContractsAECount(int year, char yearTypeVal) throws SQLException {
                query = "SELECT COUNT(*) aCount " +
                        "FROM agreement_snapshot WHERE document_code_id IN (1, 2, 5)" +
                        "AND(" + year + " BETWEEN effective_begin_year AND effective_end_year) " +
                        "AND(" + year + " BETWEEN starting_year AND ending_year)";
                rs = amountQueryHelper(yearTypeVal);
                int count = 0;
                while (rs.next()) {
                    count = rs.getInt("aCount");
                }
                return count;
                }    
            
            
            
               
            public static int getAEContractsAgenciesCount(int year,char yearTypeVal) throws SQLException {
                query = "select  count(distinct agency_id ) aCount  from agreement_snapshot"
                		+ "   where  document_code_id in (1,2,5) "
                		+ "and (" + year + " between effective_begin_year and effective_end_year)"
                		+ "and (" + year + " between starting_year and ending_year)";
               rs = amountQueryHelper(yearTypeVal);
               int count = 0;
               while (rs.next()) {
                   count = rs.getInt("aCount");
               }
               return count;
            }
           
            public static int getAEContractsPrimeVendorsCount(int year,char yearTypeVal) throws SQLException {
                query = "select  count(distinct vendor_id ) aCount  from agreement_snapshot"
                		+ "   where  document_code_id in (1,2,5) "
                		+ "and (" + year + " between effective_begin_year and effective_end_year)"
                		+ "and (" + year + " between starting_year and ending_year)";
               rs = amountQueryHelper(yearTypeVal);
               int count = 0;
               while (rs.next()) {
                   count = rs.getInt("aCount");
               }
               return count;
            }
            public static int getAEContractsAwardMethodsCount(int year,char yearTypeVal) throws SQLException {
                query = "select  count(distinct award_method_id ) aCount  from agreement_snapshot"
                		+ "   where  document_code_id in (1,2,5) "
                		+ "and ("+ year +" between effective_begin_year and effective_end_year)"
                		+ "and ("+ year +" between starting_year and ending_year)";
               rs = amountQueryHelper(yearTypeVal);
               int count = 0;
               while (rs.next()) {
                   count = rs.getInt("aCount");
               }
               return count;
            }
            public static int getAEContractsIndustriesCount(int year,char yearTypeVal) throws SQLException {
                query = "select  count(distinct contract_number ) aCount  from agreement_snapshot"
                		+ "   where  document_code_id in (1,2,5) "
                		+ "and ("+ year +" between effective_begin_year and effective_end_year)"
                		+ "and ("+ year +" between starting_year and ending_year)";
               rs = amountQueryHelper(yearTypeVal);
               int count = 0;
               while (rs.next()) {
                   count = rs.getInt("aCount");
               }
               return count;
            } 
            
            public static int getAEContractsSizeCount(int year,char yearTypeVal) throws SQLException {
                query = "select  count(distinct contract_number ) aCount  from agreement_snapshot"
                		+ "   where  document_code_id in (1,2,5) "
                		+ "and ("+ year +" between effective_begin_year and effective_end_year)"
                		+ "and ("+ year +" between starting_year and ending_year)";
               rs = amountQueryHelper(yearTypeVal);
               int count = 0;
               while (rs.next()) {
                   count = rs.getInt("aCount");
               }
               return count;
            }
            public static int getAEContractsCount(int year,char yearTypeVal) throws SQLException {
                query = "select  count(distinct contract_number ) aCount  from agreement_snapshot"
                		+ "   where  document_code_id in (1,2) "
                		+ "and ("+ year +" between effective_begin_year and effective_end_year)"
                		+ "and ("+ year +" between starting_year and ending_year)";
               rs = amountQueryHelper(yearTypeVal);
               int count = 0;
               while (rs.next()) {
                   count = rs.getInt("aCount");
               }
               return count;
            }
            public static int getAEMasterContractsCount(int year,char yearTypeVal) throws SQLException {
                query = "select  count(distinct contract_number ) aCount  from agreement_snapshot"
                		+ "   where  document_code_id in (5,6) "
                		+ "and ("+ year +" between effective_begin_year and effective_end_year)"
                		+ "and ("+ year +" between starting_year and ending_year)";
               rs = amountQueryHelper(yearTypeVal);
               int count = 0;
               while (rs.next()) {
                   count = rs.getInt("aCount");
               }
               return count;
            }
            public static int getAEMasterContractsModificationCount(int year,char yearTypeVal) throws SQLException {
                query = "select  count(distinct contract_number ) aCount  from agreement_snapshot"
                		+ "   where  document_code_id in (5,6) "
                		+ "and ("+ year +" between effective_begin_year and effective_end_year)"
                		+ "and ("+ year +" between starting_year and ending_year)"
                		+ "and maximum_contract_amount <> original_contract_amount";
               rs = amountQueryHelper(yearTypeVal);
               int count = 0;
               while (rs.next()) {
                   count = rs.getInt("aCount");
               }
               return count;
            }
            public static int getAEContractsModificationCount(int year,char yearTypeVal) throws SQLException {
                query = "select  count(distinct contract_number ) aCount  from agreement_snapshot"
                		+ "   where  document_code_id in (1,2) "
                		+ "and ("+ year +" between effective_begin_year and effective_end_year)"
                		+ "and ("+ year +" between starting_year and ending_year)"
                		+ "and maximum_contract_amount <> original_contract_amount";
               rs = amountQueryHelper(yearTypeVal);
               int count = 0;
               while (rs.next()) {
                   count = rs.getInt("aCount");
               }
               return count;
            }
            
//MWBE Active Expense contracts widget counts
            
            public static String getMWBEAEContractsAmount(int year, char yearTypeVal) throws SQLException {
                query = "SELECT SUM(maximum_contract_amount) AESum " +
                        "FROM agreement_snapshot WHERE document_code_id IN (1, 2, 5)" +
                		"and minority_type_id in (2,3,4,5,9)"+
                        "AND(" + year + " BETWEEN effective_begin_year AND effective_end_year) " +
                        "AND(" + year + " BETWEEN starting_year AND ending_year)";

                rs = amountQueryHelper(yearTypeVal);

                BigDecimal totalContractAmount = new BigDecimal(0);

                while (rs.next()) {
                    totalContractAmount = rs.getBigDecimal("AESum");
                }
                return formatNumber(totalContractAmount);
                // .divide(new BigDecimal(1000000000)).setScale(1, BigDecimal.ROUND_HALF_UP);
            } 
            

            public static int getMWBEContractsAECount(int year, char yearTypeVal) throws SQLException {
                query = "SELECT COUNT(*) aCount " +
                        "FROM agreement_snapshot WHERE document_code_id IN (1, 2, 5)" +
                        "and minority_type_id in (2,3,4,5,9)"+
                        "AND(" + year + " BETWEEN effective_begin_year AND effective_end_year) " +
                        "AND(" + year + " BETWEEN starting_year AND ending_year)";
                rs = amountQueryHelper(yearTypeVal);
                int count = 0;
                while (rs.next()) {
                    count = rs.getInt("aCount");
                }
                return count;
                }    
            
            
            
               
            public static int getMWBEAEContractsAgenciesCount(int year,char yearTypeVal) throws SQLException {
                query = "select  count(distinct agency_id ) aCount  from agreement_snapshot"
                		+ "   where  document_code_id in (1,2,5) "
                		+"and minority_type_id in (2,3,4,5,9)"
                		+ "and ("+ year +" between effective_begin_year and effective_end_year)"
                		+ "and ("+ year +" between starting_year and ending_year)";
               rs = amountQueryHelper(yearTypeVal);
               int count = 0;
               while (rs.next()) {
                   count = rs.getInt("aCount");
               }
               return count;
            }
           
            public static int getMWBEAEContractsPrimeVendorsCount(int year,char yearTypeVal) throws SQLException {
                query = "select  count(distinct vendor_id ) aCount  from agreement_snapshot"
                		+ "   where  document_code_id in (1,2,5) "
                		+"and minority_type_id in (2,3,4,5,9)"
                		+ "and ("+ year +" between effective_begin_year and effective_end_year)"
                		+ "and ("+ year +" between starting_year and ending_year)";
               rs = amountQueryHelper(yearTypeVal);
               int count = 0;
               while (rs.next()) {
                   count = rs.getInt("aCount");
               }
               return count;
            }
            public static int getMWBEAEContractsAwardMethodsCount(int year,char yearTypeVal) throws SQLException {
                query = "select  count(distinct award_method_id ) aCount  from agreement_snapshot"
                		+ "   where  document_code_id in (1,2,5) "
                		+"and minority_type_id in (2,3,4,5,9)"
                		+ "and ("+ year +" between effective_begin_year and effective_end_year)"
                		+ "and ("+ year +" between starting_year and ending_year)";
               rs = amountQueryHelper(yearTypeVal);
               int count = 0;
               while (rs.next()) {
                   count = rs.getInt("aCount");
               }
               return count;
            }
            public static int getMWBEAEContractsIndustriesCount(int year,char yearTypeVal) throws SQLException {
                query = "select  count(distinct contract_number ) aCount  from agreement_snapshot"
                		+ "   where  document_code_id in (1,2,5) "
                		+"and minority_type_id in (2,3,4,5,9)"
                		+ "and ("+ year +" between effective_begin_year and effective_end_year)"
                		+ "and ("+ year +" between starting_year and ending_year)";
               rs = amountQueryHelper(yearTypeVal);
               int count = 0;
               while (rs.next()) {
                   count = rs.getInt("aCount");
               }
               return count;
            } 
            
            public static int getMWBEAEContractsSizeCount(int year,char yearTypeVal) throws SQLException {
                query = "select  count(distinct contract_number ) aCount  from agreement_snapshot"
                		+ "   where  document_code_id in (1,2,5) "
                		+"and minority_type_id in (2,3,4,5,9)"
                		+ "and ("+ year +" between effective_begin_year and effective_end_year)"
                		+ "and ("+ year +" between starting_year and ending_year)";
               rs = amountQueryHelper(yearTypeVal);
               int count = 0;
               while (rs.next()) {
                   count = rs.getInt("aCount");
               }
               return count;
            }
            public static int getMWBEAEContractsCount(int year,char yearTypeVal) throws SQLException {
                query = "select  count(distinct contract_number ) aCount  from agreement_snapshot"
                		+ "   where  document_code_id in (1,2) "
                		+"and minority_type_id in (2,3,4,5,9)"
                		+ "and ("+ year +" between effective_begin_year and effective_end_year)"
                		+ "and ("+ year +" between starting_year and ending_year)";
               rs = amountQueryHelper(yearTypeVal);
               int count = 0;
               while (rs.next()) {
                   count = rs.getInt("aCount");
               }
               return count;
            }
            public static int getMWBEAEMasterContractsCount(int year,char yearTypeVal) throws SQLException {
                query = "select  count(distinct contract_number ) aCount  from agreement_snapshot"
                		+ "   where  document_code_id in (5,6) "
                		+"and minority_type_id in (2,3,4,5,9)"
                		+ "and ("+ year +" between effective_begin_year and effective_end_year)"
                		+ "and ("+ year +" between starting_year and ending_year)";
               rs = amountQueryHelper(yearTypeVal);
               int count = 0;
               while (rs.next()) {
                   count = rs.getInt("aCount");
               }
               return count;
            }
            public static int getMWBEAEMasterContractsModificationCount(int year,char yearTypeVal) throws SQLException {
                query = "select  count(distinct contract_number ) aCount  from agreement_snapshot"
                		+ "   where  document_code_id in (5,6) "
                		+"and minority_type_id in (2,3,4,5,9)"
                		+ "and ("+ year +" between effective_begin_year and effective_end_year)"
                		+ "and ("+ year +" between starting_year and ending_year)"
                		+ "and maximum_contract_amount <> original_contract_amount";
               rs = amountQueryHelper(yearTypeVal);
               int count = 0;
               while (rs.next()) {
                   count = rs.getInt("aCount");
               }
               return count;
            }
            public static int getMWBEAEContractsModificationCount(int year,char yearTypeVal) throws SQLException {
                query = "select  count(distinct contract_number ) aCount  from agreement_snapshot"
                		+ "   where  document_code_id in (1,2) "
                		+"and minority_type_id in (2,3,4,5,9)"
                		+ "and ("+ year +" between effective_begin_year and effective_end_year)"
                		+ "and ("+ year +" between starting_year and ending_year)"
                		+ "and maximum_contract_amount <> original_contract_amount";
               rs = amountQueryHelper(yearTypeVal);
               int count = 0;
               while (rs.next()) {
                   count = rs.getInt("aCount");
               }
               return count;
            }
            
          //Registered Expense contracts widget counts
            
            public static String getREContractsAmount(int year, char yearTypeVal) throws SQLException {
                query = "SELECT SUM(maximum_contract_amount) AESum " +
                        "FROM agreement_snapshot WHERE document_code_id IN (1, 2, 5)" +
                        "and (registered_year = " + year + ")" +
                        "AND(" + year + " BETWEEN starting_year AND ending_year)";

                rs = amountQueryHelper(yearTypeVal);

                BigDecimal totalContractAmount = new BigDecimal(0);

                while (rs.next()) {
                    totalContractAmount = rs.getBigDecimal("AESum");
                }
                return formatNumber(totalContractAmount);
                // .divide(new BigDecimal(1000000000)).setScale(1, BigDecimal.ROUND_HALF_UP);
            } 
            

            public static int getContractsRECount(int year, char yearTypeVal) throws SQLException {
                query = "SELECT COUNT(*) aCount " +
                        "FROM agreement_snapshot WHERE document_code_id IN (1, 2, 5)" +
                      "and (registered_year = " + year + ")" +
                        "AND(" + year + " BETWEEN starting_year AND ending_year)";
                rs = amountQueryHelper(yearTypeVal);
                int count = 0;
                while (rs.next()) {
                    count = rs.getInt("aCount");
                }
                return count;
                }  
            
            
            
            public static int getREContractsAgenciesCount(int year,char yearTypeVal) throws SQLException {
                query = "select  count(distinct agency_id ) aCount  from agreement_snapshot"
                		+ "   where  document_code_id in (1,2,5) "
                		+ "and registered_year = "+ year +""
                		+ "and ("+ year +" between starting_year and ending_year)";
               rs = amountQueryHelper(yearTypeVal);
               int count = 0;
               while (rs.next()) {
                   count = rs.getInt("aCount");
               }
               return count;
            }
           
            public static int getREContractsPrimeVendorsCount(int year,char yearTypeVal) throws SQLException {
                query = "select  count(distinct vendor_id ) aCount  from agreement_snapshot"
                		+ "   where  document_code_id in (1,2,5) "
                		+ "and registered_year = "+ year +""
                		+ "and ("+ year +" between starting_year and ending_year)";
               rs = amountQueryHelper(yearTypeVal);
               int count = 0;
               while (rs.next()) {
                   count = rs.getInt("aCount");
               }
               return count;
            }
            public static int getREContractsAwardMethodsCount(int year,char yearTypeVal) throws SQLException {
                query = "select  count(distinct award_method_id ) aCount  from agreement_snapshot"
                		+ "   where  document_code_id in (1,2,5) "
                		+ "and registered_year = "+ year +""
                		+ "and ("+ year +" between starting_year and ending_year)";
               rs = amountQueryHelper(yearTypeVal);
               int count = 0;
               while (rs.next()) {
                   count = rs.getInt("aCount");
               }
               return count;
            }
            public static int getREContractsIndustriesCount(int year,char yearTypeVal) throws SQLException {
                query = "select  count(distinct contract_number ) aCount  from agreement_snapshot"
                		+ "   where  document_code_id in (1,2,5) "
                		+ "and registered_year = "+ year +""
                		+ "and ("+ year +" between starting_year and ending_year)";
               rs = amountQueryHelper(yearTypeVal);
               int count = 0;
               while (rs.next()) {
                   count = rs.getInt("aCount");
               }
               return count;
            } 
            
            public static int getREContractsSizeCount(int year,char yearTypeVal) throws SQLException {
                query = "select  count(distinct contract_number ) aCount  from agreement_snapshot"
                		+ "   where  document_code_id in (1,2,5) "
                		+ "and registered_year = "+ year +""
                		+ "and ("+ year +" between starting_year and ending_year)";
               rs = amountQueryHelper(yearTypeVal);
               int count = 0;
               while (rs.next()) {
                   count = rs.getInt("aCount");
               }
               return count;
            }
            public static int getREContractsCount(int year,char yearTypeVal) throws SQLException {
                query = "select  count(distinct contract_number ) aCount  from agreement_snapshot"
                		+ "   where  document_code_id in (1,2) "
                		+ "and registered_year = "+ year +""
                		+ "and ("+ year +" between starting_year and ending_year)";
               rs = amountQueryHelper(yearTypeVal);
               int count = 0;
               while (rs.next()) {
                   count = rs.getInt("aCount");
               }
               return count;
            }
            public static int getREContractsMasterCount(int year,char yearTypeVal) throws SQLException {
                query = "select  count(distinct contract_number ) aCount  from agreement_snapshot"
                		+ "   where  document_code_id in (5,6) "
                		+ "and registered_year = "+ year +""
                		+ "and ("+ year +" between starting_year and ending_year)";
               rs = amountQueryHelper(yearTypeVal);
               int count = 0;
               while (rs.next()) {
                   count = rs.getInt("aCount");
               }
               return count;
            }
            public static int getREContractsMasterModificationsCount(int year,char yearTypeVal) throws SQLException {
                query = "select  count(distinct contract_number ) aCount  from agreement_snapshot"
                		+ "   where  document_code_id in (5,6) "
                		+ "and registered_year = "+ year +""
                		+ "and ("+ year +" between starting_year and ending_year)"
                		+ "and maximum_contract_amount <> original_contract_amount";
               rs = amountQueryHelper(yearTypeVal);
               int count = 0;
               while (rs.next()) {
                   count = rs.getInt("aCount");
               }
               return count;
            }
            public static int getREContractsModificationsCount(int year,char yearTypeVal) throws SQLException {
                query = "select  count(distinct contract_number ) aCount  from agreement_snapshot"
                		+ "   where  document_code_id in (1,2) "
                		+ "and registered_year = "+ year +""
                		+ "and ("+ year +" between starting_year and ending_year)"
                		+ "and maximum_contract_amount <> original_contract_amount";
               rs = amountQueryHelper(yearTypeVal);
               int count = 0;
               while (rs.next()) {
                   count = rs.getInt("aCount");
               }
               return count;
            }

 //MWBe Registered Expense contracts widget counts

            public static String getMWBEREContractsAmount(int year, char yearTypeVal) throws SQLException {
                query = "SELECT SUM(maximum_contract_amount) AESum " +
                        "FROM agreement_snapshot WHERE document_code_id IN (1, 2, 5) and minority_type_id in (2,3,4,5,9)" +
                        "and (registered_year = " + year + ")" +
                        "AND(" + year + " BETWEEN starting_year AND ending_year)";

                rs = amountQueryHelper(yearTypeVal);

                BigDecimal totalContractAmount = new BigDecimal(0);

                while (rs.next()) {
                    totalContractAmount = rs.getBigDecimal("AESum");
                }
                return formatNumber(totalContractAmount);
                // .divide(new BigDecimal(1000000000)).setScale(1, BigDecimal.ROUND_HALF_UP);
            }


            public static int getMWBEContractsRECount(int year, char yearTypeVal) throws SQLException {
                query = "SELECT COUNT(*) aCount " +
                        "FROM agreement_snapshot WHERE document_code_id IN (1, 2, 5) and minority_type_id in (2,3,4,5,9)" +
                      "and (registered_year = " + year + ")" +
                        "AND(" + year + " BETWEEN starting_year AND ending_year)";
                rs = amountQueryHelper(yearTypeVal);
                int count = 0;
                while (rs.next()) {
                    count = rs.getInt("aCount");
                }
                return count;
                }



            public static int getMWBEREContractsAgenciesCount(int year,char yearTypeVal) throws SQLException {
                query = "select  count(distinct agency_id ) aCount  from agreement_snapshot"
                		+ "   where  document_code_id in (1,2,5) and minority_type_id in (2,3,4,5,9) "
                		+ "and registered_year = "+ year +""
                		+ "and ("+ year +" between starting_year and ending_year)";
               rs = amountQueryHelper(yearTypeVal);
               int count = 0;
               while (rs.next()) {
                   count = rs.getInt("aCount");
               }
               return count;
            }

            public static int getMWBEREContractsPrimeVendorsCount(int year,char yearTypeVal) throws SQLException {
                query = "select  count(distinct vendor_id ) aCount  from agreement_snapshot"
                		+ "   where  document_code_id in (1,2,5) and minority_type_id in (2,3,4,5,9) "
                		+ "and registered_year = "+ year +""
                		+ "and ("+ year +" between starting_year and ending_year)";
               rs = amountQueryHelper(yearTypeVal);
               int count = 0;
               while (rs.next()) {
                   count = rs.getInt("aCount");
               }
               return count;
            }
            public static int getMWBEREContractsAwardMethodsCount(int year,char yearTypeVal) throws SQLException {
                query = "select  count(distinct award_method_id ) aCount  from agreement_snapshot"
                		+ "   where  document_code_id in (1,2,5) and minority_type_id in (2,3,4,5,9) "
                		+ "and registered_year = "+ year +""
                		+ "and ("+ year +" between starting_year and ending_year)";
               rs = amountQueryHelper(yearTypeVal);
               int count = 0;
               while (rs.next()) {
                   count = rs.getInt("aCount");
               }
               return count;
            }
            public static int getMWBEREContractsIndustriesCount(int year,char yearTypeVal) throws SQLException {
                query = "select  count(distinct contract_number ) aCount  from agreement_snapshot"
                		+ "   where  document_code_id in (1,2,5) and minority_type_id in (2,3,4,5,9) "
                		+ "and registered_year = "+ year +""
                		+ "and ("+ year +" between starting_year and ending_year)";
               rs = amountQueryHelper(yearTypeVal);
               int count = 0;
               while (rs.next()) {
                   count = rs.getInt("aCount");
               }
               return count;
            }

            public static int getMWBEREContractsSizeCount(int year,char yearTypeVal) throws SQLException {
                query = "select  count(distinct contract_number ) aCount  from agreement_snapshot"
                		+ "   where  document_code_id in (1,2,5) and minority_type_id in (2,3,4,5,9) "
                		+ "and registered_year = "+ year +""
                		+ "and ("+ year +" between starting_year and ending_year)";
               rs = amountQueryHelper(yearTypeVal);
               int count = 0;
               while (rs.next()) {
                   count = rs.getInt("aCount");
               }
               return count;
            }
            public static int getMWBEREContractsCount(int year,char yearTypeVal) throws SQLException {
                query = "select  count(distinct contract_number ) aCount  from agreement_snapshot"
                		+ "   where  document_code_id in (1,2) and minority_type_id in (2,3,4,5,9) "
                		+ "and registered_year = "+ year +""
                		+ "and ("+ year +" between starting_year and ending_year)";
               rs = amountQueryHelper(yearTypeVal);
               int count = 0;
               while (rs.next()) {
                   count = rs.getInt("aCount");
               }
               return count;
            }
            public static int getMWBEREContractsMasterCount(int year,char yearTypeVal) throws SQLException {
                query = "select  count(distinct contract_number ) aCount  from agreement_snapshot"
                		+ "   where  document_code_id in (5,6) and minority_type_id in (2,3,4,5,9) "
                		+ "and registered_year = "+ year +" "
                		+ "and ("+ year +" between starting_year and ending_year)";
               rs = amountQueryHelper(yearTypeVal);
               int count = 0;
               while (rs.next()) {
                   count = rs.getInt("aCount");
               }
               return count;
            }
            public static int getMWBEREContractsMasterModificationsCount(int year,char yearTypeVal) throws SQLException {
                query = "select  count(distinct contract_number ) aCount  from agreement_snapshot"
                		+ "   where  document_code_id in (5,6) and minority_type_id in (2,3,4,5,9) "
                		+ "and registered_year = "+ year +" "
                		+ "and ("+ year +" between starting_year and ending_year)"
                		+ "and maximum_contract_amount <> original_contract_amount";
               rs = amountQueryHelper(yearTypeVal);
               int count = 0;
               while (rs.next()) {
                   count = rs.getInt("aCount");
               }
               return count;
            }
            public static int getMWBEREContractsModificationsCount(int year,char yearTypeVal) throws SQLException {
                query = "select  count(distinct contract_number ) aCount  from agreement_snapshot"
                		+ "   where  document_code_id in (1,2) and minority_type_id in (2,3,4,5,9) "
                		+ "and registered_year = "+ year +""
                		+ "and ("+ year +" between starting_year and ending_year)"
                		+ "and maximum_contract_amount <> original_contract_amount";
               rs = amountQueryHelper(yearTypeVal);
               int count = 0;
               while (rs.next()) {
                   count = rs.getInt("aCount");
               }
               return count;
            }

 //Registered Revenue contracts widget counts
            
            public static String getRRContractsAmount(int year, char yearTypeVal) throws SQLException {
                query = "SELECT SUM(maximum_contract_amount) AESum " +
                        "FROM agreement_snapshot WHERE document_code_id IN (7)" +
                        "and (registered_year = " + year + ")" +
                        "AND(" + year + " BETWEEN starting_year AND ending_year)";

                rs = amountQueryHelper(yearTypeVal);

                BigDecimal totalContractAmount = new BigDecimal(0);

                while (rs.next()) {
                    totalContractAmount = rs.getBigDecimal("AESum");
                }
                return formatNumber(totalContractAmount);
                // .divide(new BigDecimal(1000000000)).setScale(1, BigDecimal.ROUND_HALF_UP);
            } 
            

            public static int getContractsRRCount(int year, char yearTypeVal) throws SQLException {
                query = "SELECT COUNT(*) aCount " +
                        "FROM agreement_snapshot WHERE document_code_id IN (7)" +
                      "and (registered_year = " + year + ")" +
                        "AND(" + year + " BETWEEN starting_year AND ending_year)";
                rs = amountQueryHelper(yearTypeVal);
                int count = 0;
                while (rs.next()) {
                    count = rs.getInt("aCount");
                }
                return count;
                }  
            
            
            
            public static int getRRContractsAgenciesCount(int year,char yearTypeVal) throws SQLException {
                query = "select  count(distinct agency_id ) aCount  from agreement_snapshot"
                		+ "   where  document_code_id in (7) "
                		+ "and registered_year = "+ year +""
                		+ "and ("+ year +" between starting_year and ending_year)";
               rs = amountQueryHelper(yearTypeVal);
               int count = 0;
               while (rs.next()) {
                   count = rs.getInt("aCount");
               }
               return count;
            }
           
            public static int getRRContractsPrimeVendorsCount(int year,char yearTypeVal) throws SQLException {
                query = "select  count(distinct vendor_id ) aCount  from agreement_snapshot"
                		+ "   where  document_code_id in (7) "
                		+ "and registered_year = "+ year +""
                		+ "and ("+ year +" between starting_year and ending_year)";
               rs = amountQueryHelper(yearTypeVal);
               int count = 0;
               while (rs.next()) {
                   count = rs.getInt("aCount");
               }
               return count;
            }
            public static int getRRContractsAwardMethodsCount(int year,char yearTypeVal) throws SQLException {
                query = "select  count(distinct award_method_id ) aCount  from agreement_snapshot"
                		+ "   where  document_code_id in (7) "
                		+ "and registered_year = "+ year +""
                		+ "and ("+ year +" between starting_year and ending_year)";
               rs = amountQueryHelper(yearTypeVal);
               int count = 0;
               while (rs.next()) {
                   count = rs.getInt("aCount");
               }
               return count;
            }
            public static int getRRContractsIndustriesCount(int year,char yearTypeVal) throws SQLException {
                query = "select  count(distinct contract_number ) aCount  from agreement_snapshot"
                		+ "   where  document_code_id in (7) "
                		+ "and registered_year = "+ year +""
                		+ "and ("+ year +" between starting_year and ending_year)";
               rs = amountQueryHelper(yearTypeVal);
               int count = 0;
               while (rs.next()) {
                   count = rs.getInt("aCount");
               }
               return count;
            } 
            
            public static int getRRContractsSizeCount(int year,char yearTypeVal) throws SQLException {
                query = "select  count(distinct contract_number ) aCount  from agreement_snapshot"
                		+ "   where  document_code_id in (7) "
                		+ "and registered_year = "+ year +""
                		+ "and ("+ year +" between starting_year and ending_year)";
               rs = amountQueryHelper(yearTypeVal);
               int count = 0;
               while (rs.next()) {
                   count = rs.getInt("aCount");
               }
               return count;
            }
            public static int getRRContractsCount(int year,char yearTypeVal) throws SQLException {
                query = "select  count(distinct contract_number ) aCount  from agreement_snapshot"
                		+ "   where  document_code_id in (7) "
                		+ "and registered_year = "+ year +""
                		+ "and ("+ year +" between starting_year and ending_year)";
               rs = amountQueryHelper(yearTypeVal);
               int count = 0;
               while (rs.next()) {
                   count = rs.getInt("aCount");
               }
               return count;
            }

            public static int getRRContractsModificationsCount(int year,char yearTypeVal) throws SQLException {
                query = "select  count(distinct contract_number ) aCount  from agreement_snapshot"
                		+ "   where  document_code_id in (7) "
                		+ "and registered_year = "+ year +""
                		+ "and ("+ year +" between starting_year and ending_year)"
                		+ "and maximum_contract_amount <> original_contract_amount";
               rs = amountQueryHelper(yearTypeVal);
               int count = 0;
               while (rs.next()) {
                   count = rs.getInt("aCount");
               }
               return count;
            }

 //MWbe Registered Revenue contracts widget counts

            public static String getMWBERRContractsAmount(int year, char yearTypeVal) throws SQLException {
                query = "SELECT SUM(maximum_contract_amount) AESum " +
                        "FROM agreement_snapshot WHERE document_code_id IN (7) and minority_type_id in (2,3,4,5,9)" +
                        "and (registered_year = " + year + ")" +
                        "AND(" + year + " BETWEEN starting_year AND ending_year)";

                rs = amountQueryHelper(yearTypeVal);

                BigDecimal totalContractAmount = new BigDecimal(0);

                while (rs.next()) {
                    totalContractAmount = rs.getBigDecimal("AESum");
                }
                return formatNumber(totalContractAmount);
                // .divide(new BigDecimal(1000000000)).setScale(1, BigDecimal.ROUND_HALF_UP);
            }


            public static int getMWBEContractsRRCount(int year, char yearTypeVal) throws SQLException {
                query = "SELECT COUNT(*) aCount " +
                        "FROM agreement_snapshot WHERE document_code_id IN (7) and minority_type_id in (2,3,4,5,9)" +
                      "and (registered_year = " + year + ")" +
                        "AND(" + year + " BETWEEN starting_year AND ending_year)";
                rs = amountQueryHelper(yearTypeVal);
                int count = 0;
                while (rs.next()) {
                    count = rs.getInt("aCount");
                }
                return count;
                }



            public static int getMWBERRContractsAgenciesCount(int year,char yearTypeVal) throws SQLException {
                query = "select  count(distinct agency_id ) aCount  from agreement_snapshot"
                		+ "   where  document_code_id in (7) and minority_type_id in (2,3,4,5,9) "
                		+ "and registered_year = "+ year +""
                		+ "and ("+ year +" between starting_year and ending_year)";
               rs = amountQueryHelper(yearTypeVal);
               int count = 0;
               while (rs.next()) {
                   count = rs.getInt("aCount");
               }
               return count;
            }

            public static int getMWBERRContractsPrimeVendorsCount(int year,char yearTypeVal) throws SQLException {
                query = "select  count(distinct vendor_id ) aCount  from agreement_snapshot"
                		+ "   where  document_code_id in (7) and minority_type_id in (2,3,4,5,9) "
                		+ "and registered_year = "+ year +""
                		+ "and ("+ year +" between starting_year and ending_year)";
               rs = amountQueryHelper(yearTypeVal);
               int count = 0;
               while (rs.next()) {
                   count = rs.getInt("aCount");
               }
               return count;
            }
            public static int getMWBERRContractsAwardMethodsCount(int year,char yearTypeVal) throws SQLException {
                query = "select  count(distinct award_method_id ) aCount  from agreement_snapshot"
                		+ "   where  document_code_id in (7) and minority_type_id in (2,3,4,5,9) "
                		+ "and registered_year = "+ year +""
                		+ "and ("+ year +" between starting_year and ending_year)";
               rs = amountQueryHelper(yearTypeVal);
               int count = 0;
               while (rs.next()) {
                   count = rs.getInt("aCount");
               }
               return count;
            }
            public static int getMWBERRContractsIndustriesCount(int year,char yearTypeVal) throws SQLException {
                query = "select  count(distinct contract_number ) aCount  from agreement_snapshot"
                		+ "   where  document_code_id in (7) and minority_type_id in (2,3,4,5,9) "
                		+ "and registered_year = "+ year +""
                		+ "and ("+ year +" between starting_year and ending_year)";
               rs = amountQueryHelper(yearTypeVal);
               int count = 0;
               while (rs.next()) {
                   count = rs.getInt("aCount");
               }
               return count;
            }

            public static int getMWBERRContractsSizeCount(int year,char yearTypeVal) throws SQLException {
                query = "select  count(distinct contract_number ) aCount  from agreement_snapshot"
                		+ "   where  document_code_id in (7) and minority_type_id in (2,3,4,5,9) "
                		+ "and registered_year = "+ year +""
                		+ "and ("+ year +" between starting_year and ending_year)";
               rs = amountQueryHelper(yearTypeVal);
               int count = 0;
               while (rs.next()) {
                   count = rs.getInt("aCount");
               }
               return count;
            }
            public static int getMWBERRContractsCount(int year,char yearTypeVal) throws SQLException {
                query = "select  count(distinct contract_number ) aCount  from agreement_snapshot"
                		+ "   where  document_code_id in (7) and minority_type_id in (2,3,4,5,9) "
                		+ "and registered_year = "+ year +""
                		+ "and ("+ year +" between starting_year and ending_year)";
               rs = amountQueryHelper(yearTypeVal);
               int count = 0;
               while (rs.next()) {
                   count = rs.getInt("aCount");
               }
               return count;
            }

            public static int getMWBERRContractsModificationsCount(int year,char yearTypeVal) throws SQLException {
                query = "select  count(distinct contract_number ) aCount  from agreement_snapshot"
                		+ "   where  document_code_id in (7) and minority_type_id in (2,3,4,5,9) "
                		+ "and registered_year = "+ year +""
                		+ "and ("+ year +" between starting_year and ending_year)"
                		+ "and maximum_contract_amount <> original_contract_amount";
               rs = amountQueryHelper(yearTypeVal);
               int count = 0;
               while (rs.next()) {
                   count = rs.getInt("aCount");
               }
               return count;
            }

            //Active Revenue contracts widget counts

            
            public static String getARContractsAmount(int year, char yearTypeVal) throws SQLException {
                query = "SELECT SUM(maximum_contract_amount) AESum " +
                        "FROM agreement_snapshot WHERE document_code_id IN (7)" +
                        "AND(" + year + " BETWEEN effective_begin_year AND effective_end_year) " +
                        "AND(" + year + " BETWEEN starting_year AND ending_year)";

                rs = amountQueryHelper(yearTypeVal);

                BigDecimal totalContractAmount = new BigDecimal(0);

                while (rs.next()) {
                    totalContractAmount = rs.getBigDecimal("AESum");
                }
                return formatNumber(totalContractAmount);
                // .divide(new BigDecimal(1000000000)).setScale(1, BigDecimal.ROUND_HALF_UP);
            } 
            

            public static int getContractsARCount(int year, char yearTypeVal) throws SQLException {
                query = "SELECT  count(distinct contract_number ) aCount " +
                        "FROM agreement_snapshot WHERE document_code_id IN (7)" +
                        "AND(" + year + " BETWEEN effective_begin_year AND effective_end_year) " +
                        "AND(" + year + " BETWEEN starting_year AND ending_year)";
                rs = amountQueryHelper(yearTypeVal);
                int count = 0;
                while (rs.next()) {
                    count = rs.getInt("aCount");
                }
                return count;
                }  
            
            
            
            public static int getARContractsAgenciesCount(int year,char yearTypeVal) throws SQLException {
                query = "select  count(distinct agency_id ) aCount  from agreement_snapshot"
                		+ "   where  document_code_id in (7) "
                		+ "and ("+ year +" between effective_begin_year and effective_end_year)"
                		+ "and ("+ year +" between starting_year and ending_year)";
               rs = amountQueryHelper(yearTypeVal);
               int count = 0;
               while (rs.next()) {
                   count = rs.getInt("aCount");
               }
               return count;
            }
           
            public static int getARContractsPrimeVendorsCount(int year,char yearTypeVal) throws SQLException {
                query = "select  count(distinct vendor_id ) aCount  from agreement_snapshot"
                		+ "   where  document_code_id in (7) "
                		+ "and ("+ year +" between effective_begin_year and effective_end_year)"
                		+ "and ("+ year +" between starting_year and ending_year)";
               rs = amountQueryHelper(yearTypeVal);
               int count = 0;
               while (rs.next()) {
                   count = rs.getInt("aCount");
               }
               return count;
            }
            public static int getARContractsAwardMethodsCount(int year,char yearTypeVal) throws SQLException {
                query = "select  count(distinct award_method_id ) aCount  from agreement_snapshot"
                		+ "   where  document_code_id in (7) "
                		+ "and ("+ year +" between effective_begin_year and effective_end_year)"
                		+ "and ("+ year +" between starting_year and ending_year)";
               rs = amountQueryHelper(yearTypeVal);
               int count = 0;
               while (rs.next()) {
                   count = rs.getInt("aCount");
               }
               return count;
            }
            public static int getARContractsIndustriesCount(int year,char yearTypeVal) throws SQLException {
                query = "select  count(distinct contract_number ) aCount  from agreement_snapshot"
                		+ "   where  document_code_id in (7) "
                		+ "and ("+ year +" between effective_begin_year and effective_end_year)"
                		+ "and ("+ year +" between starting_year and ending_year)";
               rs = amountQueryHelper(yearTypeVal);
               int count = 0;
               while (rs.next()) {
                   count = rs.getInt("aCount");
               }
               return count;
            } 
            
            public static int getARContractsSizeCount(int year,char yearTypeVal) throws SQLException {
                query = "select  count(distinct contract_number ) aCount  from agreement_snapshot"
                		+ "   where  document_code_id in (7) "
                		+ "and ("+ year +" between effective_begin_year and effective_end_year)"
                		+ "and ("+ year +" between starting_year and ending_year)";
               rs = amountQueryHelper(yearTypeVal);
               int count = 0;
               while (rs.next()) {
                   count = rs.getInt("aCount");
               }
               return count;
            }
            public static int getARContractsCount(int year,char yearTypeVal) throws SQLException {
                query = "select  count(distinct contract_number ) aCount  from agreement_snapshot"
                		+ "   where  document_code_id in (7) "
                		+ "and ("+ year +" between effective_begin_year and effective_end_year)"
                		+ "and ("+ year +" between starting_year and ending_year)";
               rs = amountQueryHelper(yearTypeVal);
               int count = 0;
               while (rs.next()) {
                   count = rs.getInt("aCount");
               }
               return count;
            }
        
            public static int getARContractsModificationsCount(int year,char yearTypeVal) throws SQLException {
                query = "select  count(distinct contract_number ) aCount  from agreement_snapshot"
                		+ "   where  document_code_id in (7) "
                		+ "and ("+ year +" between effective_begin_year and effective_end_year)"
                		+ "and ("+ year +" between starting_year and ending_year)"
                		+ "and maximum_contract_amount <> original_contract_amount";
               rs = amountQueryHelper(yearTypeVal);
               int count = 0;
               while (rs.next()) {
                   count = rs.getInt("aCount");
               }
               return count;
            }
            
          // MWBE Active Revenue contracts widget counts


            public static String getMWBEARContractsAmount(int year, char yearTypeVal) throws SQLException {
                query = "SELECT SUM(maximum_contract_amount) AESum " +
                        "FROM agreement_snapshot WHERE document_code_id IN (7) and minority_type_id in (2,3,4,5,9)" +
                        "AND(" + year + " BETWEEN effective_begin_year AND effective_end_year) " +
                        "AND(" + year + " BETWEEN starting_year AND ending_year)";

                rs = amountQueryHelper(yearTypeVal);

                BigDecimal totalContractAmount = new BigDecimal(0);

                while (rs.next()) {
                    totalContractAmount = rs.getBigDecimal("AESum");
                }
                return formatNumber(totalContractAmount);
                // .divide(new BigDecimal(1000000000)).setScale(1, BigDecimal.ROUND_HALF_UP);
            }


            public static int getMWBEContractsARCount(int year, char yearTypeVal) throws SQLException {
                query = "SELECT  count(distinct contract_number ) aCount " +
                        "FROM agreement_snapshot WHERE document_code_id IN (7) and minority_type_id in (2,3,4,5,9)" +
                        "AND(" + year + " BETWEEN effective_begin_year AND effective_end_year) " +
                        "AND(" + year + " BETWEEN starting_year AND ending_year)";
                rs = amountQueryHelper(yearTypeVal);
                int count = 0;
                while (rs.next()) {
                    count = rs.getInt("aCount");
                }
                return count;
                }



            public static int getMWBEARContractsAgenciesCount(int year,char yearTypeVal) throws SQLException {
                query = "select  count(distinct agency_id ) aCount  from agreement_snapshot"
                		+ "   where  document_code_id in (7)  and minority_type_id in (2,3,4,5,9)"
                		+ "and ("+ year +" between effective_begin_year and effective_end_year)"
                		+ "and ("+ year +" between starting_year and ending_year)";
               rs = amountQueryHelper(yearTypeVal);
               int count = 0;
               while (rs.next()) {
                   count = rs.getInt("aCount");
               }
               return count;
            }

            public static int getMWBEARContractsPrimeVendorsCount(int year,char yearTypeVal) throws SQLException {
                query = "select  count(distinct vendor_id ) aCount  from agreement_snapshot"
                		+ "   where  document_code_id in (7)  and minority_type_id in (2,3,4,5,9)"
                		+ "and ("+ year +" between effective_begin_year and effective_end_year)"
                		+ "and ("+ year +" between starting_year and ending_year)";
               rs = amountQueryHelper(yearTypeVal);
               int count = 0;
               while (rs.next()) {
                   count = rs.getInt("aCount");
               }
               return count;
            }
            public static int getMWBEARContractsAwardMethodsCount(int year,char yearTypeVal) throws SQLException {
                query = "select  count(distinct award_method_id ) aCount  from agreement_snapshot"
                		+ "   where  document_code_id in (7) and minority_type_id in (2,3,4,5,9) "
                		+ "and ("+ year +" between effective_begin_year and effective_end_year)"
                		+ "and ("+ year +" between starting_year and ending_year)";
               rs = amountQueryHelper(yearTypeVal);
               int count = 0;
               while (rs.next()) {
                   count = rs.getInt("aCount");
               }
               return count;
            }
            public static int getMWBEARContractsIndustriesCount(int year,char yearTypeVal) throws SQLException {
                query = "select  count(distinct contract_number ) aCount  from agreement_snapshot"
                		+ "   where  document_code_id in (7) and minority_type_id in (2,3,4,5,9) "
                		+ "and ("+ year +" between effective_begin_year and effective_end_year)"
                		+ "and ("+ year +" between starting_year and ending_year)";
               rs = amountQueryHelper(yearTypeVal);
               int count = 0;
               while (rs.next()) {
                   count = rs.getInt("aCount");
               }
               return count;
            }

            public static int getMWBEARContractsSizeCount(int year,char yearTypeVal) throws SQLException {
                query = "select  count(distinct contract_number ) aCount  from agreement_snapshot"
                		+ "   where  document_code_id in (7) and minority_type_id in (2,3,4,5,9) "
                		+ "and ("+ year +" between effective_begin_year and effective_end_year)"
                		+ "and ("+ year +" between starting_year and ending_year)";
               rs = amountQueryHelper(yearTypeVal);
               int count = 0;
               while (rs.next()) {
                   count = rs.getInt("aCount");
               }
               return count;
            }
            public static int getMWBEARContractsCount(int year,char yearTypeVal) throws SQLException {
                query = "select  count(distinct contract_number ) aCount  from agreement_snapshot"
                		+ "   where  document_code_id in (7) and minority_type_id in (2,3,4,5,9) "
                		+ "and ("+ year +" between effective_begin_year and effective_end_year)"
                		+ "and ("+ year +" between starting_year and ending_year)";
               rs = amountQueryHelper(yearTypeVal);
               int count = 0;
               while (rs.next()) {
                   count = rs.getInt("aCount");
               }
               return count;
            }

            public static int getMWBEARContractsModificationsCount(int year,char yearTypeVal) throws SQLException {
                query = "select  count(distinct contract_number ) aCount  from agreement_snapshot"
                		+ "   where  document_code_id in (7) and minority_type_id in (2,3,4,5,9) "
                		+ "and ("+ year +" between effective_begin_year and effective_end_year)"
                		+ "and ("+ year +" between starting_year and ending_year)"
                		+ "and maximum_contract_amount <> original_contract_amount";
               rs = amountQueryHelper(yearTypeVal);
               int count = 0;
               while (rs.next()) {
                   count = rs.getInt("aCount");
               }
               return count;
            }

 //PendingRevenue contracts widget counts
            
            public static String getPRContractsAmount(int year, char yearTypeVal) throws SQLException {
               // query = "SELECT SUM(original_maximum_amount) AESum " +
                 //       "FROM pending_contracts WHERE document_code_id IN (7)" ;
                        
            	query =   "select sum(a.cmcount+b.cmcount) AESum from (select sum(revised_maximum_amount ) cmcount from pending_contracts a ,"+
                        "(select contract_number, max(document_version) as document_version  from pending_contracts  group by 1)" +
                         "b where a.contract_number = b.contract_number  and a.document_version = b.document_version" +
                          "  and  latest_flag ='Y' and original_or_modified = 'N'  and document_code_id in  (7))a ," +
                        "(select sum(revised_maximum_amount - registered_contract_max_amount ) cmcount from pending_contracts a ," +
                         "(select contract_number, max(document_version) as document_version  from pending_contracts  group by 1) b " +
                         "where a.contract_number = b.contract_number  and a.document_version = b.document_version  and  latest_flag ='Y' and" +
                        " original_or_modified = 'M' and document_code_id in  (7))b";
                        
                rs = amountQueryHelper(yearTypeVal);

                BigDecimal totalContractAmount = new BigDecimal(0);

                while (rs.next()) {
                    totalContractAmount = rs.getBigDecimal("AESum");
                }
                return formatNumber(totalContractAmount);
                // .divide(new BigDecimal(1000000000)).setScale(1, BigDecimal.ROUND_HALF_UP);
            } 
            

            public static int getContractsPRCount(int year, char yearTypeVal) throws SQLException {
                query = "SELECT  count(distinct contract_number ) aCount " +
                        "FROM pending_contracts WHERE document_code_id IN (7)";
                rs = amountQueryHelper(yearTypeVal);
                int count = 0;
                while (rs.next()) {
                    count = rs.getInt("aCount");
                }
                return count;
                }  
            
            
            
            public static int getPRContractsAgenciesCount(int year,char yearTypeVal) throws SQLException {
                query = "select  count(distinct document_agency_id ) aCount  from pending_contracts"
                		+ "   where  document_code_id in (7) ";
                	
               rs = amountQueryHelper(yearTypeVal);
               int count = 0;
               while (rs.next()) {
                   count = rs.getInt("aCount");
               }
               return count;
            }
           
            public static int getPRContractsPrimeVendorsCount(int year,char yearTypeVal) throws SQLException {
                query = "select  count(distinct vendor_id ) aCount  from pending_contracts"
                		+ "   where  document_code_id in (7) ";
                	
               rs = amountQueryHelper(yearTypeVal);
               int count = 0;
               while (rs.next()) {
                   count = rs.getInt("aCount");
               }
               return count;
            }
            public static int getPRContractsAwardMethodsCount(int year,char yearTypeVal) throws SQLException {
                query = "select  count(distinct award_method_id ) aCount  from pending_contracts"
                		+ "   where  document_code_id in (7) ";
               rs = amountQueryHelper(yearTypeVal);
               int count = 0;
               while (rs.next()) {
                   count = rs.getInt("aCount");
               }
               return count;
            }
            public static int getPRContractsIndustriesCount(int year,char yearTypeVal) throws SQLException {
                query = "select  count(distinct contract_number ) aCount  from pending_contracts"
                		+ "   where  document_code_id in (7) ";
               rs = amountQueryHelper(yearTypeVal);
               int count = 0;
               while (rs.next()) {
                   count = rs.getInt("aCount");
               }
               return count;
            } 
            
            public static int getPRContractsSizeCount(int year,char yearTypeVal) throws SQLException {
                query = "select  count(distinct contract_number ) aCount  from pending_contracts"
                		+ "   where  document_code_id in (7) ";
               rs = amountQueryHelper(yearTypeVal);
               int count = 0;
               while (rs.next()) {
                   count = rs.getInt("aCount");
               }
               return count;
            }
            public static int getPRContractsCount(int year,char yearTypeVal) throws SQLException {
                query = "select  count(distinct contract_number ) aCount  from pending_contracts"
                		+ "   where  document_code_id in (7) ";
               rs = amountQueryHelper(yearTypeVal);
               int count = 0;
               while (rs.next()) {
                   count = rs.getInt("aCount");
               }
               return count;
            }
        
            public static int getPRContractsModificationsCount(int year,char yearTypeVal) throws SQLException {
                query = "select  count(distinct contract_number ) aCount  from pending_contracts"
                		+ "   where  document_code_id in (7) "
                		+ " and original_maximum_amount <> revised_maximum_amount ";
               rs = amountQueryHelper(yearTypeVal);
               int count = 0;
               while (rs.next()) {
                   count = rs.getInt("aCount");
               }
               return count;
            }
            
//MWBE PendingRevenue contracts widget counts

            public static String getMWBEPRContractsAmount(int year, char yearTypeVal) throws SQLException {
               // query = "SELECT SUM(original_maximum_amount) AESum " +
                 //       "FROM pending_contracts WHERE document_code_id IN (7)" ;

            	query =   "select sum(a.cmcount+b.cmcount) AESum from (select sum(revised_maximum_amount ) cmcount from pending_contracts a ,"+
                        "(select contract_number, max(document_version) as document_version  from pending_contracts  group by 1)" +
                         "b where a.contract_number = b.contract_number  and a.document_version = b.document_version" +
                          "  and  latest_flag ='Y' and original_or_modified = 'N'  and document_code_id in  (7))a ," +
                        "(select sum(revised_maximum_amount - registered_contract_max_amount ) cmcount from pending_contracts a ," +
                         "(select contract_number, max(document_version) as document_version  from pending_contracts  group by 1) b " +
                         "where a.contract_number = b.contract_number  and a.document_version = b.document_version  and  latest_flag ='Y' and" +
                        " original_or_modified = 'M' and document_code_id in  (7))b";

                rs = amountQueryHelper(yearTypeVal);

                BigDecimal totalContractAmount = new BigDecimal(0);

                while (rs.next()) {
                    totalContractAmount = rs.getBigDecimal("AESum");
                }
                return formatNumber(totalContractAmount);
                // .divide(new BigDecimal(1000000000)).setScale(1, BigDecimal.ROUND_HALF_UP);
            }


            public static int getMWBEContractsPRCount(int year, char yearTypeVal) throws SQLException {
                query = "SELECT  count(distinct contract_number ) aCount " +
                        "FROM pending_contracts WHERE document_code_id IN (7) and minority_type_id in (2,3,4,5,9)";
                rs = amountQueryHelper(yearTypeVal);
                int count = 0;
                while (rs.next()) {
                    count = rs.getInt("aCount");
                }
                return count;
                }



            public static int getMWBEPRContractsAgenciesCount(int year,char yearTypeVal) throws SQLException {
                query = "select  count(distinct document_agency_id ) aCount  from pending_contracts"
                		+ "   where  document_code_id in (7) and minority_type_id in (2,3,4,5,9) ";

               rs = amountQueryHelper(yearTypeVal);
               int count = 0;
               while (rs.next()) {
                   count = rs.getInt("aCount");
               }
               return count;
            }

            public static int getMWBEPRContractsPrimeVendorsCount(int year,char yearTypeVal) throws SQLException {
                query = "select  count(distinct vendor_id ) aCount  from pending_contracts"
                		+ "   where  document_code_id in (7) and minority_type_id in (2,3,4,5,9) ";

               rs = amountQueryHelper(yearTypeVal);
               int count = 0;
               while (rs.next()) {
                   count = rs.getInt("aCount");
               }
               return count;
            }
            public static int getMWBEPRContractsAwardMethodsCount(int year,char yearTypeVal) throws SQLException {
                query = "select  count(distinct award_method_id ) aCount  from pending_contracts"
                		+ "   where  document_code_id in (7) and minority_type_id in (2,3,4,5,9) ";
               rs = amountQueryHelper(yearTypeVal);
               int count = 0;
               while (rs.next()) {
                   count = rs.getInt("aCount");
               }
               return count;
            }
            public static int getMWBEPRContractsIndustriesCount(int year,char yearTypeVal) throws SQLException {
                query = "select  count(distinct contract_number ) aCount  from pending_contracts"
                		+ "   where  document_code_id in (7) and minority_type_id in (2,3,4,5,9) ";
               rs = amountQueryHelper(yearTypeVal);
               int count = 0;
               while (rs.next()) {
                   count = rs.getInt("aCount");
               }
               return count;
            }

            public static int getMWBEPRContractsSizeCount(int year,char yearTypeVal) throws SQLException {
                query = "select  count(distinct contract_number ) aCount  from pending_contracts"
                		+ "   where  document_code_id in (7) and minority_type_id in (2,3,4,5,9) ";
               rs = amountQueryHelper(yearTypeVal);
               int count = 0;
               while (rs.next()) {
                   count = rs.getInt("aCount");
               }
               return count;
            }
            public static int getMWBEPRContractsCount(int year,char yearTypeVal) throws SQLException {
                query = "select  count(distinct contract_number ) aCount  from pending_contracts"
                		+ "   where  document_code_id in (7) and minority_type_id in (2,3,4,5,9) ";
               rs = amountQueryHelper(yearTypeVal);
               int count = 0;
               while (rs.next()) {
                   count = rs.getInt("aCount");
               }
               return count;
            }

            public static int getMWBEPRContractsModificationsCount(int year,char yearTypeVal) throws SQLException {
                query = "select  count(distinct contract_number ) aCount  from pending_contracts"
                		+ "   where  document_code_id in (7) and minority_type_id in (2,3,4,5,9) "
                		+ " and original_maximum_amount <> revised_maximum_amount ";
               rs = amountQueryHelper(yearTypeVal);
               int count = 0;
               while (rs.next()) {
                   count = rs.getInt("aCount");
               }
               return count;
            }


//Pending Expense contracts widget counts
            
            public static String getPEContractsAmount(int year, char yearTypeVal) throws SQLException {
               // query = "SELECT SUM(original_maximum_amount) AESum " +
                      //  "FROM pending_contracts WHERE document_code_id IN (1,2,5,6,19,20)" ;
                
                query =   "select sum(a.cmcount+b.cmcount) AESum from (select sum(revised_maximum_amount ) cmcount from pending_contracts a ,"+
                        "(select contract_number, max(document_version) as document_version  from pending_contracts  group by 1)" +
                         "b where a.contract_number = b.contract_number  and a.document_version = b.document_version" +
                          "  and  latest_flag ='Y' and original_or_modified = 'N'  and document_code_id in  (1,2,20,5,6,19))a ," +
                        "(select sum(revised_maximum_amount - registered_contract_max_amount ) cmcount from pending_contracts a ," +
                         "(select contract_number, max(document_version) as document_version  from pending_contracts  group by 1) b " +
                         "where a.contract_number = b.contract_number  and a.document_version = b.document_version  and  latest_flag ='Y' and" +
                        " original_or_modified = 'M' and document_code_id in  (1,2,20,5,6,19))b";
                        

                rs = amountQueryHelper(yearTypeVal);

                BigDecimal totalContractAmount = new BigDecimal(0);

                while (rs.next()) {
                    totalContractAmount = rs.getBigDecimal("AESum");
                }
                return formatNumber(totalContractAmount);
                // .divide(new BigDecimal(1000000000)).setScale(1, BigDecimal.ROUND_HALF_UP);
            } 
            

            public static int getContractsBottomnNavPECount(int year, char yearTypeVal) throws SQLException {
                query = "SELECT  count(contract_number ) aCount " +
                        "FROM pending_contracts WHERE document_code_id IN (1,2,5,6,19,20)";
                rs = amountQueryHelper(yearTypeVal);
                int count = 0;
                while (rs.next()) {
                    count = rs.getInt("aCount");
                }
                return count;
                } 
            
            public static int getContractsPECount(int year, char yearTypeVal) throws SQLException {
                query = "SELECT  count(Distinct contract_number ) aCount " +
                        "FROM pending_contracts WHERE document_code_id IN (1,2,5,6,19,20)";
                rs = amountQueryHelper(yearTypeVal);
                int count = 0;
                while (rs.next()) {
                    count = rs.getInt("aCount");
                }
                return count;
                }  
            
            
            
            
            public static int getPEContractsAgenciesCount(int year,char yearTypeVal) throws SQLException {
                query = "select  count(distinct document_agency_id ) aCount  from pending_contracts"
                		+ "   where  document_code_id in (1,2,5,6,19,20) ";
                	
               rs = amountQueryHelper(yearTypeVal);
               int count = 0;
               while (rs.next()) {
                   count = rs.getInt("aCount");
               }
               return count;
            }
           
            public static int getPEContractsPrimeVendorsCount(int year,char yearTypeVal) throws SQLException {
                query = "select  count(distinct vendor_id ) aCount  from pending_contracts"
                		+ "   where  document_code_id in (1,2,5,6,19,20) ";
                	
               rs = amountQueryHelper(yearTypeVal);
               int count = 0;
               while (rs.next()) {
                   count = rs.getInt("aCount");
               }
               return count;
            }
            public static int getPEContractsAwardMethodsCount(int year,char yearTypeVal) throws SQLException {
                query = "select  count(distinct award_method_id ) aCount  from pending_contracts"
                		+ "   where  document_code_id in (1,2,5,6,19,20) ";
               rs = amountQueryHelper(yearTypeVal);
               int count = 0;
               while (rs.next()) {
                   count = rs.getInt("aCount");
               }
               return count;
            }
            public static int getPEContractsIndustriesCount(int year,char yearTypeVal) throws SQLException {
                query = "select  count(contract_number ) aCount  from pending_contracts"
                		+ "   where  document_code_id in (1,2,5,6,19,20) ";
               rs = amountQueryHelper(yearTypeVal);
               int count = 0;
               while (rs.next()) {
                   count = rs.getInt("aCount");
               }
               return count;
            } 
            
            public static int getPEContractsSizeCount(int year,char yearTypeVal) throws SQLException {
                query = "select  count(contract_number ) aCount  from pending_contracts"
                		+ "   where  document_code_id in (1,2,5,6,19,20) ";
               rs = amountQueryHelper(yearTypeVal);
               int count = 0;
               while (rs.next()) {
                   count = rs.getInt("aCount");
               }
               return count;
            }
            public static int getPEContractsCount(int year,char yearTypeVal) throws SQLException {
                query = "select  count(contract_number ) aCount  from pending_contracts"
                		+ "   where  document_code_id in (1,2,20) ";
               rs = amountQueryHelper(yearTypeVal);
               int count = 0;
               while (rs.next()) {
                   count = rs.getInt("aCount");
               }
               return count;
            }
        
            public static int getPEContractsModificationsCount(int year,char yearTypeVal) throws SQLException {
                query = "select  count(contract_number ) aCount  from pending_contracts"
                		+ "   where  document_code_id in (1,2,20) "
                		+ " and  original_maximum_amount <> revised_maximum_amount";  
               rs = amountQueryHelper(yearTypeVal);
               int count = 0;
               while (rs.next()) {
                   count = rs.getInt("aCount");
               }
               return count;
            }
            public static int getPEContractsMasterCount(int year,char yearTypeVal) throws SQLException {
                query = "select  count(contract_number ) aCount  from pending_contracts"
                		+ "   where  document_code_id in (5,6,19) ";
               rs = amountQueryHelper(yearTypeVal);
               int count = 0;
               while (rs.next()) {
                   count = rs.getInt("aCount");
               }
               return count;
            }
        
            public static int getPEContractsMasterModificationsCount(int year,char yearTypeVal) throws SQLException {
                query = "select  count(contract_number ) aCount  from pending_contracts"
                		+ "   where  document_code_id in (5,6,19) "
                		+ " and  original_maximum_amount <> revised_maximum_amount";  
               rs = amountQueryHelper(yearTypeVal);
               int count = 0;
               while (rs.next()) {
                   count = rs.getInt("aCount");
               }
               return count;
            }
            
//MWBE Pending Expense contracts widget counts
            
            public static String getMWBEPEContractsAmount(int year, char yearTypeVal) throws SQLException {
               // query = "SELECT SUM(original_maximum_amount) AESum " +
                      //  "FROM pending_contracts WHERE document_code_id IN (1,2,5,6,19,20) and minority_type_id in (2,3,4,5,9)" ;
                
                query =   "select sum(a.cmcount+b.cmcount) AESum from (select sum(revised_maximum_amount ) cmcount from pending_contracts a,"+
                        "(select contract_number, max(document_version) as document_version  from pending_contracts where minority_type_id in (2,3,4,5,9) group by 1)" +
                         "b where a.contract_number = b.contract_number  and a.document_version = b.document_version" +
                          "  and  latest_flag ='Y' and original_or_modified = 'N'  and document_code_id in  (1,2,20,5,6,19) and  minority_type_id in (2,3,4,5,9))a ," +
                        "(select sum(revised_maximum_amount - registered_contract_max_amount ) cmcount from pending_contracts  a ," +
                         "(select contract_number, max(document_version) as document_version  from pending_contracts where minority_type_id in (2,3,4,5,9) group by 1) b " +
                         "where a.contract_number = b.contract_number  and a.document_version = b.document_version  and  latest_flag ='Y' and" +
                        " original_or_modified = 'M' and document_code_id in  (1,2,20,5,6,19) and minority_type_id in (2,3,4,5,9))b";
                        

                rs = amountQueryHelper(yearTypeVal);

                BigDecimal totalContractAmount = new BigDecimal(0);

                while (rs.next()) {
                    totalContractAmount = rs.getBigDecimal("AESum");
                }
                return formatNumber(totalContractAmount);
                // .divide(new BigDecimal(1000000000)).setScale(1, BigDecimal.ROUND_HALF_UP);
            } 
            

            public static int getMWBEContractsBottomnNavPECount(int year, char yearTypeVal) throws SQLException {
                query = "SELECT  count(contract_number ) aCount " +
                        "FROM pending_contracts WHERE document_code_id IN (1,2,5,6,19,20) and minority_type_id in (2,3,4,5,9)";
                rs = amountQueryHelper(yearTypeVal);
                int count = 0;
                while (rs.next()) {
                    count = rs.getInt("aCount");
                }
                return count;
                } 
            
            public static int getMWBEContractsPECount(int year, char yearTypeVal) throws SQLException {
                query = "SELECT  count(Distinct contract_number ) aCount " +
                        "FROM pending_contracts WHERE document_code_id IN (1,2,5,6,19,20) and minority_type_id in (2,3,4,5,9)";
                rs = amountQueryHelper(yearTypeVal);
                int count = 0;
                while (rs.next()) {
                    count = rs.getInt("aCount");
                }
                return count;
                }  
            
            
            
            
            public static int getMWBEPEContractsAgenciesCount(int year,char yearTypeVal) throws SQLException {
                query = "select  count(distinct document_agency_id ) aCount  from pending_contracts"
                		+ "   where  document_code_id in (1,2,5,6,19,20) and minority_type_id in (2,3,4,5,9) ";
                	
               rs = amountQueryHelper(yearTypeVal);
               int count = 0;
               while (rs.next()) {
                   count = rs.getInt("aCount");
               }
               return count;
            }
           
            public static int getMWBEPEContractsPrimeVendorsCount(int year,char yearTypeVal) throws SQLException {
                query = "select  count(distinct vendor_id ) aCount  from pending_contracts"
                		+ "   where  document_code_id in (1,2,5,6,19,20) and minority_type_id in (2,3,4,5,9) ";
                	
               rs = amountQueryHelper(yearTypeVal);
               int count = 0;
               while (rs.next()) {
                   count = rs.getInt("aCount");
               }
               return count;
            }
            public static int getMWBEPEContractsAwardMethodsCount(int year,char yearTypeVal) throws SQLException {
                query = "select  count(distinct award_method_id ) aCount  from pending_contracts"
                		+ "   where  document_code_id in (1,2,5,6,19,20) and minority_type_id in (2,3,4,5,9) ";
               rs = amountQueryHelper(yearTypeVal);
               int count = 0;
               while (rs.next()) {
                   count = rs.getInt("aCount");
               }
               return count;
            }
            public static int getMWBEPEContractsIndustriesCount(int year,char yearTypeVal) throws SQLException {
                query = "select  count(contract_number ) aCount  from pending_contracts"
                		+ "   where  document_code_id in (1,2,5,6,19,20) and minority_type_id in (2,3,4,5,9) ";
               rs = amountQueryHelper(yearTypeVal);
               int count = 0;
               while (rs.next()) {
                   count = rs.getInt("aCount");
               }
               return count;
            } 
            
            public static int getMWBEPEContractsSizeCount(int year,char yearTypeVal) throws SQLException {
                query = "select  count(contract_number ) aCount  from pending_contracts"
                		+ "   where  document_code_id in (1,2,5,6,19,20) and minority_type_id in (2,3,4,5,9) ";
               rs = amountQueryHelper(yearTypeVal);
               int count = 0;
               while (rs.next()) {
                   count = rs.getInt("aCount");
               }
               return count;
            }
            public static int getMWBEPEContractsCount(int year,char yearTypeVal) throws SQLException {
                query = "select  count(contract_number ) aCount  from pending_contracts"
                		+ "   where  document_code_id in (1,2,20) and minority_type_id in (2,3,4,5,9) ";
               rs = amountQueryHelper(yearTypeVal);
               int count = 0;
               while (rs.next()) {
                   count = rs.getInt("aCount");
               }
               return count;
            }
        
            public static int getMWBEPEContractsModificationsCount(int year,char yearTypeVal) throws SQLException {
                query = "select  count(contract_number ) aCount  from pending_contracts"
                		+ "   where  document_code_id in (1,2,20) and minority_type_id in (2,3,4,5,9) "
                		+ " and  original_maximum_amount <> revised_maximum_amount";  
               rs = amountQueryHelper(yearTypeVal);
               int count = 0;
               while (rs.next()) {
                   count = rs.getInt("aCount");
               }
               return count;
            }
            public static int getMWBEPEContractsMasterCount(int year,char yearTypeVal) throws SQLException {
                query = "select  count(contract_number ) aCount  from pending_contracts"
                		+ "   where  document_code_id in (5,6,19) and minority_type_id in (2,3,4,5,9) ";
               rs = amountQueryHelper(yearTypeVal);
               int count = 0;
               while (rs.next()) {
                   count = rs.getInt("aCount");
               }
               return count;
            }
        
            public static int getMWBEPEContractsMasterModificationsCount(int year,char yearTypeVal) throws SQLException {
                query = "select  count(contract_number ) aCount  from pending_contracts"
                		+ "   where  document_code_id in (5,6,19) and minority_type_id in (2,3,4,5,9) "
                		+ " and  original_maximum_amount <> revised_maximum_amount";  
               rs = amountQueryHelper(yearTypeVal);
               int count = 0;
               while (rs.next()) {
                   count = rs.getInt("aCount");
               }
               return count;
            }
            
            ///Active expense contracts details page 
            
            
           
            public static int getAEContractsDetailsCount(int year,char yearTypeVal) throws SQLException {
                query =    " select  ((select  count(*) from agreement_snapshot  where "
                	    	+"	 document_code_id in (1,2) and ("+ year +" between starting_year and ending_year)"
                	    	+"	    and ("+ year +" between effective_begin_year and effective_end_year)) +"
                	    	+"	    ( select  count(*) from sub_agreement_snapshot  where contract_number in "
                	    	+"	 ( select distinct contract_number  from agreement_snapshot  where "
                	    	+"	 document_code_id in (1,2) and ("+ year +" between starting_year and ending_year)"
                	    		+"    and ("+ year +" between effective_begin_year and effective_end_year)) and latest_flag ='Y')) aCount";
                rs = amountQueryHelper(yearTypeVal);
                rs = amountQueryHelper(yearTypeVal);
                int count = 0;
               while (rs.next()) {
                   count = rs.getInt("aCount");
               }
               return count;
            }
            
            
            public static int getAEAllContractsDetailsCount(int year,char yearTypeVal) throws SQLException {
                query =    " select  ((select  count(*) from agreement_snapshot  where "
                	    	+"	 document_code_id in (1,2,5) and ("+ year +" between starting_year and ending_year)"
                	    	+"	    and ("+ year +" between effective_begin_year and effective_end_year)) +"
                	    	+"	    ( select  count(*) from sub_agreement_snapshot  where contract_number in "
                	    	+"	 ( select distinct contract_number  from agreement_snapshot  where "
                	    	+"	 document_code_id in (1,2,5) and ("+ year +" between starting_year and ending_year)"
                	    		+"    and ("+ year +" between effective_begin_year and effective_end_year)) and latest_flag ='Y')) aCount";
                rs = amountQueryHelper(yearTypeVal);
                rs = amountQueryHelper(yearTypeVal);
                int count = 0;
               while (rs.next()) {
                   count = rs.getInt("aCount");
               }
               return count;
            }
            public static int getAEMasterContractsDetailsCount(int year,char yearTypeVal) throws SQLException {
                query = "select  count(distinct contract_number ) aCount  from agreement_snapshot"
                		+ "   where  document_code_id in (5,6) "
                		+ "and ("+ year +" between effective_begin_year and effective_end_year)"
                		+ "and ("+ year +" between starting_year and ending_year)";
               rs = amountQueryHelper(yearTypeVal);
               int count = 0;
               while (rs.next()) {
                   count = rs.getInt("aCount");
               }
               return count;
            }
            public static int getAEMasterContractsModificationDetailsCount(int year,char yearTypeVal) throws SQLException {
                query = "select  count(distinct contract_number ) aCount  from agreement_snapshot"
                		+ "   where  document_code_id in (5,6) "
                		+ "and ("+ year +" between effective_begin_year and effective_end_year)"
                		+ "and ("+ year +" between starting_year and ending_year)"
                		+ "and maximum_contract_amount <> original_contract_amount";
               rs = amountQueryHelper(yearTypeVal);
               int count = 0;
               while (rs.next()) {
                   count = rs.getInt("aCount");
               }
               return count;
            }
            public static int getAEContractsModificationDetailsCount(int year,char yearTypeVal) throws SQLException {
                query = "select (( select  count(distinct contract_number )from agreement_snapshot"
                		+ "   where  document_code_id in (1,2) "
                		+ "and ("+ year +" between starting_year and ending_year)"
                		+ "and maximum_contract_amount <> original_contract_amount"
                		+"	    and ("+ year +" between effective_begin_year and effective_end_year)) +"
            	    	+"	    ( select  count(*) from sub_agreement_snapshot  where contract_number in "
            	    	+"	 ( select distinct contract_number  from agreement_snapshot  where "
            	    	+"	 document_code_id in (1,2) and ("+ year +" between starting_year and ending_year)"
            	    	+ "and maximum_contract_amount <> original_contract_amount"
            	    		+"    and ("+ year +" between effective_begin_year and effective_end_year)) and latest_flag ='Y')) aCount";
               rs = amountQueryHelper(yearTypeVal);
               int count = 0;
               while (rs.next()) {
                   count = rs.getInt("aCount");
               }
               return count;
            }
            
          ///MWBE Active expense contracts details page



            public static int getMWBEAEContractsDetailsCount(int year,char yearTypeVal) throws SQLException {
                query =    " select  ((select  count(*) from agreement_snapshot  where "
                	    	+"	 document_code_id in (1,2) and minority_type_id in (2,3,4,5,9) and ("+ year +" between starting_year and ending_year)"
                	    	+"	    and ("+ year +" between effective_begin_year and effective_end_year)) +"
                	    	+"	    ( select  count(*) from sub_agreement_snapshot  where contract_number in "
                	    	+"	 ( select distinct contract_number  from agreement_snapshot  where "
                	    	+"	 document_code_id in (1,2) and minority_type_id in (2,3,4,5,9) and ("+ year +" between starting_year and ending_year)"
                	    		+"    and ("+ year +" between effective_begin_year and effective_end_year)) and latest_flag ='Y')) aCount";
         
                rs = amountQueryHelper(yearTypeVal);
                int count = 0;
               while (rs.next()) {
                   count = rs.getInt("aCount");
               }
               return count;
            }


            public static int getMWBEAEAllContractsDetailsCount(int year,char yearTypeVal) throws SQLException {
                query =    " select  ((select  count(*) from agreement_snapshot  where "
                	    	+"	 document_code_id in (1,2,5) and minority_type_id in (2,3,4,5,9) and ("+ year +" between starting_year and ending_year)"
                	    	+"	    and ("+ year +" between effective_begin_year and effective_end_year)) +"
                	    	+"	    ( select  count(*) from sub_agreement_snapshot  where contract_number in "
                	    	+"	 ( select distinct contract_number  from agreement_snapshot  where "
                	    	+"	 document_code_id in (1,2,5) and minority_type_id in (2,3,4,5,9) and ("+ year +" between starting_year and ending_year)"
                	    		+"    and ("+ year +" between effective_begin_year and effective_end_year)) and latest_flag ='Y')) aCount";
                rs = amountQueryHelper(yearTypeVal);
              
                int count = 0;
               while (rs.next()) {
                   count = rs.getInt("aCount");
               }
               return count;
            }
            public static int getMWBEAEMasterContractsDetailsCount(int year,char yearTypeVal) throws SQLException {
                query = "select  count(distinct contract_number ) aCount  from agreement_snapshot"
                		+ "   where  document_code_id in (5,6) and minority_type_id in (2,3,4,5,9) "
                		+ "and ("+ year +" between effective_begin_year and effective_end_year)"
                		+ "and ("+ year +" between starting_year and ending_year)";
               rs = amountQueryHelper(yearTypeVal);
               int count = 0;
               while (rs.next()) {
                   count = rs.getInt("aCount");
               }
               return count;
            }
            public static int getMWBEAEMasterContractsModificationDetailsCount(int year,char yearTypeVal) throws SQLException {
                query = "select  count(distinct contract_number ) aCount  from agreement_snapshot"
                		+ "   where  document_code_id in (5,6) and minority_type_id in (2,3,4,5,9) "
                		+ "and ("+ year +" between effective_begin_year and effective_end_year)"
                		+ "and ("+ year +" between starting_year and ending_year)"
                		+ "and maximum_contract_amount <> original_contract_amount";
               rs = amountQueryHelper(yearTypeVal);
               int count = 0;
               while (rs.next()) {
                   count = rs.getInt("aCount");
               }
               return count;
            }
            public static int getMWBEAEContractsModificationDetailsCount(int year,char yearTypeVal) throws SQLException {
                query = "select (( select  count(distinct contract_number )from agreement_snapshot"
                		+ "   where  document_code_id in (1,2) and minority_type_id in (2,3,4,5,9) "
                		+ "and ("+ year +" between starting_year and ending_year)"
                		+ "and maximum_contract_amount <> original_contract_amount"
                		+"	    and ("+ year +" between effective_begin_year and effective_end_year)) +"
            	    	+"	    ( select  count(*) from sub_agreement_snapshot  where contract_number in "
            	    	+"	 ( select distinct contract_number  from agreement_snapshot  where "
            	    	+"	 document_code_id in (1,2) and minority_type_id in (2,3,4,5,9) and ("+ year +" between starting_year and ending_year)"
            	    	+ "and maximum_contract_amount <> original_contract_amount"
            	    		+"    and ("+ year +" between effective_begin_year and effective_end_year)) and latest_flag ='Y')) aCount";
               rs = amountQueryHelper(yearTypeVal);
               int count = 0;
               while (rs.next()) {
                   count = rs.getInt("aCount");
               }
               return count;
            }

            //Active Revenue details page
            
            public static int getARContractsDetailsCount(int year,char yearTypeVal) throws SQLException {
                query = "select  count(distinct contract_number ) aCount  from agreement_snapshot"
                		+ "   where  document_code_id in (7) "
                		+ "and ("+ year +" between effective_begin_year and effective_end_year)"
                		+ "and ("+ year +" between starting_year and ending_year)";
               rs = amountQueryHelper(yearTypeVal);
               int count = 0;
               while (rs.next()) {
                   count = rs.getInt("aCount");
               }
               return count;
            }
        
            public static int getARContractsModificationsDetailsCount(int year,char yearTypeVal) throws SQLException {
                query = "select  count(distinct contract_number ) aCount  from agreement_snapshot"
                		+ "   where  document_code_id in (7) "
                		+ "and ("+ year +" between effective_begin_year and effective_end_year)"
                		+ "and ("+ year +" between starting_year and ending_year)"
                		+ "and maximum_contract_amount <> original_contract_amount";
               rs = amountQueryHelper(yearTypeVal);
               int count = 0;
               while (rs.next()) {
                   count = rs.getInt("aCount");
               }
               return count;
            }
//MWBE Active Revenue details page

            public static int getMWBEARContractsDetailsCount(int year,char yearTypeVal) throws SQLException {
                query = "select  count(distinct contract_number ) aCount  from agreement_snapshot"
                		+ "   where  document_code_id in (7) "
                		+"and minority_type_id in (2,3,4,5,9) "
                		+ "and ("+ year +" between effective_begin_year and effective_end_year)"
                		+ "and ("+ year +" between starting_year and ending_year)";
               rs = amountQueryHelper(yearTypeVal);
               int count = 0;
               while (rs.next()) {
                   count = rs.getInt("aCount");
               }
               return count;
            }

            public static int getMWBEARContractsModificationsDetailsCount(int year,char yearTypeVal) throws SQLException {
                query = "select  count(distinct contract_number ) aCount  from agreement_snapshot"
                		+ "   where  document_code_id in (7) "
                		+"and minority_type_id in (2,3,4,5,9) "
                		+ "and ("+ year +" between effective_begin_year and effective_end_year)"
                		+ "and ("+ year +" between starting_year and ending_year)"
                		+ "and maximum_contract_amount <> original_contract_amount";
               rs = amountQueryHelper(yearTypeVal);
               int count = 0;
               while (rs.next()) {
                   count = rs.getInt("aCount");
               }
               return count;
            }
            //Pending Expense Details page
            
            public static int getPEContractsDetailsCount(int year,char yearTypeVal) throws SQLException {
                query = "select  count(contract_number ) aCount  from pending_contracts"
                		+ "   where  document_code_id in (1,2,20) ";
               rs = amountQueryHelper(yearTypeVal);
               int count = 0;
               while (rs.next()) {
                   count = rs.getInt("aCount");
               }
               return count;
            }
        
            public static int getPEContractsModificationsDetailsCount(int year,char yearTypeVal) throws SQLException {
                query = "select  count(contract_number ) aCount  from pending_contracts"
                		+ "   where  document_code_id in (1,2,20) "
                		+ " and  original_maximum_amount <> revised_maximum_amount";  
               rs = amountQueryHelper(yearTypeVal);
               int count = 0;
               while (rs.next()) {
                   count = rs.getInt("aCount");
               }
               return count;
            }
            public static int getPEContractsMasterDetailsCount(int year,char yearTypeVal) throws SQLException {
                query = "select  count(contract_number ) aCount  from pending_contracts"
                		+ "   where  document_code_id in (5,6,19) ";
               rs = amountQueryHelper(yearTypeVal);
               int count = 0;
               while (rs.next()) {
                   count = rs.getInt("aCount");
               }
               return count;
            }
        
            public static int getPEContractsMasterModificationsDetailsCount(int year,char yearTypeVal) throws SQLException {
                query = "select  count(contract_number ) aCount  from pending_contracts"
                		+ "   where  document_code_id in (5,6,19) "
                		+ " and  original_maximum_amount <> revised_maximum_amount";  
               rs = amountQueryHelper(yearTypeVal);
               int count = 0;
               while (rs.next()) {
                   count = rs.getInt("aCount");
               }
               return count;
            }
            
               public static int getPEAllContractsDetailsCount(int year,char yearTypeVal) throws SQLException {
                   query = "select  count(contract_number ) aCount  from pending_contracts"
                   		+ "   where  document_code_id in (1,2,5,6,19,20) ";
                  rs = amountQueryHelper(yearTypeVal);
                  int count = 0;
                  while (rs.next()) {
                      count = rs.getInt("aCount");
                  }
                  return count;
               }

               //MWBE Pending Expense Details page

               public static int getMWBEPEContractsDetailsCount(int year,char yearTypeVal) throws SQLException {
                   query = "select  count(contract_number ) aCount  from pending_contracts"
                   		+ "   where  document_code_id in (1,2,20) and minority_type_id in (2,3,4,5,9)  ";
                  rs = amountQueryHelper(yearTypeVal);
                  int count = 0;
                  while (rs.next()) {
                      count = rs.getInt("aCount");
                  }
                  return count;
               }

               public static int getMWBEPEContractsModificationsDetailsCount(int year,char yearTypeVal) throws SQLException {
                   query = "select  count(contract_number ) aCount  from pending_contracts"
                   		+ "   where  document_code_id in (1,2,20) and minority_type_id in (2,3,4,5,9)"
                   		+ " and  original_maximum_amount <> revised_maximum_amount";
                  rs = amountQueryHelper(yearTypeVal);
                  int count = 0;
                  while (rs.next()) {
                      count = rs.getInt("aCount");
                  }
                  return count;
               }
               public static int getMWBEPEContractsMasterDetailsCount(int year,char yearTypeVal) throws SQLException {
                   query = "select  count(contract_number ) aCount  from pending_contracts"
                   		+ "   where  document_code_id in (5,6,19) and minority_type_id in (2,3,4,5,9) ";
                  rs = amountQueryHelper(yearTypeVal);
                  int count = 0;
                  while (rs.next()) {
                      count = rs.getInt("aCount");
                  }
                  return count;
               }

               public static int getMWBEPEContractsMasterModificationsDetailsCount(int year,char yearTypeVal) throws SQLException {
                   query = "select  count(contract_number ) aCount  from pending_contracts"
                   		+ "   where  document_code_id in (5,6,19) and minority_type_id in (2,3,4,5,9) "
                   		+ " and  original_maximum_amount <> revised_maximum_amount";
                  rs = amountQueryHelper(yearTypeVal);
                  int count = 0;
                  while (rs.next()) {
                      count = rs.getInt("aCount");
                  }
                  return count;
               }

                  public static int getMWBEPEAllContractsDetailsCount(int year,char yearTypeVal) throws SQLException {
                      query = "select  count(contract_number ) aCount  from pending_contracts"
                      		+ "   where  document_code_id in (1,2,5,6,19,20) and minority_type_id in (2,3,4,5,9) ";
                     rs = amountQueryHelper(yearTypeVal);
                     int count = 0;
                     while (rs.next()) {
                         count = rs.getInt("aCount");
                     }
                     return count;
                  }

      //Pending Revenue Details
            public static int getPRContractsDetailsCount(int year,char yearTypeVal) throws SQLException {
                query = "select  count(distinct contract_number ) aCount  from pending_contracts"
                		+ "   where  document_code_id in (7) ";
               rs = amountQueryHelper(yearTypeVal);
               int count = 0;
               while (rs.next()) {
                   count = rs.getInt("aCount");
               }
               return count;
            }
        
            public static int getPRContractsModificationsDetailsCount(int year,char yearTypeVal) throws SQLException {
                query = "select  count(distinct contract_number ) aCount  from pending_contracts"
                		+ "   where  document_code_id in (7) "
                		+ " and original_maximum_amount <> revised_maximum_amount ";
               rs = amountQueryHelper(yearTypeVal);
               int count = 0;
               while (rs.next()) {
                   count = rs.getInt("aCount");
               }
               return count;
            }

          //MWBE Pending Revenue Details
            public static int getMWBEPRContractsDetailsCount(int year,char yearTypeVal) throws SQLException {
                query = "select  count(distinct contract_number ) aCount  from pending_contracts"
                		+ "   where  document_code_id in (7) and minority_type_id in (2,3,4,5,9) ";
               rs = amountQueryHelper(yearTypeVal);
               int count = 0;
               while (rs.next()) {
                   count = rs.getInt("aCount");
               }
               return count;
            }

            public static int getMWBEPRContractsModificationsDetailsCount(int year,char yearTypeVal) throws SQLException {
                query = "select  count(distinct contract_number ) aCount  from pending_contracts"
                		+ "   where  document_code_id in (7) and minority_type_id in (2,3,4,5,9) "
                		+ " and original_maximum_amount <> revised_maximum_amount ";
               rs = amountQueryHelper(yearTypeVal);
               int count = 0;
               while (rs.next()) {
                   count = rs.getInt("aCount");
               }
               return count;
            }
           //Registered Expense Contracts Details 
            public static int getREContractsDetailsCount(int year,char yearTypeVal) throws SQLException {
                query = "select  count(distinct contract_number ) aCount  from agreement_snapshot"
                		+ "   where  document_code_id in (1,2) "
                		+ "and registered_year = "+ year +""
                		+ "and ("+ year +" between starting_year and ending_year)";
               rs = amountQueryHelper(yearTypeVal);
               int count = 0;
               while (rs.next()) {
                   count = rs.getInt("aCount");
               }
               return count;
            }
            public static int getREContractsMasterDetailsCount(int year,char yearTypeVal) throws SQLException {
                query = "select  count(distinct contract_number ) aCount  from agreement_snapshot"
                		+ "   where  document_code_id in (5,6) "
                		+ "and registered_year = "+ year +""
                		+ "and ("+ year +" between starting_year and ending_year)";
               rs = amountQueryHelper(yearTypeVal);
               int count = 0;
               while (rs.next()) {
                   count = rs.getInt("aCount");
               }
               return count;
            }
            public static int getREContractsMasterModificationsDetailsCount(int year,char yearTypeVal) throws SQLException {
                query = "select  count(distinct contract_number ) aCount  from agreement_snapshot"
                		+ "   where  document_code_id in (5,6) "
                		+ "and registered_year = "+ year +""
                		+ "and ("+ year +" between starting_year and ending_year)"
                		+ "and maximum_contract_amount <> original_contract_amount";
               rs = amountQueryHelper(yearTypeVal);
               int count = 0;
               while (rs.next()) {
                   count = rs.getInt("aCount");
               }
               return count;
            }
            public static int getREContractsModificationsDetailsCount(int year,char yearTypeVal) throws SQLException {
                              		
                		 query = "select (( select  count(distinct contract_number )from agreement_snapshot"
                         		+ "   where  document_code_id in (1,2) "
                         		+ "and ("+ year +" between starting_year and ending_year)"
                         		+ "and maximum_contract_amount <> original_contract_amount"
                         		+" and( registered_year = "+ year +") ) +"
                     	    	+"	    ( select  count(*) from sub_agreement_snapshot  where contract_number in "
                     	    	+"	 ( select distinct contract_number  from agreement_snapshot  where "
                     	    	+"	 document_code_id in (1,2) and ("+ year +" between starting_year and ending_year)"
                     	    	+ "and maximum_contract_amount <> original_contract_amount"
                     	    		+"    and ( registered_year = "+ year +")) and latest_flag ='Y')) aCount";
               rs = amountQueryHelper(yearTypeVal);
               int count = 0;
               while (rs.next()) {
                   count = rs.getInt("aCount");
               }
               return count;
            }
        
            public static int getREAllContractsDetailsCount(int year,char yearTypeVal) throws SQLException {
                query = "select ((select  count(distinct contract_number ) aCount  from agreement_snapshot"
                		+ "   where  document_code_id in (1,2) "                		
                		+ "and ("+ year +" between starting_year and ending_year)"
                		+ "and (registered_year = "+ year +")) +"
                		+"	    ( select  count(*) from sub_agreement_snapshot  where contract_number in "
            	    	+"	 ( select distinct contract_number  from agreement_snapshot  where "
            	    	+"	 document_code_id in (1,2) and ("+ year +" between starting_year and ending_year)"
            	    		+"    and (registered_year = "+ year +")) and latest_flag ='Y')) aCount";;
               rs = amountQueryHelper(yearTypeVal);
               int count = 0;
               while (rs.next()) {
                   count = rs.getInt("aCount");
               }
               return count;
            }
            public static int getREAll1ContractsDetailsCount(int year,char yearTypeVal) throws SQLException {
                query =    " select  ((select  count(*) from agreement_snapshot  where "
                	    	+"	 document_code_id in (1,2,5) and ("+ year +" between starting_year and ending_year)"
                	    	+"	    and (registered_year = "+ year +")) +"
                	    	+"	    ( select  count(*) from sub_agreement_snapshot  where contract_number in "
                	    	+"	 ( select distinct contract_number  from agreement_snapshot  where "
                	    	+"	 document_code_id in (1,2,5) and ("+ year +" between starting_year and ending_year)"
                	    		+"    and (registered_year = "+ year +")) and latest_flag ='Y')) aCount";
                rs = amountQueryHelper(yearTypeVal);
               
                int count = 0;
               while (rs.next()) {
                   count = rs.getInt("aCount");
               }
               return count;
            }

            //MWBE Registered Expense Contracts Details
            public static int getMWBEREContractsDetailsCount(int year,char yearTypeVal) throws SQLException {
                query = "select  count(distinct contract_number ) aCount  from agreement_snapshot"
                		+ "   where  document_code_id in (1,2)  and minority_type_id in (2,3,4,5,9) "
                		+ "and registered_year = "+ year +""
                		+ "and ("+ year +" between starting_year and ending_year)";
               rs = amountQueryHelper(yearTypeVal);
               int count = 0;
               while (rs.next()) {
                   count = rs.getInt("aCount");
               }
               return count;
            }
            public static int getMWBEREContractsMasterDetailsCount(int year,char yearTypeVal) throws SQLException {
                query = "select  count(distinct contract_number ) aCount  from agreement_snapshot"
                		+ "   where  document_code_id in (5,6)  and minority_type_id in (2,3,4,5,9) "
                		+ "and registered_year = "+ year +""
                		+ "and ("+ year +" between starting_year and ending_year)";
               rs = amountQueryHelper(yearTypeVal);
               int count = 0;
               while (rs.next()) {
                   count = rs.getInt("aCount");
               }
               return count;
            }
            public static int getMWBEREContractsMasterModificationsDetailsCount(int year,char yearTypeVal) throws SQLException {
                query = "select  count(distinct contract_number ) aCount  from agreement_snapshot"
                		+ "   where  document_code_id in (5,6)  and minority_type_id in (2,3,4,5,9) "
                		+ "and registered_year = "+ year +""
                		+ "and ("+ year +" between starting_year and ending_year)"
                		+ "and maximum_contract_amount <> original_contract_amount";
               rs = amountQueryHelper(yearTypeVal);
               int count = 0;
               while (rs.next()) {
                   count = rs.getInt("aCount");
               }
               return count;
            }
            public static int getMWBEREContractsModificationsDetailsCount(int year,char yearTypeVal) throws SQLException {

                		 query = "select (( select  count(distinct contract_number )from agreement_snapshot"
                         		+ "   where  document_code_id in (1,2)  and minority_type_id in (2,3,4,5,9) "
                         		+ "and ("+ year +" between starting_year and ending_year)"
                         		+ "and maximum_contract_amount <> original_contract_amount"
                         		+" and( registered_year = "+ year +") ) +"
                     	    	+"	    ( select  count(*) from sub_agreement_snapshot  where contract_number in "
                     	    	+"	 ( select distinct contract_number  from agreement_snapshot  where "
                     	    	+"	 document_code_id in (1,2)  and minority_type_id in (2,3,4,5,9) and ("+ year +" between starting_year and ending_year)"
                     	    	+ "and maximum_contract_amount <> original_contract_amount"
                     	    		+"    and ( registered_year = "+ year +")) and latest_flag ='Y')) aCount";
               rs = amountQueryHelper(yearTypeVal);
               int count = 0;
               while (rs.next()) {
                   count = rs.getInt("aCount");
               }
               return count;
            }

            public static int getMWBEREAllContractsDetailsCount(int year,char yearTypeVal) throws SQLException {
                query = "select ((select  count(distinct contract_number ) aCount  from agreement_snapshot"
                		+ "   where  document_code_id in (1,2)  and minority_type_id in (2,3,4,5,9)  "
                		+ "and ("+ year +" between starting_year and ending_year)"
                		+ "and (registered_year = "+ year +")) +"
                		+"	    ( select  count(*) from sub_agreement_snapshot  where contract_number in "
            	    	+"	 ( select distinct contract_number  from agreement_snapshot  where "
            	    	+"	 document_code_id in (1,2)  and minority_type_id in (2,3,4,5,9) and ("+ year +" between starting_year and ending_year)"
            	    		+"    and (registered_year = "+ year +")) and latest_flag ='Y')) aCount";
               rs = amountQueryHelper(yearTypeVal);
               int count = 0;
               while (rs.next()) {
                   count = rs.getInt("aCount");
               }
               return count;
            }
            public static int getMWBEREAll1ContractsDetailsCount(int year,char yearTypeVal) throws SQLException {
                query =    " select  ((select  count(*) from agreement_snapshot  where "
                	    	+"	 document_code_id in (1,2,5)  and minority_type_id in (2,3,4,5,9) and  ("+ year +" between starting_year and ending_year)"
                	    	+"	    and (registered_year = "+ year +")) +"
                	    	+"	    ( select  count(*) from sub_agreement_snapshot  where contract_number in "
                	    	+"	 ( select distinct contract_number  from agreement_snapshot  where "
                	    	+"	 document_code_id in (1,2,5)  and minority_type_id in (2,3,4,5,9) and ("+ year +" between starting_year and ending_year)"
                	    		+"    and (registered_year = "+ year +")) and latest_flag ='Y')) aCount";
                rs = amountQueryHelper(yearTypeVal);

                int count = 0;
               while (rs.next()) {
                   count = rs.getInt("aCount");
               }
               return count;
            }
        //Registered Revenue Details pages
            
            public static int getRRContractsDetailsCount(int year,char yearTypeVal) throws SQLException {
                query = "select  count(distinct contract_number ) aCount  from agreement_snapshot"
                		+ "   where  document_code_id in (7) "
                		+ "and registered_year = "+ year +""
                		+ "and ("+ year +" between starting_year and ending_year)";
               rs = amountQueryHelper(yearTypeVal);
               int count = 0;
               while (rs.next()) {
                   count = rs.getInt("aCount");
               }
               return count;
            }

            public static int getRRContractsModificationsDetailsCount(int year,char yearTypeVal) throws SQLException {
                query = "select  count(distinct contract_number ) aCount  from agreement_snapshot"
                		+ "   where  document_code_id in (7) "
                		+ "and registered_year = "+ year +""
                		+ "and ("+ year +" between starting_year and ending_year)"
                		+ "and maximum_contract_amount <> original_contract_amount";
               rs = amountQueryHelper(yearTypeVal);
               int count = 0;
               while (rs.next()) {
                   count = rs.getInt("aCount");
               }
               return count;
            }

            public static int getTotalRegisteredSubContractsCount(int year, char yearTypeVal) throws SQLException {
                query = "select count(*) aCount from sub_agreement_snapshot WHERE document_code_id IN (1, 2)\r\n" +
                        "AND registered_year ="+year+ "AND("+year+" BETWEEN starting_year AND ending_year)";
                rs = amountQueryHelper(yearTypeVal);
                int count = 0;
                while (rs.next()) {
                    count = rs.getInt("aCount");
                }
                return count;
            }

            //MWBE Registered Revenue Details pages

            public static int getMWBERRContractsDetailsCount(int year,char yearTypeVal) throws SQLException {
                query = "select  count(distinct contract_number ) aCount  from agreement_snapshot"
                        + "   where  document_code_id in (7)  and minority_type_id in (2,3,4,5,9) "
                        + "and registered_year = "+ year +""
                        + "and ("+ year +" between starting_year and ending_year)";
                rs = amountQueryHelper(yearTypeVal);
                int count = 0;
                while (rs.next()) {
                    count = rs.getInt("aCount");
                }
                return count;
            }

            public static int getMWBERRContractsModificationsDetailsCount(int year,char yearTypeVal) throws SQLException {
                query = "select  count(distinct contract_number ) aCount  from agreement_snapshot"
                        + "   where  document_code_id in (7)  and minority_type_id in (2,3,4,5,9) "
                        + "and registered_year = "+ year +""
                        + "and ("+ year +" between starting_year and ending_year)"
                        + "and maximum_contract_amount <> original_contract_amount";
                rs = amountQueryHelper(yearTypeVal);
                int count = 0;
                while (rs.next()) {
                    count = rs.getInt("aCount");
                }
                return count;
            }
///Sub Vendors Active expense  Details
            
        	public static int getSubContractsDetailsCount(int year , char yeartypeVal) throws SQLException{

		           query = 	"select count(*) aCount from sub_agreement_snapshot  WHERE document_code_id IN (1, 2, 5)" +
		           		"AND("+year+"    BETWEEN effective_begin_year AND effective_end_year)" +
		           		" AND("+year+"    BETWEEN starting_year AND ending_year)";
		        	rs =  amountQueryHelper(yeartypeVal);
		        	  int count = 0;
			           while (rs.next()) {
			               count = rs.getInt("aCount");
			           }
			          return count;
		        }
        	
        	public static String getSubContractsDetailsAmount(int year, char yearTypeVal) throws SQLException {
        		 query = 	"select sum(maximum_contract_amount) aSum from sub_agreement_snapshot  WHERE document_code_id IN (1, 2, 5)" +
 		           		"AND("+year+"    BETWEEN effective_begin_year AND effective_end_year)" +
 		           		" AND("+year+"    BETWEEN starting_year AND ending_year)";

        	    rs = amountQueryHelper(yearTypeVal);

        	    BigDecimal totalAmount = new BigDecimal(0);

        	    while (rs.next()) {
        	        totalAmount = rs.getBigDecimal("aSum");
        	    }
        	    return formatNumber2(totalAmount);
        	    // .divide(new BigDecimal(1000000000)).setScale(1, BigDecimal.ROUND_HALF_UP);
        	}
        	
        	public static int getSubContractsModDetailsCount(int year , char yeartypeVal) throws SQLException{

		           query = 	"select count(*) aCount from sub_agreement_snapshot  WHERE document_code_id IN (1, 2, 5)" +
		           		"AND("+year+"    BETWEEN effective_begin_year AND effective_end_year)" +
		           		" AND("+year+"    BETWEEN starting_year AND ending_year)"+
		           	 "and maximum_contract_amount <> original_contract_amount";
		        	rs =  amountQueryHelper(yeartypeVal);
		        	  int count = 0;
			           while (rs.next()) {
			               count = rs.getInt("aCount");
			           }
			          return count;
		        }
     	
     	public static String getSubContractsModDetailsAmount(int year, char yearTypeVal) throws SQLException {
     		 query = 	"select sum(maximum_contract_amount) aSum from sub_agreement_snapshot  WHERE document_code_id IN (1, 2, 5)" +
		           		"AND("+year+"    BETWEEN effective_begin_year AND effective_end_year)" +
		           		" AND("+year+"    BETWEEN starting_year AND ending_year)" +
		           		"and maximum_contract_amount <> original_contract_amount";

     	    rs = amountQueryHelper(yearTypeVal);

     	    BigDecimal totalAmount = new BigDecimal(0);

     	    while (rs.next()) {
     	        totalAmount = rs.getBigDecimal("aSum");
     	    }
     	    return formatNumber2(totalAmount);
     	    // .divide(new BigDecimal(1000000000)).setScale(1, BigDecimal.ROUND_HALF_UP);
     	}
        	
///Sub Vendors Registered expense  Details
            
        	public static int getSubContractsRegisteredDetailsCount(int year , char yeartypeVal) throws SQLException{

		           query = 	"select count(*) aCount from sub_agreement_snapshot  WHERE document_code_id IN (1, 2, 5)" +
		        		   "AND registered_year =" + year + "" +
		           		"                        AND("+year+"    BETWEEN starting_year AND ending_year)";
		        	rs =  amountQueryHelper(yeartypeVal);
		        	  int count = 0;
			           while (rs.next()) {
			               count = rs.getInt("aCount");
			           }
			          return count;
		        }
        	
        	public static String getSubContractsRegisteredDetailsAmount(int year, char yearTypeVal) throws SQLException {
        		 query = 	"select sum(maximum_contract_amount) aSum from sub_agreement_snapshot  WHERE document_code_id IN (1, 2, 5)" +
        				 "AND registered_year =" + year + "" +
 		           		" AND("+ year +"  BETWEEN starting_year AND ending_year)";

        	    rs = amountQueryHelper(yearTypeVal);

        	    BigDecimal totalAmount = new BigDecimal(0);

        	    while (rs.next()) {
        	        totalAmount = rs.getBigDecimal("aSum");
        	    }
        	    return formatNumber2(totalAmount);
        	    // .divide(new BigDecimal(1000000000)).setScale(1, BigDecimal.ROUND_HALF_UP);
        	}
        	
          	public static int getSubContractsModRegisteredDetailsCount(int year , char yeartypeVal) throws SQLException{

		           query = 	"select count(*) aCount from sub_agreement_snapshot  WHERE document_code_id IN (1, 2, 5) "+		        		   
		        		   "AND registered_year =" + year + "" +
		           		"AND(  "+ year +" BETWEEN starting_year AND ending_year)"+
		           		 "and maximum_contract_amount <> original_contract_amount";
		        	rs =  amountQueryHelper(yeartypeVal);
		        	  int count = 0;
			           while (rs.next()) {
			               count = rs.getInt("aCount");
			           }
			          return count;
		        }
     	
     	public static String getSubContractsModRegisteredDetailsAmount(int year, char yearTypeVal) throws SQLException {
     		 query = 	"select sum(maximum_contract_amount) aSum from sub_agreement_snapshot  WHERE document_code_id IN (1, 2, 5) and maximum_contract_amount <> original_contract_amount " +
     				 "AND registered_year =" + year + "" +
		           		" AND("+year+"    BETWEEN starting_year AND ending_year)";

     	    rs = amountQueryHelper(yearTypeVal);

     	    BigDecimal totalAmount = new BigDecimal(0);

     	    while (rs.next()) {
     	        totalAmount = rs.getBigDecimal("aSum");
     	    }
     	    return formatNumber2(totalAmount);
     	    // .divide(new BigDecimal(1000000000)).setScale(1, BigDecimal.ROUND_HALF_UP);
     	}
         
     	//Subvendors 3rd bottom nav
    	public static int getSubContractsStatusbyPrimeContractDetailsCount(int year , char yeartypeVal) throws SQLException{

	           query = 	"select count(*) aCount from agreement_snapshot a left join (select contract_number,vendor_history_id, aprv_sta,sub_contract_id  from subcontract_details where latest_flag='Y') sd on a.contract_number=sd.contract_number"
	           		+ "  WHERE a.document_code_id IN (1, 2) and a.scntrc_status =2" +
	           		"AND("+year+" BETWEEN a.effective_begin_year AND a.effective_end_year)" +
	           		" AND("+year+" BETWEEN a.starting_year AND a.ending_year)";
	        	rs =  amountQueryHelper(yeartypeVal);
	        	  int count = 0;
		           while (rs.next()) {
		               count = rs.getInt("aCount");
		           }
		          return count;
	        }
            
            //Sub Vendors
			public static int  getTotalRegisteredPrimeVendorCount(int year, char c) throws SQLException {
				// TODO Auto-generated method stub
				query = "select count(distinct Prime_vendor_id) aCount from sub_agreement_snapshot WHERE document_code_id IN (1, 2)\r\n" +
						"AND registered_year =" + year + "" +
						"AND("+ year +" BETWEEN starting_year AND ending_year)";
				   rs = amountQueryHelper(c);
                   int count = 0;
                   while (rs.next()) {
                     count = rs.getInt("aCount");
                 }
                 return count;
			}

			public static int getTotalRegisteredSubVendorContracts(int year, char c)  throws SQLException{
				// TODO Auto-generated method stub
				query = "select count(*) aCount from sub_agreement_snapshot WHERE document_code_id IN (1, 2)\r\n" +
						"  AND registered_year ="+year+"\r\n" +
						"  AND("+year+" BETWEEN starting_year AND ending_year)";
				 rs = amountQueryHelper(c);
	                int count = 0;
	                while (rs.next()) {
	                  count = rs.getInt("aCount");
	              }
	                return count;
			}

			public static int getTotalRegisteredSubVendorContractsSizeCount(int year, char c)  throws SQLException{
				// TODO Auto-generated method stub
				query = "select count(*) aCount from sub_agreement_snapshot WHERE document_code_id IN (1, 2)\r\n" +
						"  AND registered_year ="+year+"\r\n"+
						"  AND("+year+" BETWEEN starting_year AND ending_year)";
				 rs = amountQueryHelper(c);
	                int count = 0;
	                while (rs.next()) {
	                  count = rs.getInt("aCount");
	              }
	                return count;
			}

			public static int getTotalRegisteredSubVendorAwardMethodsCount(int year, char c)throws SQLException {
				// TODO Auto-generated method stub
				query ="select count(distinct award_method_id) aCount from sub_agreement_snapshot WHERE document_code_id IN (1, 2)\r\n" +
						"AND registered_year ="+year+"\r\n" +
						"AND("+year+"  BETWEEN starting_year AND ending_year)";

				 rs = amountQueryHelper(c);
                 int count = 0;
                 while (rs.next()) {
                   count = rs.getInt("aCount");
               }
               return count;
			}

			public static int getTotalRegisteredSubVendorContractsAgenciesCount(int year, char c) throws SQLException {
				// TODO Auto-generated method stub
				query = "select count(distinct agency_id) aCount from sub_agreement_snapshot WHERE document_code_id IN (1, 2)\r\n" +
						"AND registered_year ="+year+"\r\n" +
						"AND("+year+" BETWEEN starting_year AND ending_year)";
				  rs = amountQueryHelper(c);
                  int count = 0;
                  while (rs.next()) {
                    count = rs.getInt("aCount");
                }
                return count;


			}

			public static int getTotalRegisteredSubContractModifications(int year, char c) throws SQLException {
				// TODO Auto-generated method stub
			   query=	"select count(*) aCount from sub_agreement_snapshot WHERE document_code_id IN (1, 2)\r\n" +
			   		"    and dollar_difference <> 0\r\n" +
			   		"    AND registered_year ="+year+"\r\n" +
			   		"    AND("+year+" BETWEEN starting_year AND ending_year)\r\n"
			   		;
			     rs = amountQueryHelper(c);
                int count = 0;
                while (rs.next()) {
                  count = rs.getInt("aCount");
              }
              return count;
			}

			public static int getRegisteredSubVendorContractsByIndustriesCount(int year, char c) throws  SQLException {
				// TODO Auto-generated method stub
				query = "select count(*) aCount from sub_agreement_snapshot WHERE document_code_id IN (1, 2)\r\n" +
						"  AND registered_year ="+year+"\r\n" +
						"  AND("+year+" BETWEEN starting_year AND ending_year)";
				 rs = amountQueryHelper(c);
	                int count = 0;
	                while (rs.next()) {
	                  count = rs.getInt("aCount");
	              }
	                return count;
			}

			public static int getRegisteredSubVendorsCount(int year, char c) throws SQLException {
				// TODO Auto-generated method stub
				query = "select count(distinct vendor_id) aCount from sub_agreement_snapshot WHERE document_code_id IN (1, 2)\r\n" +
						"AND registered_year ="+year+"\r\n" +
						"AND("+year+" BETWEEN starting_year AND ending_year)";
				 rs = amountQueryHelper(c);
	                int count = 0;
	                while (rs.next()) {
	                  count = rs.getInt("aCount");
	              }
	              return count;
			}
			public static int getTotalSubContractsCount(int year , char yeartypeVal) throws SQLException{

		           query = 	"select count(*) aCount from sub_agreement_snapshot  WHERE document_code_id IN (1, 2, 5)\r\n" +
		           		"AND("+year+"    BETWEEN effective_begin_year AND effective_end_year)\r\n" +
		           		"                        AND("+year+"    BETWEEN starting_year AND ending_year)";
		        	rs =  amountQueryHelper(yeartypeVal);
		        	  int count = 0;
			           while (rs.next()) {
			               count = rs.getInt("aCount");
			           }
			          return count;
		        }
		        public static int getSubVendorsCount(int year, char yearTypeVal) throws SQLException{
		        	 query = 	"SELECT count(DISTINCT vendor_id) aCount\r\n" +
		          			"          	            FROM aggregateon_subven_contracts_cumulative_spending a JOIN ref_document_code rfe\r\n" +
		          			"          			      ON rfe.document_code_id = a.document_code_id\r\n" +
		          			"          	            WHERE a.type_of_year='B' and a.status_flag='A' and rfe.document_code IN ('CTA1','CT1') and a.fiscal_year ="+ year;

		         		rs =  amountQueryHelper(yearTypeVal);
		           	  int count = 0;
		   	           while (rs.next()) {
		   	               count = rs.getInt("aCount");
		   	           }
		   	          return count;
		        }
		        public static int getTotalPrimeVendorCount(int year , char yeartypeVal) throws SQLException{
		        	 query = 	"select count(distinct Prime_vendor_id)aCount  from sub_agreement_snapshot  WHERE document_code_id IN (1, 2)\r\n" +

		        	 		"AND("+year+    " BETWEEN effective_begin_year AND effective_end_year) \r\n" +
		        	 		"                        AND("+year+"  BETWEEN starting_year AND ending_year)";

		        		rs =  amountQueryHelper(yeartypeVal);
		          	  int count = 0;
		  	           while (rs.next()) {
		  	               count = rs.getInt("aCount");
		  	           }
		  	          return count;
		        }
		        public static int getTotalAwardMethodsCount(int year , char yeartypeVal) throws SQLException{
		        	query ="SELECT  COUNT(DISTINCT a.award_method_id) aCount\r\n" +
		        			"          FROM aggregateon_subven_contracts_cumulative_spending a\r\n" +
		        			"            JOIN ref_document_code b ON a.document_code_id = b.document_code_id\r\n" +
		        			"            JOIN ref_award_method e ON e.award_method_id = a.award_method_id\r\n" +
		        			"          WHERE  a.type_of_year = 'B' AND a.status_flag = 'A' AND b.document_code IN ('MA1','CTA1','CT1') and a.fiscal_year ="+ year;
		        	rs =  amountQueryHelper(yeartypeVal);
		        	  int count = 0;
			           while (rs.next()) {
			               count = rs.getInt("aCount");
			           }
			          return count;
		        }
		        public static int getTotalSubContractModifications(int year, char yeartypeVal) throws SQLException{
		        	 query ="SELECT count(contract_number) aCount from sub_agreement_snapshot  WHERE document_code_id IN (1, 2)\r\n" +
		        	 		"AND dollar_difference <> 0 AND("+year+  "   BETWEEN effective_begin_year AND effective_end_year) \r\n" +
		        	 		"                        AND("+year+  "      BETWEEN starting_year AND ending_year)";
		         	   rs =  amountQueryHelper(yeartypeVal);
		           	   int count = 0;
		 	           while (rs.next()) {
		 	               count = rs.getInt("aCount");
		 	           }
		 	          return count;
		        }

		        public static int getTotalContractsAgenciesCount(int year, char yeartypeVal) throws SQLException{
		        	 query ="select count(distinct agency_id)aCount  from sub_agreement_snapshot  WHERE document_code_id IN (1, 2)\r\n" +

		        	 		"AND("+year+    " BETWEEN effective_begin_year AND effective_end_year) \r\n" +
		        	 		"                        AND("+year+"  BETWEEN starting_year AND ending_year)";
		        	   rs =  amountQueryHelper(yeartypeVal);
		          	   int count = 0;
			           while (rs.next()) {
			               count = rs.getInt("aCount");
			           }
			          return count;
		        }
		        public static int getTotalContractsIndustriesCount(int year, char yeartypeVal) throws SQLException{
		       	 query ="select count(*) aCount from sub_agreement_snapshot  WHERE document_code_id IN (1, 2, 5)\r\n" +
		            		"AND("+year+"    BETWEEN effective_begin_year AND effective_end_year)\r\n" +
		               		"                        AND("+year+"    BETWEEN starting_year AND ending_year)";

		       	   rs =  amountQueryHelper(yeartypeVal);
		         	   int count = 0;
			           while (rs.next()) {
			               count = rs.getInt("aCount");
			           }
			          return count;
		       }
		        public static int getTotalContractsSizeCount(int year, char yeartypeVal) throws SQLException{
		          	 query ="select count(*) aCount from sub_agreement_snapshot  WHERE document_code_id IN (1, 2, 5)\r\n" +
		                		"AND("+year+"    BETWEEN effective_begin_year AND effective_end_year)\r\n" +
		                   		"                        AND("+year+"    BETWEEN starting_year AND ending_year)";

		          	   rs =  amountQueryHelper(yeartypeVal);
		            	   int count = 0;
		   	           while (rs.next()) {
		   	               count = rs.getInt("aCount");
		   	           }
		   	          return count;
		          }
		        public static int getTotalSubVendorContracts(int year, char yeartypeVal) throws SQLException{
		         	 query ="select count(*) aCount from sub_agreement_snapshot  WHERE document_code_id IN (1, 2, 5)\r\n" +
		               		"AND("+year+"    BETWEEN effective_begin_year AND effective_end_year)\r\n" +
		                  		"                        AND("+year+"    BETWEEN starting_year AND ending_year)";

		         	   rs =  amountQueryHelper(yeartypeVal);
		           	   int count = 0;
		  	           while (rs.next()) {
		  	               count = rs.getInt("aCount");
		  	           }
		  	          return count;
		         }
//Contracts Subvendors 3rd bottom nav
			
				
				   public static int getSubContractStatusbyPrimeContractIDCount(int year, char yeartypeVal) throws SQLException{
					      query = 	"select count(distinct a.Contract_number) aCount from agreement_snapshot a left join (select contract_number,vendor_history_id, aprv_sta,sub_contract_id  from subcontract_details where latest_flag='Y') sd on a.contract_number=sd.contract_number"
				 	           		+ "  WHERE a.document_code_id IN (1, 2) and a.scntrc_status =2" +
				 	           		"AND("+year+" BETWEEN a.effective_begin_year AND a.effective_end_year)" +
				 	           		" AND("+year+" BETWEEN a.starting_year AND a.ending_year)";
				 	        	rs =  amountQueryHelper(yeartypeVal);
				 	        	  int count = 0;
				 		           while (rs.next()) {
				 		               count = rs.getInt("aCount");
				 		           }
				 		          return count;
				   }
			  	        public static int getPrimeContractSubVendorReportingCount(int year, char yeartypeVal) throws SQLException{
			 	           query = 	"select count(distinct a.Contract_number) aCount from agreement_snapshot a "
			 	           		+ "  WHERE a.document_code_id IN (1, 2) " +
			 	           		"AND("+year+" BETWEEN a.effective_begin_year AND a.effective_end_year)" +
			 	           		" AND("+year+" BETWEEN a.starting_year AND a.ending_year)";
			 	        	rs =  amountQueryHelper(yeartypeVal);
			 	        	  int count = 0;
			 		           while (rs.next()) {
			 		               count = rs.getInt("aCount");
			 		           }
			 		          return count;
			 	        }
			  	        
			  	  
}
