#include <ESP8266WiFiMulti.h>
#include <ESP8266HTTPClient.h>
#include <ESP8266WiFi.h>
#include <Wire.h>  
#include <LiquidCrystal_I2C.h>

LiquidCrystal_I2C lcd(0x27,16,2);

const char* ssid = "xiaomi1122"; // Tên wifi
const char* password = "150119999"; // Pass wifi
char host[] = "192.168.43.58";
const int port = 81;

void setup() {
  Wire.begin(D2, D1);
  // put your setup code here, to run once:
  lcd.begin();   // iInit the LCD for 16 chars 2 lines
  lcd.backlight();   // Turn on the backligt (try lcd.noBaklight() to turn it off)
  Serial.begin(9600); // COM

  //Wire.begin(D2, D1);
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
  // put your main code here, to run repeatedly:


  Serial.print("connecting to ");
  Serial.println(host);
  // Use WiFiClient class to create TCP connections
  WiFiClient client;
  if (!client.connect(host, port)) {
    Serial.println("connection failed");
    return;
  }
 // We now create a URI for the request
  String url = "/?check=1503";
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
    
    byte mo1, mo2, mo3;
    for(int i = 0; i< line.length(); i++) {
    if(line.charAt(i)=='*') {
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
    String warn = rs;
    for(int i = 0; i< rs.length(); i++) {
      if(rs.charAt(i)=='_') {
          mo3 = i;
        }
    }
    warn.remove(0, mo3+1);
    rs.remove(mo3);
    //Serial.println(mo1);
    //Serial.println(mo2);
    //Serial.println(mo3);
   if(!isnan(mo1)) {
      Serial.println("Hee");
      lcd.setCursor(2,0);
      lcd.print(rs);
      lcd.setCursor(4,1);
      lcd.print(warn+"   ");
    }
  }
  
  // Close connecting
   Serial.println();
   Serial.println("closing connection");  
}
