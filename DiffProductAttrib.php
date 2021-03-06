<?php

class DiffProductAttrib
{
	public $productAttributeId;
	public $productId;
	public $productPrice; // cena bazowa w produkcie. Powinna zawsze wynosić 0, całkowita cena
						  // atrybutu powinna być zawarta w atrybucie. Czasem może się zdarzyć,
						  // że cena nie będzie równa 0 i wtedy skrypt ją wyzeruje.
	public $reference;
	public $name;
	public $countBefore;
	public $countAfter;
	public $priceBefore;
	public $priceAfter;
	
	public function __construct(
		$productAttributeId,
		$productId,
		$productPrice,
		$reference,
		$name,
		$countBefore,
		$countAfter,
		$priceBefore,
		$priceAfter)
	{
		$this->productAttributeId = $productAttributeId;
		$this->productId = $productId;
		$this->productPrice = (float)$productPrice;
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
