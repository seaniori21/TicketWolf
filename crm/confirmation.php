<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Run NAPS2 Scan</title>
    <style>
        #preview {
            margin-top: 20px;
        }
        #loading {
            display: none;
        }
    </style>
</head>
<body>
    <h1>Run NAPS2 Scan</h1>

    <!-- Button to start scanning -->
    <button id="scanButton">Start Scan</button>

    <!-- Loading message -->
    <p id="loading">Scanning in progress...</p>

    <!-- Image preview after scan -->
    <div id="preview"></div>

    <script>
        // Handle button click
        document.getElementById('scanButton').addEventListener('click', function () {
            // Show loading message
            document.getElementById('loading').style.display = 'block';
            document.getElementById('preview').innerHTML = '';

            // Send AJAX request to start scanning
            var xhr = new XMLHttpRequest();
            xhr.open('GET', '../functions/scan_file.php', true); // The PHP file that runs the NAPS2 scan

            // On success, handle the response
            xhr.onload = function () {
                if (xhr.status === 200) {
                    // Hide loading message
                    document.getElementById('loading').style.display = 'none';
                    
                    // Display the scan result (image preview)
                    document.getElementById('preview').innerHTML = '<object data="' + xhr.responseText + '" type="application/pdf" width="600" height="400"></object>';
                    console.log("should we worikng");
                }
            };

            // Send the request
            xhr.send();
        });
    </script>
</body>
</html>
