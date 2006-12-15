package jupload.filepanel;

import java.io.File;
import java.util.Collections;
import java.util.Date;

import javax.swing.table.DefaultTableModel;

public class FilePanelDataModel extends DefaultTableModel{

  protected String[] columnNames = new String[] {
      "Name", "Size", "Directory", "Modified", "Readable?"
  };

  protected int[] columnSize = new int[] {
      150, 75, 199, 130, 75
  };

  protected Class[] columnClasses = new Class[] {
      String.class, Long.class, String.class, Date.class, Boolean.class
  };

  public void addFile(File file){
    Object[] rowData = new Object[5];
    rowData[0] = file.getName();
    rowData[1] = new Long(file.length());
    rowData[2] = file.getParent();
    rowData[3] = new Date(file.lastModified());
    rowData[4] = file.canRead() ? Boolean.TRUE : Boolean.FALSE;
    addRow(rowData);
  }

  public boolean contains(File file){
    boolean contain = false;
    for(int i = 0; i < getRowCount(); i++){
      if(file.getName().equalsIgnoreCase((String)getValueAt(i, 0)) &&
         file.getParent().equalsIgnoreCase((String)getValueAt(i,2))){
        contain = true;
        break;
      }
    }
    return contain;

  }

  public File getFileAt(int row){
    return new File((String)getValueAt(row,2), (String)getValueAt(row, 0));
  }

  public int getColumnCount() {
    return columnNames.length;
  }

  public String getColumnName(int col) {
    return columnNames[col];
  }

  public int getColumnSize(int col) {
    return columnSize[col];
  }

  public Class getColumnClass(int col) {
    return columnClasses[col];
  }

  public boolean isSortable(int col) {
    return (Boolean.class != getColumnClass(col));
  }

  public void sortColumn(int col, boolean ascending) {
    Collections.sort(getDataVector(), new ColumnComparator(col, ascending));
  }

}
