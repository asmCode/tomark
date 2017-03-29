<?php

$QUERY_ALL_PRODUCTS = "
SELECT
	st_product.id_product,
	st_product_lang.id_product,
	st_product_lang.id_lang,
	st_product.reference,
	name
FROM st_product
INNER JOIN st_product_lang
ON st_product.id_product=st_product_lang.id_product
WHERE
	st_product_lang.id_lang = '3'
";

$QUERY_PRODUCT_BY_REF_FROM_PRODUCTS = "
SELECT
	st_product.id_product,
	st_product.reference,
	st_product_lang.id_product,
	st_product_lang.name,
	st_stock_available.quantity,
	st_product_shop.price
FROM
	st_product
	INNER JOIN st_product_lang ON st_product.id_product = st_product_lang.id_product
	INNER JOIN st_stock_available ON st_product.id_product = st_stock_available.id_product
	INNER JOIN st_product_shop ON st_product.id_product = st_product_shop.id_product
WHERE
	st_product.reference = '%s' AND
	st_product_lang.id_lang = '3'
";

$QUERY_PRODUCT_BY_REF_FROM_PRODUCTS_ATTRIBS = "
SELECT
	st_product_attribute.id_product_attribute,
	st_product_attribute.id_product,
	st_product_attribute.reference,
	st_product.id_product,
	st_product_shop.price AS product_price,
	st_product_lang.id_product,
	st_product_lang.name,
	st_stock_available.quantity,
	st_product_attribute_shop.price
FROM
	st_product_attribute
	INNER JOIN st_product ON st_product_attribute.id_product = st_product.id_product
	INNER JOIN st_product_lang ON st_product_attribute.id_product = st_product_lang.id_product
	INNER JOIN st_stock_available ON st_product_attribute.id_product_attribute = st_stock_available.id_product_attribute
	INNER JOIN st_product_shop ON st_product_attribute.id_product = st_product_shop.id_product
	INNER JOIN st_product_attribute_shop ON st_product_attribute.id_product_attribute = st_product_attribute_shop.id_product_attribute
WHERE
	st_product_attribute.reference = '%s' AND
	st_product_lang.id_lang = '3'
";

$UPDATE_PRODUCTS = "
UPDATE
	st_product
SET
	quantity = '%s',
	price = '%s'
WHERE
	id_product = '%s'
";

$UPDATE_PRODUCTS_SHOP = "
UPDATE
	st_product_shop
SET
	price = '%s'
WHERE
	id_product = '%s'
";

$UPDATE_PRODUCTS_ATTRIBS = "
UPDATE
	st_product_attribute
SET
	quantity = '%s',
	price = '%s'
WHERE
	id_product_attribute = '%s'
";

$UPDATE_PRODUCTS_ATTRIBS_SHOP = "
UPDATE
	st_product_attribute_shop
SET
	price = '%s'
WHERE
	id_product_attribute = '%s'
";

$SET_ZERO_PRICE_IN_PRODUCTS = "
UPDATE
	st_product
SET
	price = '0'
WHERE
	id_product = '%s'
";

$MOVE_PRICE_PROM_PRODUCT_TO_ATTRIBS = "
UPDATE
	st_product_attribute
SET
	st_product_attribute.price = st_product_attribute.price + (SELECT price FROM st_product WHERE id_product = '%s')
WHERE
	id_product = '%s'
";

$UPDATE_QUANTITY_IN_PRODUCTS = "
UPDATE
	st_product
SET
	quantity = (SELECT
					SUM(quantity)
				FROM
					st_product_attribute
				WHERE
					id_product = '%s')
WHERE
	id_product = '%s'
";

$UPDATE_QUANTITY_IN_STOCK_AVAILABLE_FOR_PRODUCTS = "
UPDATE
	st_stock_available
SET
	quantity = '%s'
WHERE
	id_product = '%s'
";

$UPDATE_QUANTITY_IN_STOCK_AVAILABLE_FOR_PRODUCT_ATTRIBUTE = "
UPDATE
	st_stock_available
SET
	quantity = '%s'
WHERE
	id_product_attribute = '%s'
";

$SELECT_QUANTITY_IN_STOCK_AVAILABLE_FOR_PRODUCT_WITH_ATTRIBUTES = "
SELECT
  	SUM(quantity) AS quantity
FROM
	st_stock_available
WHERE
	id_product = '%s' AND
    id_product_attribute != '0'
";

$FIX_QUANTITY_IN_STOCK_AVAILABLE_FOR_PRODUCT_WITH_ATTRIBUTES = "
UPDATE
	st_stock_available
SET
	quantity = '%s'
WHERE
	id_product = '%s' AND
    id_product_attribute = '0'
";

?>

