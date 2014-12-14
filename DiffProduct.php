<?php

class DiffProduct
{
	public $productId;
	public $reference;
	public $name;
	public $countBefore;
	public $countAfter;
	public $priceBefore;
	public $priceAfter;
	
	public function __construct(
		$productId,
		$reference,
		$name,
		$countBefore,
		$countAfter,
		$priceBefore,
		$priceAfter)
	{
		$this->productId = $productId;
		$this->reference = $reference;
		$this->name = $name;
		$this->countBefore = (int)$countBefore;
		$this->countAfter = (int)$countAfter;
		$this->priceBefore = str_replace(',', '.', $priceBefore);
		$this->priceAfter = str_replace(',', '.', $priceAfter);
	}
}

?>
