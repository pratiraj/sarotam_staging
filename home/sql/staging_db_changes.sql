/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
/**
 * Author:  pratiraj
 * Created: 19 Feb, 2019
 */

--Mayur changes

alter table it_collection_register add opening_stock double DEFAULT NULL after opening_balance;

alter table it_collection_register add closing_stock double DEFAULT NULL after closing_balance;

alter table it_cr270001 add collection_reg bigint(20) NOT NULL after customer_id;

truncate it_collection_register;

insert into it_collection_register set crid = 1 , opentime = now(), closetime = now(), createtime = now();

--Akshay changes

create table stockadjustmentItemDetails (
id bigint(20) not null  AUTO_INCREMENT,
crid bigint(20),
prodid bigint(20) not null,
name varchar(150) not null,
desc1 varchar(50) DEFAULT Null,
desc2 varchar(50) DEFAULT NUll ,
thickness varchar(50) DEFAULT Null,
hsncode varchar(50),
createtime  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
updatetime datetime,
oldStock double,
addedstock double,
primary key(id)
);

create table stockadjustmentHeader (
id bigint(20) not null  AUTO_INCREMENT,
crid bigint(20),
prodid bigint(20) not null,
requestBy bigint(20),
requestDate TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
isApproved bigint(20) default 0,
approvedBy bigint(20),
approvedate datetime,
disapprovedBy bigint(20),
disapprovedate datetime,
primary key(id),
FOREIGN KEY (id) REFERENCES stockadjustmentItemDetails(id)
);            

--

alter table it_cr270001 add column uom_id bigint(20) after paymentmode;

update it_cr270001 set uom_id =1;
