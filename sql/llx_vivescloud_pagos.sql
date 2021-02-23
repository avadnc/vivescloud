
CREATE TABLE IF NOT EXISTS `llx_vivescloud_pagos` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `ref` varchar(50) NOT NULL DEFAULT '(PROV',
  `datec` datetime NOT NULL,
  `datev` datetime NOT NULL,
  `payment_type` varchar(50) NOT NULL DEFAULT '',
  `fk_bank_account` int(11) NOT NULL,
  `tms` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `fk_user` int(11) NOT NULL,
  PRIMARY KEY (`rowid`),
  KEY `rowid` (`rowid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


