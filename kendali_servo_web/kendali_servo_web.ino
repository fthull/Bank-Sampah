#include <ESP8266WiFi.h>
#include <ESP8266WebServer.h>
#include <Servo.h>

// const char* ssid     = "TP-LINK_POCKET_3020_E353A8";      // <-- ganti
const char* ssid     = "Lobby";      // <-- ganti
// const char* password = "84173164";  // <-- ganti
const char* password = "UTDI-jogja";  // <-- ganti

ESP8266WebServer server(80);
Servo myServo;

void sendCORS(int code, const String& type, const String& body){
  server.sendHeader("Access-Control-Allow-Origin", "*");
  server.sendHeader("Access-Control-Allow-Methods", "GET, OPTIONS");
  server.sendHeader("Access-Control-Allow-Headers", "Content-Type");
  server.send(code, type, body);
}

void setup() {
  Serial.begin(115200);

  myServo.attach(D2, 600, 2500); // servo di D2, hasil kalibrasi Anda

  WiFi.mode(WIFI_STA);
  WiFi.begin(ssid, password);
  Serial.print("Menghubungkan WiFi");
  while (WiFi.status() != WL_CONNECTED) { delay(400); Serial.print("."); }
  Serial.printf("\nTersambung! IP: %s\n", WiFi.localIP().toString().c_str());

  // Endpoint utama
  server.on("/", HTTP_GET, [](){
    sendCORS(200, "application/json",
             "{\"endpoints\":[\"/servo?pos=<0-180>\",\"/center\"]}");
  });

  // Endpoint atur posisi servo
  server.on("/servo", HTTP_GET, [](){
    if (server.hasArg("pos")) {
      int pos = server.arg("pos").toInt();
      pos = constrain(pos, 0, 180);
      myServo.write(pos);
      sendCORS(200, "application/json",
               String("{\"ok\":true,\"pos\":") + pos + "}");
    } else {
      sendCORS(400, "application/json", "{\"error\":\"parameter pos wajib\"}");
    }
  });

  // Endpoint cepat ke tengah (90 derajat)
  server.on("/center", HTTP_GET, [](){
    myServo.write(90);
    sendCORS(200, "application/json", "{\"ok\":true,\"pos\":90}");
  });

  // CORS handling
  server.onNotFound([](){
    if (server.method()==HTTP_OPTIONS) return sendCORS(204, "text/plain", "");
    sendCORS(404, "application/json", "{\"error\":\"not found\"}");
  });

  server.begin();
  Serial.println("HTTP API siap (port 80)");
}

void loop() {
  server.handleClient();
}