<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <link rel="stylesheet" href="assets/css/styles.css">
    <title>TowWolf - CounterWolf</title>
    <link rel="icon" href="assets/img/favicon.png" type="logo-img">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>


<div class="main-container">
    <div class='form-header'>
        <div class='header-container'>
            <div class='ben-nino'>
                    <span style="color:#4169e1">Ben</span><span style="color:#ff6347">&</span><span style="color:#4169e1">Nino</span>
            </div>
            <div class="top-right-image">
                <img src="assets/img/banner_tw.jpg" alt="Top Right Image" class='img' >
            </div>
            
        </div>
    </div>
    <h1>Line Placement</h1>

    <div class="form-container">
        <form action="functions/counter_form_submit.php" method="post" enctype="multipart/form-data">
            <div class="form-grid">
                <div class="form-group">
                    <label for="first-name">Vehicle Owner's First Name <span class="required">*</span></label>
                    <input type="text" id="first-name" name="first_name" required>
                </div>

                <div class="form-group">
                    <label for="last-name">Vehicle Owner's Last Name <span class="required">*</span></label>
                    <input type="text" id="last-name" name="last_name" required>
                </div>

                <div class="form-group" id="vin-group">
                    <label for="vin">VIN</label>
                    <input type="text" id="vin" name="vin"
                    
                    >
                </div>

                <div class="form-group">
                    <label for="drivers-license">Driver's License Number</label>
                    <input type="text" id="drivers-license" name="drivers_license">
                </div>

                <div class="form-group">
                    <label for="license-plate">License Plate Number</label>
                    <input type="text" id="license-plate" name="license_plate"

                    >
                </div>

                <div class="form-group">
                    <label for="phone">Phone Number</label>
                    <input type="tel" id="phone" name="phone" placeholder="(___) -___-____"
                        pattern="^\(\d{3}\)\s?-\s?\d{3}\s?-\s?\d{4}$"
                        oninput="formatPhone(this)" maxlength="18"
                        title="Please enter a 10 digit phone number">
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email">
                </div>


            </div>
            <!-- END OF FORM GRID -->





            <div class='form-file-section'>


                <div class="form-group">
                    <div class="form-group-files">
                        <label>Are you the vehicle owner?<span class="required">*</span></label>
                        <div class="row-flex">
                            <div class="">
                                <input type="radio" id="owner-yes" name="is_owner" value="yes" required>
                                <label for="owner-yes" class="checkbox-label">Yes</label>
                            </div>
                            <div class="">
                                <input type="radio" id="owner-no" name="is_owner" value="no" required>
                                <label for="owner-no" class="checkbox-label">No</label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group grey-bg">
                    <div class="form-group-files">
                        <label>Is the vehicle registered in New York?<span class="required">*</span></label>
                        <div class="row-flex">
                            <div class="">
                                <input type="radio" id="ny-yes" name="registered_in_ny" value="yes" required>
                                <label for="ny-yes" class="checkbox-label">Yes</label>
                            </div>
                            <div class="">
                                <input type="radio" id="ny-no" name="registered_in_ny" value="no" required>
                                <label for="ny-no" class="checkbox-label">No</label>
                            </div>
                        </div>
                    </div>
                </div>

<?php 
    define("FILECOMMENT", "*For expedited service please submit the document if you have it currently on you. If it is in your vehicle a representative will help you after submission");
