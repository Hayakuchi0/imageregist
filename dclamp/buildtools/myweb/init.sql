create database imageregist default character set utf8 collate utf8_general_ci;
use imageregist;
create table users(
username varchar(40) not null primary key,
hashlist varchar(20000) not null
);
