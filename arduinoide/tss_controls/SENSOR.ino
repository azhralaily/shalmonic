void SENSOR_RANDOM() {
  ppm = random(600, 630);
  light_intensity = random(700, 720) / 100.0;
  temp = random(0, 1000) / 10.0;
  humid = random(0, 1000) / 10.0;
  vpd = random(0, 1000);

  // ELECTRICALS
  batt = random(0, 100);
  current = random(0, 1000) / 10.0;
  voltage = random(0, 1000) / 10.0;
  power = random(0, 1000) / 10.0;
  pf = random(0, 1000) / 10.0;
  freq = random(0, 1000) / 10.0;
  energy = random(0, 1000) / 10.0;
}