CREATE TABLE rt_shop_product_to_related_category (category_id BIGINT, product_id BIGINT, position BIGINT, PRIMARY KEY(category_id, product_id)) ENGINE = INNODB;
ALTER TABLE rt_shop_product_to_related_category ADD CONSTRAINT rpri FOREIGN KEY (product_id) REFERENCES rt_shop_product(id) ON DELETE CASCADE;
ALTER TABLE rt_shop_product_to_related_category ADD CONSTRAINT rcri FOREIGN KEY (category_id) REFERENCES rt_shop_category(id) ON DELETE CASCADE;
