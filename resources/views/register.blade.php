<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #96e6a1, #d4fc79);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            color: #333;
        }

        .container {
            text-align: center;
            width: 100%;
            max-width: 400px;
        }

        h1 {
            font-size: 28px;
            color: #28a745;
            margin-bottom: 20px;
        }

        form {
            background-color: #fff;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        input[type="text"], input[type="email"], input[type="password"] {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border: 1px solid #ced4da;
            border-radius: 5px;
            font-size: 16px;
            box-sizing: border-box;
            transition: border-color 0.3s ease;
        }

        input[type="text"]:focus, input[type="email"]:focus, input[type="password"]:focus {
            outline: none;
            border-color: #28a745;
        }

        input.error {
            border-color: #dc3545;
        }

        button {
            background-color: #28a745;
            color: #fff;
            border: none;
            padding: 12px;
            font-size: 16px;
            cursor: pointer;
            border-radius: 30px;
            width: 100%;
            margin-top: 20px;
            transition: all 0.3s ease;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        button:hover {
            background-color: #218838;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }

        #responseMessage {
            color: #dc3545;
            margin-top: 15px;
            font-size: 14px;
        }

        @media (max-width: 480px) {
            .container {
                max-width: 320px;
            }

            form {
                padding: 20px;
            }

            input[type="text"], input[type="email"], input[type="password"] {
                font-size: 14px;
            }

            button {
                font-size: 14px;
                padding: 10px;
            }
        }

        /* Popup Styles */
        .modal {
            display: none; 
            position: fixed;
            z-index: 1; 
            left: 0;
            top: 0;
            width: 100%; 
            height: 200%; 
            overflow: auto; 
            background-color: rgb(0,0,0); 
            background-color: rgba(0,0,0,0.4); 
            padding-top: 60px;
            border-radius: 30px;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .modal-content {
            background-color: #fefefe;
            margin: 5% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 300px; 
            text-align: center;
            border-radius: 10px;
            animation: slide-down 0.5s ease;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }

        .modal-button {
            background-color: #28a745;
            color: #fff;
            border: none;
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
            border-radius: 30px;
            margin-top: 10px;
        }

        .modal-button:hover {
            background-color: #218838;
        }
        
        .back-link {
            margin-top: 15px;
            display: block;
            color: #007bff;
            text-decoration: none;
            font-size: 14px;
        }

        .back-link:hover {
            text-decoration: underline;
        }

        @keyframes slide-down {
            from {
                transform: translateY(-20%);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .modal.show {
            display: block;
            opacity: 1;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Register</h1>

        <form id="registerForm">
            @csrf
            <input type="text" id="name" name="name" placeholder="Name" required>
            <input type="email" id="email" name="email" placeholder="Email" required>
            <input type="password" id="password" name="password" placeholder="Password" required>
            <input type="password" id="c_password" name="c_password" placeholder="Confirm Password" required>
            <button type="submit">Register</button>
            <a href="/signin" class="back-link">Back</a>
        </form>

        <div id="responseMessage"></div>
    </div>

    <!-- The Modal -->
    <div id="successModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <p>Registration successful!</p>
            <button class="modal-button" onclick="redirectToSignIn()">Back to sign-in page</button>
        </div>
    </div>

    <script>
        document.getElementById('registerForm').addEventListener('submit', async function(e) {
            e.preventDefault();

            const name = document.getElementById('name').value;
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            const c_password = document.getElementById('c_password').value;
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            // Reset error styles
            document.getElementById('password').classList.remove('error');
            document.getElementById('c_password').classList.remove('error');
            document.getElementById('email').classList.remove('error');
            document.getElementById('responseMessage').textContent = '';

            if (password.length < 8 || c_password.length < 8) {
                document.getElementById('responseMessage').textContent = 'The password must be at least 8 digits';
                document.getElementById('password').classList.add('error');
                document.getElementById('c_password').classList.add('error');
                return;
            }

            if (password !== c_password) {
                document.getElementById('responseMessage').textContent = 'Confirm Password must be the same';
                document.getElementById('password').classList.add('error');
                document.getElementById('c_password').classList.add('error');
                return;
            }

            try {
                const response = await fetch('/api/auth/register', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: JSON.stringify({ name, email, password, c_password}),
                });

                const data = await response.json();

                if (response.ok) {
                    document.getElementById('responseMessage').style.display = 'none';
                    document.getElementById('successModal').classList.add('show');
                } else {
                    document.getElementById('responseMessage').textContent = 'The email has already been taken. Please try a new one';
                    document.getElementById('email').classList.add('error');
                }
            } catch (error) {
                console.error('Error:', error);
            }
        });

        // Get the modal
        var modal = document.getElementById("successModal");

        // Get the <span> element that closes the modal
        var span = document.getElementsByClassName("close")[0];

        // When the user clicks on <span> (x), close the modal
        span.onclick = function() {
            modal.classList.remove('show');
        }

        // When the user clicks anywhere outside of the modal, close it
        window.onclick = function(event) {
            if (event.target == modal) {
                modal.classList.remove('show');
            }
        }

        function redirectToSignIn() {
            window.location.href = '/signin';
        }
    </script>
</body>
</html>
