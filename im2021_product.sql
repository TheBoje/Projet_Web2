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