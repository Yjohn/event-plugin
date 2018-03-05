/*
* Functionality for the Events shortcode view
* This shouldn't run until the page content has been built
*/
(function() {
	
	document.addEventListener("DOMContentLoaded", docReady); 
	
	function docReady(event){ 
		event.preventDefault();
		/*
		* Add event listener to each Event header in the page
		*/
		var events = document.querySelectorAll('#accordion article');
	
		for(let event of events){ 
			//adds event listener to header
			event.querySelector('header').addEventListener('click', toggleEvent);
		}

		/*
		* Shows/Hides the Event answer when the Event header is clicked.
		*/
		function toggleEvent(event){
			event.preventDefault();
		
			//event header background turns grey when not collapsed
			this.classList.toggle('grey-background');
			this.parentElement.classList.toggle('has-shadow');
	
			//event content shows when not collapsed
			let eventContent = this.nextElementSibling;
			eventContent.classList.toggle('in');
	
			//a tag reflects changes too
			let aTag = this.querySelector('a');
			aTag.classList.toggle('collapsed');
	
			if(aTag.className.includes('collapsed')){
				aTag.setAttribute('aria-expanded','false');
			} else {
				aTag.setAttribute('aria-expanded', 'true');
			}
	
			//svg icon changes depending on whether event is collapsed or not
			let dashIcon = this.querySelector('.dashicons');
			dashIcon.classList.toggle('dashicons-arrow-down-alt2');
			dashIcon.classList.toggle('dashicons-arrow-up-alt2');
		}
	}//end docReady

})();