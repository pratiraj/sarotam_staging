/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
/**
 * Author:  pratiraj
 * Created: 18 Feb, 2019
 */

alter table it_cr270001 add column uom_id bigint(20) after paymentmode;

update it_cr270001 set uom_id =1;
