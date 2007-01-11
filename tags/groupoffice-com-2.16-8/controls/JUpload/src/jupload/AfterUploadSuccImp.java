package jupload;

import javax.swing.*;

public class AfterUploadSuccImp implements AfterUploadSucc
{
    JTextArea status = null;
    public void setStatus(JTextArea status)
    {
        this.status = status;
    }
    public void executeThis(StringBuffer svrReturn)
    {
        // You should do your own implementation for processing of files.
        if(null!= status)
        {
            status.append("INFO   : -------- Server Output Start --------\n");
            status.append(svrReturn.toString() + "\n");
            status.append("INFO   : --------- Server Output End ---------\n");
        }
    }
}