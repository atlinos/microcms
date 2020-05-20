drop table if exists comment;
drop table if exists user;
drop table if exists article;

create table article (
    id integer not null primary key auto_increment,
    title varchar(100) not null,
    content varchar(2000) not null
) engine=innodb character set utf8 collate utf8_unicode_ci;

create table user (
    id integer not null primary key auto_increment,
    username varchar(50) not null,
    password varchar(88) not null,
    salt varchar(23) not null,
    role varchar(50) not null
) engine=innodb character set utf8 collate utf8_unicode_ci;

create table comment (
    id integer not null primary key auto_increment,
    content varchar(500) not null,
    article_id integer not null,
    user_id integer not null,
    constraint fk_com_art foreign key(article_id) references article(id),
    constraint fk_com_usr foreign key(user_id) references user(id)
) engine=innodb character set utf8 collate utf8_unicode_ci;