DROP TABLE IF EXISTS `chat_chatrooms`;
CREATE TABLE chat_chatrooms (
  id            int(12)     NOT NULL auto_increment,
  created       bigint(15)  NOT NULL,
  name          varchar(20) NOT NULL,
  thema         text        NOT NULL,
  fromuser      int(12),
  fromusername  varchar(20),
  password      varchar(20),
  PRIMARY KEY (id),
  UNIQUE id_2 (id),
  KEY id (id, fromuser)
);

DROP TABLE IF EXISTS `chat_messages`;
CREATE TABLE chat_messages (
  id        int(12) unsigned NOT NULL auto_increment,
  date      bigint(15)  NOT NULL,
  username  varchar(20) NOT NULL,
  color     int(8)      NOT NULL,
  message   text        NOT NULL,
  chatroom  int(12)     NOT NULL,
  private   int(12)     NOT NULL,
  PRIMARY KEY (id),
  UNIQUE id_2 (id),
  KEY id (id)
);

DROP TABLE IF EXISTS `chat_users`;
CREATE TABLE chat_users (
  id          int(12) NOT NULL auto_increment,
  username    char(20) NOT NULL,
  lastactive  bigint(15) NOT NULL,
  chatroom    int(12) NOT NULL,
  PRIMARY KEY (id),
  UNIQUE username (username),
  KEY id (id)
);

INSERT INTO chat_chatrooms VALUES('', '1000000000', 'Main Lounge', 'Default chatroom thema', '0', '', '');
