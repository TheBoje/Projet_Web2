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

