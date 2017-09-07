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
    
    public static String getRevenueAmount(int year, char yearTypeVal) throws SQLException {
        query = "SELECT SUM(posting_amount) sumRevenueAmt "
                + "FROM revenue"
                + " WHERE budget_fiscal_year = " + year;


        rs = amountQueryHelper(yearTypeVal);

        BigDecimal totalRevenueAmount = new BigDecimal(0);

        while (rs.next()) {
            totalRevenueAmount = rs.getBigDecimal("sumRevenueAmt");
        }
        return formatNumber(totalRevenueAmount);
    }
    
    public static String getRevenueDetailsAmount(int year, char yearTypeVal) throws SQLException {
        query = "SELECT SUM(posting_amount) sumRevenueAmt "
                + "FROM revenue"
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
                    "FROM  revenue where budget_fiscal_year= " + year ;

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
    query =   "SELECT count(DISTINCT document_id) as aCount FROM aggregateon_mwbe_spending_contract WHERE type_of_year = 'B' AND year_id = "+ year;

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
    //Active Expense contracts widget counts
            
            
               
            public static int getAEContractsAgenciesCount(int year,char yearTypeVal) throws SQLException {
                query = "select  count(distinct agency_id ) aCount  from agreement_snapshot"
                		+ "   where  document_code_id in (1,2,5) "
                		+ "and (2016 between effective_begin_year and effective_end_year)"
                		+ "and (2016 between starting_year and ending_year)";
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
                		+ "and (2016 between effective_begin_year and effective_end_year)"
                		+ "and (2016 between starting_year and ending_year)";
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
                		+ "and (2016 between effective_begin_year and effective_end_year)"
                		+ "and (2016 between starting_year and ending_year)";
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
                		+ "and (2016 between effective_begin_year and effective_end_year)"
                		+ "and (2016 between starting_year and ending_year)";
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
                		+ "and (2016 between effective_begin_year and effective_end_year)"
                		+ "and (2016 between starting_year and ending_year)";
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
                		+ "and (2016 between effective_begin_year and effective_end_year)"
                		+ "and (2016 between starting_year and ending_year)";
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
                		+ "and (2016 between effective_begin_year and effective_end_year)"
                		+ "and (2016 between starting_year and ending_year)";
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
                		+ "and (2016 between effective_begin_year and effective_end_year)"
                		+ "and (2016 between starting_year and ending_year)"
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
                		+ "and (2016 between effective_begin_year and effective_end_year)"
                		+ "and (2016 between starting_year and ending_year)"
                		+ "and maximum_contract_amount <> original_contract_amount";
               rs = amountQueryHelper(yearTypeVal);
               int count = 0;
               while (rs.next()) {
                   count = rs.getInt("aCount");
               }
               return count;
            }
            
          //Registered Expense contracts widget counts
            
            
            
            public static int getREContractsAgenciesCount(int year,char yearTypeVal) throws SQLException {
                query = "select  count(distinct agency_id ) aCount  from agreement_snapshot"
                		+ "   where  document_code_id in (1,2,5) "
                		+ "and registered_year = 2016"
                		+ "and (2016 between starting_year and ending_year)";
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
                		+ "and registered_year = 2016"
                		+ "and (2016 between starting_year and ending_year)";
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
                		+ "and registered_year = 2016"
                		+ "and (2016 between starting_year and ending_year)";
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
                		+ "and registered_year = 2016"
                		+ "and (2016 between starting_year and ending_year)";
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
                		+ "and registered_year = 2016"
                		+ "and (2016 between starting_year and ending_year)";
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
                		+ "and registered_year = 2016"
                		+ "and (2016 between starting_year and ending_year)";
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
                		+ "and registered_year = 2016"
                		+ "and (2016 between starting_year and ending_year)";
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
                		+ "and registered_year = 2016"
                		+ "and (2016 between starting_year and ending_year)"
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
                		+ "and registered_year = 2016"
                		+ "and (2016 between starting_year and ending_year)"
                		+ "and maximum_contract_amount <> original_contract_amount";
               rs = amountQueryHelper(yearTypeVal);
               int count = 0;
               while (rs.next()) {
                   count = rs.getInt("aCount");
               }
               return count;
            }
            
 //Registered Revenue contracts widget counts
            
            
            
            public static int getRRContractsAgenciesCount(int year,char yearTypeVal) throws SQLException {
                query = "select  count(distinct agency_id ) aCount  from agreement_snapshot"
                		+ "   where  document_code_id in (7) "
                		+ "and registered_year = 2016"
                		+ "and (2016 between starting_year and ending_year)";
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
                		+ "and registered_year = 2016"
                		+ "and (2016 between starting_year and ending_year)";
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
                		+ "and registered_year = 2016"
                		+ "and (2016 between starting_year and ending_year)";
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
                		+ "and registered_year = 2016"
                		+ "and (2016 between starting_year and ending_year)";
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
                		+ "and registered_year = 2016"
                		+ "and (2016 between starting_year and ending_year)";
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
                		+ "and registered_year = 2016"
                		+ "and (2016 between starting_year and ending_year)";
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
                		+ "and registered_year = 2016"
                		+ "and (2016 between starting_year and ending_year)"
                		+ "and maximum_contract_amount <> original_contract_amount";
               rs = amountQueryHelper(yearTypeVal);
               int count = 0;
               while (rs.next()) {
                   count = rs.getInt("aCount");
               }
               return count;
            }
            
            //Active Revenue contracts widget counts
            
            
            
            public static int getARContractsAgenciesCount(int year,char yearTypeVal) throws SQLException {
                query = "select  count(distinct agency_id ) aCount  from agreement_snapshot"
                		+ "   where  document_code_id in (7) "
                		+ "and (2016 between effective_begin_year and effective_end_year)"
                		+ "and (2016 between starting_year and ending_year)";
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
                		+ "and (2016 between effective_begin_year and effective_end_year)"
                		+ "and (2016 between starting_year and ending_year)";
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
                		+ "and (2016 between effective_begin_year and effective_end_year)"
                		+ "and (2016 between starting_year and ending_year)";
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
                		+ "and (2016 between effective_begin_year and effective_end_year)"
                		+ "and (2016 between starting_year and ending_year)";
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
                		+ "and (2016 between effective_begin_year and effective_end_year)"
                		+ "and (2016 between starting_year and ending_year)";
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
                		+ "and (2016 between effective_begin_year and effective_end_year)"
                		+ "and (2016 between starting_year and ending_year)";
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
                		+ "and (2016 between effective_begin_year and effective_end_year)"
                		+ "and (2016 between starting_year and ending_year)"
                		+ "and maximum_contract_amount <> original_contract_amount";
               rs = amountQueryHelper(yearTypeVal);
               int count = 0;
               while (rs.next()) {
                   count = rs.getInt("aCount");
               }
               return count;
            }
            
 //PendingRevenue contracts widget counts
            
            
            
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
            
//Pending Expense contracts widget counts
            
            
            
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
            
            ///Active expense contracts details page 
            
            
           
            public static int getAEContractsDetailsCount(int year,char yearTypeVal) throws SQLException {
                query =    " select  ((select  count(*) from agreement_snapshot  where "
                	    	+"	 document_code_id in (1,2) and (2016 between starting_year and ending_year)"
                	    	+"	    and (2016 between effective_begin_year and effective_end_year)) +"
                	    	+"	    ( select  count(*) from sub_agreement_snapshot  where contract_number in "
                	    	+"	 ( select distinct contract_number  from agreement_snapshot  where "
                	    	+"	 document_code_id in (1,2) and (2016 between starting_year and ending_year)"
                	    		+"    and (2016 between effective_begin_year and effective_end_year)) and latest_flag ='Y')) aCount";              rs = amountQueryHelper(yearTypeVal);
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
                		+ "and (2016 between effective_begin_year and effective_end_year)"
                		+ "and (2016 between starting_year and ending_year)";
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
                		+ "and (2016 between effective_begin_year and effective_end_year)"
                		+ "and (2016 between starting_year and ending_year)"
                		+ "and maximum_contract_amount <> original_contract_amount";
               rs = amountQueryHelper(yearTypeVal);
               int count = 0;
               while (rs.next()) {
                   count = rs.getInt("aCount");
               }
               return count;
            }
            public static int getAEContractsModificationDetailsCount(int year,char yearTypeVal) throws SQLException {
                query = "select  count(distinct contract_number ) aCount  from agreement_snapshot"
                		+ "   where  document_code_id in (1,2) "
                		+ "and (2016 between effective_begin_year and effective_end_year)"
                		+ "and (2016 between starting_year and ending_year)"
                		+ "and maximum_contract_amount <> original_contract_amount";
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
                		+ "and (2016 between effective_begin_year and effective_end_year)"
                		+ "and (2016 between starting_year and ending_year)";
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
                		+ "and (2016 between effective_begin_year and effective_end_year)"
                		+ "and (2016 between starting_year and ending_year)"
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
           //Registered Expense Contracts Details 
            public static int getREContractsDetailsCount(int year,char yearTypeVal) throws SQLException {
                query = "select  count(distinct contract_number ) aCount  from agreement_snapshot"
                		+ "   where  document_code_id in (1,2) "
                		+ "and registered_year = 2016"
                		+ "and (2016 between starting_year and ending_year)";
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
                		+ "and registered_year = 2016"
                		+ "and (2016 between starting_year and ending_year)";
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
                		+ "and registered_year = 2016"
                		+ "and (2016 between starting_year and ending_year)"
                		+ "and maximum_contract_amount <> original_contract_amount";
               rs = amountQueryHelper(yearTypeVal);
               int count = 0;
               while (rs.next()) {
                   count = rs.getInt("aCount");
               }
               return count;
            }
            public static int getREContractsModificationsDetailsCount(int year,char yearTypeVal) throws SQLException {
                query = "select  count(distinct contract_number ) aCount  from agreement_snapshot"
                		+ "   where  document_code_id in (1,2) "
                		+ "and registered_year = 2016"
                		+ "and (2016 between starting_year and ending_year)"
                		+ "and maximum_contract_amount <> original_contract_amount";
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
                		+ "and registered_year = 2016"
                		+ "and (2016 between starting_year and ending_year)";
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
                		+ "and registered_year = 2016"
                		+ "and (2016 between starting_year and ending_year)"
                		+ "and maximum_contract_amount <> original_contract_amount";
               rs = amountQueryHelper(yearTypeVal);
               int count = 0;
               while (rs.next()) {
                   count = rs.getInt("aCount");
               }
               return count;
            }
}
