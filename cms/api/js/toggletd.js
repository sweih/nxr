		function toggleTD(myid) {
			obj = document.getElementById(myid);
			d = obj.style.display;
			if (d=="none") {
				 d="block";
				
			}
			else if (d=="" || d=="block") d="none";
			obj.style.display =d;
	
		}