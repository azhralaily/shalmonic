// void AUTOMATIC_MODE() {
//   Serial.println(">>> AUTOMATIC MODE ENABLED <<<");

//   // LOGIC PPM
//   if (ppm >= set_ppm_min && ppm <= set_ppm_max) {
//     stat_pompa_a = "OFF";
//     stat_pompa_b = "OFF";
//   } else {
//     stat_pompa_a = "ON";
//     stat_pompa_b = "ON";
//   }

//   // LOGIC PH
//   if (ph >= set_ph_min && ph <= set_ph_max) {
//     stat_pompa_up = "OFF";
//     stat_pompa_down = "OFF";
//   } else if (ph < set_ph_min) {
//     stat_pompa_up = "OFF";
//     stat_pompa_down = "ON";
//   } else if (ph > set_ph_max) {
//     stat_pompa_up = "ON";
//     stat_pompa_down = "OFF";
//   }

//   // LOGIC TIMER
//   // buat disini 0 = Auto
//   pump = 0;
//   mixer = 0;
// }

// ////////////////////////////////////////////////////////////////////////////////////////////////////////

// void MANUAL_MODE() {
//   Serial.println(">>> MANUAL MODE ENABLED <<<");

//   // Mengecek status setiap switch menggunakan switch-case
//   switch (switch1) {
//     case 1:
//       stat_pompa_up = "ON";
//       break;
//     default:
//       stat_pompa_up = "OFF";
//       break;
//   }

//   switch (switch2) {
//     case 1:
//       stat_pompa_down = "ON";
//       break;
//     default:
//       stat_pompa_down = "OFF";
//       break;
//   }

//   switch (switch3) {
//     case 1:
//       stat_pompa_a = "ON";
//       break;
//     default:
//       stat_pompa_a = "OFF";
//       break;
//   }

//   switch (switch4) {
//     case 1:
//       stat_pompa_b = "ON";
//       break;
//     default:
//       stat_pompa_b = "OFF";
//       break;
//   }

//   // LOGIC TIMER
//   if (pump == 0) {
//     // mode auto
//     Serial.println("Pompa Auto");
//   } else {
//     Serial.println(String() + "Timer Pompa " + pump + " Menit!");
//     // pump = millis(); buat countdown timer disini
//   }

//   if (mixer == 0) {
//     // mode auto
//     Serial.println("Mixer Auto");
//   } else {
//     Serial.println(String() + "Timer Mixer " + mixer + " Menit!");
//     // mixer = millis(); buat countdown timer disini
//   }
// }