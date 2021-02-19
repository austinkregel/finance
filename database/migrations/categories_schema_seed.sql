create table categories
(
	id bigint unsigned auto_increment
		primary key,
	name varchar(255) not null,
	category_id int unsigned not null,
	created_at timestamp null,
	updated_at timestamp null,
	constraint categories_category_id_unique
		unique (category_id)
) collate = utf8mb4_unicode_ci;

create index categories_name_index
	on categories (name);
