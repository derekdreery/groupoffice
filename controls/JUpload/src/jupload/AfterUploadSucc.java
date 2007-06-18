package jupload;

import javax.swing.*;

public interface AfterUploadSucc
{
    public void setStatus(JTextArea status);
    public void executeThis(StringBuffer svrReturn);
}