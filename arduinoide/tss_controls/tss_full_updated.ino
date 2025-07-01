#include <ArduinoJson.h>
#include <LiquidCrystal_I2C.h>
#include <Wire.h>
#include <RTClib.h>
#include <PZEM004Tv30.h>
#include <DHT.h>
#include <BH1750.h>
#include <SD.h>

// ========== KONFIGURASI JARINGAN & SERVER ==========
const char* ssid = "TP-Link_7ED8";
const char* password = "47428904";
const char* serverHost = "192.168.0.51";
const int serverPort = 8000;  // Port Laravel
const char* serverPathLaravel = "/api/save-data";  // Endpoint Laravel untuk monitoring
const char* serverPathControls = "/api/controls/hardware";  // Endpoint Laravel untuk kontrol

// ========== DEKLARASI LIBRARY DAN PIN ==========
LiquidCrystal_I2C lcd(0x27, 20, 4);
HardwareSerial& espSerial = Serial1;
RTC_DS3231 rtc;
PZEM004Tv30 pzem(12, 13);
#define DHTPIN 4
#define DHTTYPE DHT21
DHT dht(DHTPIN, DHTTYPE);
BH1750 lightMeter(0x23);
File myFile;
int pinCS = 53;
const int mosfetPin = 5;
const int potPin = A0;
int pwmValue = 75;
const int button1Pin = 8;
const int button2Pin = 9;
const int button3Pin = 7;
const int outputPin = 3;

// ========== VARIABEL KONTROL ==========
int slider_light_intensity = 70;  // Default value dari website
int schedule_mode = 0;            // Default: Manual
int light_status = 0;             // Default: OFF

bool scheduleActive = false;
float voltage = 0.0;
float current = 0.0;
float power = 0.0;
float energy = 0.0;
float frequency = 0.0;
float pf = 0.0;

const unsigned long periodSD = 60000;
const unsigned long periodHTTP = 10000;   // Update setiap 10 detik
const unsigned long periodControls = 5000; // Update controls setiap 5 detik
unsigned long startMillisSD = 0;
unsigned long startMillisHTTP = 0;
unsigned long startMillisControls = 0;

// ========== FUNGSI AT COMMAND ==========
String sendAT(String command, const int timeout) {
  String response = "";
  espSerial.println(command);
  long int time = millis();
  while ((millis() - time) < timeout) {
    while (espSerial.available()) {
      char c = espSerial.read();
      response += c;
    }
  }
  Serial.print("CMD: ");
  Serial.println(command);
  Serial.print("RSP: ");
  Serial.println(response);
  return response;
}

// ========== FUNGSI HTTP REQUEST YANG DIPERBAIKI ==========
void sendPostRequest(const char* path, const String& jsonData) {
  Serial.println("\n----------------------------------------");
  Serial.println("Sending POST to: " + String(path));
  
  // Start TCP connection dengan port 8000
  String cmd = "AT+CIPSTART=\"TCP\",\"" + String(serverHost) + "\"," + String(serverPort);
  String response = sendAT(cmd, 5000);
  if (response.indexOf("ERROR") != -1) {
    Serial.println("--> CIPSTART failed");
    lcd.setCursor(0, 3);
    lcd.print("ERR:TCP           ");
    return;
  }
  
  // Prepare HTTP request
  String httpRequest = "POST " + String(path) + " HTTP/1.1\r\n";
  httpRequest += "Host: " + String(serverHost)  + "\r\n";
  httpRequest += "Content-Type: application/json\r\n";
  httpRequest += "Content-Length: " + String(jsonData.length()) + "\r\n";
  httpRequest += "Connection: close\r\n\r\n";
  httpRequest += jsonData;
  
  // Send data length
  cmd = "AT+CIPSEND=" + String(httpRequest.length());
  response = sendAT(cmd, 3000);
  if (response.indexOf('>') == -1) {
    Serial.println("--> CIPSEND failed");
    lcd.setCursor(0, 3);
    lcd.print("ERR:SND           ");
    sendAT("AT+CIPCLOSE", 1000);
    return;
  }
  
  // Send HTTP request
  response = sendAT(httpRequest, 10000);
  if (response.indexOf("SEND OK") != -1) {
    Serial.println("--> Data sent successfully");
    lcd.setCursor(0, 3);
    lcd.print("SEND OK           ");
  } else {
    Serial.println("--> Failed to send data");
    lcd.setCursor(0, 3);
    lcd.print("SEND ERR          ");
  }
  
  sendAT("AT+CIPCLOSE", 1000);
  Serial.println("----------------------------------------");
}

