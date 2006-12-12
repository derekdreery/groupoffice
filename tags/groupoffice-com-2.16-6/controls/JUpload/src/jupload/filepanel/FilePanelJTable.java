package jupload.filepanel;

import java.util.Date;

import java.awt.event.MouseEvent;
import java.awt.event.MouseListener;
import javax.swing.JTable;
import javax.swing.table.JTableHeader;
import javax.swing.table.TableColumnModel;

public class FilePanelJTable extends JTable implements MouseListener {
  protected int sortedColumnIndex = -1;
  protected boolean sortedColumnAscending = true;

  public FilePanelJTable(){
    setAutoResizeMode(JTable.AUTO_RESIZE_OFF);
    setDefaultRenderer(Date.class, new DateRenderer());
    JTableHeader header = getTableHeader();
    header.setDefaultRenderer(new SortHeaderRenderer());
    header.addMouseListener(this);
  }

  public int getSortedColumnIndex() {
    return sortedColumnIndex;
  }

  public boolean isSortedColumnAscending() {
    return sortedColumnAscending;
  }

  // MouseListener implementation.
  public void mouseReleased(MouseEvent event) {
  }

  public void mousePressed(MouseEvent event) {
  }

  public void mouseClicked(MouseEvent event) {
    TableColumnModel colModel = getColumnModel();
    int index = colModel.getColumnIndexAtX(event.getX());
    int modelIndex = colModel.getColumn(index).getModelIndex();

    FilePanelDataModel model = (FilePanelDataModel) getModel();
    if (model.isSortable(modelIndex)) {
      if (sortedColumnIndex == index) {
        sortedColumnAscending = !sortedColumnAscending;
      }
      sortedColumnIndex = index;

      model.sortColumn(modelIndex, sortedColumnAscending);
    }
  }

  public void mouseEntered(MouseEvent event) {
  }

  public void mouseExited(MouseEvent event) {
  }

}
