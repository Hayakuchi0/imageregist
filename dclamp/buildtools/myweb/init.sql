create database imageregist default character set utf8 collate utf8_general_ci;
use imageregist;
create table users(
username varchar(40) not null primary key,
hashlist varchar(20000) not null
);
create table contents(
content_id integer not null primary key auto_increment,
postuser varchar(40) not null,
foreign key (postuser) references users(username),
posted_text varchar(1000) not null
);
