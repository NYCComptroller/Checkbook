package org.apache.jsp;

import javax.servlet.*;
import javax.servlet.http.*;
import javax.servlet.jsp.*;

public final class _401_jsp extends org.apache.jasper.runtime.HttpJspBase
    implements org.apache.jasper.runtime.JspSourceDependent {

  private static final JspFactory _jspxFactory = JspFactory.getDefaultFactory();

  private static java.util.List _jspx_dependants;

  private javax.el.ExpressionFactory _el_expressionfactory;
  private org.apache.AnnotationProcessor _jsp_annotationprocessor;

  public Object getDependants() {
    return _jspx_dependants;
  }

  public void _jspInit() {
    _el_expressionfactory = _jspxFactory.getJspApplicationContext(getServletConfig().getServletContext()).getExpressionFactory();
    _jsp_annotationprocessor = (org.apache.AnnotationProcessor) getServletConfig().getServletContext().getAttribute(org.apache.AnnotationProcessor.class.getName());
  }

  public void _jspDestroy() {
  }

  public void _jspService(HttpServletRequest request, HttpServletResponse response)
        throws java.io.IOException, ServletException {

    PageContext pageContext = null;
    HttpSession session = null;
    ServletContext application = null;
    ServletConfig config = null;
    JspWriter out = null;
    Object page = this;
    JspWriter _jspx_out = null;
    PageContext _jspx_page_context = null;


    try {
      response.setContentType("text/html");
      pageContext = _jspxFactory.getPageContext(this, request, response,
      			null, true, 8192, true);
      _jspx_page_context = pageContext;
      application = pageContext.getServletContext();
      config = pageContext.getServletConfig();
      session = pageContext.getSession();
      out = pageContext.getOut();
      _jspx_out = out;

      out.write('\n');

  response.setHeader("WWW-Authenticate", "Basic realm=\"Tomcat Manager Application\"");

      out.write("\n");
      out.write("<!DOCTYPE html PUBLIC \"-//W3C//DTD HTML 4.01//EN\" \"http://www.w3.org/TR/html4/strict.dtd\">\n");
      out.write("<html>\n");
      out.write(" <head>\n");
      out.write("  <title>401 Unauthorized</title>\n");
      out.write("  <style type=\"text/css\">\n");
      out.write("    <!--\n");
      out.write("    BODY {font-family:Tahoma,Arial,sans-serif;color:black;background-color:white;font-size:12px;}\n");
      out.write("    H1 {font-family:Tahoma,Arial,sans-serif;color:white;background-color:#525D76;font-size:22px;}\n");
      out.write("    PRE, TT {border: 1px dotted #525D76}\n");
      out.write("    A {color : black;}A.name {color : black;}\n");
      out.write("    -->\n");
      out.write("  </style>\n");
      out.write(" </head>\n");
      out.write(" <body>\n");
      out.write("   <h1>401 Unauthorized</h1>\n");
      out.write("   <p>\n");
      out.write("    You are not authorized to view this page. If you have not changed\n");
      out.write("    any configuration files, please examine the file\n");
      out.write("    <tt>conf/tomcat-users.xml</tt> in your installation. That\n");
      out.write("    file must contain the credentials to let you use this webapp.\n");
      out.write("   </p>\n");
      out.write("   <p>\n");
      out.write("    For example, to add the <tt>manager-gui</tt> role to a user named\n");
      out.write("    <tt>tomcat</tt> with a password of <tt>s3cret</tt>, add the following to the\n");
      out.write("    config file listed above.\n");
      out.write("   </p>\n");
      out.write("<pre>\n");
      out.write("&lt;role rolename=\"manager-gui\"/&gt;\n");
      out.write("&lt;user username=\"tomcat\" password=\"s3cret\" roles=\"manager-gui\"/&gt;\n");
      out.write("</pre>\n");
      out.write("   <p>\n");
      out.write("    Note that for Tomcat 6.0.30 onwards, the roles required to use the manager\n");
      out.write("    application were changed from the single <tt>manager</tt> role to the\n");
      out.write("    following four roles. You will need to assign the role(s) required for\n");
      out.write("    the functionality you wish to access.\n");
      out.write("   </p>\n");
      out.write("    <ul>\n");
      out.write("      <li><tt>manager-gui</tt> - allows access to the HTML GUI and the status\n");
      out.write("          pages</li>\n");
      out.write("      <li><tt>manager-script</tt> - allows access to the text interface and the\n");
      out.write("          status pages</li>\n");
      out.write("      <li><tt>manager-jmx</tt> - allows access to the JMX proxy and the status\n");
      out.write("          pages</li>\n");
      out.write("      <li><tt>manager-status</tt> - allows access to the status pages only</li>\n");
      out.write("    </ul>\n");
      out.write("   <p>\n");
      out.write("    The HTML interface is protected against CSRF but the text and JMX interfaces\n");
      out.write("    are not. To maintain the CSRF protection:\n");
      out.write("   </p>\n");
      out.write("   <ul>\n");
      out.write("    <li>The deprecated <tt>manager</tt> role should not be assigned to any\n");
      out.write("        user.</li>\n");
      out.write("    <li>Users with the <tt>manager-gui</tt> role should not be granted either\n");
      out.write("        the <tt>manager-script</tt> or <tt>manager-jmx</tt> roles.</li>\n");
      out.write("    <li>If the text or jmx interfaces are accessed through a browser (e.g. for\n");
      out.write("        testing since these interfaces are intended for tools not humans) then\n");
      out.write("        the browser must be closed afterwards to terminate the session.</li>\n");
      out.write("   </ul>\n");
      out.write("   <p>\n");
      out.write("    For more information - please see the\n");
      out.write("    <a href=\"/docs/manager-howto.html\">Manager App HOW-TO</a>.\n");
      out.write("   </p>\n");
      out.write(" </body>\n");
      out.write("\n");
      out.write("</html>");
    } catch (Throwable t) {
      if (!(t instanceof SkipPageException)){
        out = _jspx_out;
        if (out != null && out.getBufferSize() != 0)
          try { out.clearBuffer(); } catch (java.io.IOException e) {}
        if (_jspx_page_context != null) _jspx_page_context.handlePageException(t);
      }
    } finally {
      _jspxFactory.releasePageContext(_jspx_page_context);
    }
  }
}
