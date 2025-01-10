const express = require("express");
const { exec } = require("child_process");
const cors = require('cors');
const fs = require("fs");
const app = express();

app.use(cors()); // This will allow all origins

const port = 3000;

// Endpoint to run the script
app.get("/run-script", (req, res) => {
    // Replace this with the path to your batch file or script
    const batchFilePath = '"C:\\Program Files\\TOWWOLF\\script.bat"';

    exec(batchFilePath, (error, stdout, stderr) => {
        if (error) {
            return res.status(500).json({ output: `Error: ${error.message}` });
        }
        if (stderr) {
            return res.status(500).json({ output: `stderr: ${stderr}` });
        }
        const filePath = stdout.trim(); // Get the file path output by the batch file

        // Read the file and send its binary content
        fs.readFile(filePath, (err, data) => {
            if (err) {
                console.error("Error reading scanned file:", err);
                res.status(500).send("Error reading scanned file");
                return;
            }

            // Send the file data as a binary response
            res.setHeader("Content-Type", "application/pdf");
            res.setHeader("Content-Disposition", "inline; filename=scanned_document.pdf");
            res.send(data);

            // Optionally delete the temporary file after sending it
            fs.unlink(filePath, (unlinkErr) => {
                if (unlinkErr) console.error("Error deleting temporary file:", unlinkErr);
            });
        });
    });
});

// Start the server
app.listen(port, () => {
    console.log(`Plugin server listening at http://localhost:${port}`);
});
