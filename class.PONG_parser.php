<?php

class PONG_parse{
	
	/**
	* Returns a portion of the string that is either before or after the deliniator.
	* The parse is not case sensitive, but the case of the parsed string is not affected
	*
	* @param  	string 	$string 		The string to be parsed
	* @param  	string 	$deliniator		Place where split occurs
	* @param  	string 	$desired 		BEFORE / AFTER deliniator
	* @param 	string 	$type 			INCL / EXCL include deliniator or not
	* @return 	string 					Parsed string
	*/
	public static function split_string($string, $deliniator = '', $before = '', $incl = ''){

		echo("hosso");
		echo($incl);
		if(strtolower($before) == "before" || $before == '') $before = TRUE;
		if(strtolower($before) == "after") $before = FALSE;

		if(strtolower($incl) == "incl" || strtolower($incl) == "include" || $incl == '') $incl = TRUE;
		if(strtolower($incl) == "excl" || strtolower($incl) == "exclude") $incl = FALSE;

		if ($deliniator == '' || $string == '') exit ("ERROR : You must supply a string and a word to split the text on.");
		
		$_lc_string 	= strtolower($string);
		$_marker 		= strtolower($deliniator);

		// return the text BEFORE the deliniator
		if ($before)
		{
			if (!$incl) // return text EXCLUDING deliniator
			{
				$_split_here = strpos($_lc_string, $_marker);
			}
			else
			{
				$_split_here = strpos($_lc_string, $_marker) + strlen($_marker);
			}
			$_parsed_string = substr($string, 0, $_split_here);
		}

		// return the text AFTER the delineator
		else
		{
			if (!$incl) // return text EXCLUDING delinator
			{
				if
				$_split_here = strpos($_lc_string, $_marker) + strlen($_marker);
			}
			else
			{
				$_split_here = strpos($_lc_string, $_marker);
			}
			$_parsed_string = substr($string, $_split_here, strlen($string));
		}
		return $_parsed_string;

	}


	/**
	* Returns text between two given strings
	*
	* 	$value = PONG_parse::return_between($string, $start, $end, $include_delim)
	* 
	* @param  	string 	$string 		The string to be parsed
	* @param  	string 	$beg_tag		Beginning tag ie "<a>"
	* @param  	string 	$end_tag 		End tag ie "</title>"
	* @param  	string 	$incl 			Wether or not to include deliminators
	* @return 	string 					Returned string
	*/
	public static function return_between($string, $start = '', $stop = '', $incl = TRUE)
	{
		if ($start == '' || $stop == '') exit("You must supply a start word and end word");
	
		$_temp = PONG_parse::split_string($string, $start, FALSE, $incl);
		return PONG_parse::split_string($_temp, $stop, TRUE, $incl);
	}


	/**
	* Returns an array of strings that exist repeatedly in $string
	* This function is useful for returning an array that contains links,
	* images, tables, or other data that appears more than once
	* 
	* @param  	string 	$string 		The string to be parsed
	* @param  	string 	$beg_tag		Beginning tag ie "<a>"
	* @param  	string 	$end_tag 		End tag ie "</title>"
	* @return 	string 					Parsed string
	*/
	public static function parse_array($string, $beg_tag, $end_tag)
	{
		preg_match_all("($beg_tag(.*)$end_tag)siU", $string, $matching_data);
		return $matching_data;
	}


	/**
	* Returns a portion of the string that is either before or after the deliniator.
	* The parse is not case sensitive, but the case of the parsed string is not affected
	*
	*	$value = get_attribute($tag, $attribute);
	*
	* @todo  	seems to return two versions.. one with open and close tags .. one without
	* @param  	string 	$tag 			The tag that contains the attribute
	* @param  	string 	$attribute		The attibute being searched for
	* @return 	string 					Returns value of attribute in a given tag
	*/
	public static function get_attribute($tag, $attribute)
	{
		$cleaned_html = PONG_parse::tidy_html($tag);

		// remove all line feeds from th string
		$cleaned_html = str_replace("\n", "", $cleaned_html);
		$cleaned_html = str_replace("\r", "", $cleaned_html);

		$lc_cleaned_html = strtolower($cleaned_html);

		// use return_between() to find the value for the attribute
		return PONG_parse::return_between($lc_cleaned_html, strtolower($attribute) . "=\"", "\"", FALSE);
	}


	/** 
	* Removes all text between $open_tag and $close_tag
	*
	*	$value = remove($string, $open_tag, $close_tag)
	* 
	* @param  	string 	$string			The input string
	* @param  	string 	$open_tag		The open tag
	* @param 	string 	$close_tag		The close tag
	* @return 	string 					The adjusted string
	*/
	public static function remove($input_string, $open_tag, $close_tag)
	{

		// get an array of things that should be removed from input string
		$remove_array = PONG_parse::parse_array($input_string, $open_tag, $close_tag);

		// remove each occurance of each array element from string
		for ($xx = 0; $xx < count($remove_array); $xx++)
		{
			$return_string = str_replace($remove_array[$xx], " ", $input_string);
		}

		return $return_string;
	}

	/** 
	* Looks to see if string appears in a text
	*
	*	$value = PONG_parse::look_for($haystack, $needle);
	* 
	* @param  	string 	$haystack		The input string
	* @param  	string 	$needle			The search term
	* @return 	boolean 			
	*/
	public static function look_for($haystack, $needle)
	{
		$lc_haystack = strtolower($haystack);
		$lc_needle = strtolower($needle);

		return stristr($lc_haystack, $lc_needle);
	}



	/**
	* Returns a 'cleaned up' (parsable) version of raw HTML
	* 
	*	$value = tidy_html($input_string);
	* 
	* @param  	string 	$input_string	Raw HTML
	* @return 	string 					Returns string of cleaned up HTML
	*/
	public static function tidy_html($input_string)
	{
		// detect if tidy is configured
		if (function_exists('tidy_get_release'))
		{
			// tidy for PHP 4
			if (substr(phpversion(), 0, 1) == 4)
			{
				tidy_setopt('uppercase-attributes', TRUE);
				tidy_setopt('wrap', 800);
				tidy_parse_string($input_string);
				$cleaned_html = tidy_get_output();
			}

			// tidy for PHP 5
			if (substr(phpversion(), 0, 1) == 5)
			{
				$config = array(
					'uppercase-attributes' 	=> 	TRUE,
					'wrap'					=>	800
					);
				$tidy = new tidy;
				$tidy->parseString($input_string, $config, 'utf8');
				$tidy->cleanRepair();
				$cleaned_html 	= tidy_get_output($tidy);
			}
		}
		else
		{
			// tidy is not configued for this computer
			$cleaned_html = $input_string;
		}

		return $cleaned_html;
	}
	

}


?>
