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

public class OGENYCDatabaseUtil {

    private static Connection con = null;
    private static Statement stmt = null;
    private static ResultSet rs = null;
    private static String query = null;
    private static String query2 = null;

    // TODO: t.le: move to NYCBaseTest?
    // Establishes connection
    static void connectToDatabase() throws SQLException, ClassNotFoundException {
        String URL = OGENYCBaseTest.prop.getProperty("OGEDBConnectionURL");
        Properties props = new Properties();
        props.setProperty("user", OGENYCBaseTest.prop.getProperty("DBUser"));
        props.setProperty("password", OGENYCBaseTest.prop.getProperty("DBPass"));
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
    //Capital Spending Contract widget Details count
    public static int getCapitalSpendingContractsDetailsCount(int year,char yearTypeVal) throws SQLException {
	    query = "select(  (select count(*)   from disbursement_line_item_Details  where spending_category_id = 3 and fiscal_year= "+ year +" and contract_document_code in ( 'CT1', 'CTA1', 'POD', 'POC', 'PCC1', 'DO1','MA1','MMA1')))  aCount"; 
   		 
	 rs = amountQueryHelper(yearTypeVal);
	  int count = 0;
	  while (rs.next()) {
	      count = rs.getInt("aCount");
	  }

	  return count;
	  
	} 
    
    public static int getContractSpendingContractsDetailsCount(int year,char yearTypeVal) throws SQLException {
	    query = "select(  (select count(*)   from disbursement_line_item_Details  where spending_category_id = 1 and fiscal_year= "+ year +" and contract_document_code in ( 'CT1', 'CTA1', 'POD', 'POC', 'PCC1', 'DO1','MA1','MMA1')))  aCount"; 
   		 
	 rs = amountQueryHelper(yearTypeVal);
	  int count = 0;
	  while (rs.next()) {
	      count = rs.getInt("aCount");
	  }

	  return count;
	  
	} 
    
     
  //Contract Spending  widget Details count

  public static int getContractSpendingDetailsCount(int year,char yearTypeVal) throws SQLException {
    query = "select count(*) aCount   from disbursement_line_item_Details  where   spending_category_id = 1 and  fiscal_year= "+ year +" ";
   		 
  rs = amountQueryHelper(yearTypeVal);
  int count = 0;
  while (rs.next()) {
      count = rs.getInt("aCount");
  }

  return count;

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
    
    
   //OGE Capital  Spending Amount
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
    
    
   //OGE Contract Spending amount
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
    
   
    //OGE Total Spending details amounts
    
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
    
	//OGE Contracts Spending details amounts
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

	//OGE Capital Spending
	
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
	
	

	
	// OGE Total Spending widget counts
    
        
        public static int getTotalSpendingDepartmentsCount(int year,char yearTypeVal) throws SQLException {
            query = "SELECT COUNT(distinct department_id) aCount from disbursement_line_item_details where fiscal_year =" + year ;
          
           rs = amountQueryHelper(yearTypeVal);
           int count = 0;
           while (rs.next()) {
               count = rs.getInt("aCount");
           }
           return count;   
           }        
           
        public static int getTotalSpendingChecksCount(int year,char yearTypeVal) throws SQLException {
            query = "SELECT COUNT(*) aCount from disbursement_line_item_details where fiscal_year = " +year+" ";
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
	query =   " SELECT count(distinct contract_number) aCount FROM disbursement_line_item_details" 
	         +    " WHERE fiscal_year = "+ year;
    rs = amountQueryHelper(yearTypeVal);
   int count = 0;
   while (rs.next()) {
       count = rs.getInt("aCount");
   }
   return count;
}





       
//OGE Capital Spending widget counts

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
   public static int getCapitalSpendingDepartmentsCount(int year,char yearTypeVal) throws SQLException {
	    query = "SELECT COUNT(distinct department_id) aCount from disbursement_line_item_details where spending_category_id='3'  and fiscal_year =" + year ;
	  
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
	query =   " SELECT  count (distinct ( contract_number))   aCount FROM disbursement_line_item_details" 
	         +    " WHERE  contract_document_code in ( 'CT1', 'CTA1') and  spending_category_id='3'  and fiscal_year = "+ year;
rs = amountQueryHelper(yearTypeVal);
int count = 0;
while (rs.next()) {
count = rs.getInt("aCount");
}
return count;
}




//OGE Contract Spending widget counts

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

public static int getContractSpendingDepartmentsCount(int year,char yearTypeVal) throws SQLException {
    query = "SELECT COUNT(distinct department_id) aCount from disbursement_line_item_details where spending_category_id='1'  and fiscal_year =" + year ;
  
   rs = amountQueryHelper(yearTypeVal);
   int count = 0;
   while (rs.next()) {
       count = rs.getInt("aCount");
   }
   return count;   
   } 

   public static int getContractSpendingExpCategoriesCount(int year, char yearTypeVal)  throws SQLException {
 query = "SELECT COUNT(distinct expenditure_object_id) aCount from disbursement_line_item_details where spending_category_id='1' and fiscal_year =" + year ;
 //query=  "SELECT COUNT(distinct expenditure_object_code) aCount from disbursement_line_item_details where spending_category_id='1' and fiscal_year =" + year ; 
rs = amountQueryHelper(yearTypeVal);
int count = 0;
while (rs.next()) {
    count = rs.getInt("aCount");
}
return count;	           
	}  		

    public static int getContractSpendingContractsCount(int year,char yearTypeVal) throws SQLException {
query =   " SELECT count( distinct contract_number) aCount FROM disbursement_line_item_details" 
         +    " WHERE  contract_document_code in ( 'CT1', 'CTA1')and  spending_category_id='1'  and fiscal_year = "+ year;
rs = amountQueryHelper(yearTypeVal);
int count = 0;
while (rs.next()) {
count = rs.getInt("aCount");
}
return count;
}



//OGE Total Spending  widget Details count

public static int getTotalSpendingDetailsCount(int year,char yearTypeVal) throws SQLException {
     query = "select count(*) aCount from disbursement_line_item_Details  where fiscal_year= "+ year +" ";
    		 
  rs = amountQueryHelper(yearTypeVal);
   int count = 0;
   while (rs.next()) {
       count = rs.getInt("aCount");
   }
   return count;   
}    

              
   //OGE top navigation amounts contracts
            
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



            

    //Active Expense contracts widget counts
            
            public static String getAEContractsAmount(int year, char yearTypeVal) throws SQLException {
                query = "SELECT SUM(maximum_contract_amount) AESum " +
                        "FROM agreement_snapshot WHERE document_code_id IN (1, 2)" +
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
                query = "SELECT COUNT(distinct contract_number) aCount " +
                        "FROM agreement_snapshot WHERE document_code_id IN (1, 2)" +
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
                		+ "   where  document_code_id in (1,2) "
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
                		+ "   where  document_code_id in (1,2) "
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
            public static int getAEContractsIndustriesCount(int year,char yearTypeVal) throws SQLException {
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
            
            public static int getAEContractsSizeCount(int year,char yearTypeVal) throws SQLException {
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
                query = "SELECT COUNT(distinct contract_number) aCount " +
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
            
            
            
            public static int getREContractsDepartmentsCount(int year,char yearTypeVal) throws SQLException {
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
//OGE details
		
			   public static int getAEContractsDetailsCount(int year,char yearTypeVal) throws SQLException {
	                query = "select  count( * ) aCount  from OGE_contract a join ( select  distinct contract_number from  agreement_snapshot"
	                		+ "   where  document_code_id in (1,2) "
	                		+ "and ("+ year +" between effective_begin_year and effective_end_year)"
	                		+ "and ("+ year +" between starting_year and ending_year) )b on a.fms_contract_number = b.contract_number ";
	                
	           
	               rs = amountQueryHelper(yearTypeVal);
	               int count = 0;
	               while (rs.next()) {
	                   count = rs.getInt("aCount");
	               }
	               return count;

			   }

			public static int getOGEREContractsMasterDetailsCount(int year, char yearTypeVal) throws SQLException {
			       query = " select  count(distinct contract_number) aCount from  agreement_snapshot"
	                		+ "   where  document_code_id in (5,6) "
	                		+ "and registered_year = "+ year +" "
	                		+ "and ("+ year +" between starting_year and ending_year)  ";
	                
	           
	               rs = amountQueryHelper(yearTypeVal);
	               int count = 0;
	               while (rs.next()) {
	                   count = rs.getInt("aCount");
	               }
	               return count;

			   }


			public static String getOGEREContractsMasterContractsDetailsAmount(int year, char yearTypeVal) throws SQLException {
			    query = "SELECT SUM(maximum_contract_amount) aeSum " +
                        "FROM agreement_snapshot WHERE document_code_id IN (1,2)" +
                        "AND registered_year = " + year + "" +
                        "AND(" + year + " BETWEEN starting_year AND ending_year)";
                rs = amountQueryHelper(yearTypeVal);

                BigDecimal totalContractAmount = new BigDecimal(0);

                while (rs.next()) {
                    totalContractAmount = rs.getBigDecimal("AESum");
                }
                return formatNumber2(totalContractAmount);
                // .divide(new BigDecimal(1000000000)).setScale(1, BigDecimal.ROUND_HALF_UP);
            }
			
				   public static int getOGEREContractsDetailsCount(int year,char yearTypeVal) throws SQLException {
		                query = "select  count( * ) aCount  from OGE_contract a join ( select  distinct contract_number from  agreement_snapshot"
		                		+ "   where  document_code_id in (1,2) "
		                		+ "and registered_year = "+ year +" "
		                		+ "and ("+ year +" between starting_year and ending_year) )b on a.fms_contract_number = b.contract_number ";
		                
		           
		               rs = amountQueryHelper(yearTypeVal);
		               int count = 0;
		               while (rs.next()) {
		                   count = rs.getInt("aCount");
		               }
		               return count;

				   }

			
				
			      public static String getOGEREContractsDetailsAmount(int year, char yearTypeVal) throws SQLException {
		                query = "SELECT SUM(maximum_contract_amount) aeSum " +
		                        "FROM agreement_snapshot WHERE document_code_id IN (1,2)" +
		                        "AND registered_year = " + year + "" +
		                        "AND(" + year + " BETWEEN starting_year AND ending_year)";
		                rs = amountQueryHelper(yearTypeVal);

		                BigDecimal totalContractAmount = new BigDecimal(0);

		                while (rs.next()) {
		                    totalContractAmount = rs.getBigDecimal("AESum");
		                }
		                return formatNumber2(totalContractAmount);
		                // .divide(new BigDecimal(1000000000)).setScale(1, BigDecimal.ROUND_HALF_UP);
		            }
}