String sendGetRequest(const char* path) {
  Serial.println("\n----------------------------------------");
  Serial.println("Sending GET to: " + String(path));
  
  // Start TCP connection dengan port 8000
  String cmd = "AT+CIPSTART=\"TCP\",\"" + String(serverHost) + "\"," + String(serverPort);
  String response = sendAT(cmd, 5000);
  if (response.indexOf("ERROR") != -1) {
    Serial.println("--> CIPSTART failed");
    return "ERROR";
  }
  
  // Prepare HTTP request
  String httpRequest = "GET " + String(path) + " HTTP/1.1\r\n";
  httpRequest += "Host: " + String(serverHost) + ":" + String(serverPort) + "\r\n";
  httpRequest += "Connection: close\r\n\r\n";
  
  // Send data length
  cmd = "AT+CIPSEND=" + String(httpRequest.length());
  response = sendAT(cmd, 3000);
  if (response.indexOf('>') == -1) {
    Serial.println("--> CIPSEND failed");
    sendAT("AT+CIPCLOSE", 1000);
    return "ERROR";
  }
  
  // Send HTTP request
  response = sendAT(httpRequest, 10000);
  Serial.println("----------------------------------------");
  sendAT("AT+CIPCLOSE", 1000);
  return response;
}

// ========== FUNGSI SEND DATA KE SERVER ==========
void sendHTTPData() {
  DateTime now = rtc.now();
  float lux = lightMeter.readLightLevel();
  float humidity = dht.readHumidity();
  float temperature = dht.readTemperature();
  
  // Format timestamp sesuai dengan yang diharapkan Laravel
  char timestamp[25];
  sprintf(timestamp, "%04d-%02d-%02d %02d:%02d:%02d", now.year(), now.month(), now.day(), now.hour(), now.minute(), now.second());
  
  String jsonData = "{";
  jsonData += "\"timestamp\":\"" + String(timestamp) + "\",";
  jsonData += "\"light_intensity\":" + String(lux, 2) + ",";
  jsonData += "\"temp\":" + String(temperature, 2) + ",";
  jsonData += "\"humid\":" + String(humidity, 2) + ",";
  jsonData += "\"voltage\":" + String(voltage, 2) + ",";
  jsonData += "\"current\":" + String(current, 3) + ",";
  jsonData += "\"power\":" + String(power, 2) + ",";
  jsonData += "\"energy\":" + String(energy, 3) + ",";
  jsonData += "\"pf\":" + String(pf, 2) + ",";
  jsonData += "\"freq\":" + String(frequency, 1) + ",";
  int duration = 0;
  if (schedule_mode == 1) duration = 12;
  else if (schedule_mode == 2) duration = 16;
  else if (schedule_mode == 3) duration = 18;
  jsonData += "\"duration\":" + String(duration);
  jsonData += "}";
  
  Serial.println("JSON Payload Created: " + jsonData);
  // Kirim ke endpoint Laravel
  sendPostRequest(serverPathLaravel, jsonData);
}

// ========== FUNGSI GET CONTROL DATA ==========
void getControlData() {
  String httpResponse = sendGetRequest(serverPathControls);
  if (httpResponse == "ERROR") {
    Serial.println("Failed to get control data.");
    return;
  }
  
  int jsonStart = httpResponse.indexOf('{');
  int jsonEnd = httpResponse.lastIndexOf('}');
  if (jsonStart != -1 && jsonEnd != -1) {
    String jsonBody = httpResponse.substring(jsonStart, jsonEnd + 1);
    Serial.println("Received JSON: " + jsonBody);

    StaticJsonDocument<256> doc;
    DeserializationError error = deserializeJson(doc, jsonBody);

    if (error) {
      Serial.print(F("deserializeJson() failed: "));
      Serial.println(error.c_str());
      return;
    }

    // Update control variables dari server
    if (doc.containsKey("light_intensity")) {
      slider_light_intensity = doc["light_intensity"].as<int>();
      Serial.println("API Updated -> slider: " + slider_light_intensity);
    }

    if (doc.containsKey("schedule")) {
      schedule_mode = doc["schedule"].as<int>();
      Serial.println("API Updated -> schedule mode: " + schedule_mode);
    }

    if (doc.containsKey("light_status")) {
      light_status = doc["light_status"].as<int>();
      Serial.println("API Updated -> light status: " + light_status);
    }

  } else {
    Serial.println("No valid JSON found in response.");
  }
}