?>

                <div class="form-group">
                    <div class="form-group-files">
                        <label>Have Registration?<span class="required"></span></label>
                        <div class="row-flex">
                            <div class="">
                                <input type="radio" id="registration-yes" name="have_registration" value="yes" required>
                                <label for="registration-yes" class="checkbox-label">Yes</label>
                            </div>
                            <div class="">
                                <input type="radio" id="registration-no" name="have_registration" value="no" required>
                                <label for="registration-no" class="checkbox-label">No</label>
                            </div>
                        </div>
                    </div>
                    <!-- Hidden file upload section -->
                    <div class="file-upload-section" id="registration-file-upload-section" style="display: none;">
                        <!-- <label for="registration-files">Take a Photo or Upload Registration Document:</label> -->
                        <label>
                            <input type="file" id="registration-files" name="registration_files[]" multiple style='display: none;'>
                            <span style='display: inline-block; margin: 1em;
                                padding: 0.5em 1em; 
                                background-color: #007BFF; color: white; border: none; border-radius: 5px; cursor: pointer;
                            '>Take Photo or Upload Document</span>     
                        </label>
                        <p style="font-size:10px; margin:0px;"> <?php echo FILECOMMENT ?> </p>
                        <div class="file-list" id="registration-file-list"></div>
                    </div>
                </div>



                <div class="form-group grey-bg">
                    <div class="form-group-files">
                        <label>Have Insurance?<span class="required">*</span></label>
                        <div class="row-flex">
                            <div class="">
                                <input type="radio" id="insurance-yes" name="have_insurance" value="yes" required>
                                <label for="insurance-yes" class="checkbox-label">Yes</label>
                            </div>
                            <div class="">
                                <input type="radio" id="insurance-no" name="have_insurance" value="no" required>
                                <label for="insurance-no" class="checkbox-label">No</label>
                            </div>
                        </div>
                    </div>
                    <!-- Hidden file upload section -->
                    <div class="file-upload-section" id="insurance-file-upload-section" style="display: none;">
                        <!-- <label for="insurance-files">Take a Photo or Upload Insurance Document:</label> -->
                        <label>
                            <input type="file" id="insurance-files" name="insurance_files[]" multiple style='display: none;'>
                            <span style='display: inline-block; margin: 1em;
                                padding: 0.5em 1em; 
                                background-color: #007BFF; color: white; border: none; border-radius: 5px; cursor: pointer;
                            '>Take Photo or Upload Document</span>    
                        </label>
                        <p style="font-size:10px; margin:0px;"> <?php echo FILECOMMENT ?> </p>
                        <div class="file-list" id="insurance-file-list"></div>
                    </div>
                </div>


                <div class="form-group">
                    <div class="form-group-files">
                        <label>Have Title?<span class="required">*</span></label>
                        <div class="row-flex">
                            <div class="">
                                <input type="radio" id="title-yes" name="have_title" value="yes" required>
                                <label for="title-yes" class="checkbox-label">Yes</label>
                            </div>
                            <div class="">
                                <input type="radio" id="title-no" name="have_title" value="no" required>
                                <label for="title-no" class="checkbox-label">No</label>
                            </div>
                        </div>
                    </div>

                    <!-- Hidden file upload section -->
                    <div class="file-upload-section" id="title-file-upload-section" style="display: none;">
                        <!-- <label for="title-files">Take a Photo or Upload Title Document:</label> -->
                        <label>
                            <input type="file" id="title-files" name="title_files[]" multiple style='display: none;'>
                            <span style='display: inline-block; margin: 1em;
                                padding: 0.5em 1em; 
                                background-color: #007BFF; color: white; border: none; border-radius: 5px; cursor: pointer;
                            '>Take Photo or Upload Document</span>   
                        </label>
                        <p style="font-size:10px; margin:0px;"> <?php echo FILECOMMENT ?> </p>
                        <div class="file-list" id="title-file-list"></div>
                    </div>
                </div>


                <div class="form-group grey-bg">
                    <div class="form-group-files">
                        <label>Have Vehicle Owner's Driver's License?<span class="required">*</span></label>
                        <div class="row-flex">
                            <div class="">
                                <input type="radio" id="license-yes" name="have_owner_license" value="yes" required>
                                <label for="license-yes" class="checkbox-label">Yes</label>
                            </div>
                            <div class="">
                                <input type="radio" id="license-no" name="have_owner_license" value="no" required>
                                <label for="license-no" class="checkbox-label">No</label>
                            </div>
                        </div>
                    </div>

                    <!-- Hidden file upload section -->
                    <div class="file-upload-section" id="license-file-upload-section" style="display: none;">
                        <!-- <label for="license-files">Take a Photo or Upload License Document:</label> -->

                        <label>
                            <input type="file" id="license-files" name="license_files[]" multiple style='display: none;'>
                            <span style='display: inline-block; margin: 1em;
                                padding: 0.5em 1em; 
                                background-color: #007BFF; color: white; border: none; border-radius: 5px; cursor: pointer;
                            '>Take Photo or Upload Document</span>  
                        </label>
                        <p style="font-size:10px; margin:0px;"> <?php echo FILECOMMENT ?> </p>
                        <div class="file-list" id="license-file-list"></div>
                    </div>
                </div>
                
                <div class="form-group">
                    <div class="form-group-files">
                        <label>Have Additional Files?<span class="required">*</span></label>
                        <div class="row-flex">
                            <div class="">
                                <input type="radio" id="additional-yes" name="have_additional_files" value="yes" required>
                                <label for="additional-yes" class="checkbox-label">Yes</label>
                            </div>
                            <div class="">
                                <input type="radio" id="additional-no" name="have_additional_files" value="no" required>
                                <label for="additional-no" class="checkbox-label">No</label>
                            </div>
                        </div>
                    </div>

                    <!-- Hidden file upload section -->
                    <div class="file-upload-section" id="additional-file-upload-section" style="display: none;">
                        <label>
                            <input type="file" id="additional-files" name="additional_files[]" multiple style='display: none;'>
                            <span style='display: inline-block; margin: 1em;
                                padding: 0.5em 1em; 
                                background-color: #007BFF; color: white; border: none; border-radius: 5px; cursor: pointer;
                            '>Take Photo or Upload Document</span>   
                        </label>
                        <p style="font-size:10px; margin:0px;"> <?php echo FILECOMMENT ?> </p>
                        <div class="file-list" id="additional-file-list"></div>
                    </div>
                </div>




            </div><!-- End of section -->





            <div id="loading-indicator" style="display: none;">
                <p>Submitting your form, please wait...</p>
            </div>
            <div id="vin-error" class="error" style="display: none;">
                The VIN entered is not a valid VIN Number.<br>
                Please Correct
            </div>
            <div class="form-group full-width">
                <button type="submit" id="submit-btn">Submit</button>
            </div>
        </form>
    </div>

