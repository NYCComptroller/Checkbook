package org.apache.jsp;

import javax.servlet.*;
import javax.servlet.http.*;
import javax.servlet.jsp.*;
import org.apache.solr.core.SolrCore;
import java.util.Collection;

public final class index_jsp extends org.apache.jasper.runtime.HttpJspBase
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
      out.write('\n');
      out.write("\n");
      out.write("\n");
      out.write("<html>\n");
      out.write("<head>\n");
      out.write("<link rel=\"stylesheet\" type=\"text/css\" href=\"solr-admin.css\">\n");
      out.write("<link rel=\"icon\" href=\"favicon.ico\" type=\"image/ico\"></link>\n");
      out.write("<link rel=\"shortcut icon\" href=\"favicon.ico\" type=\"image/ico\"></link>\n");
      out.write("<title>Welcome to Solr</title>\n");
      out.write("</head>\n");
      out.write("\n");
      out.write("<body>\n");
      out.write("<h1>Welcome to Solr!</h1>\n");
      out.write("<a href=\".\"><img border=\"0\" align=\"right\" height=\"78\" width=\"142\" src=\"admin/solr_small.png\" alt=\"Solr\"/></a>\n");
      out.write("\n");
 
  org.apache.solr.core.CoreContainer cores = (org.apache.solr.core.CoreContainer)request.getAttribute("org.apache.solr.CoreContainer");
  Collection<SolrCore> solrCores = cores.getCores();
  if( cores != null
   && solrCores.size() > 0 // HACK! check that we have valid names...
   && solrCores.iterator().next().getName().length() != 0 ) {
    for( org.apache.solr.core.SolrCore core : cores.getCores() ) {
       String coreName = core.getName();
      if("".equals(coreName) ) coreName =".";

      out.write("\n");
      out.write("<a href=\"");
      out.print( coreName );
      out.write("/admin/\">Admin ");
      out.print( core.getName() );
      out.write("</a>\n");
      out.write("<br/>\n");
 }} else { 
      out.write("\n");
      out.write("<a href=\"admin/\">Solr Admin</a>\n");
 } 
      out.write("\n");
      out.write("\n");
      out.write("</body>\n");
      out.write("</html>\n");
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
