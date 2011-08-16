DROP TABLE IF EXISTS `ct_tags`;
CREATE TABLE `ct_tags` (
`user_id` INT NOT NULL ,
`tag` VARCHAR( 5 ) NOT NULL ,
PRIMARY KEY ( `user_id` ) ,
UNIQUE (
`tag`
)
) ENGINE = MYISAM ;