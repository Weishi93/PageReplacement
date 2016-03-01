<?php
#Wei Shi#
#N10161894#
#ws1196#
$handle = fopen($argv[1], "r");
$file_str = fread($handle, filesize($argv[1]));
fclose($handle);
$file_arr = split("\n", $file_str);
$arr_length = sizeof($file_arr);
#PFF Algorithm#
#When F is a large number, it means the interval between two page faults is easier to less than F.#
#So it's more likely to add pages to the resident set. Then the resident set is becoming larger and larger.#
#So the number of page fault is decreasing#
#When F is small number, the interval between two page faults is easier to greater than F.#
#So it's more likely to discard all pages whose use bit is 0 and shrink.#
#So the number of page fault is increasing#
#So, F is large, then the number of PF is relatively small.#
#If F is small, then the number of PF is relatively large.#
$prevTime = 0;
$F = 10;
$resSet_PFF = array();
$pageFault_PFF = 0;
$maxSize_PFF = 0;
$less10_PFF = 0;
for ($i = 1; $i < $arr_length; $i++) { 
	# code...
	if(!array_key_exists($file_arr[$i], $resSet_PFF)){ // This page is not in the resident set, then generate a page fault
		$pageFault_PFF++;
		$currTime = $i;
		if(($currTime - $prevTime) < $F){ // Need to add frames
			$resSet_PFF[$file_arr[$i]] = 1; //Set the use bit to 1
		}else{ // Need to discard all the frames whose use bit = 0, then reset all frames whose use bit = 1
			foreach ($resSet_PFF as $key => $value) {
				# code...
				if($value == 0){ // discard
					unset($resSet_PFF[$key]);
				}else{ // reset
					$resSet_PFF[$file_arr[$i]] = 0;
				}	
			}
		}
		$prevTime = $currTime;
		if(sizeof($resSet_PFF) > $maxSize_PFF){
			$maxSize_PFF = sizeof($resSet_PFF);
		}
	}

	if(sizeof($resSet_PFF) <= 10){
		$less10_PFF++;
	}
}
echo "\nF = ".$F."\n";
echo "The number of page fault in PFF is ". $pageFault_PFF."\n";
echo "The maximum number of frames in PFF is ".$maxSize_PFF."\n";
echo "The times of number of frames less than 10 in PFF is ".$less10_PFF."\n\n";
#PFF Algorithm#

#VSWS Algorithm#
#As M and Q are increasing, the number of page faults is increasing and the maximum number of frames is increasing#
#As L is increasing, the number of page faults is decreasing and the maximum number of frames is increasing#
$resSet_VSWS = array();
$pageFault_VSWS = 0; // total number of page fault
$pageFault_intv = 0; // nnumber of page fault during interval
$maxSize_VSWS = 0;
$M = 5;
$L = 30;
$Q = 10;
$prev = 0;
$less10_VSWS = 0;
for($i = 1; $i < $arr_length; $i++){
	$curr = $i;

	if(!array_key_exists($file_arr[$i], $resSet_VSWS)){ // This page is not in the resident set, so generate a page fault
		$pageFault_VSWS++;
		$pageFault_intv++;
		$resSet_VSWS[$file_arr[$i]] = 1; // add this page 
	}else{
		$resSet_VSWS[$file_arr[$i]] = 1; 
	}
	if($curr - $prev == $M && $pageFault_intv >= $Q){ // prior to an elapsed period of L, Q page faults occur, and M time has elapsed
		foreach ($resSet_VSWS as $key => $value) {
			# code...
			if($value == 0){ // discard all frames whose use bit = 0
				unset($resSet_VSWS[$key]);
			}else{ // reset all use bits to 0
				$resSet_VSWS[$file_arr[$i]] = 0;
			}	
		}
		$prev = $curr;
		$pageFault_intv = 0;
	}elseif($curr - $prev == $L){ // L time has already elapsed, end of a interval
		foreach ($resSet_VSWS as $key => $value) {
			# code...
			if($value == 0){ // discard all frames whose use bit = 0
				unset($resSet_VSWS[$key]);
			}else{ // reset all use bits to 0
				$resSet_VSWS[$file_arr[$i]] = 0;
			}	
		}
		$prev = $curr;
		$pageFault_intv = 0;
	}
	if(sizeof($resSet_VSWS) > $maxSize_VSWS){
		$maxSize_VSWS = sizeof($resSet_VSWS);
	}

	if(sizeof($resSet_VSWS) <= 10){
		$less10_VSWS++;
	}
}
echo "M is ".$M."\n";
echo "L is ".$L."\n";
echo "Q is ".$Q."\n"; 
echo "The number of page fault in VSWS is ". $pageFault_VSWS."\n";
echo "The maximum number of frames in VSWS is ".$maxSize_VSWS."\n";
echo "The times of number of frames less than 10 in VSWS is ".$less10_VSWS."\n\n";
#VSWS Algorithm#


?>