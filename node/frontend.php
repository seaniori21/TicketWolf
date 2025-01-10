<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Plugin Communication</title>
</head>
<body>
    <h1>Run Script from Plugin</h1>
    <button id="runButton">Run Script</button>
    <pre id="output"></pre>

    <script>
        // Button click event
        document.getElementById("runButton").addEventListener("click", () => {
            fetch("http://localhost:3000/run-script", {
                method: "GET",
                headers: {
                    "Content-Type": "application/json"
                }
            })
            .then(response => response.json())
            .then(data => {
                document.getElementById("output").textContent = `Script Output:\n${data.output}`;
            })
            .catch(error => {
                document.getElementById("output").textContent = `Error: ${error.message}`;
            });
        });
    </script>
</body>
</html>
