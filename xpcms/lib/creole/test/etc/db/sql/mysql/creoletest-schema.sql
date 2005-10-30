# This is a fix for InnoDB in MySQL >= 4.1.x
# It "suspends judgement" for fkey relationships until are tables are set.
SET FOREIGN_KEY_CHECKS = 0;

# -----------------------------------------------------------------------
# products
# -----------------------------------------------------------------------
drop table if exists products;

CREATE TABLE products(

  ProductID INTEGER NOT NULL ,

  ProductName VARCHAR(40) default '' NOT NULL ,

  SupplierID INTEGER ,

  CategoryID INTEGER ,

  QuantityPerUnit VARCHAR(20) ,

  UnitPrice DOUBLE ,

  UnitsInStock INTEGER ,

  UnitsOnOrder INTEGER ,

  ReorderLevel INTEGER ,

  Discontinued INTEGER default 0 NOT NULL ,

  Notes MEDIUMTEXT ,

  OrderDate DATETIME ,

  PRIMARY KEY(ProductID)) Type=InnoDB;

# -----------------------------------------------------------------------
# ref_table
# -----------------------------------------------------------------------
drop table if exists ref_table;

CREATE TABLE ref_table(

  `ID` INTEGER NOT NULL ,

  RefID1 INTEGER ,

  RefID2 INTEGER ,

  CONSTRAINT `ref_table_ibfk_1` FOREIGN KEY (`RefID1`) REFERENCES `supplier` (`SupplierID`),

  CONSTRAINT `ref_table_ibfk_2` FOREIGN KEY (`RefID2`) REFERENCES `category` (`CategoryID`),

  KEY `RefID1` (`RefID1`),

  KEY `RefID2` (`RefID2`),

PRIMARY KEY(`ID`)) Type=InnoDB;
# -----------------------------------------------------------------------
# master_table1
# -----------------------------------------------------------------------
drop table if exists master_table1;

CREATE TABLE master_table1(

  SupplierID INTEGER NOT NULL ,

  SupplierName VARCHAR(20) ,

PRIMARY KEY(SupplierID)) Type=InnoDB;
# -----------------------------------------------------------------------
# master_table2
# -----------------------------------------------------------------------
drop table if exists master_table2;

CREATE TABLE master_table2(

  CategoryID INTEGER NOT NULL ,

  CatName VARCHAR(20) ,

PRIMARY KEY(CategoryID)) Type=InnoDB;
# -----------------------------------------------------------------------
# blobtest
# -----------------------------------------------------------------------
drop table if exists blobtest;

CREATE TABLE blobtest(

  BlobID INTEGER NOT NULL ,

  BlobName VARCHAR(30) NOT NULL ,

  BlobData LONGBLOB NOT NULL ,

    PRIMARY KEY(BlobID)) Type=InnoDB;
# -----------------------------------------------------------------------
# clobtest
# -----------------------------------------------------------------------
drop table if exists clobtest;

CREATE TABLE clobtest(

  ClobID INTEGER NOT NULL ,

  ClobName VARCHAR(30) NOT NULL ,

  ClobData LONGTEXT NOT NULL ,

    PRIMARY KEY(ClobID)) Type=InnoDB;
# -----------------------------------------------------------------------
# idgentest
# -----------------------------------------------------------------------
drop table if exists idgentest;

CREATE TABLE idgentest(

  ID INTEGER NOT NULL AUTO_INCREMENT,

  Name VARCHAR(40) default '' NOT NULL ,

PRIMARY KEY(ID)) Type=InnoDB;

# -----------------------------------------------------------------------
# indexes
# -----------------------------------------------------------------------
drop table if exists indexes;

CREATE TABLE indexes(

  ProductID INTEGER NOT NULL ,

  ProductName VARCHAR(40) default '' NOT NULL ,

  SupplierID INTEGER ,

  CategoryID INTEGER ,

  UnitPrice DOUBLE ,

  KEY `ProductNameIDX` (`ProductName`),

  KEY `ComplexIDX` (`SupplierID`, `CategoryID`, `UnitPrice`),

  UNIQUE KEY `UniqueComplexIDX` (`SupplierID`, `CategoryID`, `UnitPrice`),

  PRIMARY KEY(ProductID)) Type=InnoDB;

# -----------------------------------------------------------------------
# vendor
# -----------------------------------------------------------------------
drop table if exists `vendor`;

CREATE TABLE `vendor`(

  `id` int(11) NOT NULL auto_increment,

  Content TEXT ,

  FULLTEXT KEY `Content` (`Content`),

  PRIMARY KEY(ID)) Type=MyISAM;

# This restores the fkey checks, after having unset them
# in database-start.tpl

SET FOREIGN_KEY_CHECKS = 1;
