package jupload;

import java.io.File;

import java.awt.Container;
import java.awt.Frame;
import java.awt.GridLayout;
import java.awt.event.ActionEvent;
import java.awt.event.ActionListener;
import javax.swing.BoxLayout;
import javax.swing.ImageIcon;
import javax.swing.JButton;
import javax.swing.JFileChooser;
import javax.swing.JPanel;
import javax.swing.JProgressBar;
import javax.swing.JScrollPane;
import javax.swing.JTextArea;
import javax.swing.Timer;
//Merijn
import javax.swing.JOptionPane;

import jupload.filepanel.FilePanelTableImp;

public class JUploadPanel extends JPanel implements ActionListener
{
    
    //------------- INFORMATION --------------------------------------------
    public static final String TITLE = "JUpload JUploadPanel";
    public static final String DESCRIPTION =
            "Main Panel for JUpload Application/Applet.";
    public static final String AUTHOR = "William JinHua Kwong";
    
    public static final double VERSION = 1.3;
    public static final String LAST_MODIFIED = "14 February 2004";
    
    //------------- VARIABLES ----------------------------------------------
    public static final String DEFAULT_POST_URL = "http://localhost:8084/";
    
    //----------------------------------------------------------------------
    private String postURL = null;
    
    private JPanel topPanel;
    private JButton browse, remove, removeAll;
    private JFileChooser fileChooser = null;
    
    private FilePanel fp = null;
    
    private JPanel progressPanel;
    private JButton upload, stop;
    private JProgressBar progress = null;
    
    private JTextArea status = null;
    
    private AfterUploadSucc aus = null;
    
    private Timer timer;
    
    // Timeout at DEEFAULT_TIMEOUT milliseconds
    private final static int DEFAULT_TIMEOUT = 100;
    
    FileUploadThreadV2 fut;
    //------------- CONSTRUCTOR --------------------------------------------
    
    //Initialize the applet
    public JUploadPanel(String postURL,
            JButton browse,
            JButton remove,
            JButton removeAll,
            FilePanel fp,
            JButton upload,
            JProgressBar progress,
            JButton stop,
            JTextArea status,
            JFileChooser fc)
    {
        
        this.setLayout(new BoxLayout(this, BoxLayout.Y_AXIS));
        
        this.postURL = (null==postURL)?DEFAULT_POST_URL:postURL;
        
        // Setup Top Panel
        setupTopPanel(browse, remove, removeAll);
        
        // Setup File Panel.
        //this.fp = (null==fp)?new FilePanelImp(this):fp;
        this.fp = (null==fp)?new FilePanelTableImp(this):fp;
        this.add((Container)this.fp);
        
        // Setup Progress Panel.
        setupProgressPanel(upload, progress, stop);
        
        // Setup Status Area.
        //setupStatus(status);
        
        // Setup File Chooser.
        if(null==fc)
        {
            try
            {
                this.fileChooser = new JFileChooser();
                fileChooser.setCurrentDirectory(new File(System.getProperty("user.dir")));
                fileChooser.setFileSelectionMode(JFileChooser.FILES_AND_DIRECTORIES);
                fileChooser.setMultiSelectionEnabled(true);
            }
            catch(Exception e)
            {
                JOptionPane.showMessageDialog(this,"ERROR  : " + e.getMessage());
                //this.status.append("ERROR  : " + e.getMessage() + "\n");
            }
        }
        else
        {
            this.fileChooser = fc;
        }
    }
    
    //----------------------------------------------------------------------
    
    private void setupTopPanel(JButton jbBrowse, JButton jbRemove, JButton jbRemoveAll)
    {
        topPanel = new JPanel();
        topPanel.setLayout(new GridLayout(1, 3));
        
        // -------- JButton browse --------
        if(null==jbBrowse)
        {
            browse = new JButton("Browse...");
            browse.setIcon(new ImageIcon(getClass().getResource("/images/file-manager.png")));
        }
        else
        {
            browse = jbBrowse;
        }
        browse.addActionListener(this);
        topPanel.add(browse);
        
        // -------- JButton remove --------
        if(null==jbRemove)
        {
            remove = new JButton("Remove Selected");
            remove.setIcon(new ImageIcon(getClass().getResource("/images/delete.png")));
        }
        else
        {
            remove = jbRemove;
        }
        remove.setEnabled(false);
        remove.addActionListener(this);
        topPanel.add(remove);
        
        // -------- JButton removeAll --------
        if(null==jbRemoveAll)
        {
            removeAll = new JButton("Remove All");
            removeAll.setIcon(new ImageIcon(getClass().getResource("/images/error.png")));
        }
        else
        {
            removeAll = jbRemoveAll;
        }
        removeAll.setEnabled(false);
        removeAll.addActionListener(this);
        topPanel.add(removeAll);
        
        this.add(topPanel);
    }
    
    private void setupProgressPanel(JButton jbUpload, JProgressBar jpbProgress, JButton jbStop)
    {
        progressPanel = new JPanel();
        progressPanel.setLayout(new BoxLayout(progressPanel, BoxLayout.X_AXIS));
        
        // -------- JButton upload --------
        if(null==jbUpload)
        {
            upload = new JButton("Upload");
            upload.setIcon(new ImageIcon(getClass().getResource("/images/up.png")));
        }
        else
        {
            upload = jbUpload;
        }
        upload.setEnabled(false);
        upload.addActionListener(this);
        progressPanel.add(upload);
        
        // -------- JProgressBar progress --------
        if(null == jpbProgress)
        {
            progress = new JProgressBar(JProgressBar.HORIZONTAL);
            progress.setStringPainted(true);
        }
        else
        {
            progress = jpbProgress;
        }
        progressPanel.add(progress);
        
        // -------- JButton stop --------
        if(null==jbStop)
        {
            stop = new JButton("Stop");
            stop.setIcon(new ImageIcon(getClass().getResource("/images/stop.png")));
        }
        else
        {
            stop = jbStop;
        }
        stop.setEnabled(false);
        stop.addActionListener(this);
        progressPanel.add(stop);
        
        this.add(progressPanel);
    }
    