</div>

<?php
include('includes/footer.php');
?>

<script>
    function formatPhone(input) {
        let value = input.value.replace(/\D/g, '');  // Remove non-numeric characters
        if (value.length <= 1) {
            input.value = `${value}`;
        }else if (value.length <= 3) {
            input.value = `(${value}`;
        } else if (value.length <= 6) {
            input.value = `(${value.slice(0, 3)}) - ${value.slice(3)}`;
        } else {
            input.value = `(${value.slice(0, 3)}) - ${value.slice(3, 6)} - ${value.slice(6, 10)}`;
        }
    }

    // Function to clean up the phone number on form submission
    function cleanPhoneNumber() {
        let phoneInput = document.getElementById('phone');
        let cleanValue = phoneInput.value.replace(/\D/g, '');  // Remove non-numeric characters
        phoneInput.value = cleanValue;  // Set the cleaned value (digits only)
    }

    // Registration File Upload Section
    const registrationYes = document.getElementById('registration-yes');
    const registrationNo = document.getElementById('registration-no');
    const registrationFileUploadSection = document.getElementById('registration-file-upload-section');
    const registrationFileInput = document.getElementById('registration-files');
    const registrationFileList = document.getElementById('registration-file-list');
    let registrationFiles = [];

    // Add event listeners to radio buttons
    registrationYes.addEventListener('change', function () {
        if (registrationYes.checked) {
            registrationFileUploadSection.style.display = 'flex'; 
        }
    });
    registrationNo.addEventListener('change', function () {
        if (registrationNo.checked) {
            registrationFileUploadSection.style.display = 'none'; 
        }
    });

    // Handle adding files to the list
    registrationFileInput.addEventListener('change', function () {
        const files = registrationFileInput.files;
        const fileArray = Array.from(files); 

        registrationFiles.push(files[0]);

        fileArray.forEach(file => {
            
            const listItem = document.createElement('li');
            listItem.textContent = file.name;

            // Create a delete button ('X')
            const deleteButton = document.createElement('span');
            deleteButton.textContent = ' X';
            deleteButton.classList.add('delete-file');
            deleteButton.addEventListener('click', function () {
                listItem.remove(); // Remove the file from the list

                const index = registrationFiles.indexOf(file);
                if (index > -1) {
                    registrationFiles.splice(index, 1); // Remove the file from the array
                }
            });

            
            listItem.appendChild(deleteButton);
            registrationFileList.appendChild(listItem);
        });
        registrationFileInput.nextElementSibling.textContent = 'Add Another Photo or Upload Document';

    });




    // Insurance File Upload Section
    const insuranceYes = document.getElementById('insurance-yes');
    const insuranceNo = document.getElementById('insurance-no');
    const insuranceFileUploadSection = document.getElementById('insurance-file-upload-section');
    const insuranceFileInput = document.getElementById('insurance-files');
    const insuranceFileList = document.getElementById('insurance-file-list');

    let insuranceFiles = [];
    insuranceYes.addEventListener('change', function () {
        if (insuranceYes.checked) {
            insuranceFileUploadSection.style.display = 'flex';
        }
    });

    insuranceNo.addEventListener('change', function () {
        if (insuranceNo.checked) {
            insuranceFileUploadSection.style.display = 'none'; ;
        }
    });

    // Handle file uploads (for insurance files)
    insuranceFileInput.addEventListener('change', function () {
        const files = insuranceFileInput.files;
        const fileArray = Array.from(files);

        insuranceFiles.push(files[0]);

        fileArray.forEach(file => {
            const listItem = document.createElement('li');
            listItem.textContent = file.name;

            // Create a delete button ('X')
            const deleteButton = document.createElement('span');
            deleteButton.textContent = ' X';
            deleteButton.classList.add('delete-file');
            deleteButton.addEventListener('click', function () {
                listItem.remove();
                
                const index = insuranceFiles.indexOf(file);
                if (index > -1) {
                    insuranceFiles.splice(index, 1); // Remove the file from the array
                }

            });

            listItem.appendChild(deleteButton);
            insuranceFileList.appendChild(listItem);
        });
        //insuranceFileInput.value = '';
        insuranceFileInput.nextElementSibling.textContent = 'Add Another Photo or Upload Document';
    });


    const titleYes = document.getElementById('title-yes');
    const titleNo = document.getElementById('title-no');
    const titleFileUploadSection = document.getElementById('title-file-upload-section');
    const titleFileInput = document.getElementById('title-files');
    const titleFileList = document.getElementById('title-file-list');
    let titleFiles = [];

    // Add event listeners to radio buttons
    titleYes.addEventListener('change', function () {
        if (titleYes.checked) {
            titleFileUploadSection.style.display = 'flex'; 
        }
    });
    titleNo.addEventListener('change', function () {
        if (titleNo.checked) {
            titleFileUploadSection.style.display = 'none'; 
        }
    });

    // Handle adding files to the list
    titleFileInput.addEventListener('change', function () {
        const files = titleFileInput.files;
        const fileArray = Array.from(files); 

        titleFiles.push(files[0]);

        fileArray.forEach(file => {
            
            const listItem = document.createElement('li');
            listItem.textContent = file.name;

            // Create a delete button ('X')
            const deleteButton = document.createElement('span');
            deleteButton.textContent = ' X';
            deleteButton.classList.add('delete-file');
            deleteButton.addEventListener('click', function () {
                listItem.remove(); // Remove the file from the list

                const index = titleFiles.indexOf(file);
                if (index > -1) {
                    titleFiles.splice(index, 1); // Remove the file from the array
                }
            });

            
            listItem.appendChild(deleteButton);
            titleFileList.appendChild(listItem);
        });
        titleFileInput.nextElementSibling.textContent = 'Add Another Photo or Upload Document';
    });



    const licenseYes = document.getElementById('license-yes');
    const licenseNo = document.getElementById('license-no');
    const licenseFileUploadSection = document.getElementById('license-file-upload-section');
    const licenseFileInput = document.getElementById('license-files');
    const licenseFileList = document.getElementById('license-file-list');
    let licenseFiles = [];

    // Add event listeners to radio buttons
    licenseYes.addEventListener('change', function () {
        if (licenseYes.checked) {
            licenseFileUploadSection.style.display = 'flex'; 
        }
    });
    licenseNo.addEventListener('change', function () {
        if (licenseNo.checked) {
            licenseFileUploadSection.style.display = 'none'; 
        }
    });

    // Handle adding files to the list
    licenseFileInput.addEventListener('change', function () {
        const files = licenseFileInput.files;
        const fileArray = Array.from(files); 

        licenseFiles.push(files[0]);

        fileArray.forEach(file => {
            
            const listItem = document.createElement('li');
            listItem.textContent = file.name;

            // Create a delete button ('X')
            const deleteButton = document.createElement('span');
            deleteButton.textContent = ' X';
            deleteButton.classList.add('delete-file');
            deleteButton.addEventListener('click', function () {
                listItem.remove(); // Remove the file from the list
                // Find the index of the file in the licenseFiles array and remove it
                const index = licenseFiles.indexOf(file);
                if (index > -1) {
                    licenseFiles.splice(index, 1); // Remove the file from the array
                }
            });

            
            listItem.appendChild(deleteButton);
            licenseFileList.appendChild(listItem);
        });
        licenseFileInput.nextElementSibling.textContent = 'Add Another Photo or Upload Document';
    });

    const additionalYes = document.getElementById('additional-yes');
    const additionalNo = document.getElementById('additional-no');
    const additionalFileUploadSection = document.getElementById('additional-file-upload-section');
    const additionalFileInput = document.getElementById('additional-files');
    const additionalFileList = document.getElementById('additional-file-list');
    let additionalFiles = [];

    // Add event listeners to radio buttons
    additionalYes.addEventListener('change', function () {
        if (additionalYes.checked) {
            additionalFileUploadSection.style.display = 'flex';
        }
    });
    additionalNo.addEventListener('change', function () {
        if (additionalNo.checked) {
            additionalFileUploadSection.style.display = 'none';
        }
    });

    // Handle adding files to the list
    additionalFileInput.addEventListener('change', function () {
        const files = additionalFileInput.files;
        const fileArray = Array.from(files);

        additionalFiles.push(files[0]);

        fileArray.forEach(file => {

            const listItem = document.createElement('li');
            listItem.textContent = file.name;

            // Create a delete button ('X')
            const deleteButton = document.createElement('span');
            deleteButton.textContent = ' X';
            deleteButton.classList.add('delete-file');
            deleteButton.addEventListener('click', function () {
                listItem.remove(); // Remove the file from the list
                // Find the index of the file in the additionalFiles array and remove it
                const index = additionalFiles.indexOf(file);
                if (index > -1) {
                    additionalFiles.splice(index, 1); // Remove the file from the array
                }
            });

            listItem.appendChild(deleteButton);
            additionalFileList.appendChild(listItem);
        });
        additionalFileInput.nextElementSibling.textContent = 'Add Another Photo or Upload Document';
    });


    
    // let vinData;
    async function VIN_Decoder() {
        let vin = document.getElementById('vin').value.toUpperCase();

        if (!vin) {
            vin = "blasjsn12321342121"
        }
        const result = await new Promise((resolve, reject) => {
            $.ajax({
                url: `https://vpic.nhtsa.dot.gov/api/vehicles/DecodeVINValues/${vin}?format=json`,
                type: "GET",
                dataType: "json",
                success: function(result) {
                    resolve(result);  // Resolve the promise with the result
                },
                error: function(xhr, ajaxOptions, thrownError) {
                    resolve("error");
                }
            });
        });
        // console.log(vin);
        if(result.Results[0]["ErrorCode"] != "0" ){
            return "error"
        }else{
            return result.Results[0];
        }
        
    }
    // We are creating our own type of submission
    const form = document.querySelector('form');
    const submitButton = document.getElementById('submit-btn');  // Get the button using its ID
    const loadingIndicator = document.getElementById('loading-indicator');  // Get the button using its ID
    const vinErrorWarning = document.getElementById('vin-error');
    const vinGroup = document.getElementById('vin');

    form.addEventListener('submit', async function(event) {
        event.preventDefault(); // TODO

        const formData = new FormData();//Has all form data

        const vinData = await VIN_Decoder();
        vinErrorWarning.style.display='none';
    
        // alert('In progress');
        submitButton.disabled = true;
        submitButton.style.display = 'none';
        loadingIndicator.style.display = 'block';
        cleanPhoneNumber();


        if (vinData !== "error") {
           
            const vinFields = ["Manufacturer", "VehicleType", "ModelYear", "Make", "Model", "BodyClass"];
            const sqlFields = ["manufacturer", "vehicle_type", "model_year", "make", "model", "body_class"];


            for(let i=0; i<vinFields.length; i++){
                formData.append(sqlFields[i], vinData[vinFields[i]]);
            }

            //alert("Wrong VIN Number.\nThe VIN entered is not a valid VIN Number.\nPlease Correct");
            // vinErrorWarning.style.display='block';
            // vinGroup.style.border = "3px solid red";
            // return; 
        }


        // Add other form fields to the formData
        const formElements = form.querySelectorAll('input, select, textarea');
        formElements.forEach((input) => {
            if (input.type === 'file') return; // Skip file inputs, we are handling them separately
            if (input.type === 'radio' && input.checked){
                formData.append(input.name, input.value);
            }
            if (input.type != 'radio'){
                formData.append(input.name, input.value);
            }
            
        });

        if (insuranceFiles.length > 0) {
            // Add insurance files to FormData
            insuranceFiles.forEach(file => {
                formData.append('insurance_files[]', file);
            });
        } 

        if (titleFiles.length > 0) {
            // Add title files to FormData
            titleFiles.forEach(file => {
                formData.append('title_files[]', file); 
            });
        } 

        if (licenseFiles.length > 0) {
            // Add license files to FormData
            licenseFiles.forEach(file => {
                formData.append('license_files[]', file); 
            });
        } 

        if (registrationFiles.length > 0) {
            // Add registration files to FormData
            registrationFiles.forEach(file => {
                formData.append('registration_files[]', file); 
            });
        } 

        if (additionalFiles.length > 0) {
            // Add additional files to FormData
            additionalFiles.forEach(file => {
                formData.append('additional_files[]', file);
            });
        }




        //Create an XMLHttpRequest or use fetch to send form data to the server
        fetch(form.action, {
                method: 'POST',
                body: formData,
        })
        .then(response => response.json())
        .then(data => {
            /*
            data: {
                "status": "success",
                "counter": 12345
            } 
            */
            if (data.status === "success") {
                window.location.href = "confirmation.php?counter=" + data.counter; // TODO
            }else {
                throw new Error('Form LOL failed');
            }
        })
        .catch(error => {
            console.error(error);
            alert('An error occurred.');
        })
        .finally(() =>  {
            submitButton.disabled = false;
            loadingIndicator.style.display = 'none';
            submitButton.style.display = 'flex';
        });


    });

</script>


