<?php
// If the page receives a POST request, handle the Java compilation and execution
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the posted Java code
    $data = json_decode(file_get_contents('php://input'), true);
    $javaCode = $data['code'];

    // Write the Java code to a file
    $javaFile = 'Main.java';
    file_put_contents($javaFile, $javaCode);

    // Compile the Java file using shell_exec
    $compileCommand = "javac $javaFile 2>&1";
    $compileOutput = shell_exec($compileCommand);

    // Check if there were any compilation errors
    if ($compileOutput) {
        echo json_encode(['output' => "Compilation Error:\n$compileOutput"]);
        exit;
    }

    // Run the Java program
    $runCommand = "java Main 2>&1";
    $runOutput = shell_exec($runCommand);

    // Return the output of the Java program
    echo json_encode(['output' => $runOutput]);

    // Clean up the generated class files
    shell_exec('rm Main.class');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Java Online Compiler</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        h1 {
            text-align: center;
        }
        .container {
            width: 80%;
            margin: 0 auto;
        }
        textarea {
            width: 100%;
            height: 300px;
            font-family: monospace;
            padding: 10px;
            margin-bottom: 20px;
        }
        button {
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
        }
        .output {
            background-color: #f1f1f1;
            padding: 10px;
            min-height: 150px;
            font-family: monospace;
            white-space: pre-wrap;
            border: 1px solid #ccc;
            margin-top: 20px;
        }
    </style>
</head>
<body>

    <div class="container">
        <h1>Java Online Compiler</h1>

        <!-- Java code input area -->
        <label for="javaCode">Enter your Java code:</label>
        <textarea id="javaCode" placeholder="Type your Java code here..."></textarea>

        <!-- Compile and Run button -->
        <button onclick="compileCode()">Compile & Run</button>

        <!-- Output display area -->
        <h3>Output:</h3>
        <div class="output" id="outputArea">Your output will appear here...</div>
    </div>

    <script>
        // Function to send the Java code to the server and fetch the output
        function compileCode() {
            // Get the code from the textarea
            const javaCode = document.getElementById('javaCode').value;
            const outputArea = document.getElementById('outputArea');

            // Check if code is provided
            if (!javaCode.trim()) {
                outputArea.innerText = "Please enter some Java code!";
                return;
            }

            // Send the Java code to the PHP server via fetch API (AJAX)
            fetch('', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ code: javaCode })
            })
            .then(response => response.json())
            .then(data => {
                // Display the output from the server
                outputArea.innerText = data.output;
            })
            .catch(error => {
                console.error('Error:', error);
                outputArea.innerText = "An error occurred while processing your code.";
            });
        }
    </script>

</body>
</html>
