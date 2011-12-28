<?php

class url {
	// parameter to array method (example: /post/34 gives post = 34
	public function p2a($paramstring, $sep = '/', $inner_sep = null) {
		// action/param1/33/param2/453/param3/tjohejsan
		$return = array();
		$tmp = explode($sep, $paramstring);


		if ($inner_sep == null) {
			if (count($tmp)%2 != 0) {
				echo 'Something is fuzzy about \'yer parameters, do not expect optimal output until you fix this!<br />';
			}
			for($i=0;$i<count($tmp); $i++) {
				$return[$tmp[$i++]] = $tmp[$i];
			}
		} else {
			// action/param1=33/param2=453/param3=tjohejsan
			foreach($tmp as $split) {
				$keyval = explode($inner_sep, $split);
				$return[$keyval[0]] = $keyval[1];
			}
		}
		return $return;
	}
}
