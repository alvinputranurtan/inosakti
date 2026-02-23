#include <WiFi.h>
#include <PubSubClient.h>

const char* WIFI_SSID = "YOUR_WIFI_SSID";
const char* WIFI_PASS = "YOUR_WIFI_PASSWORD";
const char* MQTT_HOST = "broker.hivemq.com";
const int MQTT_PORT = 1883;
const char* DEVICE_ID = "esp32-lab-01";
const char* TOPIC_TELEMETRY = "inosakti/esp32/esp32-lab-01/telemetry";
const char* TOPIC_COMMAND = "inosakti/esp32/esp32-lab-01/command";

WiFiClient wifiClient;
PubSubClient mqttClient(wifiClient);

unsigned long lastPublishMs = 0;
bool ledState = false;

void connectWiFi() {
  WiFi.mode(WIFI_STA);
  WiFi.begin(WIFI_SSID, WIFI_PASS);
  while (WiFi.status() != WL_CONNECTED) {
    delay(400);
  }
}

void onMessage(char* topic, byte* payload, unsigned int length) {
  String cmd;
  for (unsigned int i = 0; i < length; i++) {
    cmd += (char) payload[i];
  }
  if (String(topic) == TOPIC_COMMAND) {
    if (cmd == "LED_ON") {
      ledState = true;
      digitalWrite(2, HIGH);
    } else if (cmd == "LED_OFF") {
      ledState = false;
      digitalWrite(2, LOW);
    }
  }
}

void connectMQTT() {
  while (!mqttClient.connected()) {
    String clientId = String("esp32-") + DEVICE_ID + "-" + String(random(1000, 9999));
    if (mqttClient.connect(clientId.c_str())) {
      mqttClient.subscribe(TOPIC_COMMAND, 1);
    } else {
      delay(1500);
    }
  }
}

void publishTelemetry() {
  float temp = 26.5 + random(-15, 15) / 10.0;
  float hum = 65.0 + random(-20, 20) / 10.0;

  char payload[180];
  snprintf(payload, sizeof(payload),
           "{\"device_id\":\"%s\",\"temperature_c\":%.1f,\"humidity_pct\":%.1f,\"led\":%s}",
           DEVICE_ID, temp, hum, ledState ? "true" : "false");
  mqttClient.publish(TOPIC_TELEMETRY, payload, false);
}

void setup() {
  pinMode(2, OUTPUT);
  digitalWrite(2, LOW);
  Serial.begin(115200);
  connectWiFi();
  mqttClient.setServer(MQTT_HOST, MQTT_PORT);
  mqttClient.setCallback(onMessage);
}

void loop() {
  if (!mqttClient.connected()) {
    connectMQTT();
  }
  mqttClient.loop();

  unsigned long now = millis();
  if (now - lastPublishMs >= 5000) {
    lastPublishMs = now;
    publishTelemetry();
  }
}

