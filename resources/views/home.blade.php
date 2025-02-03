<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laravel API Status</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/axios/1.6.2/axios.min.js"></script>
    <style>
        /* Previous styles remain the same */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        }

        .container {
            max-width: 800px;
            padding: 2rem;
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .status {
            margin: 2rem 0;
            padding: 1rem;
            border-radius: 5px;
            background: #f8f9fa;
            transition: all 0.3s ease;
        }

        .success {
            color: #155724;
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
        }

        .error {
            color: #721c24;
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
        }

        .loading {
            color: #383d41;
            background-color: #e9ecef;
            border: 1px solid #dee2e6;
        }

        .logo {
            width: 100px;
            margin-bottom: 1rem;
        }

        button {
            background: #4CAF50;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        button:disabled {
            background: #9e9e9e;
            cursor: not-allowed;
        }

        .spinner {
            width: 20px;
            height: 20px;
            border: 3px solid #ffffff;
            border-top: 3px solid transparent;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .loading-dots:after {
            content: '.';
            animation: dots 1.5s steps(5, end) infinite;
        }

        @keyframes dots {
            0%, 20% { content: '.'; }
            40% { content: '..'; }
            60% { content: '...'; }
            80%, 100% { content: ''; }
        }

        /* New styles for description and GitHub link */
        .description {
            max-width: 600px;
            margin: 1rem auto 2rem;
            color: #666;
            font-size: 1.1rem;
        }

        .github-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: #24292e;
            text-decoration: none;
            padding: 8px 16px;
            border-radius: 6px;
            background: #f6f8fa;
            border: 1px solid #e1e4e8;
            margin: 1rem 0;
            transition: all 0.2s ease;
        }

        .github-link:hover {
            background: #f3f4f6;
            border-color: #bbc0c4;
        }

        .github-logo {
            width: 24px;
            height: 24px;
        }
    </style>
</head>
<body>
    <div class="container">
        <img src="{{ asset('images/laravel.svg') }}" alt="Laravel Logo" class="logo">
        <h1>Welcome to Laravel API</h1>

        <p class="description">
            This API serves as the backend infrastructure for the CCUN(Cambodia Cyber Unniversity Network) information system, providing secure and efficient data management for student to explore our University Partners, major, course information, event information, training information, and workshop information. Use the button below to verify the API's operational status.
        </p>

        <a href="https://github.com/selytheng/ccunview" target="_blank" class="github-link">
            <svg class="github-logo" viewBox="0 0 16 16" fill="currentColor">
                <path fill-rule="evenodd" d="M8 0C3.58 0 0 3.58 0 8c0 3.54 2.29 6.53 5.47 7.59.4.07.55-.17.55-.38 0-.19-.01-.82-.01-1.49-2.01.37-2.53-.49-2.69-.94-.09-.23-.48-.94-.82-1.13-.28-.15-.68-.52-.01-.53.63-.01 1.08.58 1.23.82.72 1.21 1.87.87 2.33.66.07-.52.28-.87.51-1.07-1.78-.2-3.64-.89-3.64-3.95 0-.87.31-1.59.82-2.15-.08-.2-.36-1.02.08-2.12 0 0 .67-.21 2.2.82.64-.18 1.32-.27 2-.27.68 0 1.36.09 2 .27 1.53-1.04 2.2-.82 2.2-.82.44 1.1.16 1.92.08 2.12.51.56.82 1.27.82 2.15 0 3.07-1.87 3.75-3.65 3.95.29.25.54.73.54 1.48 0 1.07-.01 1.93-.01 2.2 0 .21.15.46.55.38A8.013 8.013 0 0016 8c0-4.42-3.58-8-8-8z"></path>
            </svg>
            Frontend Integration Repository
        </a>

        <div class="status" id="apiStatus">
            Click the button below to check API status
        </div>

        <button onclick="checkApiStatus()" id="checkButton">
            <span>Check API Status</span>
            <div class="spinner" style="display: none;" id="buttonSpinner"></div>
        </button>
    </div>

    <script>
        async function checkApiStatus() {
            const statusDiv = document.getElementById('apiStatus');
            const button = document.getElementById('checkButton');
            const buttonText = button.querySelector('span');
            const spinner = document.getElementById('buttonSpinner');

            // Disable button and show loading state
            button.disabled = true;
            spinner.style.display = 'block';
            buttonText.textContent = 'Checking';
            statusDiv.textContent = 'Checking API status';
            statusDiv.className = 'status loading';

            try {
                // Add artificial delay of 2 seconds
                await new Promise(resolve => setTimeout(resolve, 2000));

                const response = await axios.get('/api/health');
                statusDiv.textContent = 'API is running successfully!';
                statusDiv.className = 'status success';
            } catch (error) {
                statusDiv.textContent = 'Error connecting to API: ' + error.message;
                statusDiv.className = 'status error';
            } finally {
                // Reset button state
                button.disabled = false;
                spinner.style.display = 'none';
                buttonText.textContent = 'Check API Status';
            }
        }
    </script>
</body>
</html>
