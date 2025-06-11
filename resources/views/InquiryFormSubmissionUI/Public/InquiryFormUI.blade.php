<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Title</title>
    <link rel="stylesheet" href="{{ asset('css/layout.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css"/>
    <style>@import url('https://fonts.googleapis.com/css?family=Roboto:300,400,400i,500');

* {
  padding: 0;
  margin: 0;
  list-style: none;
  text-decoration: none;
  box-sizing: border-box;
}

body {
  font-family: 'Roboto', sans-serif;
  background-color: #f0f0f0;
}

/* Top Header */
.main-header {
  position: fixed;
  top: 0;
  left: 0;
  height: 70px;
  width: 100%;
  display: flex;
  align-items: center;
  padding: 0 20px;
  z-index: 1000;
}

.logo-container {
  display: flex;
  align-items: center;
  gap: 10px;
}

.logo {
  height: 60px;
  width: auto;
}

.umpsa {
  height: 60px;
  width: auto;
}

.brand {
  height: 45px;
  width: auto;
  margin-top: 10px;
}

.page-name {
  font-size: 22px;
  font-weight: 500;
  color: white;
  white-space: nowrap;
  align-self: center;
  text-align: center;
  margin: 0 auto;
}

/* Sidebar */
.sidebar {
  position: fixed;
  top: 70px;
  left: 0;
  width: 250px;
  height: calc(100% - 70px);
  background: #042331;
  transition: all 0.5s ease;
  overflow-y: auto;
}

.sidebar ul a {
  display: flex;
  align-items: center;
  height: 65px;
  width: 100%;
  font-size: 16px;
  color: white;
  padding-left: 20px;
  border-bottom: 1px solid black;
  border-top: 1px solid rgba(255, 255, 255, 0.1);
  transition: 0.4s;
}

.sidebar ul li:hover a {
  padding-left: 35px;
  background-color: #ffffff1c;
}

.sidebar ul a i {
  margin-right: 16px;
  min-width: 20px;
  text-align: center;
}

/* Content Area */
.content {
  margin-left: 250px;
  margin-top: 70px;
  padding: 20px;
}

.complaint-section {
    background: #fff;
    margin: 36px auto;
    margin-left: 300px;
    padding: 36px 40px 40px 40px;
    border-radius: 18px;
    max-width: 1100px;
    box-shadow: 0 4px 24px rgba(50,92,116,0.10);
}

.form-title {
    font-size: 24px;
    font-weight: 700;
    margin-bottom: 28px;
    text-decoration: underline;
    color: #325c74;
}

.complaint-form {
    display: flex;
    flex-wrap: wrap;
    gap: 20px 36px;
    margin-bottom: 32px;
}

.form-group {
    flex: 1 1 45%;
    display: flex;
    align-items: center;
    margin-bottom: 12px;
}

.form-group label {
    min-width: 110px;
    font-weight: 500;
    color: #325c74;
}

.form-group input[type="text"],
.form-group textarea,
.form-group select {
    flex: 1;
    padding: 10px 12px;
    border: 1px solid #cfd8dc;
    border-radius: 7px;
    font-size: 15px;
    margin-left: 10px;
    background: #f7fafc;
    transition: border 0.2s;
}

.form-group input[type="text"]:focus,
.form-group textarea:focus,
.form-group select:focus {
    border: 1.5px solid #325c74;
    outline: none;
}

.form-group textarea {
    min-height: 50px;
    resize: vertical;
}

.url-label {
    margin-left: 20px;
}

.url-input {
    margin-left: 10px;
    min-width: 200px;
}

.form-group input[type="file"] {
    margin-left: 10px;
}

.file-note {
    font-size: 12px;
    color: #888;
    margin-left: 10px;
}

.form-actions {
    flex-basis: 100%;
    display: flex;
    gap: 18px;
    margin-top: 10px;
}