// ========== SETUP ==========
void setup() {
  Serial.begin(115200);
  espSerial.begin(115200);
  Wire.begin();
  lcd.begin(20, 4);
  lcdInit();
  rtc.begin();
  dht.begin();
  pinMode(pinCS, OUTPUT);
  pwmValue = 255;
  analogWrite(mosfetPin, pwmValue);
  connectWiFi();
  if (lightMeter.begin(BH1750::CONTINUOUS_HIGH_RES_MODE)) {
    Serial.println(F("BH1750 Ready"));
  }
  if (!SD.begin(pinCS)) {
    lcd.setCursor(0, 3);
    lcd.print("SD Card Failed!");
    delay(2000);
  }
  myFile = SD.open("Data.csv", FILE_WRITE);
  if (myFile) {
    myFile.println("Date,Time,Slider,Lux,Temp,Humidity,Voltage,Current,Power,Energy,PF,Freq,Duration");
    myFile.close();
  }
  pinMode(button1Pin, INPUT_PULLUP);
  pinMode(button2Pin, INPUT_PULLUP);
  pinMode(button3Pin, INPUT_PULLUP);
  pinMode(outputPin, OUTPUT);
  digitalWrite(outputPin, HIGH);
}

// ========== FUNGSI LCD ==========
void lcdInit() {
  lcd.clear();
  lcd.setCursor(0, 0);
  lcd.print("Energy Monitoring");
  lcd.setCursor(0, 1);
  lcd.print("System Loading...");
  delay(2000);
  lcd.clear();
}

// ========== FUNGSI WIFI ==========
void connectWiFi() {
  lcd.setCursor(0, 2);
  lcd.print("Connecting WiFi...");
  sendAT("AT+RST", 2000);
  sendAT("AT+CWMODE=1", 1000);
  String cmd = "AT+CWJAP=\"" + String(ssid) + "\",\"" + String(password) + "\"";
  String response = sendAT(cmd, 10000);
  if (response.indexOf("OK") != -1) {
    lcd.setCursor(0, 3);
    lcd.print("WiFi Connected      ");
    delay(1000);
  } else {
    lcd.setCursor(0, 3);
    lcd.print("WiFi Failed         ");
    delay(2000);
  }
}

// ========== FUNGSI UPDATE LCD ==========
void updateLCD(DateTime now) {
  char line1[21];
  sprintf(line1, "%02d/%02d/%04d %02d:%02d", now.day(), now.month(), now.year(), now.hour(), now.minute());
  lcd.setCursor(0, 0);
  lcd.print(line1);
  float temperature = dht.readTemperature();
  float lux = lightMeter.readLightLevel();
  lcd.setCursor(0, 1);
  lcd.print("S:");
  lcd.print(slider_light_intensity);
  lcd.print("% ");
  lcd.print("L:");
  lcd.print((int)lux);
  lcd.print(" ");
  lcd.print("T:");
  lcd.print(temperature, 1);
  lcd.print("C");
  lcd.setCursor(0, 2);
  lcd.print("V:");
  lcd.print(voltage, 1);
  lcd.print(" A:");
  lcd.print(current, 2);
  lcd.print(" P:");
  lcd.print(power, 1);
  lcd.print("W");
  lcd.setCursor(0, 3);
  lcd.print("E:");
  lcd.print(energy, 2);
  lcd.print("kWh");
}

// ========== FUNGSI SAVE TO SD ==========
void saveToSD() {
  DateTime now = rtc.now();
  float lux = lightMeter.readLightLevel();
  myFile = SD.open("Data.csv", FILE_WRITE);
  if (myFile) {
    myFile.print(now.year());
    myFile.print("-");
    myFile.print(now.month() < 10 ? "0" : "");
    myFile.print(now.month());
    myFile.print("-");
    myFile.print(now.day() < 10 ? "0" : "");
    myFile.print(now.day());
    myFile.print(",");
    myFile.print(now.hour() < 10 ? "0" : "");
    myFile.print(now.hour());
    myFile.print(":");
    myFile.print(now.minute() < 10 ? "0" : "");
    myFile.print(now.minute());
    myFile.print(":");
    myFile.print(now.second() < 10 ? "0" : "");
    myFile.print(now.second());
    myFile.print(",");
    myFile.print(slider_light_intensity);
    myFile.print(",");
    myFile.print(lux);
    myFile.print(",");
    myFile.print(dht.readTemperature());
    myFile.print(",");
    myFile.print(dht.readHumidity());
    myFile.print(",");
    myFile.print(voltage);
    myFile.print(",");
    myFile.print(current);
    myFile.print(",");
    myFile.print(power);
    myFile.print(",");
    myFile.print(energy);
    myFile.print(",");
    myFile.print(pf);
    myFile.print(",");
    myFile.print(frequency);
    myFile.print(",");
    if (schedule_mode == 1) myFile.println("12");
    else if (schedule_mode == 2) myFile.println("16");
    else if (schedule_mode == 3) myFile.println("18");
    else myFile.println("0");
    myFile.close();
  }
}