    private void setupStatus(JTextArea ta)
    {
        // -------- JTextArea status --------
        if(null==ta)
        {
            status = new StatusArea(5, 20);
        }
        else
        {
            status = ta;
        }
        
        JScrollPane pane = new JScrollPane();
        pane.setVerticalScrollBarPolicy(JScrollPane.VERTICAL_SCROLLBAR_ALWAYS);
        pane.setHorizontalScrollBarPolicy(JScrollPane.HORIZONTAL_SCROLLBAR_NEVER);
        
        pane.getViewport().add(status);
        this.add(pane);
    }
    
    //----------------------------------------------------------------------
    
    public void addDoAfterUploadSucc(AfterUploadSucc aus)
    {
        if(null == aus)
        {
            this.aus = new AfterUploadSuccImp();
        }
        else
        {
            this.aus = aus;
        }
        this.aus.setStatus(status);
    }
    
    //----------------------------------------------------------------------
    protected void addFiles(File[] f)
    {
        fp.addFiles(f);
        if(0 < fp.getFilesLength())
        {
            remove.setEnabled(true);
            removeAll.setEnabled(true);
            upload.setEnabled(true);
        }
    }
    
    public void actionPerformed(ActionEvent e)
    {
        //status.append("Action : " + e.getActionCommand() + "\n");
        if(e.getActionCommand() == browse.getActionCommand())
        {
            if(null!=fileChooser)
            {
                try
                {
                    if(JFileChooser.APPROVE_OPTION ==
                            fileChooser.showOpenDialog(new Frame()))
                    {
                        addFiles(fileChooser.getSelectedFiles());
                    }
                }
                catch(Exception ex)
                {
                    
                    JOptionPane.showMessageDialog(this,"ERROR  : " + ex.getMessage());
                    
                    //status.append("ERROR  : " + ex.getMessage() + "\n");
                }
            }
        }
        else if(e.getActionCommand() == remove.getActionCommand())
        {
            fp.removeSelected();
            if(0 >= fp.getFilesLength())
            {
                remove.setEnabled(false);
                removeAll.setEnabled(false);
                upload.setEnabled(false);
            }
        }
        else if(e.getActionCommand() == removeAll.getActionCommand())
        {
            fp.removeAll();
            remove.setEnabled(false);
            removeAll.setEnabled(false);
            upload.setEnabled(false);
        }
        else if(e.getActionCommand() == upload.getActionCommand())
        {
            
            //status.append("POST URL = " + postURL + "\n");
            fut = new FileUploadThreadV2(fp.getFiles(), postURL);
            fut.setProgressPanel(progress);
            fut.start();
            
            browse.setEnabled(false);
            remove.setEnabled(false);
            removeAll.setEnabled(false);
            upload.setEnabled(false);
            stop.setEnabled(true);
            
            //Create a timer.
            timer = new Timer(DEFAULT_TIMEOUT, new ActionListener()
            {
                public void actionPerformed(ActionEvent evt)
                {
                    if(!fut.isAlive())
                    {
                        timer.stop();
                        boolean isSuccess = false;
                        StringBuffer svrRet;
                        
                        if(null != fut.getException())
                        {
                            
                            JOptionPane.showMessageDialog(null,"ERROR  : " +fut.getException().toString());
                            
                            
                            //status.append("ERROR  : " + fut.getException().toString() + "\n");
                        }
                        else
                        {
                            
                            JOptionPane.showMessageDialog(null,fp.getFilesLength() + " Files uploaded successfully!");
                            
                            //status.append("INFO   : " + fp.getFilesLength() + " Files uploaded.\n");
                            fp.removeAll();
                            isSuccess = true;
                        }
                        svrRet = fut.getServerOutput();
                        fut.close();
                        fut = null;
                        // Setting Progress bar to 0%.
                        progress.setString(null);
                        progress.setValue(0);
                        // Do something (eg Redirect to another page for processing).
                        if((null != aus) && isSuccess) aus.executeThis(svrRet);
                        
                        stop.setEnabled(false);
                        if(!isSuccess)
                        {
                            remove.setEnabled(true);
                            removeAll.setEnabled(true);
                            upload.setEnabled(true);
                        }
                        browse.setEnabled(true);
                    }
                }
            });
            timer.start();
        }
        else if(e.getActionCommand() == stop.getActionCommand())
        {
            stop.setEnabled(false);
            if(null != timer)
            {
                timer.stop();
            }
            timer = null;
            if(null != fut)
            {
                if(fut.isAlive())
                {
                    fut.stopUpload();
                    try
                    {
                        fut.join(1000);
                    }
                    catch(InterruptedException ie)
                    {}
                }
                fut.close();
            }
            fut = null;
            remove.setEnabled(true);
            removeAll.setEnabled(true);
            upload.setEnabled(true);
            browse.setEnabled(true);
        }
    }
}

