package jupload;

import java.io.File;
import java.awt.*;
import javax.swing.*;
import java.awt.dnd.DropTarget;
import java.awt.dnd.DropTargetListener;

public class FilePanelImp extends Panel implements FilePanel
{
    
    //------------- INFORMATION --------------------------------------------
    public static final String TITLE = "JUpload DnDListener";
    public static final String DESCRIPTION = "FilePanel Implementation.";
    public static final String AUTHOR = "William JinHua Kwong";
    
    public static final double VERSION = 1.1;
    public static final String LAST_MODIFIED = "22 January 2004";
    
    //------------- VARIABLES ----------------------------------------------
    private DefaultListModel listModel;
    private JList jlist;
    
    private DropTarget dropTarget;
    private DropTargetListener dtListener;
    
    public FilePanelImp(JUploadPanel jup)
    {
        this.setLayout(new BorderLayout());
        listModel = new DefaultListModel();
        jlist = new JList(listModel);
        JScrollPane listScroll = new JScrollPane(jlist);
        
        dropTarget = new DropTarget(jlist, new DnDListener(jup));
        
        this.add(listScroll);
    }
    
    public void addFiles(File[] f)
    {
        if(null != f)
        {
            for(int i = 0; i < f.length; i++)
            {
                addDirectoryFiles(listModel, f[i]);
            }
        }
    }
    
    
    protected void addDirectoryFiles(DefaultListModel dlm, File f)
    {
        if(!f.isDirectory())
        {
            addFileOnly(dlm, f);
        }
        else
        {
            File[] dirFiles = f.listFiles();
            for(int i = 0 ; i < dirFiles.length; i++)
            {
                if(dirFiles[i].isDirectory())
                {
                    addDirectoryFiles(dlm, dirFiles[i]);
                }
                else
                {
                    addFileOnly(dlm, dirFiles[i]);
                }
            }
        }
    }
    
    protected void addFileOnly(DefaultListModel dlm, File f)
    {
        // Make sure we don't select the same file twice.
        if(!dlm.contains(f)) dlm.add(dlm.getSize(), f);
    }
    
    
    public File[] getFiles()
    {
        File[] arrayFiles = new File[listModel.size()];
        listModel.copyInto(arrayFiles);
        return arrayFiles;
    }
    
    public int getFilesLength()
    {
        return listModel.size();
    }
    
    public void removeSelected()
    {
        for(int i=0; i < listModel.size();)
        {
            if(jlist.isSelectedIndex(i))
            {
                listModel.remove(i);
            }
            else
            {
                i++;
            }
        }
    }
    
    public void removeAll()
    {
        listModel.clear();
    }
}
