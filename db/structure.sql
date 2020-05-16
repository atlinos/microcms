drop table if exists article;

create table article (
    id integer not null primary key auto_increment,
    title varchar(100) not null,
    content varchar(2000) not null
) engine=innodb character set utf8 collate utf8_unicode_ci;