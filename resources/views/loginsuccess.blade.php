<!DOCTYPE html>
<html>
<head>
    <title>Login Success</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #96e6a1, #d4fc79);
            margin: 0;
            padding: 20px;
            text-align: center;
            color: #333;
        }

        #welcomeMessage {
            color: #28a745;
            font-size: 28px;
            margin-top: 30px;
            margin-bottom: 20px;
        }

        button {
            background-color: #28a745;
            color: #fff;
            border: none;
            padding: 12px 24px;
            font-size: 16px;
            cursor: pointer;
            margin-top: 20px;
            border-radius: 30px;
            transition: all 0.3s ease;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        button:hover {
            background-color: #218838;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }

        #userList {
            margin-top: 30px;
            padding: 0 20px;
        }

        table {
            width: 700px;
            border-collapse: separate;
            border-spacing: 0;
            margin: 0 auto;
            background-color: #fff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }

        th, td {
            border: none;
            padding: 15px;
            text-align: left;
        }

        th {
            background-color: #28a745;
            color: white;
            font-weight: bold;
            text-transform: uppercase;
        }

        th.actions-column,
        td.actions-column {
            width: 100px; /* Adjust this value as needed */
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        tr:hover {
            background-color: #e9ecef;
        }

        .action-buttons {
            display: flex;
            justify-content: space-around;
        }

        .action-buttons button {
            background-color: #17a2b8;
            border: none;
            color: white;
            padding: 8px 15px;
            margin-top: 0px;
            border-radius: 20px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .action-buttons button.delete {
            background-color: #dc3545;
        }

        .action-buttons button:hover {
            opacity: 0.9;
            transform: translateY(-2px);
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.5);
            backdrop-filter: blur(5px);
        }

        .modal-content {
            background-color: #fff;
            margin: 5% auto;
            padding: 30px;
            border: none;
            width: 90%;
            max-width: 500px;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
            animation: modalFadeIn 0.3s;
        }

        @keyframes modalFadeIn {
            from {opacity: 0; transform: translateY(-50px);}
            to {opacity: 1; transform: translateY(0);}
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid #e9ecef;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }

        .modal-header h2 {
            margin: 0;
            color: #28a745;
            font-size: 24px;
        }

        .close {
            color: #aaa;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
            transition: color 0.3s ease;
        }

        .close:hover,
        .close:focus {
            color: #28a745;
        }

        .modal-body form > div {
            margin-bottom: 15px;
        }

        .modal-body label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #495057;
        }

        .modal-body input,
        .modal-body select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ced4da;
            border-radius: 5px;
            font-size: 16px;
        }

        .modal-footer {
            display: flex;
            justify-content: flex-end;
            padding-top: 20px;
            border-top: 2px solid #e9ecef;
        }

        .modal-footer button {
            margin-left: 10px;
        }
        .popup {
            position: fixed;
            top: 20px;
            right: 20px;
            background-color: #28a745;
            color: white;
            padding: 15px 20px;
            border-radius: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.2);
            z-index: 1000;
            animation: fadeInOut 3s ease-in-out;
        }

        @keyframes fadeInOut {
            0%, 100% { opacity: 0; }
            10%, 90% { opacity: 1; }
        }

        .deletebut {
            background-color: #dc3545;
        }
        .confirmdeletion{
            color: #dc3545;
        }
        .error {
            border-color: red;
        }

    </style>
</head>
<body>
    <h1 id="welcomeMessage">Loading...</h1>
    <button onclick="logout()">Logout</button>
    <div id="userList" style="display: none;">
        <!-- User table will be inserted here if role_id = 1 -->
    </div>

    <!-- Edit User Modal -->
    <div id="editUserModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                Edit User
            </div>
            <div class="modal-body">
                <input type="hidden" id="editUserId">
                <label for="editUserName">Name:</label>
                <input type="text" id="editUserName" required><br>
                <label for="editUserEmail">Email:</label>
                <input type="email" id="editUserEmail" required><br>
                <label for="editUserRole">Role:</label>
                <select id="editUserRole" required>
                    <option value="1">admin</option>
                    <option value="2">user</option>
                </select><br>
                <label for="editUserPassword">Password:</label>
                <input type="password" id="editUserPassword"><br>
                <label for="editUserPasswordConfirm">Confirm Password:</label>
                <input type="password" id="editUserPasswordConfirm"><br>
            </div>
            <div class="modal-footer">
                <button onclick="saveUser()">Save</button>
                <button onclick="closeEditUserModal()">Cancel</button>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteConfirmModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="confirmdeletion">Confirm Deletion</h2>
                <span class="close" onclick="closeDeleteConfirmModal()">&times;</span>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this user?</p>
            </div>
            <div class="modal-footer">
                <button onclick="confirmDelete()" class="deletebut">Yes, Delete</button>
                <button onclick="closeDeleteConfirmModal()">Cancel</button>
            </div>
        </div>
    </div>

    <script>
        let usersData = [];

        async function fetchUserDetails() {
            try {
                const response = await fetch('/api/auth/me', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': 'Bearer ' + localStorage.getItem('access_token'),
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                });

                const data = await response.json();

                if (response.ok) {
                    document.getElementById('welcomeMessage').innerText = 'Welcome, ' + data.name + '! You are successfully logged in.';
                    if (data.role_id === 1) {
                        fetchUsers(); // Fetch users if role_id is 1
                    } else {
                        document.getElementById('userList').style.display = 'none';
                    }
                } else {
                    document.getElementById('welcomeMessage').textContent = 'Failed to load user details.';
                }
            } catch (error) {
                console.error('Error:', error);
                document.getElementById('welcomeMessage').textContent = 'Error occurred while fetching user details.';
            }
        }

        async function fetchUsers() {
            try {
                const response = await fetch('/api/auth/allUser', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': 'Bearer ' + localStorage.getItem('access_token'),
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                });

                const data = await response.json();
                usersData = data; // Store the fetched user data in a global variable
                let table = '<table><tr><th>ID</th><th>Name</th><th>Email</th><th>Role</th><th>Actions</th></tr>';

                data.forEach(user => {
                    let role = user.role_id === 1 ? 'admin' : user.role_id === 2 ? 'user' : 'unknown';
                    table += `<tr>
                        <td>${user.id}</td>
                        <td>${user.name}</td>
                        <td>${user.email}</td>
                        <td>${role}</td>
                        <td class="action-buttons">
                            <button onclick="editUser(${user.id})">Edit</button>
                            <button class="delete" onclick="deleteUser(${user.id})">Delete</button>
                        </td>
                    </tr>`;
                });

                table += '</table>';
                document.getElementById('userList').innerHTML = table;
                document.getElementById('userList').style.display = 'block';
            } catch (error) {
                console.error('Error:', error);
                document.getElementById('welcomeMessage').textContent = 'Error occurred while fetching users.';
            }
        }

        function logout() {
            fetch('/api/auth/logout', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': 'Bearer ' + localStorage.getItem('access_token'),
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
            })
            .then(response => response.json())
            .then(data => {
                localStorage.removeItem('access_token');
                window.location.href = '/signin';
            })
            .catch(error => console.error('Error:', error));
        }

        function editUser(userId) {
            const user = usersData.find(user => user.id === userId);
            if (user) {
                document.getElementById('editUserId').value = user.id;
                document.getElementById('editUserName').value = user.name;
                document.getElementById('editUserEmail').value = user.email;
                document.getElementById('editUserRole').value = user.role_id;
                document.getElementById('editUserPassword').value = '';
                document.getElementById('editUserPasswordConfirm').value = '';
                document.getElementById('editUserModal').style.display = 'block';
            }
        }

        function closeEditUserModal() {
            document.getElementById('editUserModal').style.display = 'none';
        }

        function saveUser() {
            const userId = document.getElementById('editUserId').value;
            const userName = document.getElementById('editUserName').value;
            const userEmail = document.getElementById('editUserEmail').value;
            const userRole = document.getElementById('editUserRole').value;
            const userPassword = document.getElementById('editUserPassword').value;
            const userPasswordConfirm = document.getElementById('editUserPasswordConfirm').value;

            // Password validation
            if (userPassword !== userPasswordConfirm) {
                document.getElementById('editUserPassword').style.border = '1px solid red';
                document.getElementById('editUserPasswordConfirm').style.border = '1px solid red';
                showPopup("Confirm Password must be the same");
                return; // Exit the function if passwords do not match
            }

            if (userPassword && userPassword.length < 8) {
                document.getElementById('editUserPassword').style.border = '1px solid red';
                document.getElementById('editUserPasswordConfirm').style.border = '1px solid red';
                showPopup("The password must be at least 8 digits");
                return; // Exit the function if password is less than 8 characters
            }

            // Reset border styles if validation passes
            document.getElementById('editUserPassword').style.border = '';
            document.getElementById('editUserPasswordConfirm').style.border = '';

            const userData = {
                name: userName,
                email: userEmail,
                role_id: userRole,
            };

            if (userPassword) {
                userData.password = userPassword;
                userData.password_confirmation = userPasswordConfirm;
            }

            fetch(`/api/auth/${userId}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': 'Bearer ' + localStorage.getItem('access_token'),
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(userData)
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(errorData => {
                        // Handle specific validation errors here
                        handleValidationErrors(errorData);
                        throw new Error('Validation failed');
                    });
                }
                return response.json();
            })
            .then(data => {
                showPopup('User updated successfully.');
                closeEditUserModal();
                fetchUsers(); // Refresh the user list
            })
            .catch(error => console.error('Error:', error));
        }

        function handleValidationErrors(errorData) {
            // Example handling for email errors
            if (errorData.email && errorData.email.length > 0) {
                document.getElementById('editUserEmail').style.border = '1px solid red';
                showPopup(`Email Error: ${errorData.email.join(', ')}`);
            }
            // Handle other validation errors similarly
        }

        let userIdToDelete = null;

        function deleteUser(userId) {
            userIdToDelete = userId;
            document.getElementById('deleteConfirmModal').style.display = 'block';
        }

        function closeDeleteConfirmModal() {
            document.getElementById('deleteConfirmModal').style.display = 'none';
        }

        function confirmDelete() {
            if (userIdToDelete) {
                fetch(`/api/auth/${userIdToDelete}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': 'Bearer ' + localStorage.getItem('access_token'),
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                })
                .then(response => response.json())
                .then(data => {
                    showPopup('User deleted successfully.');
                    fetchUsers(); // Refresh the user list
                    closeDeleteConfirmModal();
                })
                .catch(error => console.error('Error:', error));
            }
        }

        function showPopup(message) {
            const popup = document.createElement('div');
            popup.className = 'popup';
            popup.textContent = message;
            document.body.appendChild(popup);

            setTimeout(() => {
                popup.remove();
            }, 3000);
        }

        document.addEventListener('DOMContentLoaded', fetchUserDetails);
    </script>
</body>
</html>
