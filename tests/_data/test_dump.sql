DROP TABLE dotp_billingcode_mytest;

CREATE TABLE `dotp_billingcode_mytest` (
  `billingcode_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `billingcode_name` varchar(25) NOT NULL DEFAULT '',
  `billingcode_value` float NOT NULL DEFAULT '0',
  `billingcode_desc` varchar(255) NOT NULL DEFAULT '',
  `billingcode_status` int(1) NOT NULL DEFAULT '0',
  `company_id` bigint(20) NOT NULL DEFAULT '0',
  PRIMARY KEY (`billingcode_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
