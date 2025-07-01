void FETCH() {
  GET_CONTROLS();
  POST_DATA();
}

bool fetch_data(String url, DynamicJsonDocument& doc) {
  WiFiClient client;
  HTTPClient http;
  http.begin(client, url);
  int httpcode = http.GET();

  if (httpcode == 200) {
    String respon = http.getString();
    if (respon.isEmpty()) {
      Serial.println("[❌] GET Response Kosong!");
      http.end();
      return false;
    }

    DeserializationError error = deserializeJson(doc, respon);
    if (error) {
      Serial.println("[❌] JSON Parsing Error!");
      http.end();
      return false;
    }

    http.end();
    return true;
  }

  Serial.printf("[❌] GET Error (%d)\n", httpcode);
  http.end();
  return false;
}

void GET_CONTROLS() {
  DynamicJsonDocument doc(100);
  if (fetch_data(endpoint + "/tss/api_controls.php", doc)) {
    set_light_intensity = doc["light_intensity"];
    set_schedule = doc["schedule"];
    set_light_status = doc["light_status"];
    Serial.printf("Control Data : %d | %d | %d\n", set_light_intensity, set_schedule, set_light_status);
  }
}

void POST_DATA() {
  // Buat payload JSON sekali saja
  StaticJsonDocument<200> doc;
  doc["ppm"] = ppm;
  doc["light_intensity"] = light_intensity;
  doc["temp"] = temp;
  doc["humid"] = humid;
  doc["vpd"] = vpd;
  doc["batt"] = batt;
  doc["current"] = current;
  doc["voltage"] = voltage;
  doc["power"] = power;
  doc["pf"] = pf;
  doc["freq"] = freq;
  doc["energy"] = energy;

  String datastream;
  serializeJson(doc, datastream);
  Serial.println("POST Data: " + datastream);

  // Fungsi helper untuk POST ke endpoint
  auto post_to_endpoint = [](const String& url, const String& data) {
    WiFiClient client;
    HTTPClient http;
    http.begin(client, url);
    http.addHeader("Content-Type", "application/json");
    int code = http.POST(data);
    http.end();
    return code;
  };

  // Kirim ke kedua endpoint
  int code1 = post_to_endpoint(endpoint + "/tss/api_datastream.php", datastream);
  int code2 = post_to_endpoint(endpoint + "/tss/api_savedb.php", datastream);

  // Handle response
  if (code1 == 200 && code2 == 200) {
    Serial.println("[✅] POST ke semua endpoint berhasil!");
  } else {
    Serial.printf("[❌] POST gagal: datastream=%d, database=%d\n", code1, code2);
  }
}