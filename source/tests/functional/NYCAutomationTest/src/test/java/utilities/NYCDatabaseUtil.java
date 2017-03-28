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

    public static int getAEAmount(int year, char yearTypeVal) throws SQLException {
        query = "SELECT SUM(maximum_contract_amount) AESum " +
                "FROM agreement_snapshot WHERE document_code_id IN (1, 2, 5)" +
                "AND(" + year + " BETWEEN effective_begin_year AND effective_end_year) " +
                "AND(" + year + " BETWEEN starting_year AND ending_year)";

        query2 = "SELECT SUM(maximum_contract_amount) AESum " +
                "FROM agreement_snapshot_cy WHERE document_code_id IN (1, 2, 5)" +
                "AND(" + year + " BETWEEN effective_begin_year AND effective_end_year) " +
                "AND(" + year + " BETWEEN starting_year AND ending_year)";

        rs = amountQueryHelper(yearTypeVal);
        return rs.getInt("AESum");
    }

    public static int getAECount(int year, char yearTypeVal) throws SQLException {
        query = "SELECT COUNT(*) AECount " +
                "FROM agreement_snapshot WHERE document_code_id IN (1, 2, 5)" +
                "AND(" + year + " BETWEEN effective_begin_year AND effective_end_year) " +
                "AND(" + year + " BETWEEN starting_year AND ending_year)";

        query2 = "SELECT SUM(maximum_contract_amount) AESum " +
                "FROM agreement_snapshot_cy WHERE document_code_id IN (1, 2, 5)" +
                "AND(" + year + " BETWEEN effective_begin_year AND effective_end_year) " +
                "AND(" + year + " BETWEEN starting_year AND ending_year)";

        rs = amountQueryHelper(yearTypeVal);
        return rs.getInt("AECount");
    }
    // Revenue Widgets
    
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
   // query = "SELECT COUNT(distinct contract_number) aCount from disbursement_line_item_details where fiscal_year =" + year ;
    query =   "select  count(distinct contract_number) aCount from ( SELECT COALESCE(master_agreement_id, agreement_id) as agreement_id,"+ 
    	       "COALESCE(master_contract_number,contract_number) as contract_number,"+ 
    	       "COALESCE(master_contract_document_code,contract_document_code) as contract_document_code,"+ 
    	      "COALESCE(master_contract_vendor_id_cy,contract_vendor_id) as vendor_id,"+ 
    	       "COALESCE(master_contract_agency_id_cy,contract_agency_id) as agency_id,"+ 
    	    "COALESCE(master_purpose,purpose) as description, "+
    	           " fiscal_year AS year_id, sum(check_amount) AS total_spending_amount,"+ 
    	     " MIN(COALESCE(maximum_spending_limit,maximum_contract_amount)) AS contract_amount"+ 
    	  "FROM disbursement_line_item_details  WHERE agreement_id IS NOT NULL AND contract_number IS NOT NULL"+ 
    	  "and fiscal_year = "+ year +
    	   "and contract_document_code in ('CT1', 'CTA1', 'POD', 'POC', 'PCC1', 'DO1','MA1','MMA1')	GROUP BY 1,2,3,4,5,6,7 ) a "; 

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
        	
}
