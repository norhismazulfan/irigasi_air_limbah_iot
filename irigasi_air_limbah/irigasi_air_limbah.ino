#include <WiFi.h>
#include <HTTPClient.h>
#include <DHT.h>
#include <Wire.h>
#include <LiquidCrystal_I2C.h>

// --- PIN DEFINITIONS ---
#define DHTPIN 25
#define DHTTYPE DHT22
#define SOIL_MOISTURE_PIN 34
#define PH_PIN 35
#define RELAY_PIN 15
#define BUZZER_PIN 13

// --- WiFi CREDENTIALS ---
const char* ssid = "Rasah Tetring";
const char* password = "SenyumDulu";

// --- SERVER URLs ---
const char* serverURL = "http://wadahinovasi.com/irigasi_air_limbah/logika_alat_masuk_ke_db/get_status_pompa.php";
const char* modeControlURL = "http://wadahinovasi.com/irigasi_air_limbah/logika_alat_masuk_ke_db/get_mode_control.php";
const char* sensorLimitsURL = "http://wadahinovasi.com/irigasi_air_limbah/logika_alat_masuk_ke_db/get_sensor_limits.php";
const char* insertSensorDataURL = "http://wadahinovasi.com/irigasi_air_limbah/logika_alat_masuk_ke_db/insert_sensor_data.php";
const char* insertPompaURL = "http://wadahinovasi.com/irigasi_air_limbah/logika_alat_masuk_ke_db/insert_pompa.php";
const char* updateSystemStatusURL = "http://wadahinovasi.com/irigasi_air_limbah/logika_alat_masuk_ke_db/update_system_status.php";

// --- OBJECTS & GLOBAL VARIABLES ---
WiFiClient client;
DHT dht(DHTPIN, DHTTYPE);
LiquidCrystal_I2C lcd(0x27, 16, 2);

// Kalibrasi Sensor Kelembapan Tanah
const int AirValue = 2540;
const int WaterValue = 975;

// Variabel untuk timer pengiriman data sensor
unsigned long lastSendMillis = 0;
const unsigned long sendInterval = 10000;

// Variabel status sistem
bool relayOn = false;
String lastPayload = "";

// --- FUNGSI BANTU ---
int soilMoisturePercent(int analogValue) {
  analogValue = constrain(analogValue, WaterValue, AirValue);
  return map(analogValue, AirValue, WaterValue, 0, 100);
}

void sendPumpStatus(int status) {
  if (WiFi.status() == WL_CONNECTED) {
    HTTPClient http;
    http.begin(client, insertPompaURL);
    http.addHeader("Content-Type", "application/x-www-form-urlencoded");
    String postData = "pump_status=" + String(status);
    http.POST(postData);
    http.end();
  }
}

// =================================================================
//   SETUP
// =================================================================
void setup() {
  Serial.begin(115200);
  pinMode(RELAY_PIN, OUTPUT);
  digitalWrite(RELAY_PIN, LOW);
  pinMode(BUZZER_PIN, OUTPUT);
  digitalWrite(BUZZER_PIN, HIGH);
  pinMode(SOIL_MOISTURE_PIN, INPUT);
  pinMode(PH_PIN, INPUT);
  dht.begin();
  lcd.init();
  lcd.backlight();

  lcd.setCursor(0, 0); lcd.print("Connecting WiFi");
  WiFi.begin(ssid, password);
  while (WiFi.status() != WL_CONNECTED) {
    delay(500);
    Serial.print(".");
    lcd.print(".");
  }
  lcd.clear(); lcd.setCursor(0, 0); lcd.print("WiFi Connected!");
  delay(1000);
}

