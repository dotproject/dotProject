#
# $Id$
# 
# DO NOT USE THIS SCRIPT DIRECTLY - USE THE INSTALLER INSTEAD.
#
# All entries must be date stamped in the correct format.
#
# 20101010
# Build the dotpermissions table and populate it.
DROP TABLE IF EXISTS `%dbprefix%dotpermissions`;
CREATE TABLE `%dbprefix%dotpermissions` (
  `acl_id` int(11) NOT NULL DEFAULT '0',
  `user_id` varchar(80) NOT NULL DEFAULT '',
  `section` varchar(80) NOT NULL DEFAULT '',
  `axo` varchar(80) NOT NULL DEFAULT '',
  `permission` varchar(80) NOT NULL DEFAULT '',
  `allow` int(11) NOT NULL DEFAULT '0',
  `priority` int(11) NOT NULL DEFAULT '0',
  `enabled` int(11) NOT NULL DEFAULT '0',
  KEY `user_id` (`user_id`,`section`,`permission`,`axo`)
);

INSERT INTO `%dbprefix%dotpermissions`
	(acl_id,user_id,section,axo,permission,allow,priority,enabled)
SELECT
	acl.id,aro.value,axo_m.section_value,axo_m.value,
	aco_m.value,acl.allow,1,acl.enabled 
FROM `%dbprefix%gacl_acl` acl
LEFT JOIN `%dbprefix%gacl_aco_map` aco_m ON acl.id=aco_m.acl_id 
LEFT JOIN `%dbprefix%gacl_aro_map` aro_m ON acl.id=aro_m.acl_id 
LEFT JOIN `%dbprefix%gacl_aro` aro ON aro_m.value=aro.value 
LEFT JOIN `%dbprefix%gacl_axo_map` axo_m on axo_m.acl_id=acl.id 
WHERE aro.name IS NOT NULL AND axo_m.value IS NOT NULL;

INSERT INTO `%dbprefix%dotpermissions` 
	(acl_id,user_id,section,axo,permission,allow,priority,enabled)
SELECT acl.id,aro.value,axo.section_value,axo.value,
	aco_m.value,acl.allow,2,acl.enabled 
FROM `%dbprefix%gacl_acl` acl 
LEFT JOIN `%dbprefix%gacl_aco_map` aco_m ON acl.id=aco_m.acl_id 
LEFT JOIN `%dbprefix%gacl_aro_map` aro_m ON acl.id=aro_m.acl_id 
LEFT JOIN `%dbprefix%gacl_aro` aro ON aro_m.value=aro.value 
LEFT JOIN `%dbprefix%gacl_axo_groups_map` axo_gm on axo_gm.acl_id=acl.id 
LEFT JOIN `%dbprefix%gacl_axo_groups` axo_g on axo_gm.group_id=axo_g.id 
LEFT JOIN `%dbprefix%gacl_groups_axo_map` g_axo_m ON axo_g.id=g_axo_m.group_id 
LEFT JOIN `%dbprefix%gacl_axo` axo ON g_axo_m.axo_id=axo.id 
WHERE aro.value IS NOT NULL AND axo_g.value IS NOT NULL;

INSERT INTO `%dbprefix%dotpermissions` 
	(acl_id,user_id,section,axo,permission,allow,priority,enabled)
SELECT  acl.id,aro.value,axo_m.section_value,axo_m.value,aco_m.value,
	acl.allow,3,acl.enabled 
FROM `%dbprefix%gacl_acl` acl 
LEFT JOIN `%dbprefix%gacl_aco_map` aco_m ON acl.id=aco_m.acl_id 
LEFT JOIN `%dbprefix%gacl_aro_groups_map` aro_gm ON acl.id=aro_gm.acl_id 
LEFT JOIN `%dbprefix%gacl_aro_groups` aro_g ON aro_gm.group_id=aro_g.id 
LEFT JOIN `%dbprefix%gacl_axo_map` axo_m on axo_m.acl_id=acl.id 
LEFT JOIN `%dbprefix%gacl_groups_aro_map` g_aro_m ON aro_g.id=g_aro_m.group_id 
LEFT JOIN `%dbprefix%gacl_aro` aro ON g_aro_m.aro_id=aro.id 
WHERE axo_m.value IS NOT NULL AND aro.name IS NOT NULL;

INSERT INTO `%dbprefix%dotpermissions` 
	(acl_id,user_id, section,axo,permission,allow,priority,enabled)
SELECT acl.id,aro.value,axo.section_value,axo.value,aco_m.value,
	acl.allow,4,acl.enabled 
FROM gacl_acl acl 
LEFT JOIN `%dbprefix%gacl_aco_map` aco_m ON acl.id=aco_m.acl_id 
LEFT JOIN `%dbprefix%gacl_aro_map` aro_m ON acl.id=aro_m.acl_id 
LEFT JOIN `%dbprefix%gacl_aro_groups_map` aro_gm ON acl.id=aro_gm.acl_id 
LEFT JOIN `%dbprefix%gacl_aro_groups` aro_g ON aro_gm.group_id=aro_g.id 
LEFT JOIN `%dbprefix%gacl_axo_groups_map` axo_gm on axo_gm.acl_id=acl.id 
LEFT JOIN `%dbprefix%gacl_axo_groups` axo_g on axo_gm.group_id=axo_g.id 
LEFT JOIN `%dbprefix%gacl_groups_aro_map` g_aro_m ON aro_g.id=g_aro_m.group_id 
LEFT JOIN `%dbprefix%gacl_aro` aro ON g_aro_m.aro_id=aro.id 
LEFT JOIN `%dbprefix%gacl_groups_axo_map` g_axo_m ON axo_g.id=g_axo_m.group_id 
LEFT JOIN `%dbprefix%gacl_axo` axo ON g_axo_m.axo_id=axo.id 
WHERE axo_g.value IS NOT NULL and aro.value IS NOT NULL;


