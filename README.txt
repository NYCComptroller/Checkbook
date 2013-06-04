Checkbook NYC Introduction:

The main objective of the Checkbook NYC 2.0 (CB 2.0) application is to establish a transparency dashboard that exhibits various New York City financial information such as Revenue, Budget, Spending, Contracts, and Payroll.  The application uses extract, transform, load (ETL) processes to perform this function.  The application will significantly increase the availability of detailed financial information to the public.  The infrastructure of the application will also support the security and confidentiality of exclusive data.

Repository Structure:

Within the Code tab, all source code and documentations can be found.  Documentations include sample data, installations instructions, business requirements documents, and more.
Within the Issues tab, a public user can post an issue that he/she discovered within the application.  The posted issue in Github will automatically be cloned in JIRA.  JIRA is a defects tracking tool that REI Systems utilizes.
Within the Graphs tab, statistical information regarding the utilizations of the repository will be available.  The information includes the number of contributors over time, commit activities over time, code frequency, etc.
Within the Pull Request tab, a user may request for the source code.
The network tab tracks the history of actions from contributors within the network.  The page also presents the relationships between a commit from one user to a commit of another.  The graph in the page displays the collaboration among different users.
The Wiki tab presents all general information regarding Checkbook and the repository.  Users can have access to Checkbook background information, NYC Checkbook repository layout information, etc.

Description of Critical Files:

INSTALL.txt file presents the software and hardware requirements to run the application.  The file also includes the installation instructions for any user to setup the application.

SOLR-INSTALL.txt file provides the step-by-step instructions for installing and setting up SOLR.

Creating new Database and running ETL Job.docx file provides the steps for creating databases supported by Greenplum (Community edition or Enterprise edition).  The file also explains how to setup Pentaho Kettle for utilizing the ETL process.
