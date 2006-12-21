package jupload;

import java.awt.*;
import java.awt.event.*;
import javax.swing.*;

public class JUpload extends JFrame{

  //------------- INFORMATION --------------------------------------------
  public static final String TITLE = "JUpload JUpload";
  public static final String DESCRIPTION =
      "Java Frame wrapper for JUploadPanel.";
  public static final String AUTHOR = "William Kwong JinHua";

  public static final double VERSION = 1.1;
  public static final String LAST_MODIFIED = "12 April 2004";

  //----------------------------------------------------------------------

  public JUpload(String postURL){
    super("Java Multiple Upload Frame.");
    this.addWindowListener(
      new WindowAdapter() {
        public void windowClosing(WindowEvent e) {
          System.exit(0);
        }
      }
    );
    this.setSize(640,300);

    Container c = this.getContentPane();
    c.setLayout(new BorderLayout());

    JTextArea status = new StatusArea(5, 20);

    JUploadPanel jp = new JUploadPanel(postURL, null,
         null, null, null, null, null, null, status, null);
    jp.addDoAfterUploadSucc(null);

    c.add(jp, BorderLayout.CENTER);
    status.append("INFO   : Post URL = " + postURL + "\n");
    this.show();
  }

  //Main method
  public static void main(String[] args) {
    String postURL = "http://localhost:80/groupoffice/modules/filesystem/upload.php";
    //String postURL = "http://localhost:8080/jupload/pages/parseRequest.jsp?URLParam=URL Parameter Value";
    if(1 == args.length){
      postURL = args[0];
    }
    JUpload ju = new JUpload(postURL);
  }

}