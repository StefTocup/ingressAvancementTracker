<?php

	function decode_chaine( $chaine )
	{
		//echo " [$chaine] => ";
		$chaine = str_replace("\xc3\xa9","\xe9",$chaine); //é
		$chaine = str_replace("\xc3\xa8","\xe8",$chaine); //è
		$chaine = str_replace("\xc3\xaa","\xEA",$chaine); //ê
		$chaine = str_replace("\xc3\xab","\xEB",$chaine); //ë

		$chaine = str_replace("\xc3\xae","\xee",$chaine); //î
		$chaine = str_replace("\xc3\xaf","\xef",$chaine); //ï

		$chaine = str_replace("\xc3\xb9","\xf9",$chaine); //ù

		$chaine = str_replace("\xc3\xa0","\xe0",$chaine); //à

		$chaine = str_replace("\xc3\xa7","\xE7",$chaine); //ç

		$chaine = str_replace("\xc3\xb4","\xf4",$chaine); //ô
		$chaine = str_replace("\xc3\xb6","\xf6",$chaine); //ö
		$chaine = str_replace("\xc2\xb2","²",$chaine); //²

//		$chaine = iconv("UTF-8", "CP1252", $chaine);
		//echo " [$chaine]";
		return $chaine;

	}

?>
