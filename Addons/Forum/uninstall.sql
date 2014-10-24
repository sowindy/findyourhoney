DELETE FROM `wp_attribute` WHERE model_id = (SELECT id FROM wp_model WHERE `name`='forum_topics' ORDER BY id DESC LIMIT 1);
DELETE FROM `wp_model` WHERE `name`='forum_topics' ORDER BY id DESC LIMIT 1;
DROP TABLE IF EXISTS `wp_forum_topics`;

DELETE FROM `wp_attribute` WHERE model_id = (SELECT id FROM wp_model WHERE `name`='forum_comment' ORDER BY id DESC LIMIT 1);
DELETE FROM `wp_model` WHERE `name`='forum_comment' ORDER BY id DESC LIMIT 1;
DROP TABLE IF EXISTS `wp_forum_comment`;

DELETE FROM `wp_attribute` WHERE model_id = (SELECT id FROM wp_model WHERE `name`='forum_message' ORDER BY id DESC LIMIT 1);
DELETE FROM `wp_model` WHERE `name`='forum_message' ORDER BY id DESC LIMIT 1;
DROP TABLE IF EXISTS `wp_forum_message`;