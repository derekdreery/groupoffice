<?php
  //getchatrooms
  $result = query("select * from chat_chatrooms order by fromuser");
  if ($result) {
    while ($row = fetch_object($result)) {
      $un = $row->fromusername;
      if (!$un) $un = " ";
      echo ($row->id)."|".($row->created)."|".($row->name)."|".($row->thema)."|".($row->fromuser)."|".($un)."|".($row->password)."\n";
    }
  }
  echo "done\n";
?>
