<?php

	function decode_chaine( $chaine )
	{
		//echo " [$chaine] => ";
		$chaine = str_replace("\xc3\xa9","\xe9",$chaine); //�
		$chaine = str_replace("\xc3\xa8","\xe8",$chaine); //�
		$chaine = str_replace("\xc3\xaa","\xEA",$chaine); //�
		$chaine = str_replace("\xc3\xab","\xEB",$chaine); //�

		$chaine = str_replace("\xc3\xae","\xee",$chaine); //�
		$chaine = str_replace("\xc3\xaf","\xef",$chaine); //�

		$chaine = str_replace("\xc3\xb9","\xf9",$chaine); //�

		$chaine = str_replace("\xc3\xa0","\xe0",$chaine); //�

		$chaine = str_replace("\xc3\xa7","\xE7",$chaine); //�

		$chaine = str_replace("\xc3\xb4","\xf4",$chaine); //�
		$chaine = str_replace("\xc3\xb6","\xf6",$chaine); //�
		$chaine = str_replace("\xc2\xb2","�",$chaine); //�

//		$chaine = iconv("UTF-8", "CP1252", $chaine);
		//echo " [$chaine]";
		return $chaine;

	}

?>