// ========== FUNGSI UPDATE SENSORS ==========
void updateSensors() {
  float v = pzem.voltage();
  if (!isnan(v)) voltage = v;
  else voltage = 0;
  float i = pzem.current();
  if (!isnan(i)) current = i;
  else current = 0;
  float p = pzem.power();
  if (!isnan(p)) power = p;
  else power = 0;
  float e = pzem.energy();
  if (!isnan(e)) energy = e;
  float f = pzem.frequency();
  if (!isnan(f)) frequency = f;
  else frequency = 0;
  float p_f = pzem.pf();
  if (!isnan(p_f)) pf = p_f;
  else pf = 0;

  // Hapus pembacaan potentiometer karena sekarang dikontrol dari website
  // slider_light_intensity = map(analogRead(potPin), 0, 1023, 0, 100);
}

// ========== LOOP PRINCIPAL ==========
void loop() {
  unsigned long currentMillis = millis();
  DateTime now = rtc.now();
  readButtons();
  updateSensors();
  updateLCD(now);
  
  if (currentMillis - startMillisSD >= periodSD) {
    saveToSD();
    startMillisSD = currentMillis;
  }
  if (currentMillis - startMillisHTTP >= periodHTTP) {
    sendHTTPData();
    startMillisHTTP = currentMillis;
  }
  if (currentMillis - startMillisControls >= periodControls) {
    getControlData();
    startMillisControls = currentMillis;
  }
  controlSchedule(now);
}

// ========== FUNGSI READ BUTTONS ==========
void readButtons() {
  if (digitalRead(button1Pin) == LOW) {
    delay(50);
    if (digitalRead(button1Pin) == LOW) {
      schedule_mode = 1;
    }
  } else if (digitalRead(button2Pin) == LOW) {
    delay(50);
    if (digitalRead(button2Pin) == LOW) {
      schedule_mode = 2;
    }
  } else if (digitalRead(button3Pin) == LOW) {
    delay(50);
    if (digitalRead(button3Pin) == LOW) {
      schedule_mode = 3;
    }
  } else if (digitalRead(button1Pin) == LOW && digitalRead(button2Pin) == LOW) {
    delay(50);
    schedule_mode = 0;
  }
}

// ========== FUNGSI CONTROL SCHEDULE ==========
void controlSchedule(DateTime now) {
  if (schedule_mode == 0) {  // MANUAL MODE
    if (light_status == 0) {
      digitalWrite(outputPin, HIGH);  // lampu OFF
    } else {
      digitalWrite(outputPin, LOW);   // lampu ON
    }
    Serial.println(String() + "Schedule mode : " + schedule_mode);
    Serial.println(String() + "Light status : " + light_status);
  }

  scheduleActive = (schedule_mode > 0);
  if (scheduleActive) {
    bool isTimeOn = false;
    switch (schedule_mode) {
      case 1:
        if (now.hour() >= 6 && now.hour() < 18) { isTimeOn = true; }
        break;
      case 2:
        if (now.hour() >= 6 && now.hour() < 22) { isTimeOn = true; }
        break;
      case 3:
        if (now.hour() >= 6) { isTimeOn = true; }
        break;
    }
    if (isTimeOn) {
      digitalWrite(outputPin, LOW);
    } else {
      digitalWrite(outputPin, HIGH);
    }
  }

  // Update PWM berdasarkan intensitas dari website
  slider_light_intensity = roundToNearest10(slider_light_intensity);
  slider_light_intensity = constrain(slider_light_intensity, 0, 100);
  pwmValue = map(slider_light_intensity, 0, 100, 255, 75);
  analogWrite(mosfetPin, pwmValue);
  Serial.println(String() + "PWM : " + pwmValue);
}

// Tambahkan fungsi pembulatan ke kelipatan 10 terdekat
template<typename T>
T roundToNearest10(T value) {
  return ((value + 5) / 10) * 10;
}