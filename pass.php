<?php
session_start();

// Koneksi ke database
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "luxury_rent";

$conn = new mysqli($host, $user, $pass, $dbname);

// Cek koneksi database
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Pastikan pengguna sudah login
if (!isset($_SESSION['username'])) {
    header("Location: Login.php");
    exit();
}

$username = $_SESSION['username'];

// Proses update password
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $password = $_POST['password'];
    $newPassword = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_password'];

    // Validasi input kosong
    if (empty($password) || empty($newPassword) || empty($confirmPassword)) {
        echo "<script>alert('Semua kolom harus diisi!'); window.history.back();</script>";
        exit();
    }

    // Validasi password lama
    $stmt = $conn->prepare("SELECT password FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        if ($row['password'] !== $password) {
            echo "<script>alert('Password lama salah!'); window.history.back();</script>";
            exit();
        }
    } else {
        echo "<script>alert('Pengguna tidak ditemukan!'); window.history.back();</script>";
        exit();
    }

    // Validasi konfirmasi password baru
    if ($newPassword !== $confirmPassword) {
        echo "<script>alert('Konfirmasi password tidak cocok!'); window.history.back();</script>";
        exit();
    }

    // Update password di database
    $updateStmt = $conn->prepare("UPDATE users SET password = ? WHERE username = ?");
    $updateStmt->bind_param("ss", $newPassword, $username);

    if ($updateStmt->execute()) {
        echo "<script>alert('Password berhasil diupdate!'); window.location.href = 'Profile.php';</script>";
    } else {
        echo "<script>alert('Terjadi kesalahan saat mengupdate password!'); window.history.back();</script>";
    }

    $updateStmt->close();
    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password</title>
    <style>
        /* Reset styling */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        body {
            background-color: #f9f9f9;
        }

        .container {
            display: flex;
            justify-content: space-around;
            margin: 50px auto;
            width: 90%;
            max-width: 1000px;
        }

        /* Sidebar */
        .sidebar {
            background-color: white;
            border-radius: 15px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
            padding: 20px;
            width: 250px; 
            padding: 15px; /* Padding lebih kecil */
            height: 400px;
        }

        .sidebar .profile-pic {
            display: flex;
            justify-content: center;
            align-items: center;
            border: 3px solid #66a1ed;
            border-radius: 50%;
            width: 100px;
            height: 100px;
            margin: 0 auto 15px;
            background-color: #9067c6;
            color: white;
            font-size: 36px;
            font-weight: bold;
            cursor: pointer;
        }

        .sidebar h3 {
            margin: 10px 0;
            font-size: 18px;
            font-weight: bold;
        }

        .sidebar p {
            color: #888;
            font-size: 14px;
            margin-bottom: 20px;
        }

        .sidebar button {
            display: block;
            background-color: #66a1ed;
            color: white;
            text-align: center;
            border: none;
            border-radius: 5px;
            padding: 10px;
            width: 100%;
            margin-bottom: 10px;
            cursor: pointer;
            font-weight: bold;
        }

        /* Form Section */
        .profile-section {
            background-color: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 60%;
            position: relative;
        }

        .profile-section .profile-header {
            text-align: center;
            margin-bottom: 20px;
        }

        .profile-section h2 {
            background-color: #66a1ed;
            color: white;
            padding: 10px;
            font-size: 16px;
        }

        .profile-picture {
            display: block;
            margin: 0 auto;
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background-color: #9067c6;
            color: white;
            font-size: 36px;
            font-weight: bold;
            text-align: center;
            line-height: 100px;
            border: 3px solid #66a1ed;
            cursor: pointer;
        }

        .profile-section label {
            display: block;
            margin: 10px 0 5px;
            font-weight: bold;
        }

        .profile-section input {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .update-btn {
            display: block;
            width: 100%;
            background-color: #66a1ed;
            color: white;
            border: none;
            border-radius: 5px;
            padding: 10px;
            font-size: 16px;
            cursor: pointer;
            font-weight: bold;
        }

        /* Hover effect pada tombol Update profile */
        .update-btn:hover {
            background-color: #4c8cd4;
            color: #fff;
            transition: background-color 0.3s ease;
        }

        /* Menu Buttons */
        .menu-button {
            display: flex;
            align-items: center;
            text-decoration: none;
            color: #333;
            background-color: white;
            font-size: 16px;
            font-weight: bold;
            padding: 10px 15px;
            margin: 5px 0;
            border-radius: 5px;
            transition: background-color 0.3s, color 0.3s;
        }

        .menu-button:hover,
        .menu-button.active {
            background-color: #66a1ed;
            color: white;
        }

        .menu-button img {
            width: 20px;
            margin-right: 10px;
        }

        /* Hover effect pada button dan icon */
        .menu-button:hover img,
        .menu-button.active img {
            filter: brightness(0) invert(1);
        }
                  /* Header Fixed */
header {
    position: fixed; /* Tetap di atas saat scroll */
    top: 0;
    left: 0;
    width: 100%;
    background-color: #fff; /* Latar putih */
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); /* Bayangan */
    z-index: 1000;
}

