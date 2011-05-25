DROP TABLE IF EXISTS `fb_acl`;
CREATE TABLE `fb_acl` (
`user_id` INT NOT NULL ,
`acl_id` INT NOT NULL ,
PRIMARY KEY ( `user_id` , `acl_id` )
) ENGINE = MYISAM CHARACTER SET utf8 COLLATE utf8_general_ci;