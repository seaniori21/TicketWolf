<?php
    try {
        
        // Define the output file path (use absolute path for desired location)
        $outputFile = "../crm/scannedfiles/scan_" . time() . ".pdf";  // You can customize this if you want dynamic naming like 'scan_' + time()

        // Define the command to run NAPS2 console scanner
        $command = "\"..\NAPS2\NAPS2.Console.exe\" --profile \"Scan\" -o \"$outputFile\" -f";


        // Execute the command
        exec($command, $output, $return_var);

        // Check if the command was successful
        if ($return_var !== 0) {
            throw new Exception("Error occurred while scanning. Return Code: $return_var\n" . implode("\n", $output));
        }

        // If successful, display the file name
        echo json_encode(["filename" => $outputFile, "command" => $command]);

    } catch (Exception $e) {
        // Catch and display the error message
        echo "An error occurred: " . $e->getMessage();
    }
?>