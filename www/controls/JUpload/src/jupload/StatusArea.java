package jupload;

import java.awt.Color;
import javax.swing.JTextArea;

public class StatusArea extends JTextArea{
  public StatusArea(int rows, int columns) {
    super(rows, columns);

    setBackground(new Color(255,255,203));
    setEditable(false);
    setLineWrap(true);
    setWrapStyleWord(true);
  }

  // Overwriting the default Append class such that it scrolls to the
  // bottom of the text. The JFC doesn't always rememeber to do that.
  public void append(String str) {
    super.append(str);
    setCaretPosition(getText().length());
  }

}
