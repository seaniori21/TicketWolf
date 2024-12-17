<?php
// Include shared components
include '../includes/header.php';
?>

<div class="main-container">
    <h1>Ticket Request Form</h1>

    <div class="form-container">
        <form action="../functions/ticket_form_submit.php" method="post" enctype="multipart/form-data">
            <div class="form-grid">
                <div class="form-group">
                    <label for="first-name">Vehicle Owner's First Name <span class="required">*</span></label>
                    <input type="text" id="first-name" name="first_name" required>
                </div>

                <div class="form-group">
                    <label for="last-name">Vehicle Owner's Last Name <span class="required">*</span></label>
                    <input type="text" id="last-name" name="last_name" required>
                </div>

                <div class="form-group">
                    <label for="email">Email <span class="required">*</span></label>
                    <input type="email" id="email" name="email" required>
                </div>

                <div class="form-group">
                    <label for="phone">Phone Number <span class="optional">(Optional)</span></label>
                    <input type="tel" id="phone" name="phone">
                </div>

                <div class="form-group">
                    <label for="vin">VIN <span class="required">*</span></label>
                    <input type="text" id="vin" name="vin" required>
                </div>

                <div class="form-group">
                    <label for="drivers-license">Driver's License Number</label>
                    <input type="text" id="drivers-license" name="drivers_license">
                </div>

                <div class="form-group">
                    <label for="license-plate">License Plate Number</label>
                    <input type="text" id="license-plate" name="license_plate">
                </div>

                <div></div>


                <div class="form-group">
                    <label>Are you the vehicle owner? <span class="required">*</span></label>
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

                <div class="form-group">
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
            <!-- END OF FORM GRID -->


            <div class="form-group">
                <div class="form-group-files">
                    <label>Have Insurance? <span class="required">*</span></label>
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
                    <label for="insurance-files">Upload Insurance Document:</label>
                    <input type="file" id="insurance-files" name="insurance_files[]" multiple >
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
                    <label for="title-files">Upload Title Document:</label>
                    <input type="file" id="title-files" name="title_files[]" multiple>
                    <div class="file-list" id="title-file-list"></div>
                </div>
            </div>


            <div class="form-group">
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
                    <label for="license-files">Upload License Document:</label>
                    <input type="file" id="license-files" name="license_files[]" multiple>
                    <div class="file-list" id="license-file-list"></div>
                </div>
            </div>


            <div class="form-group full-width">
                <button type="submit">Submit</button>
            </div>
        </form>
    </div>

</div>

<script>
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
            insuranceFileUploadSection.style.display = 'none';
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
            });

            listItem.appendChild(deleteButton);
            insuranceFileList.appendChild(listItem);
        });
        insuranceFileInput.value ='';
    });

    // Ensure files are included when the form is submitted
    const form = document.querySelector('form');
    form.addEventListener('submit', function(event) {
        if (insuranceFiles.length > 0) {
            const formData = new FormData();

            // Add other form fields to the formData
            const formElements = form.querySelectorAll('input, select, textarea');
            formElements.forEach((input) => {
                if (input.type === 'file') return; // Skip file inputs, we are handling them separately
                formData.append(input.name, input.value);
            });

            // Add insurance files to FormData
            insuranceFiles.forEach(file => {
                formData.append('insurance_files[]', file); // Use 'insurance_files[]' to allow multiple file upload
            });

            // Create an XMLHttpRequest or use fetch to send form data to the server
            fetch(form.action, {
                method: 'POST',
                body: formData,
            })
            .then(response => response.text())
            .then(data => {
                console.log(data);
                // alert('Form submitted successfully!');
            })
            .catch(error => {
                console.error(error);
                // alert('An error occurred.');
            });

            // Prevent default form submission
            event.preventDefault();
        } else {
            alert('Please add at least one insurance file.');
        }
    });





    const titleYes = document.getElementById('title-yes');
    const titleNo = document.getElementById('title-no');
    const titleFileUploadSection = document.getElementById('title-file-upload-section');
    const titleFileInput = document.getElementById('title-files');
    const titleFileList = document.getElementById('title-file-list');
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

        fileArray.forEach(file => {
            
            const listItem = document.createElement('li');
            listItem.textContent = file.name;

            // Create a delete button ('X')
            const deleteButton = document.createElement('span');
            deleteButton.textContent = ' X';
            deleteButton.classList.add('delete-file');
            deleteButton.addEventListener('click', function () {
                listItem.remove(); // Remove the file from the list
            });

            
            listItem.appendChild(deleteButton);
            titleFileList.appendChild(listItem);
        });

    });



    const licenseYes = document.getElementById('license-yes');
    const licenseNo = document.getElementById('license-no');
    const licenseFileUploadSection = document.getElementById('license-file-upload-section');
    const licenseFileInput = document.getElementById('license-files');
    const licenseFileList = document.getElementById('license-file-list');
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

        fileArray.forEach(file => {
            
            const listItem = document.createElement('li');
            listItem.textContent = file.name;

            // Create a delete button ('X')
            const deleteButton = document.createElement('span');
            deleteButton.textContent = ' X';
            deleteButton.classList.add('delete-file');
            deleteButton.addEventListener('click', function () {
                listItem.remove(); // Remove the file from the list
            });

            
            listItem.appendChild(deleteButton);
            licenseFileList.appendChild(listItem);
        });

    });

</script>


