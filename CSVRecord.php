<?php

class CSVRecord
{
	public $reference;
	public $count;
	public $price;
	
	public $existsInProducts;
	public $existsInAttributes;
	public $manyInstancesInProducts;
	public $manyInstancesInAttributes;
	
    public function __construct($reference, $price, $count)
	{
        $this->reference = $reference;
		$this->price = $price;
		$this->count = $count;
		
		$this->existsInProducts = false;
		$this->existsInAttributes = false;
		$this->manyInstancesInProducts = false;
		$this->manyInstancesInAttributes = false;
    }
	
	public static function LoadRecordsFromFile($filename)
	{		
		$file = fopen($filename, "r");
		if ($file == FALSE)
			return NULL;
			
		$records = array();
		$recordIndex = 0;
		
		$firstLine = true;
			
		while (!feof($file))
		{
			$line = fgets($file);
			if ($line == FALSE)
				continue;
				
			if (strlen($line) == 0)
				continue;
				
			if ($firstLine)
			{
				$firstLine = false;
				continue;
			}
				
			$values = explode(';', $line);
			if (count($values) != 3)
				continue;
				
			$records[$recordIndex] = new CSVRecord(trim($values[0]), str_replace(' ', '', trim($values[1])), str_replace(' ', '', trim($values[2])));
			$recordIndex++;
		}
		
		return $records;
	}
}

?>
