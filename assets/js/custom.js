
/*=============================================================
    Authour URI: www.binarytheme.com
    License: Commons Attribution 3.0

    http://creativecommons.org/licenses/by/3.0/

    100% Free To use For Personal And Commercial Use.
    IN EXCHANGE JUST GIVE US CREDITS AND TELL YOUR FRIENDS ABOUT US
   
    ========================================================  */

(function ($) {
    "use strict";
    var mainApp = {
        slide_fun: function () {

            $('#carousel-example').carousel({
                interval:3000 // THIS TIME IS IN MILLI SECONDS
            })

        },
        dataTable_fun: function () {

            $('#dataTables-example').dataTable();

        },
       
        custom_fun:function()
        {
            /*====================================
             WRITE YOUR   SCRIPTS  BELOW
            ======================================*/




        },

    }
   
   
    $(document).ready(function () {
        mainApp.slide_fun();
        mainApp.dataTable_fun();
        mainApp.custom_fun();
    });
}(jQuery));


// JavaScript to display selected file name
document.addEventListener('DOMContentLoaded', function() {
    var fileInput = document.getElementById('image');
    var fileNameDisplay = document.getElementById('file-name-display');
    
    if (fileInput && fileNameDisplay) {
        fileInput.addEventListener('change', function() {
            if (fileInput.files.length > 0) {
                fileNameDisplay.textContent = fileInput.files[0].name;
            } else {
                fileNameDisplay.textContent = 'No file chosen';
            }
        });
    }
});


document.addEventListener("DOMContentLoaded", function() {
    // Add event listener to the file input
    document.getElementById('file').addEventListener('change', function() {
        // Get the selected file
        var fileInput = this;
        var fileNameDisplay = document.getElementById('file-name-display');
        
        // Check if a file is selected
        if (fileInput.files.length > 0) {
            // Display the file name
            fileNameDisplay.textContent = fileInput.files[0].name;
        } else {
            // If no file is selected, display default text
            fileNameDisplay.textContent = 'No file chosen';
        }
    });
});





