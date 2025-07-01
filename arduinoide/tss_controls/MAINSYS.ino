void RUN_SYSTEM() {
  xTaskCreate(SYSTEM_READ_SENSOR, "1", 5000, NULL, 1, NULL);
  xTaskCreate(SYSTEM_MAIN_CONTROL, "2", 5000, NULL, 1, NULL);
  xTaskCreate(SYSTEM_FETCH_DATA, "3", 5000, NULL, 1, NULL);
}

void SYSTEM_READ_SENSOR(void* parameter) {
  while (true) {
    SENSOR_RANDOM();
    //
    vTaskDelay(pdMS_TO_TICKS(500));
  }
}

void SYSTEM_MAIN_CONTROL(void* parameter) {
  while (true) {
    // switch (mode) {
    //   case 1:
    //     AUTOMATIC_MODE();
    //     break;
    //   case 0:
    //     MANUAL_MODE();
    //     break;
    //   default:
    //     MANUAL_MODE();
    //     break;
    // }
    //
    vTaskDelay(pdMS_TO_TICKS(500));
  }
}

void SYSTEM_FETCH_DATA(void* parameter) {
  while (true) {
    FETCH();
    //
    vTaskDelay(pdMS_TO_TICKS(500));
  }
}