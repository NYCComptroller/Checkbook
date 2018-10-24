package suites;

import org.junit.runner.RunWith;
import org.junit.runners.Suite;
import org.junit.runners.Suite.SuiteClasses;


import FunctionalSpendingOGE.*;
import FunctionalContractsOGE.*;

import utilities.OGENYCBaseTest;


/**
 *
 * @author sproddutur
 */
@RunWith(Suite.class)
@SuiteClasses({
/*	OGE Spending *
	
	OGETotalSpendingWidgetTest.class,	
	OGETotalSpendingWidgetDetailsTest.class,
	
	OGECapitalSpendingWidgetTest.class,
	OGECapitalSpendingWidgetDetailsTest.class,   

	OGEContractSpendingWidgetTest.class,
	OGEContractSpendingWidgetDetailsTest.class,
	
	/* OGE Contracts
	OGEActiveExpenseContractsTest.class,
	OGEActiveExpenseContractsDetailsTest.class,
	OGERegisteredExpenseContractsTest.class,*/
	OGERegisteredExpenseContractsDetailsTest.class, 
	
	


})
public class OGEFunctionalTest extends OGENYCBaseTest
//public class FunctionalTest extends TestStatusReport

{	
	
}