// =================================================================
//   LOOP
// =================================================================
void loop() {
  // 1. BACA SEMUA SENSOR
  float temperature = dht.readTemperature();
  int soilRaw = analogRead(SOIL_MOISTURE_PIN);
  int soilPercent = soilMoisturePercent(soilRaw);
  
  static float phValue = 7.0;
  if (Serial.available() > 0) {
    phValue = Serial.readStringUntil('\n').toFloat();
    Serial.print("pH diubah manual menjadi: "); Serial.println(phValue);
  }

  // ===============================================================
  //   FIX 1: Menambahkan kembali debug Serial Monitor
  // ===============================================================
  Serial.printf("[DBG] T:%.1fC S:%d%% pH:%.1f P:%s\n",
                temperature, soilPercent, phValue, (relayOn ? "ON" : "OFF"));
  
  int systemStatus = 0; // 0=Diam, 1=Menyiram, 2=Siaga

  // 2. AMBIL MODE KONTROL DARI SERVER
  int modeControl = 1; 
  if (WiFi.status() == WL_CONNECTED) {
    HTTPClient http;
    http.begin(client, modeControlURL);
    if (http.GET() > 0) modeControl = http.getString().toInt();
    http.end();
  }

  static int lastModeControl = -1;
  if (lastModeControl == 1 && modeControl == 0) {
    digitalWrite(RELAY_PIN, LOW);
    if (relayOn) sendPumpStatus(0);
    relayOn = false;
  }
  lastModeControl = modeControl;

  // 3. LOGIKA UTAMA BERDASARKAN MODE
  if (modeControl == 0) {
    // --- MODE MANUAL ---
    if (WiFi.status() == WL_CONNECTED) {
      HTTPClient http;
      http.begin(client, serverURL);
      if (http.GET() > 0) {
        String payload = http.getString(); payload.trim();
        if (payload != lastPayload) {
          lastPayload = payload;
          if (payload == "1" && !relayOn) { digitalWrite(RELAY_PIN, HIGH); relayOn = true; sendPumpStatus(1); }
          else if (payload == "0" && relayOn) { digitalWrite(RELAY_PIN, LOW); relayOn = false; sendPumpStatus(0); }
        }
      }
      http.end();
    }

  } else {
    // --- MODE OTOMATIS ---
    float ph_limit = 7.5;
    int moisture_limit = 50;
    int moisture_limit_tinggi = 80;
    
    bool pumpState = false;

    if (phValue > ph_limit && soilPercent < moisture_limit) {
      systemStatus = 1; // Menyiram
      pumpState = true;
    } else if (phValue > ph_limit && soilPercent >= moisture_limit_tinggi) {
      systemStatus = 2; // Siaga/Alihkan
      pumpState = false;
    } else {
      systemStatus = 0; // Diam
      pumpState = false;
    }

    if (pumpState != relayOn) {
      digitalWrite(RELAY_PIN, pumpState ? HIGH : LOW);
      sendPumpStatus(pumpState ? 1 : 0);
      relayOn = pumpState;
    }
  }

  // 4. KIRIM STATUS SISTEM KE SERVER JIKA BERUBAH
  static int lastSystemStatus = -1;
  if (systemStatus != lastSystemStatus) {
    if (WiFi.status() == WL_CONNECTED) {
      HTTPClient http;
      http.begin(client, updateSystemStatusURL);
      http.addHeader("Content-Type", "application/x-www-form-urlencoded");
      String postData = "status=" + String(systemStatus);
      http.POST(postData);
      http.end();
    }
    lastSystemStatus = systemStatus;
  }
  
  // 5. LOGIKA BUZZER
  static int lastBuzzerStatus = -1;
  if (systemStatus != lastBuzzerStatus) {
    if (systemStatus == 1) { // Menyiram: Bip 2x
      for(int i=0; i<2; i++){ digitalWrite(BUZZER_PIN, LOW); delay(150); digitalWrite(BUZZER_PIN, HIGH); delay(150); }
    } else if (systemStatus == 2) { // Siaga: Bip 4x
      for(int i=0; i<4; i++){ digitalWrite(BUZZER_PIN, LOW); delay(150); digitalWrite(BUZZER_PIN, HIGH); delay(150); }
    }
    lastBuzzerStatus = systemStatus;
  }

  // ===============================================================
  //   FIX 2: Memperbarui tampilan LCD agar lebih informatif
  // ===============================================================
  String statusText = "OK   ";
  if (systemStatus == 1) statusText = "SIRAM";
  if (systemStatus == 2) statusText = "SIAGA";

  // Gabungkan status pompa dengan status sistem
  String pumpStatusText = relayOn ? "P:ON " : "P:OFF";
  String fullStatusMsg = pumpStatusText + " " + statusText;
  
  lcd.clear();
  lcd.setCursor(0, 0);
  lcd.print("T:"); lcd.print(temperature, 1); lcd.print((char)223); lcd.print("C S:"); lcd.print(soilPercent); lcd.print("%");
  lcd.setCursor(0, 1);
  lcd.print("pH:"); lcd.print(phValue, 1);
  lcd.setCursor(8, 1); // Posisikan di kolom ke-8
  lcd.print(fullStatusMsg); // Tampilkan status gabungan


  // 7. KIRIM DATA SENSOR SECARA BERKALA
  if (millis() - lastSendMillis > sendInterval) {
    lastSendMillis = millis();
    if (WiFi.status() == WL_CONNECTED) {
      HTTPClient http;
      http.begin(client, insertSensorDataURL);
      http.addHeader("Content-Type", "application/x-www-form-urlencoded");
      String postData = "ph=" + String(phValue, 2) + "&temperature=" + String(temperature, 2) + "&soil_moisture=" + String(soilPercent);
      http.POST(postData);
      http.end();
    }
  }

  delay(500);
}
