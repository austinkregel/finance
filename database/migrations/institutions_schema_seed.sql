create table institutions
(
    id             int unsigned auto_increment
        primary key,
    name           varchar(255)  not null,
    institution_id varchar(255)  not null,
    logo           text          null,
    site_url       varchar(2048) null,
    products       text          null,
    primary_color  varchar(8)    null,
    created_at     timestamp     null,
    updated_at     timestamp     null,
    constraint institutions_name_institution_id_unique
        unique (name, institution_id)
) collate = utf8mb4_unicode_ci;

create index institutions_institution_id_index
    on institutions (institution_id);
