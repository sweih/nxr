<?php
	// Author: Sven Weih 2002

	/**
	 * Starts the Benchmark for measuring the script-execuction time.
	 */
	function startBenchmark() {
		global $benchmarkStartTime;

		$microtime = explode(" ", microtime());
		$benchmarkStartTime = $microtime[1] + $microtime[0];
	}

	/**
	 * Stops the benchmark and returns the seconds the script executed.
	 * @param integer Precission of measurement
	 * @returns number time elapsed for execution
	 */
	function stopBenchmark($precission = 5) {
		global $benchmarkStartTime;

		$microtime = explode(" ", microtime());
		$stopTime = $microtime[1] + $microtime[0];
		return number_format(($stopTime - $benchmarkStartTime), $precission);
	}
?>