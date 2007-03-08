package jupload;

import java.applet.*;
import java.awt.*;
import java.awt.event.*;
import java.io.*;
import java.net.*;
import javax.swing.*;

public class JUploadApplet extends Applet{

  //------------- INFORMATION --------------------------------------------
  public static final String TITLE = "JUpload JUploadApplet";
  public static final String DESCRIPTION =
      "Java Applet wrapper for JUploadPanel.";
  public static final String AUTHOR = "William JinHua Kwong";

  public static final double VERSION = 1.3;
  public static final String LAST_MODIFIED = "14 February 2004";

  //public final static String DEFAULT_POST_URL = "http://localhost/groupoffice/modules/filesystem/upload.php?path=%2Ftmp%2F";
  public final static String DEFAULT_POST_URL = "http://macau:8084/jupload/pages/writeOut.jsp?path=%2Ftmp%2F";
  //----------------------------------------------------------------------

  private boolean isStandalone = false;
  //Get a parameter value
  public String getParameter(String key, String def) {
    return isStandalone ? System.getProperty(key, def) :
      (getParameter(key) != null ? getParameter(key) : def);
  }

  //----------------------------------------------------------------------

  //Initialize the applet
  public void init() {
    // Getting Parameters.
    String postURL = this.getParameter("postURL", DEFAULT_POST_URL);

    this.setLayout(new BorderLayout());

    JTextArea status = new StatusArea(5, 20);

    JUploadPanel jp = new JUploadPanel(postURL, null, null, null, null, null, null, null, status, null);
    jp.addDoAfterUploadSucc(null);

    this.add(jp, BorderLayout.CENTER);
    status.append("INFO   : Post URL = " + postURL + "\n");

  }
}
