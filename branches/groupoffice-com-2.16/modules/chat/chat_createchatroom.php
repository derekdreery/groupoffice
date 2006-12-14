<?php
  //createchatroom
  $name = $_GET["name"];
  $thema = $_GET["thema"];
  $userid = $_GET["userid"];
  $username = $_GET["username"];
  $password = $_GET["password"];

  if ($name && $thema && $userid && $username) {
    //check lengths and qality
    $name = str_replace("'", "\\'", $name);
    $thema = str_replace("'", "\\'", $thema);
    $username = str_replace("'", "\\'", $username);
    if (strlen($name) > 20) $name = substr($name, 0, 20);
    if (strlen($username) > 20) $username = substr($username, 0, 20);

    query("delete from chat_chatrooms where fromuser=$userid");

    $now = time();
    query("insert into chat_chatrooms (id, created, name, thema, fromuser, fromusername, password) values (".(($CONNECTION == "pgsql")?("nextval('chat_chatrooms_sequence')"):("''")).", $now, '$name', '$thema', $userid, '$username', '$password')");

    $result = query("select id from chat_chatrooms where name='$name' and created=$now");
    if ($result) {
      if ($row = fetch_object($result)) {
        echo $row->id;
      }
      else echo "0";
    }
    else echo "0";
  }
?>
