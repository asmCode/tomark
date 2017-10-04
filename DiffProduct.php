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
		$this->priceBefore = (float)str_replace(',', '.', $priceBefore);
		$this->priceAfter = (float)str_replace(',', '.', $priceAfter);
	}
	
	public function IsUpdateRequired()
	{
		return
			$this->countBefore != $this->countAfter ||
			$this->priceBefore != $this->priceAfter;
	}
}

?>
