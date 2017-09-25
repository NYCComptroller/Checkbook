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

public class NYCDatabaseUtilSpending {

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
  
    public static String getSpendingAmount(int year, char yearTypeVal) throws SQLException {
        query = "SELECT SUM(check_amount) sumSpendingAmt "
                + "FROM disbursement_line_item_details"
                + " WHERE fiscal_year = " + year;

        query2 = "SELECT sum(check_amount) sumSpendingAmt "
                + "FROM disbursement_line_item_details "
                + "WHERE calendar_fiscal_year = " + year;

        rs = amountQueryHelper(yearTypeVal);

        BigDecimal totalSpendingAmount = new BigDecimal(0);

        while (rs.next()) {
            totalSpendingAmount = rs.getBigDecimal("sumSpendingAmt");
        }
        return formatNumber(totalSpendingAmount);
        // .divide(new BigDecimal(1000000000)).setScale(1, BigDecimal.ROUND_HALF_UP);
    }

  
        //Spending widget counts
        
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
           // query2 = "SELECT COUNT(*) aCount from disbursement_line_item_details where fiscal_year =" + year ;  

           rs = amountQueryHelper(yearTypeVal);
           int count = 0;
           while (rs.next()) {
               count = rs.getInt("aCount");
           }
        
           return count;
           
        }
        

		public static int getTotalSpendingExpCategoriesCount(int year, char yearTypeVal)  throws SQLException {
			// TODO Auto-generated method stub
		       query = "SELECT COUNT(distinct expenditure_object_id) aCount from disbursement_line_item_details where fiscal_year =" + year ;
	           // query2 = "SELECT COUNT(*) aCount from disbursement_line_item_details where fiscal_year =" + year ;  

	           rs = amountQueryHelper(yearTypeVal);
	           int count = 0;
	           while (rs.next()) {
	               count = rs.getInt("aCount");
	           }
	        
	           return count;
	           
	       		}  		
        
public static int getTotalSpendingContractsCount(int year,char yearTypeVal) throws SQLException {
    query =   "SELECT count(DISTINCT document_id) as aCount FROM aggregateon_mwbe_spending_contract WHERE type_of_year = 'B' AND year_id = "+ year;

   rs = amountQueryHelper(yearTypeVal);
   int count = 0;
   while (rs.next()) {
       count = rs.getInt("aCount");
   }

   return count;
   
}


// total Spending  widget Details count

public static int getTotalSpendingDetailsCount(int year,char yearTypeVal) throws SQLException {
     query = "select(  (select count(*)   from disbursement_line_item_Details  where fiscal_year= 2016 ) + " 
   +"  (select count(*) from subcontract_spending_Details where fiscal_year = 2016 )  ) aCount";
    		 
  rs = amountQueryHelper(yearTypeVal);
   int count = 0;
   while (rs.next()) {
       count = rs.getInt("aCount");
   }

   return count;
   
}    

public static int getTotalSpendingContractsDetailsCount(int year,char yearTypeVal) throws SQLException {
    query = "select(  (select count(*)   from disbursement_line_item_Details  where fiscal_year= 2016 and contract_document_code in ( 'CT1', 'CTA1', 'POD', 'POC', 'PCC1', 'DO1','MA1','MMA1')) + " 
  +"  (select count(*) from subcontract_spending_Details where fiscal_year = 2016 and contract_document_code in ( 'CT1', 'CTA1', 'POD', 'POC', 'PCC1', 'DO1','MA1','MMA1'))  ) aCount";
   		 
 rs = amountQueryHelper(yearTypeVal);
  int count = 0;
  while (rs.next()) {
      count = rs.getInt("aCount");
  }

  return count;
  
}  


//Contract Spending  widget Details count

public static int getContractSpendingDetailsCount(int year,char yearTypeVal) throws SQLException {
  query = "select(  (select count(*)   from disbursement_line_item_Details  where   spending_category_id = 1 and  fiscal_year= 2016 ) + " 
+"  (select count(*) from subcontract_spending_Details where fiscal_year = 2016 )  ) aCount";
 		 
rs = amountQueryHelper(yearTypeVal);
int count = 0;
while (rs.next()) {
    count = rs.getInt("aCount");
}

return count;

}    

public static int getContractSpendingContractsDetailsCount(int year,char yearTypeVal) throws SQLException {
 query = "select(  (select count(*)   from disbursement_line_item_Details  where fiscal_year= 2016 and contract_document_code in ( 'CT1', 'CTA1', 'POD', 'POC', 'PCC1', 'DO1','MA1','MMA1')) + " 
+"  (select count(*) from subcontract_spending_Details where fiscal_year = 2016 and contract_document_code in ( 'CT1', 'CTA1', 'POD', 'POC', 'PCC1', 'DO1','MA1','MMA1'))  ) aCount";
		 
rs = amountQueryHelper(yearTypeVal);
int count = 0;
while (rs.next()) {
   count = rs.getInt("aCount");
}

return count;

} 


