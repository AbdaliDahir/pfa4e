/*
 * Welcome to your sweet alert main JavaScript file!
*/

import Swal from 'sweetalert2/dist/sweetalert2.js'

import 'sweetalert2/src/sweetalert2.scss'

// In your Javascript (external .js resource or <script> tag)
$(document).ready(function() {
	"use-strict";
    
    $body = $('body');
    
    if($body.find('.alertConfirm')) {
			$('.alertConfirm').on('click', (e) =>{
					e.preventDefault();
					Swal.fire({
					title: "Are you sure?",
					text: "Are you sure you want to delete this item?",
					icon: "warning"
				}).then(willDelete => {
					if (willDelete) {
						const $sender = $(this).parent('.alertForm').submit();
						console.log($sender);
						if($sender) {
							Swal.fire("Deleted!", "Your imaginary file has been deleted!", "success");
						}
					}
				});
			});
    }

});

