<?php

$QUERY_ALL_PRODUCTS = "
SELECT
	st_product.id_product,
	st_product_lang.id_product,s
	t_product_lang.id_lang,
	st_product.reference,
	name
FROM st_product
INNER JOIN st_product_lang
ON st_product.id_product=st_product_lang.id_product";

$QUERY_PRODUCT_BY_REF_FROM_PRODUCTS = "
SELECT
	st_product.id_product,
	st_product.quantity,
	st_product.price,
	st_product.reference,
	st_product_lang.id_product,
	st_product_lang.name
FROM
	st_product INNER JOIN st_product_lang
	ON st_product.id_product = st_product_lang.id_product
WHERE
	st_product.reference = '%s' AND
	(CAST(st_product.quantity AS DECIMAL(30, 30)) <> CAST('%s' AS DECIMAL(30, 30)) OR
	CAST(st_product.price AS DECIMAL(30, 30)) <> CAST('%s' AS DECIMAL(30, 30)))
";

$QUERY_PRODUCT_BY_REF_FROM_PRODUCTS_ATTRIBS = "
SELECT
	st_product_attribute.id_product_attribute,
	st_product_attribute.id_product,
	st_product_attribute.quantity,
	st_product_attribute.price,
	st_product_attribute.reference,
	st_product.id_product,
	st_product.price AS product_price,
	st_product_lang.id_product,
	st_product_lang.name
FROM
	st_product_attribute
	INNER JOIN st_product ON st_product_attribute.id_product = st_product.id_product
	INNER JOIN st_product_lang ON st_product_attribute.id_product = st_product_lang.id_product
WHERE
	st_product_attribute.reference = '%s' AND
	(CAST(st_product_attribute.quantity AS DECIMAL(30, 30)) <> CAST('%s' AS DECIMAL(30, 30)) OR
	CAST(st_product_attribute.price AS DECIMAL(30, 30)) <> CAST('%s' AS DECIMAL(30, 30)))
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

$UPDATE_PRODUCTS_ATTRIBS = "
UPDATE
	st_product_attribute
SET
	quantity = '%s',
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

?>

