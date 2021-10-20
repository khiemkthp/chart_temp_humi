#include <Wire.h>
#include "DHT.h"

const int DHTPIN = 5;       //Đọc dữ liệu từ DHT11 ở chân 2 trên mạch Arduino
const int DHTTYPE = DHT11;  
DHT dht(DHTPIN, DHTTYPE);
float temp, humi;
String data;
int piezoPin = 3;

void setup() {
  
 Serial.begin(9600);
 dht.begin();
 Wire.begin(8);                /* join i2c bus with address 8 */
 Wire.onReceive(receiveEvent); /* register receive event */
 Wire.onRequest(requestEvent); /* register request event */
 /* start serial for debug */
}



// function that executes whenever data is received from master
void receiveEvent(int howMany) {
  String result;
 while (0 <Wire.available()) {
    char c = Wire.read();      /* receive byte as a character */
    Serial.print(c); /* print the character */
    result = String(result+c);
    
   
  }
  if(result=="warning") {
      
      tone(piezoPin, 3000,1000);
    } else {
      noTone(piezoPin); 
      } 
 Serial.println();             /* to newline */
}

// function that executes whenever data is requested from master
  void requestEvent() {
    data="";
    data = String(temp) +","+ String(humi);
    Serial.println(data.c_str());
    Wire.write(data.c_str());  /*send string on request */
}

void loop() {
 
 temp = dht.readTemperature();
 humi = dht.readHumidity();
}
