#include <ESP8266WiFiMulti.h>
#include <ESP8266HTTPClient.h>
#include <ESP8266WiFi.h>
#include <Wire.h>

const char* ssid = "xiaomi1122"; // Tên wifi
const char* password = "150119999"; // Pass wifi
char host[] = "192.168.43.58";
const int port = 81;


void setup() {
  Serial.begin(9600); // COM
  
  Wire.begin(D1, D2);
  Serial.print("Connecting to the Newtork");
  WiFi.begin(ssid, password);
  while (WiFi.status() != WL_CONNECTED)
  {
    delay(500);
    Serial.print(".");
  }
  Serial.println("");
  Serial.println("WiFi connected");
  Serial.println("IP address: ");
  Serial.println(WiFi.localIP());
  
}
void loop() {
  Wire.beginTransmission(8); /* begin with device address 8 */
  Wire.write("Hello Arduino");  /* sends hello string */
  Wire.endTransmission();    /* stop transmitting */
  
  String result, t, h;
  byte moc;
  Wire.requestFrom(8, 13); /* request & read data of size 13 from slave */
  while(Wire.available()){
    char c = Wire.read();
    result = String(result+c);
  Serial.print(c);
 }
  for(int i = 0; i< result.length(); i++) {
    if(result.charAt(i)==',') {
        moc = i;
      }
    }
 t = result;
 h = result;
 t.remove(moc);
 h.remove(0,moc +1);
 float temp = t.toFloat();
 float humi = h.toFloat();
 
 Serial.println();
 



  ////////////////////////////////////////////////////////////
  if(isnan(temp) || isnan(humi)) {
      Serial.println("Error");
      return;
    }
  Serial.print("connecting to ");
  Serial.println(host);
  // Use WiFiClient class to create TCP connections
  WiFiClient client;
  if (!client.connect(host, port)) {
    Serial.println("connection failed");
    return;
  }
 // We now create a URI for the request
  String url = "/?temp="+String(temp)+"&humi="+String(humi);
  // Send request
  Serial.print("Requesting URL: ");
  Serial.println(url);

  client.print(String("GET ") + url + " HTTP/1.1\r\n" +"Host: " + host + "\r\n" +"Connection: close\r\n\r\n");
  unsigned long timeout = millis();
  while (client.available() == 0) {
  if (millis() - timeout > 5000) {
    Serial.println(">>> Client Timeout !");
    client.stop();
    return;
  }
 }
  // Read all the lines from the answer
  while(client.available()){
    String line = client.readStringUntil('\0');
    Serial.print(line);
    byte mo1,mo2;
    for(int i = 0; i< line.length(); i++) {
    if(line.charAt(i)=='_') {
        mo1 = i;
      }
    }
    String rs = line;
    rs.remove(0,mo1+1);
    for(int i = 0; i< rs.length(); i++) {
      if(rs.charAt(i)=='+') {
          mo2 = i;
        }
    }
    rs.remove(mo2);
    
    Serial.println(rs);
    if(rs=="warning") {
      
     Wire.beginTransmission(8);
     Wire.write(rs.c_str());
     Wire.endTransmission();
    } 
  }
  
  // Close connecting
   Serial.println();
   Serial.println("closing connection");  
}