.btn-submit {
    background: linear-gradient(90deg, #4be04b 0%, #43c943 100%);
    color: #fff;
    border: none;
    padding: 10px 32px;
    border-radius: 8px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    transition: background 0.2s, box-shadow 0.2s;
    box-shadow: 0 2px 8px rgba(75,224,75,0.10);
}

.btn-submit:hover {
    background: linear-gradient(90deg, #43c943 0%, #4be04b 100%);
    box-shadow: 0 4px 16px rgba(75,224,75,0.18);
}

.btn-add {
    background: #e0e0e0;
    color: #325c74;
    border: none;
    padding: 10px 32px;
    border-radius: 8px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    transition: background 0.2s, color 0.2s;
}

.btn-add:hover {
    background: #325c74;
    color: #fff;
}

.complaint-table-container {
    margin-top: 28px;
    overflow-x: auto;
}

.complaint-table {
    width: 100%;
    border-collapse: collapse;
    background: #f7f7f7;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(50,92,116,0.06);
}

.complaint-table th, .complaint-table td {
    padding: 14px 10px;
    text-align: left;
    border-bottom: 1px solid #e0e0e0;
    font-size: 15px;
}

.complaint-table th {
    background: #e0e0e0;
    font-weight: 700;
    color: #325c74;
}

.complaint-table tbody tr:nth-child(even) {
    background: #f2f6fa;
}

.complaint-table tbody tr:hover {
    background: #e3f2fd;
    transition: background 0.2s;
}

.complaint-table tr:last-child td {
    border-bottom: none;
}

.btn-delete {
    background: #e53935;
    color: #fff;
    border: none;
    padding: 7px 20px;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: background 0.2s, box-shadow 0.2s;
    box-shadow: 0 2px 8px rgba(229,57,53,0.10);
}

.btn-delete:hover {
    background: #b71c1c;
    box-shadow: 0 4px 16px rgba(229,57,53,0.18);
}

.btn-edit {
    background: #325c74;
    color: #fff;
    border: none;
    padding: 7px 20px;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    margin-right: 8px;
    transition: background 0.2s, box-shadow 0.2s;
    box-shadow: 0 2px 8px rgba(50,92,116,0.10);
    display: inline-flex;
    align-items: center;
    gap: 6px;
}

.btn-edit:hover {
    background: #22394a;
    box-shadow: 0 4px 16px rgba(50,92,116,0.18);
}
</style>
</head>
<body>

<!-- Top Header -->
<div class="main-header" style="background: #325c74; color: white;">
    <div class="logo-container">
        <img src="{{ asset('images/logo.png') }}" alt="umpsa" class="umpsa">
        <img src="{{ asset('images/brand.png') }}" alt="brand" class="brand">
    </div>
    <span class="page-name">PageName</span>
</div>

<!-- Sidebar -->
<div class="sidebar" style="background: #325c74;">
    <ul>
        <li><a href="#"><i class="fas fa-home"></i> Home</a></li>
        <li><a href="#"><i class="fas fa-user"></i> User Profile</a></li>
        <li><a href="#"><i class="fas fa-users"></i> View User Data</a></li>
        <li><a href="#"><i class="fas fa-history"></i> Activity Log</a></li>

        <li><a href="resources/views/inquiry/index"><i class="fas fa-stream"></i> Module</a></li>
        <li><a href="#"><i class="fas fa-calendar"></i> Module</a></li>
        <li><a href="#"><i class="fas fa-walking"></i> Module</a></li>
        <li><a href="#"><i class="fas fa-th-list"></i> Module</a></li>
        <li><a href="#"><i class="fas fa-clipboard"></i> Module</a></li>
        <li><a href="#"><i class="fas fa-edit"></i> Module</a></li>
        <li><a href="#"><i class="far fa-arrow-alt-circle-left"></i> Log Out</a></li>
    </ul>
</div>

<!-- Content Section -->
<section class="content">
    <h1>Welcome to MySebenarnya</h1>
</section>
 <!-- Complaint Form Section -->
        <section class="complaint-section">
            <h2 class="form-title">Complaint Form</h2>
            <form class="complaint-form">
                <div class="form-group">
                    <label for="title">*Title :</label>
                    <input type="text" id="title" name="title">
                </div>
                <div class="form-group">
                    <label for="description">*Description :</label>
                    <textarea id="description" name="description"></textarea>
                </div>
                <div class="form-group">
                    <label for="url" class="url-label">*URL Link :</label>
                    <input type="text" id="url" name="url" class="url-input">
                </div>
                <div class="form-group">
                    <label>Supporting Evidence :</label>
                    <input type="file" id="evidence" name="evidence">
                    <span class="file-note">*not exceed 5MB</span>
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn-submit">Submit</button>
                    <button type="button" class="btn-add">Add</button>
                </div>
            </form>

              <!-- Table -->
    <div class="complaint-table-container">
        <table class="complaint-table" id="complaintTable">
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Title</th>
                    <th>Description</th>
                    <th>URL</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <!-- New rows will be added here -->
            </tbody>
        </table>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('complaintForm');
    const table = document.getElementById('complaintTable').getElementsByTagName('tbody')[0];
    let complaintCount = 0;
    let editIndex = null;

    form.addEventListener('submit', function(e) {
        e.preventDefault();

        const title = document.getElementById('title').value.trim();
        const description = document.getElementById('description').value.trim();
        const url = document.getElementById('url').value.trim();
        const date = new Date().toLocaleDateString('en-GB');
        const status = "Submitted";

        if (editIndex !== null) {
            // Update existing row
            const row = table.rows[editIndex];
            row.cells[1].textContent = title;
            row.cells[2].textContent = description;
            row.cells[3].innerHTML = `<a href="${url}" target="_blank">${url}</a>`;
            row.cells[4].textContent = date;
            row.cells[5].textContent = status;
            editIndex = null;
        } else {
            // Add new row
            complaintCount++;
            const newRow = table.insertRow();
            newRow.innerHTML = `
                <td>${complaintCount}.</td>
                <td>${title}</td>
                <td>${description}</td>
                <td><a href="${url}" target="_blank">${url}</a></td>
                <td>${date}</td>
                <td>${status}</td>
                <td>
                    <button class="btn-edit"><i class="fas fa-edit"></i> Edit</button>
                    <button class="btn-delete">Delete</button>
                </td>
            `;
        }

        form.reset();
    });

    table.addEventListener('click', function(e) {
        if (e.target.classList.contains('btn-delete')) {
            const row = e.target.closest('tr');
            row.remove();
            // Re-number rows
            Array.from(table.rows).forEach((row, idx) => {
                row.cells[0].textContent = (idx + 1) + '.';
            });
            complaintCount = table.rows.length;
        }
        if (e.target.classList.contains('btn-edit') || e.target.closest('.btn-edit')) {
            const row = e.target.closest('tr');
            document.getElementById('title').value = row.cells[1].textContent;
            document.getElementById('description').value = row.cells[2].textContent;
            document.getElementById('url').value = row.cells[3].textContent;
            editIndex = Array.from(table.rows).indexOf(row);
        }
    });
});
</script>
</body>
</html>
