<?php

/*****************************************************************
*                                                                *
*          ROUND TO THE APPROPRATE THOUSANDS                     *
*                                                                *
******************************************************************/
function kilomega( $val ) {
	$options = get_option('socialWarfareOptions');
	if($val):
		if( $val < 1000 ):
			return number_format($val);
		else:
			$val = intval($val) / 1000;
			return number_format($val,$options['swDecimals']).'K';
		endif;
	else:
		return 0;
	endif;
}