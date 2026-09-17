# Smart Irrigation & Wastewater Monitoring System 🌿💧

Sistem pemantauan dan irigasi air limbah berbasis IoT yang dirancang untuk memantau kualitas air serta kondisi kelembapan tanah secara *real-time*. Projek ini mengintegrasikan mikrokontroler **ESP32** dengan *dashboard* web interaktif untuk pemantauan dan kontrol otomatisasi.

---

## 🚀 Fitur Utama

* **Pemantauan Real-Time:** Memantau kelembapan tanah, suhu udara, dan pH air secara *live*.
* **Kontrol Otomatis & Manual:** Mode penyiraman otomatis berdasarkan nilai sensor atau kontrol manual melalui web dashboard.
* **Visualisasi Data:** Grafik riwayat sensor untuk menganalisis tren kondisi air dan tanah.
* **Manajemen Pengguna:** Akses masuk (*login*) sistem yang aman untuk administrator dan pengguna.

---

## 🛠️ Alat & Teknologi

### Hardware
* **Mikrokontroler:** ESP32
* **Sensor:**
  * Sensor Kelembapan Tanah (*Soil Moisture Sensor*)
  * Sensor Suhu & Kelembapan Udara (DHT22)
  * Sensor pH Air
* **Aktuator:** Relay & Pompa Air

### Software & Backend
* **Bahasa Pemrograman:** C++ (Arduino IDE), PHP, JavaScript, HTML/CSS
* **Database:** MySQL
* **Framework/Library:** Bootstrap 5

---
##🔧 Panduan Instalasi & Penggunaan
1. Pengaturan Database
Buka MySQL Server (misal via XAMPP / phpMyAdmin / Laragon).

Buat database baru dengan nama irrigation_system.

Import file irrigation_system.sql ke dalam database tersebut.

2. Pengaturan Web Backend
Pindahkan seluruh folder projek ini ke direktori web server kamu (misal: htdocs/ pada XAMPP).

Buka file db.php lalu sesuaikan kredensial database sesuai dengan environment kamu:

PHP
$host = "localhost";
$user = "YOUR_DB_USER";
$pass = "YOUR_DB_PASSWORD";
$db   = "irrigation_system";
3. Pengaturan ESP32 (Firmware)
Buka file irigasi_air_limbah/irigasi_air_limbah.ino menggunakan Arduino IDE.

Sesuaikan konfigurasi WiFi dan URL endpoint backend:

C++
const char* ssid = "YOUR_WIFI_SSID";
const char* password = "YOUR_WIFI_PASSWORD";
String serverName = "http://IP_SERVER_KAMU/logika_alat_masuk_ke_db/insert_sensor_data.php";
Upload program ke board ESP32.
--

## 🔄 Alur Komunikasi Data
Plaintext
[ Sensor (pH, Soil, DHT22) ] ──► [ ESP32 ] ──( HTTP POST )──► [ API PHP ] ──► [ Database MySQL ]
                                   │                                                │
                                   └───────( Baca Status Relay via HTTP GET )───────┘
Pengiriman Data: ESP32 membaca data sensor lalu mengirimkannya ke endpoint insert_sensor_data.php.

Penyimpanan: Backend PHP memproses dan menyimpan data ke database MySQL.

Pemberian Perintah: ESP32 membaca status/mode kontrol dari server untuk mengaktifkan atau mematikan pompa air.
--

## 📸 Tampilan Dashboard
(Unggah foto alat atau screenshot dashboard di sini)
--

## 📝 Catatan & Lisensi
Projek ini dikembangkan sebagai bagian dari Tugas Akhir / Portofolio Pengembang IoT & Web.

--

## 📁 Struktur Repositori

```text
.
├── irigasi_air_limbah/
│   └── irigasi_air_limbah.ino       # Program C++ untuk ESP32
├── logika_alat_masuk_ke_db/
│   ├── get_mode_control.php         # API mode kontrol alat
│   ├── get_sensor_limits.php        # API batasan nilai sensor
│   ├── get_status_pompa.php         # API pemantauan status pompa
│   ├── insert_pompa.php             # API pencatatan aktivitas pompa
│   └── insert_sensor_data.php       # API penerima data sensor dari ESP32
├── control_pompa.php                # Kontrol manual pompa via web
├── data_sensor.php                  # Halaman riwayat data sensor
├── db.php                           # File koneksi database MySQL
├── get_sensor_data.php              # API penarik data sensor
├── index_admin.php                  # Dashboard khusus admin
├── index_user.php                   # Dashboard khusus pengguna
├── index.php                        # Halaman utama / dashboard
├── irrigation_system.sql            # File dump struktur database MySQL
├── login.php                        # Halaman otentikasi masuk
├── logout.php                       # Fitur keluar sistem
├── register.php                     # Halaman pendaftaran akun
├── sensor_limits.php                # Pengaturan batas sensor
└── README.md                        # Dokumentasi projek