//Payroll Spending  widget Details count

public static int getPayrollSpendingDetailsCount(int year,char yearTypeVal) throws SQLException {
query = "select(  (select count(*)   from disbursement_line_item_Details  where  spending_category_id = 2 and fiscal_year= 2016 ) + " 
+"  (select count(*) from subcontract_spending_Details where fiscal_year = 2016 )  ) aCount";
		 
rs = amountQueryHelper(yearTypeVal);
int count = 0;
while (rs.next()) {
  count = rs.getInt("aCount");
}

return count;

}    



//Capital Spending  widget Details count

public static int getCapitalSpendingDetailsCount(int year,char yearTypeVal) throws SQLException {
query = "select(  (select count(*)   from disbursement_line_item_Details  where  spending_category_id = 3 and  fiscal_year= 2016 ) + " 
+"  (select count(*) from subcontract_spending_Details where fiscal_year = 2016 )  ) aCount";
		 
rs = amountQueryHelper(yearTypeVal);
int count = 0;
while (rs.next()) {
count = rs.getInt("aCount");
}

return count;

}    

public static int getCapitalSpendingContractsDetailsCount(int year,char yearTypeVal) throws SQLException {
query = "select(  (select count(*)   from disbursement_line_item_Details  where fiscal_year= 2016 and contract_document_code in ( 'CT1', 'CTA1', 'POD', 'POC', 'PCC1', 'DO1','MA1','MMA1')) + " 
+"  (select count(*) from subcontract_spending_Details where fiscal_year = 2016 and contract_document_code in ( 'CT1', 'CTA1', 'POD', 'POC', 'PCC1', 'DO1','MA1','MMA1'))  ) aCount";
		 
rs = amountQueryHelper(yearTypeVal);
int count = 0;
while (rs.next()) {
count = rs.getInt("aCount");
}

return count;

} 


//Trust Agency Spending  widget Details count

public static int getTrustAgencySpendingDetailsCount(int year,char yearTypeVal) throws SQLException {
query = "select(  (select count(*)   from disbursement_line_item_Details  where  spending_category_id = 5 and  fiscal_year= 2016 ) + " 
+"  (select count(*) from subcontract_spending_Details where fiscal_year = 2016 )  ) aCount";
		 
rs = amountQueryHelper(yearTypeVal);
int count = 0;
while (rs.next()) {
count = rs.getInt("aCount");
}

return count;

}    

public static int getTrustAgencySpendingContractsDetailsCount(int year,char yearTypeVal) throws SQLException {
query = "select(  (select count(*)   from disbursement_line_item_Details  where fiscal_year= 2016 and contract_document_code in ( 'CT1', 'CTA1', 'POD', 'POC', 'PCC1', 'DO1','MA1','MMA1')) + " 
+"  (select count(*) from subcontract_spending_Details where fiscal_year = 2016 and contract_document_code in ( 'CT1', 'CTA1', 'POD', 'POC', 'PCC1', 'DO1','MA1','MMA1'))  ) aCount";
		 
rs = amountQueryHelper(yearTypeVal);
int count = 0;
while (rs.next()) {
count = rs.getInt("aCount");
}

return count;

} 

//Trust Agency Spending  widget Details count

public static int OtherAgencySpendingDetailsCount(int year,char yearTypeVal) throws SQLException {
query = "select(  (select count(*)   from disbursement_line_item_Details  where  spending_category_id = 4 and  fiscal_year= 2016 ) + " 
+"  (select count(*) from subcontract_spending_Details where fiscal_year = 2016 )  ) aCount";
		 
rs = amountQueryHelper(yearTypeVal);
int count = 0;
while (rs.next()) {
count = rs.getInt("aCount");
}

return count;

}    

public static int getOtehrSpendingContractsDetailsCount(int year,char yearTypeVal) throws SQLException {
query = "select(  (select count(*)   from disbursement_line_item_Details  where fiscal_year= 2016 and contract_document_code in ( 'CT1', 'CTA1', 'POD', 'POC', 'PCC1', 'DO1','MA1','MMA1')) + " 
+"  (select count(*) from subcontract_spending_Details where fiscal_year = 2016 and contract_document_code in ( 'CT1', 'CTA1', 'POD', 'POC', 'PCC1', 'DO1','MA1','MMA1'))  ) aCount";
		 
rs = amountQueryHelper(yearTypeVal);
int count = 0;
while (rs.next()) {
count = rs.getInt("aCount");
}

return count;

} 


}
