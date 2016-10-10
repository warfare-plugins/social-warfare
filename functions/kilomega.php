<?php

/*****************************************************************
*                                                                *
*          ROUND TO THE APPROPRATE THOUSANDS                     *
*                                                                *
******************************************************************/
function swp_kilomega( $val ) {

	// Fetch the user assigned options
	$options = swp_get_user_options();

	// Check if we even have a value to format
	if($val):

		// Check if the value is less than 1,000....
		if( $val < 1000 ):

			// If less than 1,000 just format and kick it back
			return number_format($val);

		// Check if the value is greater than 1,000 and less than 1,000,000....
		elseif( $val < 1000000):

			// Start by deviding the value by 1,000
			$val = intval($val) / 1000;

			// If the decimal separator is a period
			if($options['swp_decimal_separator'] == 'period'):

				// Then format the number to the appropriate number of decimals
				return number_format($val,$options['swDecimals'],'.',',').'K';

			// If the decimal separator is a comma
			else:

				// Then format the number to the appropriate number of decimals
				return number_format($val,$options['swDecimals'],',','.').'K';

			endif;

		// Check if the value is greater than 1,000,000....
		else:

			// Start by deviding the value by 1,000,000
			$val = intval($val) / 1000000;

			// If the decimal separator is a period
			if($options['swp_decimal_separator'] == 'period'):

				// Then format the number to the appropriate number of decimals
				return number_format($val,$options['swDecimals'],'.',',').'M';

			// If the decimal separator is a comma
			else:

				// Then format the number to the appropriate number of decimals
				return number_format($val,$options['swDecimals'],',','.').'M';

			endif;

		endif;

	// If there is no value, return a zero
	else:

		return 0;

	endif;
}
