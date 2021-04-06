create table im2021_user
(
    id        INTEGER      not null
        primary key autoincrement,
    login     VARCHAR(255) not null,
    password  VARCHAR(40)  not null,
    name      VARCHAR(32)  not null,
    firstname VARCHAR(32)  not null,
    birthdate DATE         not null,
    is_admin  BOOLEAN default '0' not null
);

create unique index log_idx
    on im2021_user (login);

INSERT INTO im2021_user (id, login, password, name, firstname, birthdate, is_admin) VALUES (3, 'Altimor', '00d70c561892a94980befd12a400e26aeb4b8599', 'Vincent', 'Commin', '2000-06-02', 0);
INSERT INTO im2021_user (id, login, password, name, firstname, birthdate, is_admin) VALUES (5, 'admin', '49439d9ca8c4b351ad727dc27c4bdae7e7076a9f', 'Admin', 'Admin', '1921-01-01', 1);
INSERT INTO im2021_user (id, login, password, name, firstname, birthdate, is_admin) VALUES (6, 'gilles', 'ab9240da95937a0d51b41773eafc8ccb8e7d58b5', 'Subrenat', 'Gilles', '2010-09-12', 0);
INSERT INTO im2021_user (id, login, password, name, firstname, birthdate, is_admin) VALUES (7, 'rita', '1811ed39aa69fa4da3c457bdf096c1f10cf81a9b', 'Zrour', 'Rita', '1921-01-01', 0);

create table im2021_product
(
    id       INTEGER       not null
        primary key autoincrement,
    label    VARCHAR(255)  not null,
    quantity INTEGER       not null,
    price    NUMERIC(5, 2) not null
);

INSERT INTO im2021_product (id, label, quantity, price) VALUES (2, 'NotCoolBeer', 3, 4.00);
INSERT INTO im2021_product (id, label, quantity, price) VALUES (5, 'WhatIsThisBeer', 0, 9.50);
INSERT INTO im2021_product (id, label, quantity, price) VALUES (6, 'SomeBeer', 6, 9.00);
INSERT INTO im2021_product (id, label, quantity, price) VALUES (7, 'Coffee', 8, 4.00);
INSERT INTO im2021_product (id, label, quantity, price) VALUES (8, 'La Since', 20, 135.00);

create table im2021_order
(
    id       INTEGER not null
        primary key autoincrement,
    client   INTEGER not null
        constraint FK_1076FD5FC7440455
        references im2021_user,
    product  INTEGER not null
        constraint FK_1076FD5FD34A04AD
        references im2021_product,
    quantity INTEGER not null
);

create index IDX_1076FD5FC7440455
    on im2021_order (client);

create index IDX_1076FD5FD34A04AD
    on im2021_order (product);

create unique index prod_user_idx
    on im2021_order (client, product);