<?php

/*
 * This is a statistic class that accepts one or two unidimensional arrays of data.
 * It returns a stat array using the getStats() method.
 * If only one array is sent, it will return the min,max, sum,  median, average and standard 
 * deviation for this array. If two arrays are sent, in addition to these values for both
 * arrays, a linear regression line is computed: m is the slope b is the Yaxis intersect,
 * r is the correlation, a measure of the quality of the data fit by the regression line (r=1 
 * being a perfect fit) and t is the "t"test, to test whether the association between X and Y 
 * data is real or merely apparent, I leave the user to interpret this test himself. (See the
 * tutorial and the table at: http://www.bmj.com/collections/statsbk/apptabb.html).
 * The regression line will be calculated only if both arrays have the same number of samples. 
 * Note that it is the responsibility of the user to make sure that his data are in linear form
 * (For the linear regression to work, Median and Average values should be close).
 * I tried to use the histogram class wrote by Jesus Castagnetto as an inheritance for
 * this script, but it was too complicated since that I needed two arrays. Probably the other
 * way around, using Stat1 as an inheritance for an histogram class, will be easier now. 
 * Tested with PHP4 beta 4.
 * (c) Alain M. Samoun 3/12/00 alain@samoun.com
 * License: Same as the GNU's Free Software Foundation or the open source PHP license as you
   prefer;-).
    
    This library is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
    Lesser General Public License for more details.

 */


  
class Stats1 {

/* variables */

	var $N,$NY,$MINX,$MAXX,$MAXY,$MINY,$AVGX,$AVGY,$STDVX,$STDVY,$SUMX,$SUMY,$SUMX2,$SUMY2,$SUMXY;
	var $X = array(),$Y = array();
	var $MEDX,$MEDY,$DEV,$m,$b,$r;
	var $STATS = array();

	
    /**
     * Constructor 
     * Rewritten by sweih.
     * Parse phpOpenTracker arrays and then start parsing.
     */
	function Stats1($X="", $Y="",$DEV=0, $title="") {
		$PX=array();
		$PY=array();
		
		if (is_array($X)) {
		  for ($i=1; $i < count($X); $i++) {
		  	$PX[$i] = $X[$i]["value"];
		  }	
		}
		
		
		if (is_array($Y)) {
		  for ($i=0; $i < count($Y); $i++) {
		  	$PY[$i] = $Y[$i]["value"];
		  }	
		}
		
		$this->create($PX,$PY,$title);
		
	}

	/* Create the class */
	
	function create($X,$Y,$title="") {
		/* Check if we got a valid set of data*/
		(($this->N = count($X))  > 1) or die("Not enough data, number of values : ".$this->N."\n");
		
		/* initialize values */
		$this->MINX = (float) min($X);
		$this->MAXX= (float) max($X);	
		
		/* Compute X Median */
		# Sort values in array X
		$XX = $X;
		sort ($XX);
		if ($this->N%2 == 0){
			$this->MEDX = (($XX[($this->N)/2])+($XX[1+($this->N/2)]))/2;
		}else{
			$this->MEDX = $XX[floor($this->N/2)];
		}
		
		$this->NY = count($Y);
		$this->MINY = (float) min($Y);
		$this->MAXY= (float) max($Y);
		
		/* Compute Y Median */
		# Sort values in array Y
		$YY = $Y;
		sort ($YY);
		if ($this->NY%2 == 0){
			$this->MEDY = (($YY[($this->NY)/2])+($YY[1+($this->NY/2)]))/2;
		}else{
			$this->MEDY = $YY[floor($this->NY/2)];
		}
		
		 
		$this->setTitle($title);
		
		
		
		
		/* stats */
		for ($i = 0; $i < $this->N ; $i++) {
			$this->SUMX += (float) $X[$i];
			$this->SUMX2 += (float) pow($X[$i],2);
			$this->SUMXY += (float) $X[$i]* (float)$Y[$i];
		}
		$this->AVGX = (float) $this->SUMX/ (float) $this->N;
		$this->STDVX = (float) sqrt(($this->SUMX2 - $this->N*pow($this->AVGX,2))/(float)($this->N - 1));

		
		for ($i = 0; $i < $this->NY ; $i++) {
			$this->SUMY += (float) $Y[$i];
			$this->SUMY2 += (float) pow($Y[$i],2);
		}
		$this->AVGY = (float) $this->SUMY/ (float)$this->NY;
		$this->STDVY = (float) sqrt(($this->SUMY2 - $this->NY*pow($this->AVGY,2))/(float)($this->NY - 1));
		
		if ($this->NY == $this->N){
			$this->DEV = (float) (($this->SUMX2 * $this->N)- ($this->SUMX * $this->SUMX));
			$this->m = (float)(($this->SUMXY * $this->N)- ($this->SUMX * $this->SUMY))/$this->DEV;
			$this->b = (float)(($this->SUMX2 * $this->SUMY)- ($this->SUMXY * $this->SUMX))/$this->DEV;
			$this->r = (float) ($this->SUMXY -($this->N * $this->AVGX * $this->AVGY))/(($this->N-1)*$this->STDVX * $this->STDVY);
			$this->t = (float) $this->r * sqrt(($this->N -2)/(1- pow($this->r,2)));
		}
	/* make the STATS array */
		$this->STATS =	array (
									N=>$this->N,
									NY=>$this->NY,
									MINX=>$this->MINX,
									MAXX=>$this->MAXX,
									SUMX=>$this->SUMX,
									MEDX=>$this->MEDX,
									AVGX=>$this->AVGX,
									STDVX=>$this->STDVX,
									MINY=>$this->MINY,
									MAXY=>$this->MAXY,
									SUMY=>$this->SUMY,
									MEDY=>$this->MEDY,
									AVGY=>$this->AVGY,
									STDVY=>$this->STDVY,
									m=>$this->m,
									b=>$this->b,
									r=>$this->r,
									t=>$this->t
									
								);

	}
	
	
	/* sets the Title */
	function setTitle($title) {
		$this->TITLE=$title;
	}

	/* send back STATS array */
	function getStats() {
		return $this->STATS;
	}
	
	
	/* simple printStats */
	function printStats() {
		$s = "Statistics for : ".$this->TITLE."\n";
		$s .= "Number of data in X axis: $this->N \n";
		$s .= sprintf("Min = %-2.4f\tMax = %-2.4f\tAvg = %-2.4f\tMED = %-2.4f\tStDev = %-2.4f \n",$this->MINX,$this->MAXX,$this->AVGX,$this->MEDX, $this->STDVX  );
		$s .= "\nNumber of data in Y axis: $this->NY \n";
		$s .= sprintf("Min = %-2.4f\tMax = %-2.4f\tAvg = %-2.4f\tMED = %-2.4f\tStDev = %-2.4f \n",$this->MINY,$this->MAXY,$this->AVGY,$this->MEDY, $this->STDVY  );
		if($this->NY ==$this->N){	
			$s .= "\nLinear regression line: \n";
			if ($this->b >=0){
				$s .= "Y = $this->m X + $this->b   with correlation (r)= $this->r\n  \"t\"test (df=$this->N-2) = $this->t";
			}else{
				$s .= "Y = $this->m X  $this->b    with correlation (r)= $this->r\n  \"t\"test (df=$this->N-2) = $this->t";
			}
		}
		echo $s;
		
		
	}
	
	
	
}?>