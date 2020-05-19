drop table if exists comment;
drop table if exists article;

create table article (
    id integer not null primary key auto_increment,
    title varchar(100) not null,
    content varchar(2000) not null
) engine=innodb character set utf8 collate utf8_unicode_ci;

create table comment (
    id integer not null primary key auto_increment,
    author varchar(100) not null,
    content varchar(500) not null,
    article_id integer not null,
    constraint fk_com_art foreign key(article_id) references article(id)
) engine=innodb character set utf8 collate utf8_unicode_ci;