/* Navbar Styling */
nav {
    display: flex;
    justify-content: space-between; /* Logo di kiri, link di kanan */
    align-items: center;
    padding: 1rem 3rem;
    font-family: Arial, sans-serif;
}

/* Logo Styling */
.logo {
    font-size: 1.5rem;
    font-weight: bold;
    color: black;
}

.logo span {
    color: #4c94ce; 
}

/* Navbar Links */
nav ul {
    list-style: none;
    display: flex;
    margin: 0;
    padding: 0;
}

nav ul li {
    margin-left: 2rem; /* Jarak antar link */
}

nav ul li a {
    text-decoration: none;
    color: #000;
    font-size: 1rem;
    font-weight: bold;
    transition: color 0.3s ease;
}

/* Hover dan Active Effect */
nav ul li a:hover,
nav ul li a.active {
    color: #66a1ed; /* Warna biru saat aktif atau di-hover */
}
    </style>
</head>
<body>
        <!-- Header -->
        <header>
            <nav>
                <div class="logo">Luxury<span>Rent</span></div>
                <ul>
                    <li><a href="LandingPage.html">Home</a></li>
                    <li><a href="Profile.php">Profile</a></li>
    
                </ul>
            </nav>
        </header>
        <br><br><br><br>
    <div class="container">
        <!-- Sidebar -->
        <div class="sidebar">
        <!-- Foto Profil -->
        <br>
        <h3>Profil Anda</h3>
        <br>

  
        <!-- Menu -->
        <a href="Profile.php" class="menu-button">
            <img src="https://img.icons8.com/ios-glyphs/30/66a1ed/user.png" alt="icon"> Profil
        </a>
        <a href="pass.php" class="menu-button active">
            <img src="https://img.icons8.com/ios-glyphs/30/66a1ed/lock.png" alt="icon"> Kata Sandi
        </a>
        <a href="reservasisaya.php" class="menu-button">
          <img src="https://img.icons8.com/ios-glyphs/30/66a1ed/calendar.png" alt="icon"> Reservasi saya
        </a>
        <a href="Welcome.html" class="menu-button">
          <img src="https://img.icons8.com/ios-glyphs/30/66a1ed/logout-rounded.png" alt="icon"> Keluar
        </a>

      </div>

        <!-- Profile Section -->
        <div class="profile-section">
            <div class="profile-header">
                <h2>PASSWORD</h2>
            </div>
            <form method="POST" action="">
                <!-- Input Password Lama -->
                <label for="password">Password Lama</label>
                <div class="input-group">
                    <input type="password" id="password" name="password" placeholder="Masukkan password lama" required>
                </div>

                <!-- Input Password Baru -->
                <label for="new-password">Password Baru</label>
                <div class="input-group">
                <input type="password" id="new-password" name="new_password" placeholder="Masukkan password baru" required>
                </div>

                <!-- Input Konfirmasi Password -->
                <label for="confirm-password">Konfirmasi Password</label>
                <div class="input-group">
                <input type="password" id="confirm-password" name="confirm_password" placeholder="Konfirmasi password baru" required>
                </div>
                <button type="submit" class="update-btn">Update Password</button>
            </form>
        </div>
    </div>
</body>
</html>
